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
    <div style="margin-left: 2%;margin-right: 2%;margin-top: 2%;margin-bottom: 2%">
        <?php include 'plugins/barcode/barcode128.php'; ?>

        <?php
            $lot = $db->prepare('SELECT * FROM LOTS_LOTS WHERE idLot = :idLot;');
            $lot->execute(array('idLot'=>$_GET['id']));
            $lot=$lot->fetch();
        ?>

        <h1><center><?= date('Y-m-d').' '.$APPNAME?></center></h1>
        <h2><center>Codes barres des emplacements du lot "<?=$lot['libelleLot']?>"</center></h2>

        <table border cellspacing="0">
            <thead>
                <tr>
                    <th>Sac</th>
                    <th>Emplacement</th>
                    <th style="width: 250px">Code Barre</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $emplacements = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE s.idLot = :idLot ORDER BY libelleSac, libelleEmplacement');
                    $emplacements->execute(array('idLot'=>$_GET['id']));
                    while($emplacement = $emplacements->fetch())
                    { ?>
                        <tr>
                            <td><?=$emplacement['libelleSac']?></td>
                            <td><?=$emplacement['libelleEmplacement']?></td>
                            <td><?php echo "<center><p class='inline'>".bar128(stripcslashes('GPMEMP'.$emplacement['idEmplacement']))."</p></center>";?></td>
                        </tr>
                    <?php }
                ?>
            </tbody>
        </table>

    

        <p style="page-break-before:always">

        <h1><center><?= date('Y-m-d').' '.$APPNAME?></center></h1>
        <h2><center>Codes barres internes matériels présents dans le lot "<?=$lot['libelleLot']?>"</center></h2>

        <table border cellspacing="0">
            <thead>
                <tr>
                    <th>Element</th>
                    <th style="width: 250px">Code Barre</th>
                    <th>Péremption</th>
                    <th>Commentaires</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $codes = $db->prepare('
                    SELECT
                        c.*,
                        b.*
                    FROM
                        MATERIEL_CATALOGUE c
                        LEFT OUTER JOIN CODES_BARRE b ON c.idMaterielCatalogue = b.idMaterielCatalogue
                        LEFT OUTER JOIN
                            (
                                SELECT DISTINCT
                                    e.idMaterielCatalogue
                                FROM
                                    MATERIEL_ELEMENT e
                                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT em ON e.idEmplacement = em.idEmplacement
                                    LEFT OUTER JOIN MATERIEL_SAC s ON em.idSac = s.idSac
                                WHERE
                                    s.idLot = :idLot
                            ) mat ON c.idMaterielCatalogue = mat.idMaterielCatalogue
                    WHERE
                        b.idCode IS NOT NULL
                        AND
                        b.internalReference = 1
                        AND
                        mat.idMaterielCatalogue IS NOT NULL
                    ORDER BY
                        c.libelleMateriel ASC
                ;');
                $codes->execute(array('idLot'=>$_GET['id']));
                while($code=$codes->fetch())
                { ?>
                    <tr>
                        <td><?=$code['libelleMateriel']?></td>
                        <td><?php echo "<center><p class='inline'>".bar128(stripcslashes($code['codeBarre']))."</p></center>";?></td>
                        <td><?=$code['peremptionConsommable']?></td>
                        <td><?=nl2br($code['commentairesCode'])?></td>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
        
    </div>
</body>
</html>