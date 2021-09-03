<?php require_once('config/config.php'); ?>
<?php
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';
?>
<?php
if ($_SESSION['codeBarre_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>

<html>
<head>
<style>
p.inline {display: inline-block;}
span { font-size: 13px;}
</style>
<style type="text/css" media="print">
    @page 
    {
        size: auto;   /* auto is the initial value */
        margin: 0mm;  /* this affects the margin in the printer settings */

    }
</style>
</head>
<body onload="window.print();">
    <div style="margin-left: 5%">
        <?php

        $code = $db->prepare('SELECT c.*, m.libelleMateriel FROM CODES_BARRE c LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue WHERE idCode = :idCode');
        $code->execute(array('idCode'=>$_GET['id']));
        $code = $code->fetch();

        include 'plugins/barcode/barcode128.php';

        for($i=1;$i<=$_POST['qtt'];$i++)
        {
            echo "<p class='inline'><span ><b>[".$APPNAME."]</b></span>".bar128(stripcslashes($code['codeBarre']))."<span ><b>".$code['libelleMateriel'];
            if(isset($code['peremptionConsommable'])){echo " (".$code['peremptionConsommable'].")";}
            echo " </b><span></p>&nbsp&nbsp&nbsp&nbsp";
        }

        ?>
    </div>
</body>
</html>