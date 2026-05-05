<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';
$user_id = get_user_id();

switch ($action) {
    case 'borrow':
        // Pinjam buku
        $book_id = $_POST['book_id'] ?? 0;
        $tanggal_pinjam = $_POST['borrow_date'] ?? '';
        $tanggal_kembali = $_POST['return_date'] ?? '';
        $catatan = $_POST['note'] ?? '';
        
        // Check if book exists and has stock
        $check = $conn->query("SELECT * FROM buku WHERE id = $book_id");
        if ($check->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Buku tidak ditemukan!']);
            break;
        }
        
        $book = $check->fetch_assoc();
        if ($book['stok'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Stok buku habis!']);
            break;
        }
        
        // Check if user already borrowed this book
        $checkBorrow = $conn->query("SELECT * FROM peminjaman WHERE user_id = $user_id AND book_id = $book_id AND status = 'dipinjam'");
        if ($checkBorrow->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Anda sudah meminjam buku ini!']);
            break;
        }
        
        // Insert into peminjaman table
        $insertQuery = "INSERT INTO peminjaman (user_id, book_id, tanggal_pinjam, tanggal_kembali, catatan, status, created_at) 
                        VALUES ($user_id, $book_id, '$tanggal_pinjam', '$tanggal_kembali', '$catatan', 'dipinjam', NOW())";
        
        if ($conn->query($insertQuery)) {
            // Decrease stock
            $newStock = $book['stok'] - 1;
            $conn->query("UPDATE buku SET stok = $newStock WHERE id = $book_id");
            
            // Log to riwayat
            $conn->query("INSERT INTO riwayat (user_id, book_id, aktivitas, tanggal, keterangan) 
                         VALUES ($user_id, $book_id, 'pinjam', NOW(), 'Meminjam buku: {$book['title']}')");
            
            echo json_encode(['success' => true, 'message' => 'Berhasil meminjam buku! Stok otomatis berkurang.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal meminjam buku: ' . $conn->error]);
        }
        break;

    case 'return':
        // Kembalikan buku
        $borrow_id = $_POST['borrow_id'] ?? 0;
        
        // Get borrow record
        $check = $conn->query("SELECT p.*, b.title FROM peminjaman p JOIN buku b ON p.book_id = b.id WHERE p.id = $borrow_id AND p.user_id = $user_id AND p.status = 'dipinjam'");
        if ($check->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Data peminjaman tidak ditemukan!']);
            break;
        }
        
        $borrow = $check->fetch_assoc();
        $book_id = $borrow['book_id'];
        
        // Calculate denda if late
        $today = time();
        $return_date = strtotime($borrow['tanggal_kembali']);
        $denda = 0;
        $status = 'dikembalikan';
        
        if ($today > $return_date) {
            $days_late = floor(($today - $return_date) / (60 * 60 * 24));
            $denda = $days_late * 2000; // Rp 2000 per hari
            $status = 'terlambat';
        }
        
        // Update status to returned
        $updateQuery = "UPDATE peminjaman SET status = '$status', tanggal_dikembalikan = NOW(), denda = $denda WHERE id = $borrow_id";
        
        if ($conn->query($updateQuery)) {
            // Increase stock
            $conn->query("UPDATE buku SET stok = stok + 1 WHERE id = $book_id");
            
            // Log to riwayat
            $keterangan = "Mengembalikan buku: {$borrow['title']}";
            if ($denda > 0) {
                $keterangan .= " (Denda: Rp " . number_format($denda, 0, ',', '.') . ")";
            }
            $conn->query("INSERT INTO riwayat (user_id, book_id, aktivitas, tanggal, keterangan) 
                         VALUES ($user_id, $book_id, 'kembalikan', NOW(), '$keterangan')");
            
            if ($denda > 0) {
                $conn->query("INSERT INTO riwayat (user_id, book_id, aktivitas, tanggal, keterangan) 
                             VALUES ($user_id, $book_id, 'denda', NOW(), 'Denda keterlambatan: Rp " . number_format($denda, 0, ',', '.') . "')");
            }
            
            $message = 'Buku berhasil dikembalikan! Stok otomatis bertambah.';
            if ($denda > 0) {
                $message .= "\n\nDenda keterlambatan: Rp " . number_format($denda, 0, ',', '.');
            }
            
            echo json_encode(['success' => true, 'message' => $message, 'denda' => $denda]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengembalikan buku: ' . $conn->error]);
        }
        break;

    case 'extend':
        // Perpanjang peminjaman
        $borrow_id = $_POST['borrow_id'] ?? 0;
        $days = $_POST['days'] ?? 0;
        
        if ($days <= 0 || $days > 7) {
            echo json_encode(['success' => false, 'message' => 'Jumlah hari tidak valid! Maksimal 7 hari.']);
            break;
        }
        
        // Get borrow record
        $check = $conn->query("SELECT p.*, b.title FROM peminjaman p JOIN buku b ON p.book_id = b.id WHERE p.id = $borrow_id AND p.user_id = $user_id AND p.status = 'dipinjam'");
        if ($check->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Data peminjaman tidak ditemukan!']);
            break;
        }
        
        $borrow = $check->fetch_assoc();
        $book_id = $borrow['book_id'];
        
        // Extend return date
        $new_return_date = date('Y-m-d', strtotime($borrow['tanggal_kembali'] . " +$days days"));
        
        $updateQuery = "UPDATE peminjaman SET tanggal_kembali = '$new_return_date' WHERE id = $borrow_id";
        
        if ($conn->query($updateQuery)) {
            // Log to riwayat
            $conn->query("INSERT INTO riwayat (user_id, book_id, aktivitas, tanggal, keterangan) 
                         VALUES ($user_id, $book_id, 'perpanjang', NOW(), 'Memperpanjang peminjaman buku: {$borrow['title']} selama $days hari')");
            
            echo json_encode(['success' => true, 'message' => "Peminjaman berhasil diperpanjang $days hari!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperpanjang peminjaman: ' . $conn->error]);
        }
        break;

    case 'check_borrowed':
        // Check if user already borrowed this book
        $book_id = $_GET['book_id'] ?? 0;
        
        $check = $conn->query("SELECT * FROM peminjaman WHERE user_id = $user_id AND book_id = $book_id AND status = 'dipinjam'");
        
        echo json_encode(['success' => true, 'isBorrowed' => $check->num_rows > 0]);
        break;

    case 'get_active':
        // Get active borrowings
        $query = "SELECT p.*, b.title, b.author, b.cover 
                  FROM peminjaman p 
                  JOIN buku b ON p.book_id = b.id 
                  WHERE p.user_id = $user_id AND p.status = 'dipinjam' 
                  ORDER BY p.tanggal_pinjam DESC";
        
        $result = $conn->query($query);
        
        $borrowings = [];
        while ($row = $result->fetch_assoc()) {
            $borrowings[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $borrowings]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>