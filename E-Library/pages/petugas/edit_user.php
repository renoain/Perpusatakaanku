<?php
include("../../config/koneksi.php");

if(isset($_POST['update'])) {
    // Ambil data yang dikirimkan dari form modal
    $UserID = $_POST['UserID'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $NamaLengkap = $_POST['NamaLengkap'];
    $alamat = $_POST['alamat'];
    $level = $_POST['level'];

    // Lakukan validasi data di sini jika diperlukan

    // Update data user ke database
    $query = "UPDATE user SET 
                username = '$username', 
                password = '$password', 
                email = '$email', 
                NamaLengkap = '$NamaLengkap', 
                alamat = '$alamat', 
                level = '$level' 
              WHERE UserID = $UserID";
    
    $result = mysqli_query($koneksi, $query);

    if($result) {
        // Jika update berhasil, berikan pesan sukses
        echo "<script>alert('Data user berhasil diperbarui');</script>";
        echo "<script>window.location.href = 'index.php?pages=user';</script>"; // Ganti 'index.php' dengan halaman yang sesuai
    } else {
        // Jika update gagal, berikan pesan error
        echo "<script>alert('Gagal memperbarui data user');</script>";
        echo "<script>window.location.href = 'index.php?pages=user';</script>"; // Ganti 'index.php' dengan halaman yang sesuai
    }
}
?>
