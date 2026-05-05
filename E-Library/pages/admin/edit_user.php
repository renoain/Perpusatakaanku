<?php
include("../../config/koneksi.php");

if(isset($_POST['update'])) {
    // Ambil data yang dikirimkan dari form modal
    $UserID = $_POST['UserID'];
    $username = $_POST['Username'];
    $password = $_POST['Password'];
    $email = $_POST['Email'];
    $NamaLengkap = $_POST['NamaLengkap'];
    $alamat = $_POST['alamat'];
    $level = $_POST['Level'];

    // Lakukan validasi data di sini jika diperlukan

    // Update data user ke database
    $query = "UPDATE user SET 
                Username = '$username', 
                Password = '$password', 
                Email = '$email', 
                NamaLengkap = '$NamaLengkap', 
                alamat = '$alamat', 
                Level = '$level' 
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
