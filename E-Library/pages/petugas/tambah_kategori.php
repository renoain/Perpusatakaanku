<?php
  require '../../config/koneksi.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Periksa apakah tombol submit diklik
   
        // Ambil data kategori dari formulir
        $kategori_buku = $_POST["kategori"];

     $query ="INSERT INTO kategoribuku VALUES (null, '$kategori_buku')";
     $result = mysqli_query($koneksi, $query);

     if($result) {
        header("Location: index.php?pages=Kategori");
        exit();
     } else {
        echo "Error:" . mysqli_error($koneksi);
     }
       
}

// Jika formulir tidak terkirim, mungkin tindakan yang sesuai seperti menampilkan pesan kesalahan atau melakukan redirect
?>
