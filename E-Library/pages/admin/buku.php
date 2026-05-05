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
        background-color:#6495ED;
        border-color: #007bff;
      
    }
    .btn-close {
    color: #007bff; /* Menggunakan kode hex */
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
        <div class="title">
            <h3>Buku</h3>
        </div>
        <div class="row">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap">Tambah Buku</button>

            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Buku</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="tambah_buku.php" method="post" id="formTambahBuku">
                            <label for="recipient-name" class="col-form-label">kategori</label>
                                <select class="form-select" name="KategoriID" id="KategoriID">
                                <?php
                                include "../../config/koneksi.php";
                                    $kategori = mysqli_query($koneksi, "SELECT * FROM kategoribuku");
                                    while ($data = mysqli_fetch_assoc($kategori)) {
                                ?>
                                <option value="<?=$data['KategoriID']?>"><?=$data['NamaKategori']?></option>
                                <?php
                                    }
                                ?>
                                
                                </select> <br>
                                <div class="mb-3">
                                    <label for="recipient-name" class="col-form-label">Judul Buku :</label>
                                    <input type="text" class="form-control" name="Judul" id="recipient-name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="recipient-name" class="col-form-label">Penulis :</label>
                                    <input type="text" class="form-control" name="Penulis" id="recipient-name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="recipient-name" class="col-form-label">Penerbit :</label>
                                    <input type="text" class="form-control" name="Penerbit" id="recipient-name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="recipient-name" class="col-form-label">Tahun Terbit :</label>
                                    <input type="date" class="form-control" name="TahunTerbit" id="recipient-name" required>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <input type="submit" name="tambah" class="btn btn-primary" form="formTambahBuku" value="Tambah">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-10 ml-auto mr-auto">
                <!-- <h4><small>Data Buku</small></h4> -->
                <div class="table-responsive">
                    <table id="myTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Penerbit</th>
                                <th>Tahun Terbit</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require '../../config/koneksi.php';
                            $no = 0;
                            $query_data_buku = mysqli_query($koneksi, "SELECT * FROM buku INNER JOIN kategoribuku ON buku.KategoriID= kategoribuku.KategoriID");
                            while ($data = mysqli_fetch_assoc($query_data_buku)) {
                                $no++;
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no ?></td>
                                    <td><?= $data['Judul'] ?></td>
                                    <td><?= $data['NamaKategori'] ?></td>
                                    <td><?= $data['Penulis'] ?></td>
                                    <td><?= $data['Penerbit'] ?></td>
                                    <td><?= $data['TahunTerbit'] ?></td>
                                    <td class="td-actions text-center">
                                        
                                

        </div>
    </div>
</div>  
                                        

                                        <a href="index.php?pages=edit_buku&BukuID=<?= $data['BukuID'] ?>">
                                            <button type="button" rel="tooltip" class="btn btn-success btn-round btn-just-icon btn-sm" data-original-title="" title="">
                                                <i class="material-icons">edit</i>
                                            </button>
                                        </a>

                                        <button type="button" rel="tooltip" class="btn btn-danger btn-round btn-just-icon btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal<?= $data['BukuID'] ?>" data-original-title="" title="">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Modal konfirmasi hapus -->
                                <div class="modal fade" id="hapusModal<?= $data['BukuID'] ?>" tabindex="-1" aria-labelledby="hapusModalLabel<?= $data['BukuID'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="hapusModalLabel<?= $data['BukuID'] ?>">Konfirmasi Hapus Buku</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda yakin ingin menghapus buku ini?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <!-- Tambahkan URL hapus buku pada atribut href -->
                                                <a href="hapus_buku.php?BukuID=<?= $data['BukuID'] ?>" class="btn btn-danger">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Akhir Modal konfirmasi hapus -->
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
