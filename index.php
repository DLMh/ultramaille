<?php
  session_start();

 if (isset($_SESSION["vmatr"])){
   header("location: front/general/menugeneral.php");
 }else {
   header("location: front/general/Login/Login.php");
 }
?>
