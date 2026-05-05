<!DOCTYPE html>
<html lang="en">

<head>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-material-design@4.1.1/dist/css/bootstrap-material-design.min.css" integrity="sha384-wXznGJNEXNG1NFsbm0ugrLFMQPWswR3lds2VeinahP8N0zJw9VWSopbjv2x7WCvX" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
    <style>
        /* Add your custom CSS here */
        /* ... */
    </style>
</head>

<body>
    <div class="container">
        <div class="title">
            <h3>Edit Buku</h3>
        </div>

        <?php
        // Include the database connection and necessary functions
        require '../../config/koneksi.php';

        // Check if BukuID is set and not empty
        if (isset($_GET['BukuID']) && !empty($_GET['BukuID'])) {
            $BukuID = $_GET['BukuID'];

            // Fetch data for the selected book
            $query = mysqli_query($koneksi, "SELECT * FROM buku WHERE BukuID = $BukuID");
            $data = mysqli_fetch_assoc($query);

            // Check if the form is submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
                // Retrieve form data
                $kategori = $_POST['KategoriID']; // Ganti dari $_POST['kategori']
                $judul = $_POST['judul'];
                $penulis = $_POST['penulis'];
                $penerbit = $_POST['penerbit'];
                $tahun_terbit = $_POST['tahun_terbit'];

                // Update book information in the database
                $updateQuery = "UPDATE buku SET KategoriID = '$kategori', judul = '$judul', penulis = '$penulis', penerbit = '$penerbit', TahunTerbit = '$tahun_terbit' WHERE BukuID = $BukuID";

                if (mysqli_query($koneksi, $updateQuery)) {
                    // Redirect to the book list page upon successful update
                    header('Location: index.php?pages=buku');
                    exit();
                } else {
                    // Display an error message if the update fails
                    echo '<div class="alert alert-danger" role="alert">Error updating book information.</div>';
                }
            }
        ?>
            <div class="row">
                <div class="col-lg-12 col-md-10 ml-auto mr-auto">
                    <form action="edit_buku.php?BukuID=<?= $BukuID ?>" method="post" id="formEditBuku">
                        <input type="hidden" name="BukuID" value="<?= $data['BukuID'] ?>">
                        <label for="recipient-name" class="col-form-label">kategori</label>
                        <select class="form-select" name="KategoriID" id="KategoriID">
                            <?php
                            include "../../config/koneksi.php";
                            $kategoriQuery = mysqli_query($koneksi, "SELECT * FROM kategoribuku");
                            while ($kategoriData = mysqli_fetch_assoc($kategoriQuery)) {
                            ?>
                                <option value="<?= $kategoriData['KategoriID'] ?>" <?php if ($kategoriData['KategoriID'] == $data['KategoriID']) echo 'selected' ?>>
                                    <?= $kategoriData['NamaKategori'] ?>
                                </option>
                            <?php
                            }
                            ?>
                        </select> <br>
                        <div class="mb-3">
                            <label for="judul" class="col-form-label">Judul Buku :</label>
                            <input type="text" class="form-control" name="judul" id="judul" value="<?= $data['judul'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="penulis" class="col-form-label">Penulis :</label>
                            <input type="text" class="form-control" name="penulis" id="penulis" value="<?= $data['penulis'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="penerbit" class="col-form-label">Penerbit :</label>
                            <input type="text" class="form-control" name="penerbit" id="penerbit" value="<?= $data['penerbit'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_terbit" class="col-form-label">Tahun Terbit :</label>
                            <input type="date" class="form-control" name="tahun_terbit" id="tahun_terbit" value="<?= $data['TahunTerbit'] ?>" required>
                        </div>
                        <div class="modal-footer">
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                            <input type="submit" name="update" class="btn btn-primary" form="formEditBuku" value="Update">
                        </div>
                    </form>
                </div>
            </div>
        <?php
        } else {
            echo '<div class="alert alert-danger" role="alert">Invalid BukuID.</div>';
        }
        ?>
    </div>

    <script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>

</body>

</html>
