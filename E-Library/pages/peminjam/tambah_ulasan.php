<?php


include "../../config/koneksi.php";

if (isset($_POST['tambah_ulasan'])) {
    // Retrieve form data
    $user = $_POST['UserID'];
    $buku = $_POST['BukuID'];
    $ulasan = $_POST['Ulasan'];
    $rating = $_POST['rating'];

    // Insert the review into the database
    $query_input = mysqli_query($koneksi, "INSERT INTO ulasanbuku VALUES (NULL,'$user', '$buku', '$ulasan', '$rating')");
     
    if ($query_input) {
        echo "<script>
            alert('Data Berhasil ditambahkan!!');
            window.location='index.php?pages=ulasan';
        </script> ";
    } else {
        echo "<script>
            alert('Data gagal ditambahkan!!');
            window.location='index.php?pages=ulasan';
        </script> ";
    }
}
?>

