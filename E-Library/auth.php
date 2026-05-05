<?php 
require_once 'config.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>E-Library | Login & Register</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <!-- Favicon -->
  <link href="img/favicon.ico" rel="icon">

  <!-- Google Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Template CSS -->
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/custom.css" rel="stylesheet">

  <!-- Custom Style for Auth -->
  <style>
    body { background-color: #f8f9fa; }

    .auth-card {
      max-width: 420px;
      margin: auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 2rem;
      transition: all 0.3s ease;
      animation: fadeInUp 0.6s ease;
    }

    .auth-toggle {
      cursor: pointer;
      color: var(--bs-primary);
      font-weight: 600;
    }
    .auth-toggle:hover { text-decoration: underline; }

    /* 👁️ Rapiin posisi ikon mata */
    .password-wrapper {
      position: relative;
    }
    .password-wrapper input {
      padding-right: 42px; /* ruang untuk ikon */
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
      font-size: 1.1rem;
      transition: color 0.2s ease;
    }
    .toggle-password:hover {
      color: var(--bs-primary);
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>E-Library</h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="index.html" class="nav-item nav-link">Home</a>
        <a href="books.html" class="nav-item nav-link">Koleksi</a>
        <a href="wishlist.html" class="nav-item nav-link">Wishlist</a>
        <a href="riwayat.html" class="nav-item nav-link">Riwayat</a>
        <a href="profile.html" class="nav-item nav-link">Profil</a>
      </div>
      <a id="authButton" href="auth.html" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">
        Login / Register <i class="fa fa-arrow-right ms-3"></i>
      </a>
    </div>
  </nav>

  <!-- Page Header -->
  <div class="container-fluid py-5 mb-5 page-header position-relative"
     style="background: url('img/All Page.jpg') center center / cover no-repeat; height: 400px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(24, 29, 56, .6);"></div>
    <div class="container py-5 position-relative">
      <div class="row justify-content-center">
        <div class="col-lg-10 text-center">
          <h1 class="display-3 text-white animated slideInDown">Login / Registrasi</h1>
          <p class="text-white mb-0">Masuk atau buat akun baru untuk mengakses fitur E-Library.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Auth Section -->
  <div class="container py-5">
    <div class="auth-card wow fadeInUp" data-wow-delay="0.2s">
      <h4 id="formTitle" class="text-center text-primary mb-4">Masuk ke Akun</h4>

      <!-- LOGIN FORM -->
      <form id="loginForm" autocomplete="off" novalidate>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input id="loginEmail" type="email" class="form-control" placeholder="Masukkan email kamu" autocomplete="off" required>
        </div>
        <div class="mb-3 password-wrapper">
          <label class="form-label">Password</label>
          <input id="loginPassword" type="password" class="form-control" placeholder="Masukkan password" autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <p class="mt-3 text-center text-muted">Belum punya akun?
          <span id="toRegister" class="auth-toggle">Daftar Sekarang</span>
        </p>
      </form>

      <!-- REGISTER FORM -->
      <form id="registerForm" class="d-none" autocomplete="off" novalidate>
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input id="regName" type="text" class="form-control" placeholder="Masukkan nama lengkap" autocomplete="off" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input id="regEmail" type="email" class="form-control" placeholder="Masukkan email" autocomplete="off" required>
        </div>
        <div class="mb-3 password-wrapper">
          <label class="form-label">Password</label>
          <input id="regPassword" type="password" class="form-control" placeholder="Buat password (min. 6 karakter)" autocomplete="new-password" minlength="6" required>
        </div>
        <div class="mb-3 password-wrapper">
          <label class="form-label">Konfirmasi Password</label>
          <input id="regConfirm" type="password" class="form-control" placeholder="Ulangi password" autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
        <p class="mt-3 text-center text-muted">Sudah punya akun?
          <span id="toLogin" class="auth-toggle">Login Sekarang</span>
        </p>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-light mt-5 p-4 text-center">
    &copy; 2025 E-Library. All Rights Reserved.
  </footer>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="lib/wow/wow.min.js"></script>
  <script src="js/main.js"></script>

  <!-- Auth Logic -->
  <script>
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const formTitle = document.getElementById('formTitle');

    // Ganti tampilan Login ↔ Register
    document.getElementById('toRegister').onclick = () => {
      loginForm.classList.add('d-none');
      registerForm.classList.remove('d-none');
      formTitle.innerText = 'Buat Akun Baru';
    };
    document.getElementById('toLogin').onclick = () => {
      registerForm.classList.add('d-none');
      loginForm.classList.remove('d-none');
      formTitle.innerText = 'Masuk ke Akun';
    };

    // 👁️ Toggle password visibility
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

    // ✉️ Email format validation
    const validEmail = (email) => /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email);

    // Register
    registerForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = regName.value.trim();
      const email = regEmail.value.trim();
      const password = regPassword.value.trim();
      const confirm = regConfirm.value.trim();

      if (!validEmail(email)) return alert("⚠️ Masukkan format email yang valid!");
      if (password.length < 6) return alert("⚠️ Password minimal 6 karakter!");
      if (password !== confirm) return alert("❌ Konfirmasi password tidak cocok!");
      if (localStorage.getItem(email)) return alert("⚠️ Email sudah terdaftar!");

      localStorage.setItem(email, JSON.stringify({ name, email, password, role: "user" }));
      alert("✅ Registrasi berhasil! Silakan login.");
      document.getElementById('toLogin').click();
    });

    // Login
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = loginEmail.value.trim();
      const password = loginPassword.value.trim();

      if (!validEmail(email)) return alert("⚠️ Masukkan format email yang valid!");

      const data = localStorage.getItem(email);
      if (!data) return alert("⚠️ Email belum terdaftar!");
      const user = JSON.parse(data);
      if (user.password !== password) return alert("❌ Password salah!");

      localStorage.setItem('currentUser', JSON.stringify(user));
      alert(`✅ Selamat datang, ${user.name}!`);
      window.location.href = "user.html";
    });
  </script>
</body>
</html>
