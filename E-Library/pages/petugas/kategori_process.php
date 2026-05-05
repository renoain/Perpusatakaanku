<?php
require '../../config/koneksi.php';



    // Edit Kategori
    if (isset($_POST['edit_kategori'])) {
        $kategoriID = $_POST['KategoriID'];
        $kategori = $_POST['kategori'];

        $query = mysqli_query($koneksi, "UPDATE kategoribuku SET NamaKategori='$kategori' WHERE KategoriID=$kategoriID");

        if ($query) {
            header('Location: index.php'); // Ganti dengan halaman yang sesuai setelah berhasil edit
        } else {
            echo "Gagal mengedit kategori.";
        }
    }

    // Hapus Kategori
    if (isset($_POST['delete_kategori'])) {
        $kategoriID = $_POST['KategoriID'];

        $query = mysqli_query($koneksi, "DELETE FROM kategoribuku WHERE KategoriID='$kategoriID'");

        if ($query) {
            header('Location: index.php?pages=Kategori'); // Ganti dengan halaman yang sesuai setelah berhasil hapus
        } else {
            echo "Gagal menghapus kategori.";
        }
    }
?>
