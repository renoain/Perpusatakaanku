<?php
include("../../config/koneksi.php");

if (isset($_POST['tambahkan'])){
    $UserID            = $_POST['UserID'];
    $BukuID            = $_POST['BukuID'];

    $query_simpan = mysqli_query($koneksi, "INSERT INTO koleksipribadi VALUES (NULL,'$UserID','$BukuID')");

    if ($query_simpan) {
       
        echo "<script>
            alert('Data Berhasil disimpan!!');
            window.location='index.php?pages=koleksi_pribadi';
        </script> ";
    } else {
        echo "<script>
            alert('Data gagal disimpan!!');
            window.location='index.php?pages=koleksi_pribadi';
        </script> ";
    }  
}

?>