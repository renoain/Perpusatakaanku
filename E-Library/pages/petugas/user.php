<?php
include ("../../config/koneksi.php");
?>

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

<div class="container">
    <div class="row mt-2">
        <div class="col" style="min-height: 675px;">
            <div class="card">
                <div class="card-header">
                    DATA USER
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tambahUser" data-bs-whatever="@mdo">TAMBAH DATA</button>

                            <!-- MODAL TAMBAH user -->
                            <div class="modal fade" id="tambahUser" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="userModalLabel">Tambah Data</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="tambah_user.php" method="POST">
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label">Username:</label>
                                                <input type="text" name="Username" class="form-control" id="recipient-name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label">Password:</label>
                                                <input type="password" name="Password" class="form-control" id="recipient-name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label">Email:</label>
                                                <input type="text" name="Email" class="form-control" id="recipient-name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label">Nama Lengkap:</label>
                                                <input type="text" name="Nama_Lengkap" class="form-control" id="recipient-name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label">Alamat:</label>
                                                <input type="text" name="Alamat" class="form-control" id="recipient-name">
                                            </div>
                                            <div class="mb-3">
                                              <label for="recipient-name" class="col-form-label">Level:</label>
                                                  <select name="Level" class="form-select">
                                                    <option value="peminjam">Peminjam</option>
                                                    <option value="petugas">Petugas</option>
                                                    <option value="admin">Administrator</option>
                                                  </select>
                                              </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <input type="submit" class="btn btn-primary" name="submit" value="Simpan">
                                    </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- END MODAL TAMBAH BUKU -->
                            
                            </div>
                        </div>  
                        <div class="col">
                            <form class="row g-3 float-end" action="" method="POST">
                               
                            </form>
                        </div>         
                        <div class="row">
                            <div class="col">
                                <table id="myTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">User ID</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Password</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Nama Lengkap</th>
                                        <th scope="col">Alamat</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if (isset($_POST['cari'])) {
                                            $keyword = $_POST['pencarian'];
                                            $query=mysqli_query($koneksi, "SELECT * FROM user WHERE username like '%$keyword%'");
                                        } else{
                                            $query=mysqli_query($koneksi, "SELECT * FROM user");
                                        }
                                        $no = 0;
                                            while ($data = mysqli_fetch_assoc($query)) {
                                            $no++;
                                        ?>
                                        <tr>
                                        <td><?=$no; ?></td>
                                        <td><?=$data['UserID']?></td>
                                        <td><?=$data['username']?></td>
                                        <td><?=$data['password']?></td>
                                        <td><?=$data['email']?></td>
                                        <td><?=$data['NamaLengkap']?></td>
                                        <td><?=$data['Alamat']?></td>
                                        <td><?=$data['level']?></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-danger btn-round btn-just-icon btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal<?= $data['UserID'] ?>">
                                                  <i class="material-icons">delete_forever</i>
                                            </button>
                                            <button type="button" class="btn btn-dark btn-round btn-just-icon btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['UserID'] ?>">
                                                <i class="material-icons">edit_square</i>
                                            </button>
                                        </td>
                                        </tr>

                                        <!-- MODAL HAPUS USER -->
                                        <div class="modal" tabindex="-1" id="hapusModal<?= $data['UserID'] ?>" aria-labelledby="hapusModalLabel<?= $data['UserID'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Konfirmasi</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form action="hapus_user.php" method="POST">
                                                                <p>Apa anda yakin ingin menghapus akun <b><?=$data['username']?></b>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">BATAL</button>
                                                                    <input type="hidden" name="UserID" value="<?= $data['UserID'] ?>">
                                                                    <input type="submit" class="btn btn-danger" name="hapus" value="HAPUS">
                                                            </div>
                                                            </form>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- END MODAL HAPUS -->

                                                <!-- MODAL EDIT -->
                                                <div class="modal fade" id="editModal<?= $data['UserID'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $data['UserID'] ?>" aria-hidden="true">
                                                  <div class="modal-dialog">
                                                      <div class="modal-content">
                                                      <div class="modal-header">
                                                          <h1 class="modal-title fs-5" id="editModalLabel">Edit Data</h1>
                                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                      </div>
                                                      <div class="modal-body">
                                                            <form action="edit_user.php" method="POST">
                                                            <input type="hidden" name="UserID" value="<?= $data['UserID'] ?>">
                                                                <div class="mb-3">
                                                                    <label for="recipient-name" class="col-form-label">Username:</label>
                                                                    <input type="text" name="username" class="form-control" id="recipient-name" value="<?= $data['username'] ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="recipient-name" class="col-form-label">Password:</label>
                                                                    <input type="text" name="password" class="form-control" id="recipient-name" value="<?= $data['password'] ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="recipient-name" class="col-form-label">Email:</label>
                                                                    <input type="text" name="email" class="form-control" id="recipient-name" value="<?= $data['email'] ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="recipient-name" class="col-form-label">Nama Lengkap:</label>
                                                                    <input type="text" name="NamaLengkap" class="form-control" id="recipient-name" value="<?= $data['NamaLengkap'] ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="recipient-name" class="col-form-label">Alamat:</label>
                                                                    <input type="text" name="alamat" class="form-control" id="recipient-name" value="<?= $data['alamat'] ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="recipient-name" class="col-form-label">Level:</label>
                                                                    <select name="level" class="form-select">
                                                                      <option value="peminjam" <?php if ($data['level'] == "peminjam") echo 'selected' ?>>Peminjam</option>
                                                                      <option value="petugas" <?php if ($data['level'] == "petugas") echo 'selected' ?>>Petugas</option>
                                                                      <option value="admin" <?php if ($data['level'] == "admin") echo 'selected' ?>>Administrator</option>
                                                                    </select>
                                                                </div>
                                                            <div class="modal-footer">
                                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                              <input type="submit" class="btn btn-primary" name="update" value="Simpan">
                                                            </div>
                                                          </form>
                                                      </div>
                                                    </div>
                                                    <!-- END MODAL EDIT -->
                                        <?php } 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
