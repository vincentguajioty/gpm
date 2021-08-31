<?php
require_once '../config/bdd.php';


$query = $db->query(file_get_contents ("update1.sql"));
echo "<script type='text/javascript'>document.location.replace('../login.php');</script>";


?>