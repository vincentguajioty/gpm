<?php
	
	$source = '../config/bdd.exemple.php';
	$target = '../config/bdd.php';

	$str = file_get_contents($source);

	$str = str_replace("val1" , $_POST['ip']       , $str);
	$str = str_replace("val2" , $_POST['dbname']   , $str);
	$str = str_replace("val3" , $_POST['user']     , $str);
	$str = str_replace("val4" , $_POST['password'] , $str);
	
	file_put_contents($target, $str);

	echo "<script type='text/javascript'>document.location.replace('../login.php');</script>";

?>