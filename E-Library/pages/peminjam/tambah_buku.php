<?php
session_start();
require '../../config/koneksi.php';

if (isset($_POST['tambah'])) {
  $kategori_id = $_POST['KategoriID'];
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $TahunTerbit = $_POST['tahunterbit'];

    // Masukkan data buku ke database
  $query_simpan = mysqli_query($koneksi, "INSERT INTO buku VALUES (NULL,'$kategori_id','$judul','$penulis','$penerbit','$TahunTerbit')");

  if ($query_simpan) {
    echo "
    <script>
    window.alert('data berhasil disimpan'); window.location= 'index.php?pages=buku';
    </script>
    ";
  } 
}
?>
