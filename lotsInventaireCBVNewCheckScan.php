<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lots_modification']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php require_once 'config/bdd.php'; ?>

    <?php
        $lot = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE idLot = :idLot;');
        $lot->execute(array(
            'idLot' => $_GET['id']
        ));
        $lot = $lot->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Validation de l'inventaire du lot: <?php echo $lot['libelleLot']; ?>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <?php
            if(isset($_POST['barcodes']) AND $_POST['barcodes'] != '' AND $_POST['barcodes'] != Null)
            {
                $codes = preg_split('/\r\n|[\r\n]/', $_POST['barcodes']);
                foreach ($codes as $idLigneSaisie => $code){
                    if ($code == '')
                    {
                        unset($codes[$idLigneSaisie]);
                    }
                }

                $idEmplacement = Null;
                foreach ($codes as $idLigneSaisie => $code){
                    if (substr($code, 0, 6) == 'GPMEMP')
                    {
                        $idEmplacement = substr($code, 6);
                    }
                    else
                    {
                        $finalArray[$idEmplacement][$code] += 1;
                    }
                }

                foreach($finalArray as $idEmplacement => $codesArray)
                {
                    foreach ($codesArray as $code => $nb) {
                        for($i=1;$i<=$nb;$i++){
                            $insertTemp = $db->prepare('
                                INSERT INTO
                                    LOTS_INVENTAIRES_TEMP
                                SET
                                    idLot         = Null,
                                    idSac         = Null,
                                    idEmplacement = :idEmplacement,
                                    codeBarre     = :codeBarre
                            ');
                            $insertTemp->execute(array(
                                'idEmplacement' => $idEmplacement,
                                'codeBarre' => $code,
                            ));
                        }
                    }
                }

                $clean = $db->query('
                    DELETE FROM
                        LOTS_INVENTAIRES_TEMP
                    WHERE
                        idEmplacement IS NULL
                ');

                $fillLots = $db->prepare('
                    UPDATE
                        LOTS_INVENTAIRES_TEMP t
                        LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON t.idEmplacement = e.idEmplacement
                        LEFT OUTER JOIN (SELECT * FROM MATERIEL_SAC WHERE idLot = :idLot) s ON e.idSac = s.idSac
                    SET
                        t.idLot = s.idLot,
                        t.idSac = e.idSac
                    WHERE
                        t.idLot IS NULL
                        OR
                        t.idSac IS NULL
                ');
                $fillLots->execute(array('idLot' => $_GET['id']));

                $clean = $db->query('
                    DELETE FROM
                        LOTS_INVENTAIRES_TEMP
                    WHERE
                        idLot IS NULL
                ');
            }

            ?>


            <form role="form" class="spinnerAttenteSubmit" action="lotsInventaireCBNewFinalize.php?id=<?=$_GET['id']?>" method="POST">
                <?php
                    $sacs = $db->prepare('SELECT * FROM MATERIEL_SAC WHERE idLot = :idLot;');
                    $sacs->execute(array('idLot' => $_GET['id']));
                    while ($sac = $sacs->fetch())
                    { ?>
                        <div class="box box-info box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= $sac['libelleSac'] ?></h3>
                                <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-minus"></i></button></div>
                            </div>
                            <div class="box-body">
                                <?php
                                    $emplacements = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac;');
                                    $emplacements->execute(array('idSac' => $sac['idSac']));
                                    while($emplacement = $emplacements->fetch())
                                    {
                                        $scanFailure = $db->prepare('
                                            SELECT
                                                SUM(quantite) as nb
                                            FROM
                                                VIEW_SCAN_RESULTS_LOTS 
                                            WHERE
                                                idEmplacement = :idEmplacement
                                                AND
                                                idMaterielCatalogue IS NULL
                                        ;');
                                        $scanFailure->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
                                        $scanFailure = $scanFailure->fetch();

                                        $manque = $db->prepare('
                                            SELECT
                                                COUNT(*) as nb
                                            FROM
                                                MATERIEL_ELEMENT m
                                                LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                                                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                                                LEFT OUTER JOIN (SELECT * FROM REFERENTIELS WHERE idTypeLot = :idTypeLot)ref ON m.idMaterielCatalogue = ref.idMaterielCatalogue
                                                LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_LOTS WHERE idEmplacement = :idEmplacement) v ON m.idMaterielCatalogue = v.idMaterielCatalogue
                                            WHERE
                                                m.idEmplacement = :idEmplacement
                                                AND
                                                v.idMaterielCatalogue IS NULL
                                            ORDER BY
                                                c.libelleMateriel;
                                        ;');
                                        $manque->execute(array(
                                            'idEmplacement' => $emplacement['idEmplacement'],
                                            'idTypeLot'     => $lot['idTypeLot'],
                                        ));
                                        $manque = $manque->fetch();

                                        $surplus = $db->prepare('
                                            SELECT
                                                COUNT(*) as nb
                                            FROM
                                                VIEW_SCAN_RESULTS_LOTS s
                                                LEFT OUTER JOIN (SELECT * FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement) e ON e.idMaterielCatalogue = s.idMaterielCatalogue
                                            WHERE
                                                s.idEmplacement = :idEmplacement
                                                AND
                                                e.idEmplacement IS NULL
                                                AND
                                                s.idMaterielCatalogue IS NOT NULL
                                        ;');
                                        $surplus->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
                                        $surplus = $surplus->fetch();

                                        $nberreurs = $scanFailure['nb']+$manque['nb']+$surplus['nb'];

                                        if($nberreurs > 0)
                                        { ?>
                                            <div class="box box-danger collapsed-box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><?= $emplacement['libelleEmplacement'] ?> <i class="fa fa-arrow-right"></i> <?= $nberreurs ?> points d'attention</h3>
                                                    <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i></button></div>
                                                </div>
                                                <div class="box-body">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <?php if($scanFailure['nb']>0){?><th>Nombre de codes barre non-reconnus</th><?php } ?>
                                                                <?php if($manque['nb']>0){?><th>Elements manquants (non-scannés)</th><?php } ?>
                                                                <?php if($surplus['nb']>0){?><th>Elements en surplus qu'il faut enlever</th><?php } ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <?php if($scanFailure['nb']>0){?><td><?=$scanFailure['nb']?></td><?php } ?>
                                                                <?php if($manque['nb']>0){?><td>
                                                                    <table class="table table-striped">
                                                                        <thead>
                                                                        <tr>
                                                                            <th style="width: 10px">#</th>
                                                                            <th>Libelle du matériel</th>
                                                                            <th>Stock d'alerte</th>
                                                                            <?php if($lot['idTypeLot'] != Null){echo '<th>Requis par le référentiel</th>' ;} ?>
                                                                            <th>Quantité</th>
                                                                            <th>Péremption</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $materiels = $db->prepare('
                                                                                SELECT
                                                                                    m.*,
                                                                                    e.*,
                                                                                    c.*,
                                                                                    ref.*
                                                                                FROM
                                                                                    MATERIEL_ELEMENT m
                                                                                    LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                                                                                    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                                                                                    LEFT OUTER JOIN (SELECT * FROM REFERENTIELS WHERE idTypeLot = :idTypeLot)ref ON m.idMaterielCatalogue = ref.idMaterielCatalogue
                                                                                    LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_LOTS WHERE idEmplacement = :idEmplacement) v ON m.idMaterielCatalogue = v.idMaterielCatalogue
                                                                                WHERE
                                                                                    m.idEmplacement = :idEmplacement
                                                                                    AND
                                                                                    v.idMaterielCatalogue IS NULL
                                                                                ORDER BY
                                                                                    c.libelleMateriel;');
                                                                            $materiels->execute(array(
                                                                                'idEmplacement' => $emplacement['idEmplacement'],
                                                                                'idTypeLot'     => $lot['idTypeLot'],
                                                                            ));
                                                                            while ($materiel = $materiels->fetch()) { ?>
                                                
                                                                                <tr>
                                                                                    <td><?php echo $materiel['idElement']; ?></td>
                                                                                    <td><?php echo $materiel['libelleMateriel']; ?></td>
                                                                                    <td><?php echo $materiel['quantiteAlerte']; ?></td>
                                                                                    <?php if($lot['idTypeLot'] != Null){echo '<td>'.$materiel['quantiteReferentiel'].'</td>' ;} ?>
                                                                                    <td><input type="text" class="form-control" required value="<?php echo $materiel['quantite']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idSac']; ?>][<?php echo $materiel['idEmplacement']; ?>][<?php echo $materiel['idElement']; ?>][qtt]"></td>
                                                                                    <td><input type="text" class="input-datepicker form-control" value="<?php echo $materiel['peremption']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idSac']; ?>][<?php echo $materiel['idEmplacement']; ?>][<?php echo $materiel['idElement']; ?>][per]" <?php if ($materiel['peremption'] != Null) echo 'required';?>></td>
                                                                                </tr>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                        </tbody>
                                                                    </table>
                                                                </td><?php } ?>
                                                                <?php if($surplus['nb']>0){?><td>
                                                                    <ul>
                                                                        <?php
                                                                            $surplus = $db->prepare('
                                                                                SELECT
                                                                                    s.*
                                                                                FROM
                                                                                    VIEW_SCAN_RESULTS_LOTS s
                                                                                    LEFT OUTER JOIN (SELECT * FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement) e ON e.idMaterielCatalogue = s.idMaterielCatalogue
                                                                                WHERE
                                                                                    s.idEmplacement = :idEmplacement
                                                                                    AND
                                                                                    e.idEmplacement IS NULL
                                                                                    AND
                                                                                    s.idMaterielCatalogue IS NOT NULL
                                                                            ');
                                                                            $surplus->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
                                                                            while($surplu = $surplus->fetch())
                                                                            {
                                                                                echo '<li>'.$surplu['libelleMateriel'].'</li>';
                                                                            }
                                                                        ?>
                                                                    </ul>
                                                                </td><?php } ?>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php }
                                        else
                                        { ?>
                                            <div class="box box-success box-solid">
                                                <div class="box-header">
                                                    <h3 class="box-title"><?= $emplacement['libelleEmplacement'] ?> <i class="fa fa-arrow-right"></i> Inventaire terminé !</h3>
                                                </div>
                                            </div>
                                        <?php }
                                    }
                                ?>
                            </div>
                        </div>
                    <?php }
                ?>

                <div class="box">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3" name="commentairesInventaire"></textarea>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <a href="lotsInventaireNewAbort.php?id=<?=$_GET['id']?>" class="btn btn-default">Annuler l'inventaire</a>
                        <button type="submit" class="btn btn-info pull-right">Valider mon inventaire</button>
                    </div>
                </div>
            </form>


            <div class="row"></div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php include('scripts.php'); ?>
</body>
</html>