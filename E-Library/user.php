<?php 
require_once 'config.php';

// Cek login
if (!is_logged_in()) {
    redirect('login.php');
}

$user_name = get_user_name();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>E-Library | Dashboard User</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    
    <style>
        .header-carousel .owl-nav { display: none !important; }
        .header-carousel .owl-carousel-item { height: 600px; position: relative; }
        .header-carousel .owl-carousel-item img { width: 100%; height: 100%; object-fit: cover; }
        body { overflow-x: hidden; }
        .container-fluid { padding-left: 0; padding-right: 0; }
        .welcome-banner {
            background: linear-gradient(135deg, #06BBCC 0%, #05a0b0 100%);
            padding: 30px; border-radius: 15px; color: white; margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(6, 187, 204, 0.3);
        }
        .welcome-banner h2 { font-weight: 700; margin-bottom: 10px; }
        .welcome-banner p { margin: 0; opacity: 0.9; }
    </style>
</head>

<body>
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
    </div>

    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="user.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>E-Library</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="user.php" class="nav-item nav-link active">Home</a>
                <a href="books-user.php" class="nav-item nav-link">Koleksi</a>
                <a href="wishlist.php" class="nav-item nav-link">Wishlist</a>
                <a href="riwayat.php" class="nav-item nav-link">Riwayat</a>
                
            </div>
            <a href="profile.php" class="btn btn-danger py-4 px-lg-5 d-none d-lg-block">
                <i class="fa fa-sign-out-alt me-2"></i>logout
            </a>
        </div>
    </nav>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="welcome-banner wow fadeInUp" data-wow-delay="0.1s">
                <h2>Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h2>
                <p>Selamat datang di dashboard E-Library. Nikmati akses ke ribuan koleksi buku digital.</p>
            </div>
        </div>
    </div>

    <div class="container-fluid p-0 mb-5">
        <div class="owl-carousel header-carousel position-relative">
            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="img/Homepage 1.jpg" alt="Perpustakaan Digital">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
                    style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">E-Library</h5>
                                <h1 class="display-3 text-white animated slideInDown">Temukan & Baca Buku Favoritmu</h1>
                                <p class="fs-5 text-white mb-4 pb-2">Akses koleksi buku digital, pinjam instan, simpan wishlist, dan pantau riwayat peminjaman.</p>
                                <a href="books-user.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Jelajahi Buku</a>
                                <a href="wishlist.php" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Wishlist</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="owl-carousel-item position-relative">
                <img class="img-fluid" src="img/Homepage 2.jpg" alt="Pinjam & Kelola Buku">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
                    style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">E-Library</h5>
                                <h1 class="display-3 text-white animated slideInDown">Pinjam & Kelola Buku dengan Mudah</h1>
                                <p class="fs-5 text-white mb-4 pb-2">Sistem peminjaman terintegrasi dengan riwayat & notifikasi pengingat otomatis.</p>
                                <a href="riwayat.php" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Lihat Riwayat</a>
                                <a href="profile.php" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Profil Pengguna</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-search text-primary mb-4"></i>
                            <h5 class="mb-3">Pencarian Buku</h5>
                            <p>Cari buku dengan cepat menggunakan kata kunci dan filter.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-book text-primary mb-4"></i>
                            <h5 class="mb-3">Peminjaman Digital</h5>
                            <p>Pinjam buku secara digital dan simpan riwayatmu.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-heart text-primary mb-4"></i>
                            <h5 class="mb-3">Wishlist</h5>
                            <p>Simpan buku favorit untuk dibaca nanti.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-chart-line text-primary mb-4"></i>
                            <h5 class="mb-3">Profil Pengguna</h5>
                            <p>Kelola informasi pribadi dan pengaturan akunmu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="img/about.jpg" alt="Tentang E-Library" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">Tentang Kami</h6>
                    <h1 class="mb-4">Selamat Datang di E-Library</h1>
                    <p class="mb-4">Platform perpustakaan digital untuk membaca, meminjam, dan mengelola koleksi buku secara online.</p>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="books-user.php">Jelajahi Koleksi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Menu Cepat</h4>
                    <a class="btn btn-link" href="user.php">Home</a>
                    <a class="btn btn-link" href="books-user.php">Koleksi Buku</a>
                    <a class="btn btn-link" href="wishlist.php">Wishlist</a>
                    <a class="btn btn-link" href="riwayat.php">Riwayat</a>
                    <a class="btn btn-link" href="profile.php">Profil</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Kontak Kami</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Jl. Karangrejo Sawah II, Wonokromo, Kec. Wonokromo, Surabaya, Jawa Timur</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+62 857-3075-2609</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>24051214236@mhs.unesa.ac.id</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Galeri</h4>
                    <div class="row g-2 pt-2">
                        <div class="col-4"><img class="img-fluid bg-light p-1" src="img/Galeri 1.jpg" alt=""></div>
                        <div class="col-4"><img class="img-fluid bg-light p-1" src="img/Galeri 2.jpg" alt=""></div>
                        <div class="col-4"><img class="img-fluid bg-light p-1" src="img/Galeri 3.jpg" alt=""></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Tentang Kami</h4>
                    <p><strong>E-Library</strong> adalah platform perpustakaan digital modern.</p>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright text-center py-3">
                &copy; 2025 E-Library. All rights reserved.
            </div>
        </div>
    </div>

    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>