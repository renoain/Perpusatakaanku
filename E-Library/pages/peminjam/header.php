<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
   <link href ="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  </head>
  <body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="?pages=home">Perpustakaan</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="?pages=home">
          Home</a>
        </li>
        <li>
        <a href="?pages=buku" class="nav-link" >Buku</a>

      </li>

  
      <li>
        <a href="?pages=ulasan" class="nav-link" >Ulasan </a>

      </li>
      
        <li class="nav-item">
          <a href="?pages=peminjam" class="nav-link " aria-disabled="true">Peminjaman</a>
          </li>
        <li class="nav-item">
          <a href="?pages=koleksi_pribadi" class="nav-link " aria-disabled="true">koleksi</a>
        </li>
        
       
      </ul>
     </ul>
    <ul class="navbar-nav me-0 mb-lg-0">
      <li>
       <ul class="navbar-nav me-0 mb-lg-0">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Profil
              </a>
              <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                  <div class="mx-3 mt-2">
                <h5>Profil</h5>
                <table>
                  <tr>
                    <td>username </td>
                    <td> </td>
                    <td>:</td>
                    <td><b><?php echo $_SESSION['username']; ?></b></td>
                  </tr>
                  <tr>
                    <td>email </td>
                    <td> </td>
                    <td>:</td>
                    <td><b><?php echo $_SESSION['email']; ?></b></td>
                  </tr>
                  <tr>
                    <td>level </td>
                    <td> </td>
                    <td>:</td>
                    <td><b><?php echo $_SESSION['level']; ?></b></td>
                  </tr>
                </table>
                </div><br>
                <li>
                  <div class="d-grid gap-2">
                  <button type="button" class="btn btn-outline-primary mx-3" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
                  </div>
                </li>
              </ul>
              <!-- MODAL LOGOUT -->
             <!-- MODAL LOGOUT -->
<div class="modal" tabindex="-1" id="logoutModal" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="logoutForm" action="../../logout.php" method="POST">
                    <p>Anda yakin ingin keluar dari akun?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">BATAL</button>
                <input type="submit" class="btn btn-danger" name="hapus" value="KELUAR">
            </div>
                </form>
        </div>
    </div>
</div>

</form>

      </li>
    </ul>
    </div>
  </div>
</nav>
    <!-- akhir navbar -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
	<script type="text/javascript">
		$(document).ready( function () {
			$('#myTable').DataTable();
		} );
	</script>
</html>