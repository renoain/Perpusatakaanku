<?php 
error_reporting(0); // Matikan error reporting agar tidak mengganggu JSON output
ini_set('display_errors', 0);

require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Get all books
if ($action === 'list') {
    try {
        $query = "SELECT * FROM buku ORDER BY id DESC";
        $result = $conn->query($query);
        
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
            exit;
        }
        
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $books]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Check if book is borrowed by current user
if ($action === 'check_borrowed') {
    try {
        $book_id = $_GET['book_id'] ?? '';
        $user_id = get_user_id();

        if (!$user_id) {
            echo json_encode(['success' => false, 'isBorrowed' => false]);
            exit;
        }

        $stmt = $conn->prepare("SELECT id FROM peminjaman WHERE user_id = ? AND book_id = ? AND status = 'dipinjam'");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        echo json_encode([
            'success' => true,
            'isBorrowed' => $result->num_rows > 0
        ]);
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Borrow a book
if ($action === 'borrow') {
    try {
        $book_id = $_POST['book_id'] ?? '';
        $borrow_date = $_POST['borrow_date'] ?? '';
        $return_date = $_POST['return_date'] ?? '';
        $note = $_POST['note'] ?? '';
        $user_id = get_user_id();

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
            exit;
        }

        if (empty($book_id)) {
            echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
            exit;
        }

        if (empty($borrow_date) || empty($return_date)) {
            echo json_encode(['success' => false, 'message' => 'Tanggal harus diisi']);
            exit;
        }

        // Mulai transaction
        $conn->begin_transaction();

        // Cek stok buku dengan FOR UPDATE untuk mencegah race condition
        $stmt = $conn->prepare("SELECT id, title, stok FROM buku WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();

        if (!$book) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan']);
            exit;
        }

        $current_stock = intval($book['stok']);
        
        if ($current_stock <= 0) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Stok buku habis']);
            exit;
        }

        // Cek apakah user sudah meminjam buku ini dan belum mengembalikan
        $checkStmt = $conn->prepare("SELECT id FROM peminjaman WHERE user_id = ? AND book_id = ? AND status = 'dipinjam'");
        $checkStmt->bind_param("ii", $user_id, $book_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Anda sudah meminjam buku ini']);
            exit;
        }

        // Insert peminjaman - sesuaikan dengan struktur tabel peminjaman
        // Kolom: id, user_id, book_id, tanggal_pinjam, tanggal_kembali, tanggal_dikembalikan, status, denda, catatan, created_at
        $insertStmt = $conn->prepare("
            INSERT INTO peminjaman 
            (user_id, book_id, tanggal_pinjam, tanggal_kembali, status, catatan) 
            VALUES (?, ?, ?, ?, 'dipinjam', ?)
        ");
        $insertStmt->bind_param("iisss", $user_id, $book_id, $borrow_date, $return_date, $note);

        if (!$insertStmt->execute()) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan peminjaman: ' . $insertStmt->error]);
            exit;
        }

        // Kurangi stok buku
        $new_stock = $current_stock - 1;
        $updateStmt = $conn->prepare("UPDATE buku SET stok = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $new_stock, $book_id);
        
        if (!$updateStmt->execute()) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Gagal mengurangi stok: ' . $updateStmt->error]);
            exit;
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true, 
            'message' => 'Buku "' . $book['title'] . '" berhasil dipinjam! Stok berkurang dari ' . $current_stock . ' menjadi ' . $new_stock,
            'peminjaman_id' => $insertStmt->insert_id,
            'new_stock' => $new_stock
        ]);
        
        $stmt->close();
        $insertStmt->close();
        $updateStmt->close();
        
    } catch (Exception $e) {
        if ($conn) {
            $conn->rollback();
        }
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Add stock
if ($action === 'add_stock') {
    try {
        $book_id = $_POST['book_id'] ?? '';
        $amount = $_POST['amount'] ?? 0;

        if (empty($book_id) || $amount < 1) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE buku SET stok = stok + ? WHERE id = ?");
        $stmt->bind_param("ii", $amount, $book_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => "Stok berhasil ditambah $amount"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambah stok: ' . $stmt->error]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
?>