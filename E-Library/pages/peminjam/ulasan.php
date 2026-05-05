<?php
session_start();
include "../../config/koneksi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulasan</title>
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

<body>
    <div class="container">
        <h1>Ulasan</h1><br>
        <table id="myTable" class="table table-striped">
            <thead>
                <tr>
                    <th>no</th>
                    <th>Buku</th>
                    <th>Nama Pengulas</th>
                    <th>Ulasan</th>
                    <th>Rating</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require '../../config/koneksi.php';
                $no = 1;
                $query_ulasan = mysqli_query($koneksi, "SELECT * FROM ulasanbuku 
                                                        INNER JOIN buku ON ulasanbuku.BukuID=buku.BukuID
                                                        INNER JOIN user ON ulasanbuku.UserID=user.UserID");
                while ($data = mysqli_fetch_assoc($query_ulasan)) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $data['Judul'] ?></td>
                        <td><?= $data['NamaLengkap'] ?></td>
                        <td><?= $data['Ulasan'] ?></td>
                        <td><?= $data['Rating'] ?></td>
                        <td>
                            <?php if ($data['UserID'] == $_SESSION['user_id']) { ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalEditUlasan<?= $data['UlasanID'] ?>">Edit</button>
                                <!-- Modal -->
                                <div class="modal fade" id="ModalEditUlasan<?= $data['UlasanID'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Form Edit Ulasan</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="edit_ulasan.php" method="post">
                                                    <input type="hidden" name="UlasanID" value="<?= $data['UlasanID'] ?>" class="form-control">
                                                    <input type="hidden" name="UserID" value="<?= $_SESSION['user_id'] ?>" class="form-control">
                                                    <input type="hidden" name="BukuID" value="<?= $data['BukuID'] ?>" class="form-control">
                                                    <div class="mb-3">
                                                        <label for="exampleFormControlTextarea1" class="form-label">Ulasan</label>
                                                        <textarea class="form-control" name="Ulasan" rows="3"><?= $data['Ulasan'] ?></textarea>
                                                    </div>
                                                    <label for="recipient-name" class="col-form-label">Rating</label>
                                                    <select name="rating" class="form-select" aria-label="Default select example">
                                                        <?php
                                                        for ($r = 1; $r <= 10; $r++) { ?>
                                                            <option <?php if ($data['rating'] == $r) echo 'selected'; ?>><?= $r; ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <input type="submit" name="edit_ulasan" value="Update" class="btn btn-primary">
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- end modal edit -->
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambahUlasan">Tambah Ulasan</button>
        <div class="modal fade" id="ModalTambahUlasan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Form Tambah Ulasan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="tambah_ulasan.php" method="post">
                            <input type="hidden" name="UserID" value="<?= $_SESSION['user_id'] ?>" class="form-control">
                            <label for="recipient-name" class="col-form-label">Buku</label>
                            <select class="form-select" name="BukuID" id="BukuID">
                                <?php
                                include "../../config/koneksi.php";
                                $buku = mysqli_query($koneksi, "SELECT * FROM buku");
                                while ($data = mysqli_fetch_assoc($buku)) {
                                ?>
                                    <option value="<?= $data['BukuID'] ?>"><?= $data['judul'] ?></option>
                                <?php
                                }
                                ?>
                            </select> <br>
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Ulasan</label>
                                <textarea class="form-control" name="Ulasan" id="exampleFormControlTextarea1" rows="3"></textarea>
                            </div>
                            <label for="recipient-name" class="col-form-label">Rating</label>
                            <select name="rating" class="form-select" aria-label="Default select example">
                                <option selected>Ranting</option>
                                <?php for ($r = 1; $r <= 10; $r++) { ?>
                                    <option value="<?= $r ?>"><?= $r ?></option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="tambah_ulasan" class="btn btn-primary">Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
