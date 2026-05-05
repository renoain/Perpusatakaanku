<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- <script src="../../../assets/js/sweetalert.js"></script> -->
</head>
</html>

<?php
include("../../config/koneksi.php");

    if (isset($_POST['hapus'])) {
        $userID      = $_POST['UserID'];

        $query_hapus = mysqli_query($koneksi, "DELETE FROM user WHERE UserID = $userID");

if ($query) {
        $_SESSION['success_message'] = "Data berhasil dihapus";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus buku";
    }
}

// Redirect kembali ke halaman yang menampilkan data buku
header("Location: index.php?pages=user");
exit();

?>