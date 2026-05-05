<?php
include "../../config/koneksi.php";
if(isset($_POST['edit_ulasan'])){
    $ulasan_id  = $_POST['UlasanID'];
    $user_id    = $_POST['UserID'];
    $buku_id    = $_POST['BukuID'];
    $ulasan     = $_POST['Ulasan'];
    $rating     = $_POST['rating'];

    $query_edit = mysqli_query($koneksi, "UPDATE ulasanbuku SET UserID='$user_id', BukuID='$buku_id',Ulasan='$ulasan',rating='$rating' WHERE UlasanID=$ulasan_id");
    if ($query_edit) {
        echo "<script>
            alert('Data Berhasil diedit!!');
            window.location='index.php?pages=ulasan';
        </script> ";
    } else {
        echo "<script>
            alert('Data gagal diedit!!');
            window.location='index.php?pages=ulasan';
        </script> ";
    }
}
?>