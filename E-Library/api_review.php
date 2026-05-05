<?php 
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Get reviews for a book
if ($action === 'get_reviews') {
    try {
        $book_id = $_GET['book_id'] ?? '';

        if (empty($book_id)) {
            echo json_encode(['success' => false, 'message' => 'Book ID tidak valid']);
            exit;
        }

        // Cek apakah tabel review ada
        $checkTable = "SHOW TABLES LIKE 'review'";
        $result = $conn->query($checkTable);
        
        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => true, 
                'data' => []
            ]);
            exit;
        }

        // Ambil semua review untuk buku ini
        $stmt = $conn->prepare("
            SELECT r.*, u.username as user_name, 
                   DATE_FORMAT(r.created_at, '%d %b %Y') as date
            FROM review r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.book_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = [
                'id' => $row['id'],
                'rating' => intval($row['rating']),
                'text' => $row['review_text'],
                'user_name' => $row['user_name'] ?? 'Anonim',
                'date' => $row['date']
            ];
        }

        echo json_encode([
            'success' => true, 
            'data' => $reviews
        ]);
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Add review
if ($action === 'add') {
    try {
        $book_id = $_POST['book_id'] ?? '';
        $rating = $_POST['rating'] ?? 0;
        $review_text = $_POST['review_text'] ?? '';
        $user_id = get_user_id();

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
            exit;
        }

        if (empty($book_id) || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }

        if (empty($review_text)) {
            echo json_encode(['success' => false, 'message' => 'Ulasan tidak boleh kosong']);
            exit;
        }

        // Cek apakah tabel review ada, jika tidak buat
        $checkTable = "SHOW TABLES LIKE 'review'";
        $result = $conn->query($checkTable);
        
        if ($result->num_rows === 0) {
            // Buat tabel review jika belum ada
            $createTable = "
                CREATE TABLE review (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    book_id INT NOT NULL,
                    rating INT NOT NULL,
                    review_text TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (book_id) REFERENCES buku(id) ON DELETE CASCADE
                )
            ";
            if (!$conn->query($createTable)) {
                echo json_encode(['success' => false, 'message' => 'Gagal membuat tabel review']);
                exit;
            }
        }

        // Cek apakah user sudah pernah review buku ini
        $checkStmt = $conn->prepare("SELECT id FROM review WHERE user_id = ? AND book_id = ?");
        $checkStmt->bind_param("ii", $user_id, $book_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Update review yang sudah ada
            $updateStmt = $conn->prepare("
                UPDATE review 
                SET rating = ?, review_text = ?, created_at = NOW()
                WHERE user_id = ? AND book_id = ?
            ");
            $updateStmt->bind_param("isii", $rating, $review_text, $user_id, $book_id);
            
            if ($updateStmt->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Ulasan berhasil diperbarui!'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Gagal memperbarui ulasan: ' . $updateStmt->error
                ]);
            }
            $updateStmt->close();
        } else {
            // Insert review baru
            $insertStmt = $conn->prepare("
                INSERT INTO review (user_id, book_id, rating, review_text) 
                VALUES (?, ?, ?, ?)
            ");
            $insertStmt->bind_param("iiis", $user_id, $book_id, $rating, $review_text);
            
            if ($insertStmt->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Ulasan berhasil ditambahkan!',
                    'review_id' => $insertStmt->insert_id
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Gagal menambahkan ulasan: ' . $insertStmt->error
                ]);
            }
            $insertStmt->close();
        }
        
        $checkStmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Delete review
if ($action === 'delete') {
    try {
        $review_id = $_POST['review_id'] ?? '';
        $user_id = get_user_id();

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Anda harus login terlebih dahulu']);
            exit;
        }

        if (empty($review_id)) {
            echo json_encode(['success' => false, 'message' => 'Review ID tidak valid']);
            exit;
        }

        // Hapus review (hanya jika milik user yang login)
        $stmt = $conn->prepare("DELETE FROM review WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $review_id, $user_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Ulasan berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ulasan tidak ditemukan atau bukan milik Anda']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus ulasan: ' . $stmt->error]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
?>