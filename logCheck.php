<?php
session_start();
if ($_SESSION['connexion_connexion'] == 0)
        echo "<script type='text/javascript'>document.location.replace('logout.php');</script>";
?>