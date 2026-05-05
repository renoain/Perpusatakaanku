<?php
include("../../config/koneksi.php");

$query = mysqli_query($koneksi, "SELECT * FROM koleksipribadi
            LEFT JOIN buku ON koleksipribadi.BukuID = buku.BukuID
            LEFT JOIN user ON koleksipribadi.UserID = user.UserID");

?>

<div class="container">
    <div class="row mt-2">
        <div class="col" style="min-height: 675px;">
            <div class="card">
                <div class="card-header">
                    Data Koleksi pribadi
                </div>
                <div class="card-body">
                    <div class="row">
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
                                            <th scope="col">Nama User</th>
                                            <th scope="col">Buku</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 0;
                                        while ($data = mysqli_fetch_assoc($query)) {
                                            $no++;
                                        ?>
                                            <tr>
                                                <td><?= $no; ?></td>
                                                <td><?= $data['Username'] ?></td>
                                                <td><?= $data['Judul'] ?></td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-outline-danger btn-round btn-just-icon btn-sm" data-bs-toggle="modal" data-bs-target="#hapusModal<?= $data['KoleksiID'] ?>">
                                                        <i class="material-icons">delete_forever</i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- MODAL HAPUS KATEGORI -->
                                            <div class="modal" tabindex="-1" id="hapusModal<?= $data['KoleksiID'] ?>" aria-labelledby="hapusModalLabel<?= $data['KoleksiID'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Konfirmasi</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="hapus_koleksi.php" method="POST">
                                                                <p>Apa anda yakin ingin menghapus buku <b><?= $data['judul'] ?></b> dari koleksi pribadi?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">BATAL</button>
                                                            <input type="hidden" name="KoleksiID" value="<?= $data['KoleksiID'] ?>">
                                                            <input type="submit" class="btn btn-danger" name="hapus" value="HAPUS">
                                                        </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END MODAL HAPUS -->
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
