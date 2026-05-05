<?php 
require_once 'config.php';

if (is_logged_in()) {
    redirect('user.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>E-Library | Bergabung Sekarang</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="img/favicon.ico" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background-color: #f8f9fa; }
    .navbar { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 0; }
    .navbar-brand { padding: 1rem 1.5rem; }
    .navbar-brand h2 { margin: 0; color: #06BBCC; }
    .page-header {
      background: linear-gradient(rgba(24, 29, 56, .7), rgba(24, 29, 56, .7)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400"><rect fill="%2306BBCC" width="1200" height="400"/></svg>');
      background-size: cover; background-position: center; height: 300px;
      display: flex; align-items: center; justify-content: center; margin-bottom: 3rem;
    }
    .auth-card {
      max-width: 420px; margin: auto; background: #fff; border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 2rem;
      transition: all 0.3s ease; animation: fadeInUp 0.6s ease;
    }
    .auth-toggle { cursor: pointer; color: #06BBCC; font-weight: 600; text-decoration: none; }
    .auth-toggle:hover { text-decoration: underline; }
    .password-wrapper { position: relative; }
    .password-wrapper input { padding-right: 45px; }
    .toggle-password {
      position: absolute; top: 50%; right: 15px; transform: translateY(-50%);
      cursor: pointer; color: #6c757d; font-size: 1rem;
      transition: color 0.2s ease; z-index: 10; user-select: none;
    }
    .toggle-password:hover { color: #06BBCC; }
    .form-control { height: 45px; border: 1px solid #dee2e6; border-radius: 5px; }
    .form-control:focus { border-color: #06BBCC; box-shadow: 0 0 0 0.2rem rgba(6, 187, 204, 0.25); }
    .btn-primary { background-color: #06BBCC; border-color: #06BBCC; height: 45px; font-weight: 600; }
    .btn-primary:hover { background-color: #05a0b0; border-color: #05a0b0; }
    .back-btn {
      position: fixed; bottom: 30px; left: 30px; z-index: 999;
      width: 50px; height: 50px; background: #06BBCC; color: white;
      border: none; border-radius: 50%; display: flex;
      align-items: center; justify-content: center; font-size: 20px;
      cursor: pointer; box-shadow: 0 4px 12px rgba(6, 187, 204, 0.4);
      transition: all 0.3s ease;
    }
    .back-btn:hover { background: #05a0b0; transform: translateY(-3px); box-shadow: 0 6px 16px rgba(6, 187, 204, 0.5); }
    footer { background: #181d38; color: white; padding: 1.5rem; text-align: center; margin-top: 3rem; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container-fluid">
      <a href="index.php" class="navbar-brand d-flex align-items-center">
        <h2 class="m-0"><i class="fa fa-book me-3"></i>E-Library</h2>
      </a>
    </div>
  </nav>

  <div class="page-header">
    <div class="container text-center">
      <h1 class="display-3 text-white animate__animated animate__slideInDown">Bergabung Sekarang</h1>
      <p class="text-white mb-0">Buat akun baru untuk mengakses semua fitur E-Library.</p>
    </div>
  </div>

  <div class="container py-5">
    <div class="auth-card animate__animated animate__fadeInUp">
      <h4 class="text-center mb-4" style="color: #06BBCC;">Buat Akun Baru</h4>

      <form id="registerForm" autocomplete="off">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input id="regUsername" type="text" class="form-control" placeholder="Masukkan username" required>
        </div>
      
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input id="regEmail" type="email" class="form-control" placeholder="Masukkan email" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="password-wrapper">
            <input id="regPassword" type="password" class="form-control" placeholder="Buat password (min. 6 karakter)" minlength="6" required>
            <i class="fas fa-eye toggle-password" data-target="regPassword"></i>
          </div>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Konfirmasi Password</label>
          <div class="password-wrapper">
            <input id="regConfirm" type="password" class="form-control" placeholder="Ulangi password" required>
            <i class="fas fa-eye toggle-password" data-target="regConfirm"></i>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">
          <i class="fa fa-user-plus me-2"></i>Daftar Sekarang
        </button>
        
        <p class="mt-3 text-center text-muted">Sudah punya akun?
          <a href="login.php" class="auth-toggle">Login Sekarang</a>
        </p>
      </form>
    </div>
  </div>

  <button class="back-btn" onclick="window.history.back()" title="Kembali">
    <i class="fas fa-arrow-left"></i>
  </button>

  <footer>&copy; 2025 E-Library. All Rights Reserved.</footer>

  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(icon => {
      icon.addEventListener('click', () => {
        const target = document.getElementById(icon.dataset.target);
        if (target.type === 'password') {
          target.type = 'text';
          icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
          target.type = 'password';
          icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
      });
    });

    // Register Form
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const username = document.getElementById('regUsername').value.trim();
      const email = document.getElementById('regEmail').value.trim();
      const password = document.getElementById('regPassword').value.trim();
      const confirm = document.getElementById('regConfirm').value.trim();

      // Validasi
      if (!username || !email || !password) {
        alert("⚠️ Semua field harus diisi!");
        return;
      }

      if (password.length < 6) {
        alert("⚠️ Password minimal 6 karakter!");
        return;
      }

      if (password !== confirm) {
        alert("❌ Konfirmasi password tidak cocok!");
        return;
      }

      // Email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert("⚠️ Format email tidak valid!");
        return;
      }

      const formData = new FormData();
      formData.append('action', 'register');
      formData.append('username', username);
      formData.append('email', email);
      formData.append('password', password);

      // Disable button sementara
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Mendaftar...';

      fetch('auth_process.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(`✅ ${data.message}`);
          window.location.href = data.data.redirect;
        } else {
          alert(`❌ ${data.message}`);
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan. Silakan coba lagi.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      });
    });
  </script>
</body>
</html>