<?php
session_start();
require '../../config/koneksi.php';

if (isset($_POST['tambah'])) {
  $kategori_id = $_POST['KategoriID'];
    $judul = $_POST['Judul'];
    $penulis = $_POST['Penulis'];
    $penerbit = $_POST['Penerbit'];
    $TahunTerbit = $_POST['TahunTerbit'];

   
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
