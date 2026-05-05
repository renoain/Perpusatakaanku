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
 if (isset($_POST['submit'])) {
    $Username          = $_POST['Username'];
    $Password          = md5($_POST['Password']);
    $Email             = $_POST['Email'];
    $Nama_Lengkap      = $_POST['Nama_Lengkap'];
    $Alamat            = $_POST['Alamat'];
    $Level             = $_POST['Level'];

    $query_daftar = mysqli_query($koneksi,"INSERT INTO user VALUES ('','$Username','$Password',' $Email ',' $Nama_Lengkap ',' $Alamat ','$Level')");
    
    if ($query_daftar) {
    header('Location: index.php?pages=user'); 
} else {
    header('Location: index.php?pages=user');
}
 }
?>