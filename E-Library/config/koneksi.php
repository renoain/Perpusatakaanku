<?php
   error_reporting(0);
   session_start();
    $host = "localhost";
    $user = "root";
    $pass = "";
    $database = "e-library_db";

    $koneksi = mysqli_connect($host, $user, $pass, $database);
?>