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

        include 'plugins/barcode/barcode128.php';

        $codes = $db->prepare('SELECT e.*, s.libelleSac, l.libelleLot FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE s.idLot = :idLot');
        $codes->execute(array('idLot'=>$_GET['id']));
        while($code = $codes->fetch())
        {
            for($i=1;$i<=$_POST['qtt'];$i++)
            {
                echo "<p class='inline'><span ><b>[".$APPNAME."] ".$code['libelleLot']."</b></span>".bar128(stripcslashes('GPMEMP'.$code['idEmplacement']))."<span ><b>".$code['libelleSac'].' > '.$code['libelleEmplacement'];
                echo " </b><span></p>&nbsp&nbsp&nbsp&nbsp";
            }
        }

        ?>
    </div>
</body>
</html>