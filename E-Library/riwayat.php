<?php 
require_once 'config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();

// Ambil semua peminjaman (baik yang masih dipinjam maupun sudah dikembalikan)
$query = "SELECT p.*, b.title, b.author, b.cover, b.category 
          FROM peminjaman p 
          JOIN buku b ON p.book_id = b.id 
          WHERE p.user_id = ? 
          ORDER BY p.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$all_peminjaman = [];
$active_peminjaman = [];
$history_peminjaman = [];

while ($row = $result->fetch_assoc()) {
    $all_peminjaman[] = $row;
    
    if ($row['status'] == 'dipinjam') {
        $active_peminjaman[] = $row;
    } else {
        $history_peminjaman[] = $row;
    }
}

// Statistik
$total_peminjaman = count($all_peminjaman);
$tepat_waktu = count(array_filter($history_peminjaman, function($p) {
    return $p['status'] == 'dikembalikan' && $p['denda'] == 0;
}));
$terlambat = count(array_filter($all_peminjaman, function($p) {
    return $p['denda'] > 0;
}));
$total_denda = array_sum(array_column($all_peminjaman, 'denda'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Riwayat | E-Library</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body { font-family: 'Heebo', sans-serif; background: #f8f9fa; }
    .back-btn {
      position: fixed; bottom: 30px; left: 30px; z-index: 999; width: 50px; height: 50px;
      background: #06BBCC; color: white; border: none; border-radius: 50%;
      display: flex; align-items: center; justify-content: center; font-size: 20px;
      cursor: pointer; box-shadow: 0 4px 12px rgba(6, 187, 204, 0.4); transition: all 0.3s ease;
    }
    .back-btn:hover { background: #05a0b0; transform: translateY(-3px); }
    .history-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .history-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
    .status-badge { padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="user.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>E-Library</h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        
    </div>
  </nav>

  <div class="page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 100px 0; margin-bottom: 50px; position: relative;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(24, 29, 56, .4);"></div>
    <div class="container position-relative text-center">
      <h1 class="display-3 text-white mb-3"><i class="fa fa-history me-3"></i>Riwayat Peminjaman</h1>
      <p class="text-white lead">Lihat semua peminjaman dan pengembalian buku Anda</p>
    </div>
  </div>

  <section class="container py-5">
    <!-- Info Alert -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-info d-flex align-items-center" style="border-left: 4px solid #0dcaf0;">
          <i class="fa fa-info-circle fa-2x me-3"></i>
          <div>
            <strong>Informasi:</strong> Untuk mengembalikan buku yang dipinjam, silakan hubungi admin perpustakaan atau datang langsung ke perpustakaan. Pengembalian buku hanya dapat diproses oleh admin.
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-5">
      <div class="col-md-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="fa fa-book fa-3x text-primary mb-3"></i>
            <h3 class="mb-0"><?php echo $total_peminjaman; ?></h3>
            <p class="text-muted mb-0">Total Peminjaman</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
            <h3 class="mb-0"><?php echo $tepat_waktu; ?></h3>
            <p class="text-muted mb-0">Tepat Waktu</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h3 class="mb-0"><?php echo $terlambat; ?></h3>
            <p class="text-muted mb-0">Terlambat</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <i class="fa fa-money-bill-wave fa-3x text-danger mb-3"></i>
            <h3 class="mb-0">Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></h3>
            <p class="text-muted mb-0">Total Denda</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs nav-fill mb-4" id="historyTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button">
          <i class="fa fa-book-reader me-2"></i>Sedang Dipinjam (<?php echo count($active_peminjaman); ?>)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
          <i class="fa fa-history me-2"></i>Riwayat (<?php echo count($history_peminjaman); ?>)
        </button>
      </li>
    </ul>

    <div class="tab-content" id="historyTabContent">
      <!-- Active Peminjaman Tab -->
      <div class="tab-pane fade show active" id="active" role="tabpanel">
        <?php if (count($active_peminjaman) == 0): ?>
          <div class="text-center py-5">
            <i class="fa fa-book-open fa-5x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Tidak Ada Buku yang Dipinjam</h3>
            <p class="text-muted mb-4">Anda belum meminjam buku saat ini.</p>
            <a href="books-user.php" class="btn btn-primary btn-lg">
              <i class="fa fa-search me-2"></i>Browse Koleksi Buku
            </a>
          </div>
        <?php else: ?>
          <div class="row g-4">
            <?php foreach ($active_peminjaman as $p): 
              $today = strtotime(date('Y-m-d'));
              $due_date = strtotime($p['tanggal_kembali']);
              $days_left = floor(($due_date - $today) / (60 * 60 * 24));
              $is_overdue = $days_left < 0;
              $is_warning = $days_left >= 0 && $days_left <= 3;
            ?>
              <div class="col-lg-6">
                <div class="card history-card h-100 shadow-sm border-0">
                  <div class="card-body">
                    <div class="d-flex gap-3">
                      <img src="<?php echo $p['cover']; ?>" alt="<?php echo $p['title']; ?>" 
                           style="width: 100px; height: 140px; object-fit: cover; border-radius: 8px;"
                           onerror="this.src='https://via.placeholder.com/100x140?text=No+Image'">
                      
                      <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <h5 class="mb-0"><?php echo $p['title']; ?></h5>
                          <span class="status-badge bg-info text-white">
                            <i class="fa fa-book-reader me-1"></i>DIPINJAM
                          </span>
                        </div>
                        
                        <p class="text-muted mb-2"><?php echo $p['author']; ?></p>
                        <span class="badge bg-primary mb-2"><?php echo strtoupper($p['category']); ?></span>
                        
                        <div class="mt-3">
                          <small class="text-muted d-block mb-1">
                            <i class="fa fa-calendar me-1"></i>
                            <strong>Dipinjam:</strong> <?php echo date('d M Y', strtotime($p['tanggal_pinjam'])); ?>
                          </small>
                          <small class="text-muted d-block mb-1">
                            <i class="fa fa-calendar-check me-1"></i>
                            <strong>Jatuh Tempo:</strong> <?php echo date('d M Y', $due_date); ?>
                          </small>
                          <small class="d-block mb-2">
                            <i class="fa fa-clock me-1"></i>
                            <strong>Sisa Waktu:</strong> 
                            <span class="<?php echo $is_overdue ? 'text-danger' : ($is_warning ? 'text-warning' : 'text-success'); ?>">
                              <?php 
                                if ($is_overdue) {
                                  echo '<strong>TERLAMBAT ' . abs($days_left) . ' hari!</strong>';
                                } else {
                                  echo $days_left . ' hari';
                                }
                              ?>
                            </span>
                          </small>
                        </div>
                        
                        <?php if ($is_overdue): ?>
                          <div class="alert alert-danger py-2 px-3 mb-2">
                            <small>
                              <i class="fa fa-exclamation-triangle me-1"></i>
                              <strong>Buku sudah melewati batas waktu! Segera kembalikan ke perpustakaan.</strong>
                            </small>
                          </div>
                        <?php elseif ($is_warning): ?>
                          <div class="alert alert-warning py-2 px-3 mb-2">
                            <small>
                              <i class="fa fa-exclamation-circle me-1"></i>
                              <strong>Batas waktu pengembalian hampir tiba!</strong>
                            </small>
                          </div>
                        <?php endif; ?>
                        
                        <?php if ($p['catatan']): ?>
                          <div class="alert alert-light py-2 px-3 mb-2">
                            <small><i class="fa fa-sticky-note me-1"></i><?php echo $p['catatan']; ?></small>
                          </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-secondary py-2 px-3 mb-0 text-center">
                          <small>
                            <i class="fa fa-lock me-1"></i>
                            <strong>Pengembalian buku hanya dapat dilakukan oleh admin perpustakaan</strong>
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- History Tab -->
      <div class="tab-pane fade" id="history" role="tabpanel">
        <?php if (count($history_peminjaman) == 0): ?>
          <div class="text-center py-5">
            <i class="fa fa-history fa-5x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Belum Ada Riwayat</h3>
            <p class="text-muted mb-4">Anda belum pernah mengembalikan buku.</p>
          </div>
        <?php else: ?>
          <div class="row g-4">
            <?php foreach ($history_peminjaman as $p): 
              $is_late = $p['denda'] > 0;
              $duration = floor((strtotime($p['tanggal_dikembalikan']) - strtotime($p['tanggal_pinjam'])) / (60 * 60 * 24));
            ?>
              <div class="col-lg-6">
                <div class="card history-card h-100 shadow-sm border-0">
                  <div class="card-body">
                    <div class="d-flex gap-3">
                      <img src="<?php echo $p['cover']; ?>" alt="<?php echo $p['title']; ?>" 
                           style="width: 100px; height: 140px; object-fit: cover; border-radius: 8px;"
                           onerror="this.src='https://via.placeholder.com/100x140?text=No+Image'">
                      
                      <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <h6 class="mb-0"><?php echo $p['title']; ?></h6>
                          <span class="status-badge <?php echo $is_late ? 'bg-danger' : 'bg-success'; ?> text-white">
                            <?php echo $is_late ? 'TERLAMBAT' : 'TEPAT WAKTU'; ?>
                          </span>
                        </div>
                        
                        <p class="text-muted mb-2 small"><?php echo $p['author']; ?></p>
                        
                        <div class="mb-2">
                          <small class="text-muted d-block">
                            <i class="fa fa-calendar me-1"></i>Dipinjam: <?php echo date('d M Y', strtotime($p['tanggal_pinjam'])); ?>
                          </small>
                          <small class="text-muted d-block">
                            <i class="fa fa-calendar-check me-1"></i>Dikembalikan: 
                            <?php echo date('d M Y', strtotime($p['tanggal_dikembalikan'])); ?>
                          </small>
                          <small class="text-muted d-block">
                            <i class="fa fa-clock me-1"></i>Durasi: <?php echo $duration; ?> hari
                          </small>
                        </div>
                        
                        <?php if ($is_late): ?>
                          <div class="alert alert-danger py-2 px-3 mb-2">
                            <small>
                              <i class="fa fa-exclamation-triangle me-1"></i>
                              <strong>Denda: Rp <?php echo number_format($p['denda'], 0, ',', '.'); ?></strong>
                            </small>
                          </div>
                        <?php endif; ?>
                        
                        <?php if ($p['catatan']): ?>
                          <div class="alert alert-light py-2 px-3 mb-0">
                            <small><i class="fa fa-sticky-note me-1"></i><?php echo $p['catatan']; ?></small>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <footer class="bg-dark text-light pt-5 mt-5">
    <div class="container text-center pb-3">
      <p>&copy; 2025 E-Library. All Rights Reserved.</p>
    </div>
  </footer>

  <button class="back-btn" onclick="window.location.href='user.php'"><i class="fa fa-arrow-left"></i></button>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    console.log('📚 Riwayat Peminjaman loaded');
    console.log('Total peminjaman aktif: <?php echo count($active_peminjaman); ?>');
    console.log('Total riwayat: <?php echo count($history_peminjaman); ?>');
  });
</script>
</body>
</html>