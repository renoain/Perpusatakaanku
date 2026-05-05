<?php
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ============================================
// TOGGLE WISHLIST (ADD/REMOVE)
// ============================================
if ($action == 'toggle' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = (int)$_POST['book_id'];

    if ($book_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
        exit;
    }

    try {
        // Check if wishlist table exists, create if not
        $check_table = $conn->query("SHOW TABLES LIKE 'wishlist'");
        
        if ($check_table->num_rows == 0) {
            $create_table = "CREATE TABLE wishlist (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                book_id INT NOT NULL,
                added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_wishlist (user_id, book_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (book_id) REFERENCES buku(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            if (!$conn->query($create_table)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal membuat tabel wishlist: ' . $conn->error
                ]);
                exit;
            }
        }

        // Check if book exists
        $book_check = $conn->query("SELECT id, title FROM buku WHERE id = $book_id");
        if ($book_check->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan']);
            exit;
        }

        $book = $book_check->fetch_assoc();

        // Check if already in wishlist
        $check_wishlist = $conn->query("SELECT id FROM wishlist WHERE user_id = $user_id AND book_id = $book_id");
        
        if ($check_wishlist->num_rows > 0) {
            // Remove from wishlist
            $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND book_id = $book_id";
            
            if ($conn->query($delete_query)) {
                echo json_encode([
                    'success' => true,
                    'action' => 'removed',
                    'in_wishlist' => false,
                    'message' => '❤️ Dihapus dari wishlist: ' . $book['title']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menghapus dari wishlist: ' . $conn->error
                ]);
            }
        } else {
            // Add to wishlist
            $insert_query = "INSERT INTO wishlist (user_id, book_id, added_at) VALUES ($user_id, $book_id, NOW())";
            
            if ($conn->query($insert_query)) {
                echo json_encode([
                    'success' => true,
                    'action' => 'added',
                    'in_wishlist' => true,
                    'message' => '💖 Ditambahkan ke wishlist: ' . $book['title']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal menambahkan ke wishlist: ' . $conn->error
                ]);
            }
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ============================================
// LIST WISHLIST
// ============================================
if ($action == 'list') {
    try {
        // Check if wishlist table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'wishlist'");
        
        if ($check_table->num_rows == 0) {
            echo json_encode([
                'success' => true,
                'data' => [],
                'message' => 'Tabel wishlist belum ada'
            ]);
            exit;
        }

        // Get user's wishlist with book details
        $query = "SELECT w.*, b.title, b.author, b.year, b.category, b.cover, b.description, b.stok 
                  FROM wishlist w 
                  JOIN buku b ON w.book_id = b.id 
                  WHERE w.user_id = $user_id 
                  ORDER BY w.added_at DESC";
        
        $result = $conn->query($query);
        
        if (!$result) {
            echo json_encode([
                'success' => false,
                'message' => 'Query error: ' . $conn->error
            ]);
            exit;
        }

        $wishlist = [];
        while ($row = $result->fetch_assoc()) {
            $wishlist[] = $row;
        }

        echo json_encode([
            'success' => true,
            'data' => $wishlist,
            'total' => count($wishlist)
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ============================================
// ADD TO WISHLIST
// ============================================
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = (int)$_POST['book_id'];

    if ($book_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
        exit;
    }

    try {
        // Check if wishlist table exists, create if not
        $check_table = $conn->query("SHOW TABLES LIKE 'wishlist'");
        
        if ($check_table->num_rows == 0) {
            $create_table = "CREATE TABLE wishlist (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                book_id INT NOT NULL,
                added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_wishlist (user_id, book_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (book_id) REFERENCES buku(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            if (!$conn->query($create_table)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal membuat tabel wishlist: ' . $conn->error
                ]);
                exit;
            }
        }

        // Check if book exists
        $book_check = $conn->query("SELECT id, title FROM buku WHERE id = $book_id");
        if ($book_check->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan']);
            exit;
        }

        $book = $book_check->fetch_assoc();

        // Check if already in wishlist
        $check_wishlist = $conn->query("SELECT id FROM wishlist WHERE user_id = $user_id AND book_id = $book_id");
        
        if ($check_wishlist->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Buku sudah ada di wishlist',
                'already_exists' => true
            ]);
            exit;
        }

        // Add to wishlist
        $insert_query = "INSERT INTO wishlist (user_id, book_id, added_at) VALUES ($user_id, $book_id, NOW())";
        
        if ($conn->query($insert_query)) {
            echo json_encode([
                'success' => true,
                'message' => 'Berhasil ditambahkan ke wishlist: ' . $book['title']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan ke wishlist: ' . $conn->error
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ============================================
// REMOVE FROM WISHLIST
// ============================================
if ($action == 'remove' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = (int)$_POST['book_id'];

    if ($book_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID buku tidak valid']);
        exit;
    }

    try {
        // Check if wishlist table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'wishlist'");
        
        if ($check_table->num_rows == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Tabel wishlist tidak ditemukan'
            ]);
            exit;
        }

        // Get book title for response message
        $book_query = $conn->query("SELECT b.title FROM buku b WHERE b.id = $book_id");
        $book_title = 'Buku';
        if ($book_query && $book_query->num_rows > 0) {
            $book_data = $book_query->fetch_assoc();
            $book_title = $book_data['title'];
        }

        // Remove from wishlist
        $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND book_id = $book_id";
        
        if ($conn->query($delete_query)) {
            if ($conn->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => '"' . $book_title . '" berhasil dihapus dari wishlist'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan di wishlist'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus dari wishlist: ' . $conn->error
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ============================================
// CHECK IF IN WISHLIST
// ============================================
if ($action == 'check' && isset($_GET['book_id'])) {
    $book_id = (int)$_GET['book_id'];

    try {
        $check_table = $conn->query("SHOW TABLES LIKE 'wishlist'");
        
        if ($check_table->num_rows == 0) {
            echo json_encode([
                'success' => true,
                'in_wishlist' => false
            ]);
            exit;
        }

        $check_query = $conn->query("SELECT id FROM wishlist WHERE user_id = $user_id AND book_id = $book_id");
        
        echo json_encode([
            'success' => true,
            'in_wishlist' => $check_query->num_rows > 0
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ============================================
// GET WISHLIST COUNT
// ============================================
if ($action == 'count') {
    try {
        $check_table = $conn->query("SHOW TABLES LIKE 'wishlist'");
        
        if ($check_table->num_rows == 0) {
            echo json_encode([
                'success' => true,
                'count' => 0
            ]);
            exit;
        }

        $count_query = $conn->query("SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id");
        $count_data = $count_query->fetch_assoc();

        echo json_encode([
            'success' => true,
            'count' => (int)$count_data['total']
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Invalid action
echo json_encode([
    'success' => false,
    'message' => 'Invalid action: ' . $action
]);
?>