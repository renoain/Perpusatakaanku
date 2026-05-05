<head>
  <style>
    body {
        background-color: #f8f9fa;
        color: #495057;
    }

    .container {
        background-color: #ffffff;
        border-radius: 8px;
        margin-top: 20px;
        padding: 20px;
    }

    .title h3 {
        color: #007bff;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .modal-content {
        background-color: #ffffff;
        border-radius: 8px;
    }

    .modal-header {
        background-color: #007bff;
        color: #ffffff;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    .table {
        background-color: #ffffff;
    }

    .table th,
    .table td {
        border: 1px solid #dee2e6;
    }

    .table th {
        background-color: #007bff;
        color: #ffffff;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .btn-success,
    .btn-danger {
        background-color: #ffffff;
        border-color: #dee2e6;
        color: #495057;
    }

    .btn-success:hover,
    .btn-danger:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #495057;
    }

    .rating {
        color: #ffc107;
    }
</style>
</head>
<table class="table table-striped table-hover">
    <thead>
        <th>No</th>
        <th>Nama Peminjam</th>
        <th>Buku</th>
        <th>Tanggal Peminjaman</th>
        <th>Tanggal Pengembalian</th>
        <th>Status Peminjaman</th>
      
    </thead>
    <tbody>
        <?php
        include "../../config/koneksi.php";
        $no = 1;
        $query_data_peminjam = mysqli_query($koneksi, "SELECT * FROM peminjaman LEFT JOIN user ON peminjaman.UserID=user.UserID
        LEFT JOIN buku ON peminjaman.BukuID=buku.BukuID");
        while ($data_peminjam = mysqli_fetch_assoc($query_data_peminjam)) {
        ?>
        <tr>
            <td><?=$no++;?></td>
            <td><?=$data_peminjam['NamaLengkap']?></td>
            <td><?=$data_peminjam['judul']?></td>
            <td><?=$data_peminjam['TanggalPeminjaman']?></td>
            <td><?=$data_peminjam['TanggalPengembalian']?></td>
            <td><?=$data_peminjam['StatusPeminjaman']?></td>
            <td>       
              <?php if($data_peminjam['StatusPeminjaman'] == "dipinjam") { ?>
            
            <!-- modal edit pinjam buku -->

<!-- end modal -->
<?php } ?>
            </td>
        </tr>
         <?php } ?>
    </tbody>
</table>
<!-- modal pinjam buku -->

              </form>
<!-- end modal -->
<?php
include "../../config/koneksi.php";

if(isset($_POST['tambah_peminjam'])){
    // Get form data
    $userID = $_POST['UserID'];
    $bukuID = $_POST['BukuID'];
    $tanggalPeminjaman = $_POST['TanggalPeminjaman'];
    $tanggalPengembalian = $_POST['TanggalPengembalian'];
    $statusPeminjaman = $_POST['StatusPeminjaman'];

    // Insert data into the peminjaman table
    $insertQuery = "INSERT INTO peminjaman (UserID, BukuID, TanggalPeminjaman, TanggalPengembalian, StatusPeminjaman) 
                    VALUES ('$userID', '$bukuID', '$tanggalPeminjaman', '$tanggalPengembalian', '$statusPeminjaman')";

    if(mysqli_query($koneksi, $insertQuery)){
      echo "<script>
      alert('Data Berhasil ditambahkan!!');
      window.location='index.php?pages=peminjam';
  </script> ";
} else {
  echo "<script>
      alert('Data gagal ditambahkan!!');
      window.location='index.php?pages=peminjam';
  </script> ";
}
}

mysqli_close($koneksi);
?>
<!-- hapus proses -->
<?php
include "../../config/koneksi.php";

// Proses hapus peminjam
if(isset($_POST['hapus_peminjam'])){
    // Get PeminjamanID from the form
    $peminjamanID = $_POST['PeminjamanID'];

    // Query untuk menghapus peminjam berdasarkan PeminjamanID
    $deleteQuery = "DELETE FROM peminjaman WHERE PeminjamanID = '$peminjamanID'";

    // Eksekusi query
    if(mysqli_query($koneksi, $deleteQuery)){
      echo "<script>
      alert('Data Berhasil dihapus!!');
      window.location='index.php?pages=peminjam';
  </script> ";
} else {
  echo "<script>
      alert('Data gagal dihapus!!');
      window.location='index.php?pages=peminjam';
  </script> ";
}
}

// ... (sisa kode PHP lainnya)

?>

<!-- end hapus proses -->

<!-- // Edit process -->
<?php
include "../../config/koneksi.php";
if (isset($_POST['edit_peminjam'])) {
    $peminjamanID         = $_POST['PeminjamanID'];
    $userID               = $_POST['UserID'];
    $bukuID               = $_POST['BukuID'];
    $tanggalPeminjaman    = $_POST['TanggalPeminjaman'];
    $tanggalPengembalian  = $_POST['TanggalPengembalian'];
    $statusPeminjaman     = $_POST['StatusPeminjaman'];

    $updateQuery = "UPDATE peminjaman SET 
                    UserID = '$userID',
                    BukuID = '$bukuID',
                    TanggalPeminjaman = '$tanggalPeminjaman', 
                    TanggalPengembalian = '$tanggalPengembalian', 
                    StatusPeminjaman = '$statusPeminjaman' 
                    WHERE PeminjamanID = '$peminjamanID'";

    if (mysqli_query($koneksi, $updateQuery)) {
        echo "<script>
            alert('Data Berhasil diubah!!');
            window.location='index.php?pages=peminjam';
        </script> ";
    } else {
        echo "<script>
            alert('Data gagal diubah!!');
            window.location='index.php?pages=peminjam';
        </script> ";
    }
}

?>