

<?php
include("../../config/koneksi.php");

    if (isset($_POST['hapus'])) {
        $koleksiID      = $_POST['KoleksiID'];

        $query_hapus = mysqli_query($koneksi, "DELETE FROM koleksipribadi WHERE KoleksiID = $koleksiID");

if ($query_hapus)  {
        echo "<script>
            alert('Data Berhasil dihapus!!');
            window.location='index.php?pages=koleksi_pribadi';
        </script> ";
    } else {
        echo "<script>
            alert('Data gagal dihapus!!');
            window.location='index.php?pages=koleksi_pribadi';
        </script> ";
    }
    }

?>