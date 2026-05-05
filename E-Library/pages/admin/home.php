


<div class="container">
   <div class="p-5 mb-4 mt-2 bg-body-tertiary rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Selamat Datang  <?php echo $_SESSION['Email'];?></h1>
        <h4>Di Aplikasi Perpustakaan Kita Bisa Memilih Buku Yang Akan Pinjam Dengan Mudah   </h4>
      
      </div>
   </div>
   <div class="row" style="min-height: 295px;" color bg="primary">
      <div class="col">
      <div class="card">
      <div class="card-body">
        <h5 class="card-title">Peminjaman</h5>
        <p class="card-text">Jumlah Peminjaman <?php include "../../config/koneksi.php"; echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM peminjaman")) ?></p>
        <a href="?pages=peminjam" class="btn btn-primary">Peminjaman</a>
   </div>
   </div>
   </div>
         
   <div class="col">
      <div class="card">
      <div class="card-body">
        <h5 class="card-title">Buku</h5>
        <p class="card-text">Jumlah Buku <?php include "../../config/koneksi.php"; echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM buku")) ?></p>
        <a href="?pages=buku" class="btn btn-primary">Buku</a>
   </div>
   </div>
   </div>
   
   <div class="col">
      <div class="card">
      <div class="card-body">
        <h5 class="card-title">Kategori</h5>
        <p class="card-text">Jumlah Kategori <?php include "../../config/koneksi.php"; echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kategoribuku")) ?></p>
        <a href="?pages=Kategori" class="btn btn-primary">Kategori</a>
   </div>
   </div>
   </div>
   </div>


</div>