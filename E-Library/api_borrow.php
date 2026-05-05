<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$user_id = get_user_id();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'borrow':
        $book_id = $_POST['book_id'] ?? 0;
        $borrower_name = $_POST['borrower_name'] ?? '';
        $borrower_phone = $_POST['borrower_phone'] ?? '';
        $borrower_address = $_POST['borrower_address'] ?? '';
        $due_date = $_POST['due_date'] ?? '';
        
        // Validasi input
        if (!$book_id || !$borrower_name || !$borrower_phone || !$borrower_address || !$due_date) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
            exit;
        }
        
        // Mulai transaksi
        $conn->begin_transaction();
        
        try {
            // Cek apakah user sudah meminjam buku ini dan belum dikembalikan
            $stmt = $conn->prepare("
                SELECT id FROM borrows 
                WHERE user_id = ? AND book_id = ? AND status = 'dipinjam'
            ");
            $stmt->bind_param("ii", $user_id, $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                throw new Exception('Anda sudah meminjam buku ini. Kembalikan terlebih dahulu.');
            }
            
            // Cek stok buku dengan FOR UPDATE untuk lock row
            $stmt = $conn->prepare("SELECT stok FROM books WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception('Buku tidak ditemukan');
            }
            
            $book = $result->fetch_assoc();
            $current_stock = intval($book['stok']);
            
            if ($current_stock <= 0) {
                throw new Exception('Stok buku habis. Tidak dapat meminjam.');
            }
            
            // Kurangi stok buku
            $new_stock = $current_stock - 1;
            $stmt = $conn->prepare("UPDATE books SET stok = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_stock, $book_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Gagal mengurangi stok buku');
            }
            
            // Insert data peminjaman
            $borrow_date = date('Y-m-d');
            $stmt = $conn->prepare("
                INSERT INTO borrows (user_id, book_id, borrower_name, borrower_phone, borrower_address, borrow_date, due_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'dipinjam')
            ");
            $stmt->bind_param("iisssss", $user_id, $book_id, $borrower_name, $borrower_phone, $borrower_address, $borrow_date, $due_date);
            
            if (!$stmt->execute()) {
                throw new Exception('Gagal menyimpan data peminjaman');
            }
            
            // Commit transaksi
            $conn->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => '✅ Berhasil meminjam buku! Stok berkurang dari ' . $current_stock . ' menjadi ' . $new_stock,
                'new_stock' => $new_stock
            ]);
            
        } catch (Exception $e) {
            // Rollback jika ada error
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'return':
        $borrow_id = $_POST['borrow_id'] ?? 0;
        
        if (!$borrow_id) {
            echo json_encode(['success' => false, 'message' => 'ID peminjaman tidak valid']);
            exit;
        }
        
        // Mulai transaksi
        $conn->begin_transaction();
        
        try {
            // Ambil data peminjaman dengan FOR UPDATE
            $stmt = $conn->prepare("
                SELECT book_id, status FROM borrows 
                WHERE id = ? AND user_id = ? FOR UPDATE
            ");
            $stmt->bind_param("ii", $borrow_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception('Data peminjaman tidak ditemukan');
            }
            
            $borrow = $result->fetch_assoc();
            
            if ($borrow['status'] === 'dikembalikan') {
                throw new Exception('Buku ini sudah dikembalikan sebelumnya');
            }
            
            // Update status peminjaman
            $return_date = date('Y-m-d');
            $stmt = $conn->prepare("
                UPDATE borrows 
                SET status = 'dikembalikan', return_date = ? 
                WHERE id = ?
            ");
            $stmt->bind_param("si", $return_date, $borrow_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Gagal mengupdate status peminjaman');
            }
            
            // Tambah stok buku kembali dengan FOR UPDATE
            $book_id = $borrow['book_id'];
            $stmt = $conn->prepare("SELECT stok FROM books WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $book = $result->fetch_assoc();
            
            $old_stock = intval($book['stok']);
            $new_stock = $old_stock + 1;
            
            $stmt = $conn->prepare("UPDATE books SET stok = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_stock, $book_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Gagal menambah stok buku');
            }
            
            // Commit transaksi
            $conn->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => '✅ Buku berhasil dikembalikan! Stok bertambah dari ' . $old_stock . ' menjadi ' . $new_stock,
                'new_stock' => $new_stock
            ]);
            
        } catch (Exception $e) {
            // Rollback jika ada error
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;
        
    case 'check':
        $book_id = $_GET['book_id'] ?? 0;
        
        if (!$book_id) {
            echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
            exit;
        }
        
        $stmt = $conn->prepare("
            SELECT id FROM borrows 
            WHERE user_id = ? AND book_id = ? AND status = 'dipinjam'
        ");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo json_encode([
            'success' => true, 
            'data' => ['is_borrowed' => $result->num_rows > 0]
        ]);
        break;
        
    case 'get_history':
        $stmt = $conn->prepare("
            SELECT b.*, bk.title, bk.author, bk.cover, bk.category,
                   DATE_FORMAT(b.borrow_date, '%d %M %Y') as borrow_date_formatted,
                   DATE_FORMAT(b.due_date, '%d %M %Y') as due_date_formatted,
                   DATE_FORMAT(b.return_date, '%d %M %Y') as return_date_formatted,
                   DATEDIFF(CURDATE(), b.due_date) as days_overdue
            FROM borrows b
            JOIN books bk ON b.book_id = bk.id
            WHERE b.user_id = ?
            ORDER BY b.borrow_date DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $history]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid']);
        break;
}
?>