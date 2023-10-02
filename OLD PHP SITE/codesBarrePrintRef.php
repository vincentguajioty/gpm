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

        <h1><center><?= date('Y-m-d').' '.$APPNAME?></center></h1>
        <h2><center>Référentiel des codes barre matériels par catégories</center></h2>

        <table border cellspacing="0">
            <thead>
                <tr>
                    <th>Element</th>
                    <th>Base</th>
                    <th style="width: 250px">Code Barre</th>
                    <th>Péremption</th>
                    <th>Commentaires</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $categories = $db->query('
                        SELECT
                            temp.*
                        FROM
                            (SELECT
                                cat.*,
                                COUNT(idCode) as nbCode
                            FROM
                                MATERIEL_CATEGORIES cat
                                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON cat.idCategorie = c.idCategorie
                                LEFT OUTER JOIN CODES_BARRE b ON c.idMaterielCatalogue = b.idMaterielCatalogue
                            GROUP BY
                                cat.idCategorie
                            ORDER BY
                                libelleCategorie ASC) temp
                        WHERE
                            temp.nbCode > 0
                    ;');
                    while($categorie = $categories->fetch())
                    {
                        echo '<tr><td colspan="5"><center>'.$categorie['libelleCategorie'].'</center></td></tr>';
                        ?>
                        
                                <?php
                                $codes = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN CODES_BARRE b ON c.idMaterielCatalogue = b.idMaterielCatalogue WHERE c.idCategorie = :idCategorie AND b.idCode IS NOT NULL ORDER BY libelleMateriel ASC');
                                $codes->execute(array('idCategorie'=>$categorie['idCategorie']));
                                while($code=$codes->fetch())
                                { ?>
                                    <tr>
                                        <td><?=$code['libelleMateriel']?></td>
                                        <td><?php if($code['internalReference']){echo 'Interne';}else{echo 'Fournisseur';} ?></td>
                                        <td><?php echo "<center><p class='inline'>".bar128(stripcslashes($code['codeBarre']))."</p></center>";?></td>
                                        <td><?=$code['peremptionConsommable']?></td>
                                        <td><?=nl2br($code['commentairesCode'])?></td>
                                    </tr>
                                <?php }
                                ?>
                            
                        <?php
                    }
                ?>
            </tbody>
        </table>

        <p style="page-break-before:always">

        <h1><center><?=$APPNAME?></center></h1>
        <h2><center>Référentiel des codes barre matériels par odre alphabétique</center></h2>

        <table border cellspacing="0">
            <thead>
                <tr>
                    <th>Element</th>
                    <th>Base</th>
                    <th style="width: 250px">Code Barre</th>
                    <th>Péremption</th>
                    <th>Commentaires</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $codes = $db->query('SELECT * FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN CODES_BARRE b ON c.idMaterielCatalogue = b.idMaterielCatalogue WHERE b.idCode IS NOT NULL ORDER BY libelleMateriel ASC');
                while($code=$codes->fetch())
                { ?>
                    <tr>
                        <td><?=$code['libelleMateriel']?></td>
                        <td><?php if($code['internalReference']){echo 'Interne';}else{echo 'Fournisseur';} ?></td>
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