<?php 
require_once 'config.php';

// Check if admin is logged in (optional - remove if not using authentication)
// Uncomment lines below if you want to require login
/*
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
*/

// Get page parameter
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// Handle AJAX return book request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_book_ajax'])) {
    header('Content-Type: application/json');
    
    $borrow_id = (int)$_POST['borrow_id'];
    
    // Get borrowing data
    $borrow_query = $conn->query("SELECT * FROM peminjaman WHERE id = $borrow_id AND status = 'dipinjam'");
    
    if ($borrow_query->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Data peminjaman tidak ditemukan atau sudah dikembalikan!']);
        exit;
    }
    
    $borrow = $borrow_query->fetch_assoc();
    
    // Calculate late fee
    $return_date = date('Y-m-d');
    $due_date = $borrow['tanggal_kembali'];
    $denda = 0;
    $new_status = 'dikembalikan';
    
    if (strtotime($return_date) > strtotime($due_date)) {
        $days_late = floor((strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24));
        $denda = $days_late * 5000; // Rp 5.000 per hari
        $new_status = 'terlambat';
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update peminjaman status
        $update_query = "UPDATE peminjaman SET 
                         status = '$new_status', 
                         tanggal_dikembalikan = '$return_date',
                         denda = $denda 
                         WHERE id = $borrow_id";
        
        if (!$conn->query($update_query)) {
            throw new Exception('Gagal mengupdate status peminjaman');
        }
        
        // Update book stock (add back)
        $update_stock = "UPDATE buku SET stok = stok + 1 WHERE id = " . $borrow['book_id'];
        if (!$conn->query($update_stock)) {
            throw new Exception('Gagal mengupdate stok buku');
        }
        
        // Insert into riwayat (history) table
        $check_riwayat = $conn->query("SHOW TABLES LIKE 'riwayat'");
        if ($check_riwayat->num_rows > 0) {
            $keterangan = $new_status == 'terlambat' 
                ? "Mengembalikan buku (TERLAMBAT - Denda: Rp " . number_format($denda, 0, ',', '.') . ")"
                : "Mengembalikan buku tepat waktu";
            
            $book_info = $conn->query("SELECT title FROM buku WHERE id = " . $borrow['book_id'])->fetch_assoc();
            $keterangan .= ": " . $book_info['title'];
            
            $keterangan_safe = $conn->real_escape_string($keterangan);
            $insert_riwayat = "INSERT INTO riwayat (user_id, book_id, aktivitas, tanggal, keterangan) 
                              VALUES ({$borrow['user_id']}, {$borrow['book_id']}, 'kembalikan', NOW(), '$keterangan_safe')";
            
            if (!$conn->query($insert_riwayat)) {
                throw new Exception('Gagal menyimpan riwayat');
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        $message = 'Buku berhasil dikembalikan!';
        if ($denda > 0) {
            $message .= "\nDenda keterlambatan: Rp " . number_format($denda, 0, ',', '.');
        }
        
        echo json_encode([
            'success' => true, 
            'message' => $message,
            'denda' => $denda
        ]);
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ADD BOOK
    if (isset($_POST['add_book'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $author = $conn->real_escape_string($_POST['author']);
        $year = (int)$_POST['year'];
        $category = $conn->real_escape_string($_POST['category']);
        $cover = $conn->real_escape_string($_POST['cover']);
        $description = $conn->real_escape_string($_POST['description']);
        $stok = (int)$_POST['stok'];
        
        $query = "INSERT INTO buku (title, author, year, category, cover, description, stok, created_at) 
                  VALUES ('$title', '$author', $year, '$category', '$cover', '$description', $stok, NOW())";
        
        if ($conn->query($query)) {
            echo "<script>alert('Buku berhasil ditambahkan!'); window.location.href='admin.php?page=books';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
    
    // EDIT BOOK
    if (isset($_POST['edit_book'])) {
        $book_id = (int)$_POST['book_id'];
        $title = $conn->real_escape_string($_POST['title']);
        $author = $conn->real_escape_string($_POST['author']);
        $year = (int)$_POST['year'];
        $category = $conn->real_escape_string($_POST['category']);
        $cover = $conn->real_escape_string($_POST['cover']);
        $description = $conn->real_escape_string($_POST['description']);
        $stok = (int)$_POST['stok'];
        
        $query = "UPDATE buku SET title='$title', author='$author', year=$year, category='$category', 
                  cover='$cover', description='$description', stok=$stok WHERE id=$book_id";
        
        if ($conn->query($query)) {
            echo "<script>alert('Buku berhasil diupdate!'); window.location.href='admin.php?page=books';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
    
    // ADD USER
    if (isset($_POST['add_user'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username, email, password, created_at) 
                  VALUES ('$username', '$email', '$password', NOW())";
        
        if ($conn->query($query)) {
            echo "<script>alert('User berhasil ditambahkan!'); window.location.href='admin.php?page=users';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
    
    // EDIT USER
    if (isset($_POST['edit_user'])) {
        $user_id = (int)$_POST['user_id'];
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "UPDATE users SET username='$username', email='$email', password='$password' WHERE id=$user_id";
        } else {
            $query = "UPDATE users SET username='$username', email='$email' WHERE id=$user_id";
        }
        
        if ($conn->query($query)) {
            echo "<script>alert('User berhasil diupdate!'); window.location.href='admin.php?page=users';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Handle delete
if ($action == 'delete' && $id > 0) {
    if ($page == 'books') {
        $conn->query("DELETE FROM buku WHERE id = $id");
        echo "<script>alert('Buku berhasil dihapus!'); window.location.href='admin.php?page=books';</script>";
    } elseif ($page == 'users') {
        $conn->query("DELETE FROM users WHERE id = $id");
        echo "<script>alert('User berhasil dihapus!'); window.location.href='admin.php?page=users';</script>";
    } elseif ($page == 'reviews') {
        $conn->query("DELETE FROM review WHERE id = $id");
        echo "<script>alert('Ulasan berhasil dihapus!'); window.location.href='admin.php?page=reviews';</script>";
    }
}

// Check if tables exist
$tables_exist = true;
$error_message = '';

$check_peminjaman = $conn->query("SHOW TABLES LIKE 'peminjaman'");
if ($check_peminjaman->num_rows == 0) {
    $tables_exist = false;
    $error_message = "Tabel 'peminjaman' belum dibuat. Silakan import file FIX_TABLES.sql di phpMyAdmin.";
}

// Check if review table exists
$check_review = $conn->query("SHOW TABLES LIKE 'review'");
$review_exists = ($check_review->num_rows > 0);

// Get statistics
$total_books = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

if ($tables_exist) {
    $total_borrowed = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'")->fetch_assoc()['total'];
    $total_late = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam' AND tanggal_kembali < CURDATE()")->fetch_assoc()['total'];
} else {
    $total_borrowed = 0;
    $total_late = 0;
}

// Get total reviews
if ($review_exists) {
    $total_reviews = $conn->query("SELECT COUNT(*) as total FROM review")->fetch_assoc()['total'];
} else {
    $total_reviews = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard | E-Library</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body { font-family: 'Heebo', sans-serif; background: #f5f7fa; }
    .sidebar {
      position: fixed; top: 0; left: 0; bottom: 0; width: 260px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 20px 0; overflow-y: auto; z-index: 1000;
    }
    .sidebar .brand { padding: 0 20px 30px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
    .sidebar .brand h3 { color: white; margin: 0; font-weight: 700; }
    .sidebar .brand small { color: rgba(255,255,255,0.7); }
    .sidebar .nav-link {
      color: rgba(255,255,255,0.8); padding: 12px 20px; margin: 5px 0;
      border-radius: 0; transition: all 0.3s; display: flex; align-items: center;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      background: rgba(255,255,255,0.15); color: white;
    }
    .sidebar .nav-link i { width: 25px; font-size: 18px; }
    .main-content { margin-left: 260px; padding: 30px; }
    .stat-card {
      border-radius: 12px; padding: 25px; background: white;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: all 0.3s;
      border-left: 4px solid #667eea;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
    .stat-card .icon {
      width: 60px; height: 60px; border-radius: 12px; display: flex;
      align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px;
    }
    .stat-card.books { border-left-color: #667eea; }
    .stat-card.books .icon { background: #e8eaf6; color: #667eea; }
    .stat-card.users { border-left-color: #28a745; }
    .stat-card.users .icon { background: #e8f5e9; color: #28a745; }
    .stat-card.borrowed { border-left-color: #ffc107; }
    .stat-card.borrowed .icon { background: #fff8e1; color: #ffc107; }
    .stat-card.late { border-left-color: #dc3545; }
    .stat-card.late .icon { background: #ffebee; color: #dc3545; }
    .stat-card.reviews { border-left-color: #17a2b8; }
    .stat-card.reviews .icon { background: #e0f7fa; color: #17a2b8; }
    .table-card {
      background: white; border-radius: 12px; padding: 25px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-top: 30px;
    }
    .table-card h5 { margin-bottom: 20px; font-weight: 600; }
    .review-card {
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      transition: all 0.3s;
    }
    .review-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }
    .btn-return { position: relative; }
    .btn-return.loading { pointer-events: none; opacity: 0.6; }
    @media (max-width: 768px) {
      .sidebar { width: 100%; position: relative; }
      .main-content { margin-left: 0; }
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="brand text-center">
      <i class="fa fa-book fa-3x mb-2" style="color: white;"></i>
      <h3>E-Library</h3>
      <small>Admin Dashboard</small>
    </div>
    
    <nav class="nav flex-column">
      <a class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>" href="admin.php">
        <i class="fa fa-tachometer-alt"></i> Dashboard
      </a>
      <a class="nav-link <?php echo $page == 'books' ? 'active' : ''; ?>" href="admin.php?page=books">
        <i class="fa fa-book"></i> Kelola Buku
      </a>
      <a class="nav-link <?php echo $page == 'users' ? 'active' : ''; ?>" href="admin.php?page=users">
        <i class="fa fa-users"></i> Kelola User
      </a>
      <a class="nav-link <?php echo $page == 'borrowings' ? 'active' : ''; ?>" href="admin.php?page=borrowings">
        <i class="fa fa-book-reader"></i> Peminjaman
      </a>
      <a class="nav-link <?php echo $page == 'reviews' ? 'active' : ''; ?>" href="admin.php?page=reviews">
        <i class="fa fa-star"></i> Kelola Ulasan
      </a>
      <a class="nav-link <?php echo $page == 'history' ? 'active' : ''; ?>" href="admin.php?page=history">
        <i class="fa fa-history"></i> Riwayat
      </a>
      <hr style="border-color: rgba(255,255,255,0.2); margin: 20px;">
      <a class="nav-link" href="index.php">
        <i class="fa fa-home"></i> Ke Halaman Utama
      </a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <?php if (!$tables_exist && $page == 'dashboard'): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fa fa-exclamation-triangle me-2"></i>Database Belum Lengkap!</h5>
        <p class="mb-2"><?php echo $error_message; ?></p>
        <hr>
        <p class="mb-0"><strong>Solusi:</strong> Import file <code>FIX_TABLES.sql</code> di phpMyAdmin</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php
    // ============================================
    // ROUTING - DASHBOARD
    // ============================================
    if ($page == 'dashboard'):
    ?>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="mb-0">Dashboard Admin</h2>
          <p class="text-muted mb-0">Selamat datang di dashboard E-Library</p>
        </div>
        <div>
          <span class="text-muted"><i class="fa fa-calendar me-2"></i><?php echo date('l, d F Y'); ?></span>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
          <div class="stat-card books">
            <div class="icon"><i class="fa fa-book"></i></div>
            <h3 class="mb-1"><?php echo $total_books; ?></h3>
            <p class="text-muted mb-0">Total Buku</p>
            <small class="text-muted"><i class="fa fa-arrow-up text-success me-1"></i>Koleksi aktif</small>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
          <div class="stat-card users">
            <div class="icon"><i class="fa fa-users"></i></div>
            <h3 class="mb-1"><?php echo $total_users; ?></h3>
            <p class="text-muted mb-0">Total User</p>
            <small class="text-muted"><i class="fa fa-user-check text-success me-1"></i>Member terdaftar</small>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-6">
          <div class="stat-card borrowed">
            <div class="icon"><i class="fa fa-book-reader"></i></div>
            <h3 class="mb-1"><?php echo $total_borrowed; ?></h3>
            <p class="text-muted mb-0">Dipinjam</p>
            <small class="text-muted"><i class="fa fa-clock text-warning me-1"></i>Aktif</small>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-6">
          <div class="stat-card late">
            <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
            <h3 class="mb-1"><?php echo $total_late; ?></h3>
            <p class="text-muted mb-0">Terlambat</p>
            <small class="text-muted"><i class="fa fa-bell text-danger me-1"></i>Perlu tindakan</small>
          </div>
        </div>
        
        <div class="col-lg-2 col-md-6">
          <div class="stat-card reviews">
            <div class="icon"><i class="fa fa-star"></i></div>
            <h3 class="mb-1"><?php echo $total_reviews; ?></h3>
            <p class="text-muted mb-0">Ulasan</p>
            <small class="text-muted"><i class="fa fa-comment text-info me-1"></i>Total review</small>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="table-card">
        <h5><i class="fa fa-bolt text-warning me-2"></i>Quick Actions</h5>
        <div class="row g-3">
          <div class="col-md-2">
            <a href="admin.php?page=books&action=add" class="btn btn-primary w-100">
              <i class="fa fa-plus me-2"></i>Tambah Buku
            </a>
          </div>
          <div class="col-md-2">
            <a href="admin.php?page=users&action=add" class="btn btn-success w-100">
              <i class="fa fa-user-plus me-2"></i>Tambah User
            </a>
          </div>
          <div class="col-md-3">
            <a href="admin.php?page=borrowings" class="btn btn-info w-100">
              <i class="fa fa-list me-2"></i>Lihat Peminjaman
            </a>
          </div>
          <div class="col-md-2">
            <a href="admin.php?page=reviews" class="btn btn-warning w-100">
              <i class="fa fa-star me-2"></i>Ulasan
            </a>
          </div>
          <div class="col-md-3">
            <a href="admin.php?page=history" class="btn btn-secondary w-100">
              <i class="fa fa-history me-2"></i>Riwayat
            </a>
          </div>
        </div>
      </div>

      <?php if ($tables_exist): 
        $latest_borrowings = $conn->query("
          SELECT p.*, b.title, b.cover, u.username, u.email 
          FROM peminjaman p 
          JOIN buku b ON p.book_id = b.id 
          JOIN users u ON p.user_id = u.id 
          WHERE p.status = 'dipinjam'
          ORDER BY p.created_at DESC 
          LIMIT 10
        ");
        
        $low_stock = $conn->query("SELECT * FROM buku WHERE stok <= 3 ORDER BY stok ASC LIMIT 10");
      ?>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="table-card">
            <h5><i class="fa fa-clock text-primary me-2"></i>Peminjaman Terbaru</h5>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Harus Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody id="borrowingsTable">
                  <?php if ($latest_borrowings->num_rows > 0): ?>
                    <?php while ($row = $latest_borrowings->fetch_assoc()): 
                      $is_late = strtotime($row['tanggal_kembali']) < time();
                    ?>
                      <tr id="borrow-row-<?php echo $row['id']; ?>">
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars(substr($row['title'], 0, 30)); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_kembali'])); ?></td>
                        <td>
                          <span class="badge bg-<?php echo $is_late ? 'danger' : 'warning'; ?>">
                            <?php echo $is_late ? 'TERLAMBAT' : 'Dipinjam'; ?>
                          </span>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-success btn-return" onclick="returnBook(<?php echo $row['id']; ?>)" data-borrow-id="<?php echo $row['id']; ?>">
                            <i class="fa fa-undo"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr id="no-data-row"><td colspan="6" class="text-center">Tidak ada peminjaman aktif</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="table-card">
            <h5><i class="fa fa-exclamation-triangle text-danger me-2"></i>Stok Terbatas</h5>
            <div class="list-group list-group-flush">
              <?php while ($book = $low_stock->fetch_assoc()): ?>
                <div class="list-group-item px-0">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong class="d-block"><?php echo htmlspecialchars(substr($book['title'], 0, 30)); ?></strong>
                      <small class="text-danger"><i class="fa fa-box me-1"></i>Stok: <?php echo $book['stok']; ?></small>
                    </div>
                    <a href="admin.php?page=books&action=edit&id=<?php echo $book['id']; ?>" class="btn btn-sm btn-primary">
                      <i class="fa fa-edit"></i>
                    </a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>

      <?php endif; ?>

    <?php
    // ============================================
    // ROUTING - BOOKS
    // ============================================
    elseif ($page == 'books'):
    ?>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="mb-0">Kelola Buku</h2>
          <p class="text-muted mb-0">Tambah, edit, atau hapus buku</p>
        </div>
        <?php if ($action == 'list'): ?>
          <a href="admin.php?page=books&action=add" class="btn btn-primary">
            <i class="fa fa-plus me-2"></i>Tambah Buku
          </a>
        <?php endif; ?>
      </div>

      <?php if ($action == 'list'): ?>
        <!-- List Books -->
        <div class="table-card">
          <div class="mb-3">
            <input type="text" id="searchBook" class="form-control" placeholder="Cari buku...">
          </div>
          
          <div class="table-responsive">
            <table class="table table-hover" id="booksTable">
              <thead>
                <tr>
                  <th>Cover</th>
                  <th>Judul</th>
                  <th>Penulis</th>
                  <th>Tahun</th>
                  <th>Kategori</th>
                  <th>Stok</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $books = $conn->query("SELECT * FROM buku ORDER BY id DESC");
                while ($book = $books->fetch_assoc()):
                ?>
                  <tr>
                    <td>
                      <img src="<?php echo htmlspecialchars($book['cover']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" 
                           style="width: 40px; height: 55px; object-fit: cover; border-radius: 4px;"
                           onerror="this.src='https://via.placeholder.com/40x55?text=No+Image'">
                    </td>
                    <td><strong><?php echo htmlspecialchars($book['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo $book['year']; ?></td>
                    <td><span class="badge bg-primary"><?php echo strtoupper($book['category']); ?></span></td>
                    <td>
                      <span class="badge bg-<?php echo $book['stok'] <= 3 ? 'danger' : 'success'; ?>">
                        <?php echo $book['stok']; ?>
                      </span>
                    </td>
                    <td>
                      <a href="admin.php?page=books&action=edit&id=<?php echo $book['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fa fa-edit"></i>
                      </a>
                      <a href="admin.php?page=books&action=delete&id=<?php echo $book['id']; ?>" 
                         class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus buku ini?')">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

        <script>
          document.getElementById('searchBook').addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#booksTable tbody tr');
            
            rows.forEach(row => {
              const text = row.textContent.toLowerCase();
              row.style.display = text.includes(value) ? '' : 'none';
            });
          });
        </script>

      <?php elseif ($action == 'add'): ?>
        <!-- Add Book Form -->
        <div class="table-card">
          <form method="POST">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Judul Buku *</label>
                <input type="text" name="title" class="form-control" required>
              </div>
              
              <div class="col-md-4">
                <label class="form-label">Tahun *</label>
                <input type="number" name="year" class="form-control" min="1900" max="2025" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">Penulis *</label>
                <input type="text" name="author" class="form-control" required>
              </div>
              
              <div class="col-md-4">
                <label class="form-label">Kategori *</label>
                <select name="category" class="form-select" required>
                  <option value="">Pilih Kategori</option>
                  <option value="novel">Novel</option>
                  <option value="komik">Komik</option>
                  <option value="majalah">Majalah</option>
                  <option value="kamus">Kamus</option>
                  <option value="film">Film</option>
                </select>
              </div>
              
              <div class="col-md-2">
                <label class="form-label">Stok *</label>
                <input type="number" name="stok" class="form-control" min="0" value="10" required>
              </div>
              
              <div class="col-12">
                <label class="form-label">URL Cover</label>
                <input type="text" name="cover" class="form-control" 
                       placeholder="https://example.com/cover.jpg" 
                       value="https://via.placeholder.com/300x400?text=No+Cover">
              </div>
              
              <div class="col-12">
                <label class="form-label">Deskripsi *</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
              </div>
              
              <div class="col-12">
                <button type="submit" name="add_book" class="btn btn-primary">
                  <i class="fa fa-save me-2"></i>Simpan Buku
                </button>
                <a href="admin.php?page=books" class="btn btn-secondary">
                  <i class="fa fa-times me-2"></i>Batal
                </a>
              </div>
            </div>
          </form>
        </div>

      <?php elseif ($action == 'edit' && $id > 0): 
        $book = $conn->query("SELECT * FROM buku WHERE id = $id")->fetch_assoc();
        if (!$book) {
          echo "<script>alert('Buku tidak ditemukan!'); window.location.href='admin.php?page=books';</script>";
          exit;
        }
      ?>
        <!-- Edit Book Form -->
        <div class="table-card">
          <form method="POST">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Judul Buku *</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required>
              </div>
              
              <div class="col-md-4">
                <label class="form-label">Tahun *</label>
                <input type="number" name="year" class="form-control" min="1900" max="2025" value="<?php echo $book['year']; ?>" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">Penulis *</label>
                <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required>
              </div>
              
              <div class="col-md-4">
                <label class="form-label">Kategori *</label>
                <select name="category" class="form-select" required>
                  <option value="">Pilih Kategori</option>
                  <option value="novel" <?php echo $book['category'] == 'novel' ? 'selected' : ''; ?>>Novel</option>
                  <option value="komik" <?php echo $book['category'] == 'komik' ? 'selected' : ''; ?>>Komik</option>
                  <option value="majalah" <?php echo $book['category'] == 'majalah' ? 'selected' : ''; ?>>Majalah</option>
                  <option value="kamus" <?php echo $book['category'] == 'kamus' ? 'selected' : ''; ?>>Kamus</option>
                  <option value="film" <?php echo $book['category'] == 'film' ? 'selected' : ''; ?>>Film</option>
                </select>
              </div>
              
              <div class="col-md-2">
                <label class="form-label">Stok *</label>
                <input type="number" name="stok" class="form-control" min="0" value="<?php echo $book['stok']; ?>" required>
              </div>
              
              <div class="col-12">
                <label class="form-label">URL Cover</label>
                <input type="text" name="cover" class="form-control" value="<?php echo htmlspecialchars($book['cover']); ?>">
              </div>
              
              <div class="col-12">
                <label class="form-label">Deskripsi *</label>
                <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($book['description']); ?></textarea>
              </div>
              
              <div class="col-12">
                <button type="submit" name="edit_book" class="btn btn-primary">
                  <i class="fa fa-save me-2"></i>Update Buku
                </button>
                <a href="admin.php?page=books" class="btn btn-secondary">
                  <i class="fa fa-times me-2"></i>Batal
                </a>
              </div>
            </div>
          </form>
        </div>

      <?php endif; ?>

    <?php
    // ============================================
    // ROUTING - USERS
    // ============================================
    elseif ($page == 'users'):
    ?>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="mb-0">Kelola User</h2>
          <p class="text-muted mb-0">Tambah, edit, atau hapus user</p>
        </div>
        <?php if ($action == 'list'): ?>
          <a href="admin.php?page=users&action=add" class="btn btn-success">
            <i class="fa fa-user-plus me-2"></i>Tambah User
          </a>
        <?php endif; ?>
      </div>

      <?php if ($action == 'list'): ?>
        <!-- List Users -->
        <div class="table-card">
          <div class="mb-3">
            <input type="text" id="searchUser" class="form-control" placeholder="Cari user...">
          </div>
          
          <div class="table-responsive">
            <table class="table table-hover" id="usersTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Bergabung</th>
                  <th>Total Pinjam</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $users = $conn->query("
                  SELECT u.*, COUNT(p.id) as total_borrowed 
                  FROM users u 
                  LEFT JOIN peminjaman p ON u.id = p.user_id 
                  GROUP BY u.id 
                  ORDER BY u.id DESC
                ");
                while ($user = $users->fetch_assoc()):
                ?>
                  <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                             style="width: 35px; height: 35px; font-size: 14px;">
                          <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td><span class="badge bg-info"><?php echo $user['total_borrowed']; ?>x</span></td>
                    <td>
                      <a href="admin.php?page=users&action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fa fa-edit"></i>
                      </a>
                      <a href="admin.php?page=users&action=delete&id=<?php echo $user['id']; ?>" 
                         class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus user ini?')">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

        <script>
          document.getElementById('searchUser').addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
              const text = row.textContent.toLowerCase();
              row.style.display = text.includes(value) ? '' : 'none';
            });
          });
        </script>

      <?php elseif ($action == 'add'): ?>
        <!-- Add User Form -->
        <div class="table-card">
          <form method="POST">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              
              <div class="col-12">
                <button type="submit" name="add_user" class="btn btn-success">
                  <i class="fa fa-save me-2"></i>Simpan User
                </button>
                <a href="admin.php?page=users" class="btn btn-secondary">
                  <i class="fa fa-times me-2"></i>Batal
                </a>
              </div>
            </div>
          </form>
        </div>

      <?php elseif ($action == 'edit' && $id > 0): 
        $user = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();
        if (!$user) {
          echo "<script>alert('User tidak ditemukan!'); window.location.href='admin.php?page=users';</script>";
          exit;
        }
      ?>
        <!-- Edit User Form -->
        <div class="table-card">
          <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
              </div>
              
              <div class="col-md-6">
                <label class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-control">
              </div>
              
              <div class="col-12">
                <button type="submit" name="edit_user" class="btn btn-success">
                  <i class="fa fa-save me-2"></i>Update User
                </button>
                <a href="admin.php?page=users" class="btn btn-secondary">
                  <i class="fa fa-times me-2"></i>Batal
                </a>
              </div>
            </div>
          </form>
        </div>

      <?php endif; ?>

    <?php
    // ============================================
    // ROUTING - BORROWINGS
    // ============================================
    elseif ($page == 'borrowings'):
    ?>
      <div class="mb-4">
        <h2 class="mb-0">Kelola Peminjaman</h2>
        <p class="text-muted mb-0">Lihat dan kelola semua peminjaman aktif</p>
      </div>

      <div class="table-card">
        <div class="alert alert-info mb-3">
          <i class="fa fa-info-circle me-2"></i>
          Menampilkan <strong><?php echo $total_borrowed; ?> peminjaman aktif</strong> yang sedang berlangsung
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>User</th>
                <th>Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Harus Kembali</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="activeBorrowingsTable">
              <?php
              $active = $conn->query("
                SELECT p.*, b.title, b.cover, u.username 
                FROM peminjaman p 
                JOIN buku b ON p.book_id = b.id 
                JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'dipinjam'
                ORDER BY p.tanggal_pinjam DESC
              ");
              
              if ($active->num_rows > 0):
                while ($row = $active->fetch_assoc()):
                  $is_late = strtotime($row['tanggal_kembali']) < time();
                  $days_borrowed = floor((time() - strtotime($row['tanggal_pinjam'])) / (60 * 60 * 24));
                  $days_until_return = floor((strtotime($row['tanggal_kembali']) - time()) / (60 * 60 * 24));
                ?>
                  <tr id="borrow-row-<?php echo $row['id']; ?>" class="<?php echo $is_late ? 'table-danger' : ''; ?>">
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                             style="width: 30px; height: 30px; font-size: 12px;">
                          <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                        </div>
                        <?php echo htmlspecialchars($row['username']); ?>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="<?php echo htmlspecialchars($row['cover']); ?>" alt="Cover" 
                             style="width: 30px; height: 40px; object-fit: cover; border-radius: 3px;" 
                             class="me-2"
                             onerror="this.src='https://via.placeholder.com/30x40?text=No+Image'">
                        <div>
                          <strong class="d-block"><?php echo htmlspecialchars(substr($row['title'], 0, 40)); ?></strong>
                        </div>
                      </div>
                    </td>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                    <td>
                      <strong class="<?php echo $is_late ? 'text-danger' : 'text-warning'; ?>">
                        <?php echo date('d M Y', strtotime($row['tanggal_kembali'])); ?>
                      </strong>
                    </td>
                    <td>
                      <small class="text-muted">
                        <?php echo $days_borrowed; ?> hari
                        <?php if (!$is_late): ?>
                          <br><span class="text-success">(<?php echo $days_until_return; ?> hari lagi)</span>
                        <?php endif; ?>
                      </small>
                    </td>
                    <td>
                      <span class="badge bg-<?php echo $is_late ? 'danger' : 'warning'; ?>">
                        <?php 
                        if ($is_late) {
                          $days_late = abs($days_until_return);
                          echo "TERLAMBAT ($days_late hari)";
                        } else {
                          echo "Sedang Dipinjam";
                        }
                        ?>
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-success btn-return" onclick="returnBook(<?php echo $row['id']; ?>)" title="Kembalikan Buku" data-borrow-id="<?php echo $row['id']; ?>">
                        <i class="fa fa-undo me-1"></i>Kembalikan
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr id="no-data-row">
                  <td colspan="7" class="text-center py-4">
                    <i class="fa fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">Tidak ada peminjaman aktif saat ini</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php
    // ============================================
    // ROUTING - REVIEWS
    // ============================================
    elseif ($page == 'reviews'):
    ?>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="mb-0">Kelola Ulasan</h2>
          <p class="text-muted mb-0">Lihat dan kelola semua ulasan buku dari user</p>
        </div>
      </div>

      <?php if (!$review_exists): ?>
        <div class="alert alert-warning">
          <i class="fa fa-exclamation-triangle me-2"></i>
          Tabel review belum ada. Ulasan akan otomatis dibuat saat user memberikan review pertama.
        </div>
      <?php else: ?>
        <div class="row g-4 mb-4">
          <div class="col-md-3">
            <div class="stat-card reviews">
              <div class="icon"><i class="fa fa-star"></i></div>
              <h3 class="mb-1"><?php echo $total_reviews; ?></h3>
              <p class="text-muted mb-0">Total Ulasan</p>
            </div>
          </div>
          
          <?php
          $avg_rating_result = $conn->query("SELECT AVG(rating) as avg_rating FROM review");
          $avg_rating = $avg_rating_result->fetch_assoc()['avg_rating'];
          $avg_rating = $avg_rating ? number_format($avg_rating, 1) : 0;
          ?>
          <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #ffc107;">
              <div style="width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px; background: #fff8e1; color: #ffc107;">
                <i class="fa fa-star-half-alt"></i>
              </div>
              <h3 class="mb-1"><?php echo $avg_rating; ?></h3>
              <p class="text-muted mb-0">Rating Rata-rata</p>
            </div>
          </div>
          
          <?php
          $books_with_reviews = $conn->query("SELECT COUNT(DISTINCT book_id) as total FROM review")->fetch_assoc()['total'];
          ?>
          <div class="col-md-3">
            <div class="stat-card books">
              <div class="icon"><i class="fa fa-book"></i></div>
              <h3 class="mb-1"><?php echo $books_with_reviews; ?></h3>
              <p class="text-muted mb-0">Buku Direview</p>
            </div>
          </div>
          
          <?php
          $users_with_reviews = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM review")->fetch_assoc()['total'];
          ?>
          <div class="col-md-3">
            <div class="stat-card users">
              <div class="icon"><i class="fa fa-user-edit"></i></div>
              <h3 class="mb-1"><?php echo $users_with_reviews; ?></h3>
              <p class="text-muted mb-0">User Aktif Review</p>
            </div>
          </div>
        </div>

        <div class="table-card">
          <div class="mb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-comments me-2"></i>Semua Ulasan</h5>
            <input type="text" id="searchReview" class="form-control" style="max-width: 300px;" placeholder="Cari ulasan...">
          </div>
          
          <?php
          $reviews = $conn->query("
            SELECT r.*, u.username, b.title as book_title, b.cover 
            FROM review r 
            JOIN users u ON r.user_id = u.id 
            JOIN buku b ON r.book_id = b.id 
            ORDER BY r.created_at DESC
          ");
          
          if ($reviews->num_rows > 0):
          ?>
            <div id="reviewsContainer">
              <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="review-card">
                  <div class="row">
                    <div class="col-md-2 text-center">
                      <img src="<?php echo htmlspecialchars($review['cover']); ?>" alt="Cover" 
                           style="width: 80px; height: 110px; object-fit: cover; border-radius: 4px;"
                           onerror="this.src='https://via.placeholder.com/80x110?text=No+Image'">
                    </div>
                    <div class="col-md-8">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <h6 class="mb-1">
                            <strong><?php echo htmlspecialchars($review['book_title']); ?></strong>
                          </h6>
                          <div class="mb-2">
                            <span class="text-warning">
                              <?php for($i=0; $i<$review['rating']; $i++): ?>
                                <i class="fa fa-star"></i>
                              <?php endfor; ?>
                              <?php for($i=$review['rating']; $i<5; $i++): ?>
                                <i class="far fa-star"></i>
                              <?php endfor; ?>
                            </span>
                            <span class="badge bg-primary ms-2"><?php echo $review['rating']; ?>/5</span>
                          </div>
                          <small class="text-muted">
                            <i class="fa fa-user me-1"></i><?php echo htmlspecialchars($review['username']); ?>
                            <i class="fa fa-calendar ms-3 me-1"></i><?php echo date('d M Y H:i', strtotime($review['created_at'])); ?>
                          </small>
                        </div>
                      </div>
                      <p class="mb-0" style="line-height: 1.6;">
                        "<?php echo htmlspecialchars($review['review_text']); ?>"
                      </p>
                    </div>
                    <div class="col-md-2 text-end">
                      <button class="btn btn-sm btn-danger" onclick="if(confirm('Yakin hapus ulasan ini?')) window.location.href='admin.php?page=reviews&action=delete&id=<?php echo $review['id']; ?>'" title="Hapus Ulasan">
                        <i class="fa fa-trash me-1"></i>Hapus
                      </button>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="fa fa-comment-slash fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">Belum ada ulasan</h5>
              <p class="text-muted">User belum memberikan ulasan untuk buku apapun</p>
            </div>
          <?php endif; ?>
        </div>

        <script>
          document.getElementById('searchReview').addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const cards = document.querySelectorAll('.review-card');
            
            cards.forEach(card => {
              const text = card.textContent.toLowerCase();
              card.style.display = text.includes(value) ? '' : 'none';
            });
          });
        </script>
      <?php endif; ?>

    <?php
    // ============================================
    // ROUTING - HISTORY
    // ============================================
    elseif ($page == 'history'):
    ?>
      <div class="mb-4">
        <h2 class="mb-0">Riwayat Peminjaman</h2>
        <p class="text-muted mb-0">Semua buku yang pernah dipinjam (aktif & selesai)</p>
      </div>

      <div class="table-card">
        <div class="mb-3">
          <input type="text" id="searchHistory" class="form-control" placeholder="Cari riwayat peminjaman...">
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover" id="historyTable">
            <thead>
              <tr>
                <th>Waktu Pinjam</th>
                <th>User</th>
                <th>Buku</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
                <th>Denda</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $history = $conn->query("
                SELECT p.*, b.title, u.username 
                FROM peminjaman p 
                JOIN buku b ON p.book_id = b.id 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC 
                LIMIT 200
              ");
              
              if ($history->num_rows > 0):
                while ($row = $history->fetch_assoc()):
                  $is_late = ($row['status'] == 'dipinjam' && strtotime($row['tanggal_kembali']) < time());
                  $status_badge = '';
                  $status_text = '';
                  
                  if ($row['status'] == 'dipinjam') {
                    $status_badge = $is_late ? 'danger' : 'warning';
                    $status_text = $is_late ? 'TERLAMBAT' : 'Sedang Dipinjam';
                  } elseif ($row['status'] == 'dikembalikan') {
                    $status_badge = 'success';
                    $status_text = 'Dikembalikan';
                  } elseif ($row['status'] == 'terlambat') {
                    $status_badge = 'danger';
                    $status_text = 'Terlambat (Sudah Kembali)';
                  }
                ?>
                  <tr>
                    <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                             style="width: 30px; height: 30px; font-size: 12px;">
                          <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                        </div>
                        <?php echo htmlspecialchars($row['username']); ?>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars(substr($row['title'], 0, 45)); ?></td>
                    <td>
                      <?php if ($row['status'] == 'dipinjam'): ?>
                        <span class="text-muted">Harus: <?php echo date('d M Y', strtotime($row['tanggal_kembali'])); ?></span>
                      <?php else: ?>
                        <?php echo date('d M Y', strtotime($row['tanggal_dikembalikan'])); ?>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-<?php echo $status_badge; ?>"><?php echo $status_text; ?></span>
                    </td>
                    <td>
                      <span class="badge bg-<?php echo $row['denda'] > 0 ? 'danger' : 'success'; ?>">
                        Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?>
                      </span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center py-4">Belum ada riwayat peminjaman</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <script>
        document.getElementById('searchHistory').addEventListener('keyup', function() {
          const value = this.value.toLowerCase();
          const rows = document.querySelectorAll('#historyTable tbody tr');
          
          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
          });
        });
      </script>

    <?php endif; ?>

  </div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
async function returnBook(borrowId) {
  if (!confirm('Kembalikan buku ini?')) return;
  
  // Find button and add loading state
  const btn = document.querySelector(`button[data-borrow-id="${borrowId}"]`);
  const originalHtml = btn ? btn.innerHTML : '';
  
  if (btn) {
    btn.classList.add('loading');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Proses...';
  }
  
  try {
    const formData = new FormData();
    formData.append('return_book_ajax', '1');
    formData.append('borrow_id', borrowId);
    
    const response = await fetch('admin.php', {
      method: 'POST',
      body: formData
    });
    
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    
    const result = await response.json();
    
    if (result.success) {
      alert(result.message);
      
      // Remove the row from the table
      const row = document.getElementById(`borrow-row-${borrowId}`);
      if (row) {
        row.remove();
      }
      
      // Check if there are any remaining rows
      const table = document.getElementById('activeBorrowingsTable') || document.getElementById('borrowingsTable');
      if (table) {
        const remainingRows = table.querySelectorAll('tr:not(#no-data-row)');
        if (remainingRows.length === 0) {
          // Add "no data" row if all books are returned
          table.innerHTML = '<tr id="no-data-row"><td colspan="7" class="text-center py-4"><i class="fa fa-inbox fa-3x text-muted mb-3 d-block"></i><p class="text-muted mb-0">Tidak ada peminjaman aktif saat ini</p></td></tr>';
        }
      }
      
      // Optionally reload the page after a short delay to update statistics
      setTimeout(() => {
        location.reload();
      }, 1500);
    } else {
      alert('Error: ' + result.message);
      if (btn) {
        btn.classList.remove('loading');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Terjadi kesalahan saat mengembalikan buku! Silakan coba lagi.');
    if (btn) {
      btn.classList.remove('loading');
      btn.disabled = false;
      btn.innerHTML = originalHtml;
    }
  }
}
</script>
</body>
</html>