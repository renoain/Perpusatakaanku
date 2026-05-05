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


    .rating {
        color: #ffc107;
    }
</style>
</head>


<div class="container">
<div class="title">
  <h1>Peminjaman</h1><br>
</div>
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalPinjamBuku">
Pinjam Buku
</button> -->
<table id="myTable" class="table table-striped table-hover">
    <thead>
        <th>No</th>
        <th>Nama Peminjam</th>
        <th>Buku</th>
        <th>Tanggal Peminjaman</th>
        <th>Tanggal Pengembalian</th>
        <th>Status Peminjaman</th>
        <th>Aksi</th>
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
            <td><?=$data_peminjam['Judul']?></td>
            <td><?=$data_peminjam['TanggalPeminjaman']?></td>
            <td><?=$data_peminjam['TanggalPengembalian']?></td>
            <td><?=$data_peminjam['StatusPeminjaman']?></td>
            <td>       
              <?php if($data_peminjam['StatusPeminjaman'] == "dipinjam") { ?>
            
            <!-- modal edit pinjam buku -->
<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#Modaleditpinjaman<?=$data_peminjam['PeminjamanID'] ?> ">

edit
</button>
<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#ModalHapusPinjaman<?=$data_peminjam['PeminjamanID'] ?>">

hapus
</button>

<!-- Modal -->

        <div class="modal fade" id="Modaleditpinjaman<?=$data_peminjam['PeminjamanID'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">edit</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
     
              
              <form action="" method="post" >

                  <div class="mb-3">
                    <input type="hidden" name="PeminjamanID" value="<?=$data_peminjam['PeminjamanID'] ?>"  class="form-control" id="exampleFormControlInput1" >
                    <input type="hidden" name="UserID" value="<?= $_SESSION['user_id']?>"  class="form-control" id="exampleFormControlInput1" >
                    </div>
                       <label for="BukuID" class="col-form-label">Buku</label>
                                <select class="form-select" name="BukuID" id="BukuID">
                                <?php
                                include "../../config/koneksi.php";
                                    $buku = mysqli_query($koneksi, "SELECT * FROM buku");
                                    while ($data_buku = mysqli_fetch_assoc($buku)) {
                                ?>
                                <option 
                                <?php if($data_peminjam['BukuID'] == $data_buku['BukuID'] ) echo 'selected'; ?>
                                value="<?=$data_buku['BukuID']?>"><?=$data_buku['Judul']?>
                              
                              </option>
                                <?php
                                    }
                                ?>
                                
                                </select> <br>
              </div>
                     <div class="mb-3">
                         <label for="TanggalPeminjaman" class="form-label">Tanggal Peminjaman</label>  
                         <input type="date" name="TanggalPeminjaman" class="form-control" id="exampleFormControlInput1"
                         value="<?=$data_peminjam['TanggalPeminjaman']?>">
                                   
                     </div>  
                     <div class="mb-3">
                         <label for="TanggalPengembalian" class="form-label">Tanggal Pengembalian</label>  
                         <input type="date" name="TanggalPengembalian" class="form-control" id="exampleFormControlInput1"
                          value="<?=$data_peminjam['TanggalPeminjaman']?>">                
                     </div>  
                   <div class="mb-3">
    <label for="StatusPeminjaman" class="form-label">Status Peminjaman</label>
    <select name="StatusPeminjaman" class="form-select">
        <option value="dipinjam" <?= ($data_peminjam['StatusPeminjaman'] == 'dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
        <option value="dikembalikan" <?= ($data_peminjam['StatusPeminjaman'] == 'dikembalikan') ? 'selected' : '' ?>>Dikembalikan</option>
    </select>
</div>



                                       <!-- form        -->
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="edit_peminjam" class="btn btn-info">Edit</button>
              </div>
              </form>
                                          </div>
                                          
                                          </div>
                                          </div>
                                          </div>
                                          </div>
                                          
                                 

                <!-- modal hapus pinjam buku -->

<!-- Modal hapus pinjam -->

        <div class="modal fade" id="ModalHapusPinjaman<?=$data_peminjam['PeminjamanID'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">hapus</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
              
              <form action="" method="post" >

                  <div class="mb-3">
                    <input type="hidden" name="PeminjamanID" value="<?= $data_peminjam['PeminjamanID']?>"  >
                     
                    <label for="BukuID" class="form-label">Buku</label>
                    <input type="text" name="judul" disabled value="<?= $data_peminjam['Judul']?>" class="form-control" id="exampleControlInput1" >
                    
                    </div>
                          
                     </div>  


                                              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="hapus_peminjam" class="btn btn-danger">Hapus</button>
              </div>
              </form>
<!-- end modal -->
<?php } ?>
            </td>
        </tr>
         <?php } ?>
    </tbody>
</table>
<!-- modal pinjam buku -->


<!-- Modal -->

        <div class="modal fade" id="ModalPinjamBuku" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Form Pinjam Buku</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
              
              <form action="" method="post" >

                  <div class="mb-3">
                    <input type="hidden" name="UserID" value="<?= $_SESSION['user_id']?>"  class="form-control" id="exampleFormControlInput1" >
                    </div>
                    <label for="recipient-name" class="col-form-label">Buku</label>
                                        <select class="form-select" name="BukuID" id="BukuID">
                                        <?php
                                        include "../../config/koneksi.php";
                                            $buku = mysqli_query($koneksi, "SELECT * FROM buku");
                                            while ($data = mysqli_fetch_assoc($buku)) {
                                        ?>
                                        <option value="<?=$data['BukuID']?>"><?=$data['judul']?></option>
                                        <?php
                                            }
                                        ?>
                                        
                                        </select> <br>
              </div>
                     <div class="mb-3">
                         <label for="TanggalPeminjaman" class="form-label">Tanggal Peminjaman</label>  
                         <input type="date" name="TanggalPeminjaman" class="form-control" id="exampleFormControlInput1">                 
                     </div>  
                     <div class="mb-3">
                         <label for="TanggalPengembalian" class="form-label">Tanggal Pengembalian</label>  
                         <input type="date" name="TanggalPengembalian" class="form-control" id="exampleFormControlInput1">                 
                     </div>  
                     <div class="mb-3">
                         <label for="StatusPeminjaman" class="form-label">Tanggal Pengembalian</label>  
                         <select name="StatusPeminjaman" class="form-select">     
                            <option value="dipinjam">Dipinjam</option>            
                            <option value="dikembalikan">dikembalikan</option>    
                                        </select>        
                     </div>  


                                              
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="tambah_peminjam" class="btn btn-primary">tambah</button>
              </div>
              </form>
              </div>
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