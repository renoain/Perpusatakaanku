<?php
session_start();
require '../../config/koneksi.php';

if (isset($_GET['BukuID'])) {
    $BukuID = $_GET['BukuID'];

    // Lakukan query DELETE ke database sesuai dengan BukuID yang diterima
    $query = mysqli_query($koneksi, "DELETE FROM buku WHERE BukuID = '$BukuID'");

    if ($query) {
        $_SESSION['success_message'] = "Buku berhasil dihapus";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus buku";
    }
}

// Redirect kembali ke halaman yang menampilkan data buku
header("Location: index.php?pages=buku");
exit();
?>
