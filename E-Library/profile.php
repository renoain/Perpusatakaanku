<?php 
require_once 'config.php';

// Cek login
if (!is_logged_in()) {
    redirect('login.php');
}

// Ambil data user dari database
$user_id = get_user_id();
$user = get_user_by_id($user_id);

if (!$user) {
    session_destroy();
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Profil Pengguna | E-Library</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="img/favicon.ico" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .profile-card {
      border-radius: 15px; padding: 30px; background: #fff;
      box-shadow: 0 0 15px rgba(0,0,0,0.1); text-align: center;
    }
    .back-btn {
      position: fixed; bottom: 30px; left: 30px; z-index: 999;
      width: 50px; height: 50px; background: #06BBCC; color: white;
      border: none; border-radius: 50%; display: flex;
      align-items: center; justify-content: center; font-size: 20px;
      cursor: pointer; box-shadow: 0 4px 12px rgba(6, 187, 204, 0.4);
      transition: all 0.3s ease;
    }
    .back-btn:hover { background: #05a0b0; transform: translateY(-3px); }
    .profile-pic {
      width: 140px; height: 140px; object-fit: cover;
      border-radius: 50%; border: 4px solid #06BBCC; margin-bottom: 20px;
    }
    .info-box {
      text-align: left; margin-top: 25px; background: #f8f9fa;
      padding: 15px 20px; border-radius: 10px;
    }
    .info-box p { margin-bottom: 8px; }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="user.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>E-Library</h2>
    </a>
  </nav>

  <div class="container-fluid py-5 mb-5 page-header position-relative"
       style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 400px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(24, 29, 56, .6);"></div>
    <div class="container py-5 position-relative text-center">
      <h1 class="display-3 text-white animated slideInDown mb-3">Profil Pengguna</h1>
      <p class="text-white mb-0">Kelola informasi akun dan data pribadimu di sini</p>
    </div>
  </div>

  <div class="container-xxl py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="profile-card">
            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077012.png" alt="Foto Profil" class="profile-pic">

            <h4 class="fw-bold mt-3"><?php echo htmlspecialchars($user['username']); ?></h4>
            <p class="text-muted mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?> mb-3">
              <?php echo ucfirst($user['role']); ?>
            </span>

            <div class="info-box mt-4">
              <h6 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Informasi Akun</h6>
              <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
              <p><strong>Bergabung:</strong> <?php echo format_date($user['created_at']); ?></p>
            </div>

            <div class="mt-4">
              <a href="logout.php" class="btn btn-danger" onclick="return confirm('Yakin ingin logout?')">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button class="back-btn" onclick="window.location.href='user.php'" title="Kembali">
    <i class="fa fa-arrow-left"></i>
  </button>

  <footer class="bg-dark text-light mt-5 p-4 text-center">
    &copy; 2025 E-Library. All Rights Reserved.
  </footer>

  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>