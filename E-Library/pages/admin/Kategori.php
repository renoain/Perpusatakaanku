<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kategori - Perpustakaan</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
    
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
    } */

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

    /* .btn-success,
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
    } */

    .rating {
        color: #ffc107;
    }
</style>
</head>

<body>
    <div class="container">
        <div class="col-lg-12 col-md-10 ml-auto mr-auto">
            <h3>Kategori</h3><br>
            <!-- Button to trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap">Tambah Kategori</button>

            <!-- Modal for adding category -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Tambah Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form for adding category -->
                            <form action="tambah_kategori.php" method="POST">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Nama Kategori:</label>
                                    <input type="text" class="form-control" id="kategori" name="kategori" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of modal -->

           

            <div class="table-responsive">
                <table id="myTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kategori</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require '../../config/koneksi.php';
                        $query = mysqli_query($koneksi, "SELECT * FROM kategoribuku");
                        $no = 0;
                        while ($data = mysqli_fetch_assoc($query)) {
                            $no++;
                        ?>
                            <tr>
                                <td class="text-center"><?= $no ?></td>
                                <td><?= $data['NamaKategori'] ?></td>
                                <td class="td-actions text-center">
                                 <button type="button" rel="tooltip" class="btn btn-primary btn-round btn-just-icon btn-sm" data-original-title="" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['KategoriID'] ?>" title="">
    <i class="material-icons">edit</i>
</button>
 <!-- Modal for editing category -->
 <div class="modal fade" id="editModal<?= $data['KategoriID'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Kategori</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form for editing category -->
                            <form action="kategori_process.php" method="POST">
                                <input type="text" id="editKategoriID" name="KategoriID" value="<?= $data['KategoriID'] ?>">
                                <div class="mb-3">
                                    <label for="editKategori" class="form-label">Nama Kategori:</label>
                                    <input type="text" class="form-control" id="editKategori" name="kategori" value="<?=$data['NamaKategori']?>"required>
                                </div>
                                <input type="submit" name="edit_kategori" value="Edit" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of edit modal -->
                                   


                                    <button type="button" rel="tooltip" class="btn btn-primary btn-round btn-just-icon btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal<?= $data['KategoriID'] ?>" data-original-title="" title="">
    <i class="material-icons">delete</i>
</button>
                                    </button>
                                    <!-- Modal for deleting category -->
                                    <div class="modal fade" id="hapusModal<?= $data['KategoriID'] ?>" tabindex="-1" aria-labelledby="hapusModalLabel<?= $data['KategoriID'] ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="hapusModalLabel<?= $data['KategoriID'] ?>">Konfirmasi Hapus Kategori</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Anda yakin ingin menghapus kategori ini?</p>
                                                    <form action="kategori_process.php" method="post">
                                                        <input type="hidden" name="KategoriID" value="<?= $data['KategoriID'] ?>">
                                                        <input type="submit" name="delete_kategori" value="Hapus" class="btn btn-danger">
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End of delete modal -->
                                    <!-- Add other action buttons here if needed -->
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->

    <script>
        // Function to set the values in the edit modal
        function setEditModalValues(kategoriID, namaKategori) {
            document.getElementById('editKategoriID').value = kategoriID;
            document.getElementById('editKategori').value = namaKategori;
            $('#editModal').modal('show'); // Show the edit modal
        }
    </script>
</body>

</html>
