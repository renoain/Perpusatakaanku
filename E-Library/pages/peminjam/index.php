<?php 
session_start();
if(empty($_SESSION['user_id'] )) {
    header("Location: ../../login.php");
}
?>
<?php
include("header.php");
?>

<?php 
   include("../../content.php");
?>
<?php 
   include("footer.php");
?>