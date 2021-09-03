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
            if($_GET['methode']==1)
            {
                if(isset($_POST['formArray']))
                {
                    foreach ($_POST['formArray'] as $idLot => $sacs) {
                        foreach ($sacs as $idSac => $emplacements) {
                            foreach ($emplacements as $idEmplacement => $materiels) {
                                foreach ($materiels as $idElement => $matos){
                                    $codes = preg_split('/\r\n|[\r\n]/', $matos);
                                    foreach ($codes as $idLigneSaisie => $code){
                                        if ($code == '')
                                        {
                                            unset($codes[$idLigneSaisie]);
                                        }
                                    }

                                    foreach ($codes as $idLigneSaisie => $code){
                                        $insertTemp = $db->prepare('
                                            INSERT INTO
                                                LOTS_INVENTAIRES_TEMP
                                            SET
                                                idLot         = :idLot,
                                                idSac         = :idSac,
                                                idEmplacement = :idEmplacement,
                                                codeBarre     = :codeBarre
                                        ');
                                        $insertTemp->execute(array(
                                            'idLot' => $idLot,
                                            'idSac' => $idSac,
                                            'idEmplacement' => $idEmplacement,
                                            'codeBarre' => $code,
                                        ));
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if($_GET['methode']==2)
            {
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
                                        idLot         = :idLot,
                                        idSac         = Null,
                                        idEmplacement = :idEmplacement,
                                        codeBarre     = :codeBarre
                                ');
                                $insertTemp->execute(array(
                                    'idEmplacement' => $idEmplacement,
                                    'codeBarre' => $code,
                                    'idLot' => $_GET['id'],
                                ));
                                $resultSQL = $query->errorCode();
                            }
                        }
                    }

                    $clean = $db->prepare('
                        DELETE FROM
                            LOTS_INVENTAIRES_TEMP
                        WHERE
                            idEmplacement IS NULL
                            AND
                            idLot = :idLot
                    ');
                    $clean->execute(array(
                        'idLot' => $_GET['id'],
                    ));

                    $fillLots = $db->prepare('
                        UPDATE
                            LOTS_INVENTAIRES_TEMP t
                            LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON t.idEmplacement = e.idEmplacement
                            LEFT OUTER JOIN (SELECT * FROM MATERIEL_SAC WHERE idLot = :idLot) s ON e.idSac = s.idSac
                        SET
                            t.idLot = s.idLot,
                            t.idSac = e.idSac
                        WHERE
                            t.idLot = :idLot
                    ');
                    $fillLots->execute(array('idLot' => $_GET['id']));

                    
                    $clean = $db->query('
                        DELETE FROM
                            LOTS_INVENTAIRES_TEMP
                        WHERE
                            idLot IS NULL
                    ');

                }
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

                                        $scanAbsent = $db->prepare('
                                            SELECT
                                                COUNT(*) as nb
                                            FROM
                                                MATERIEL_ELEMENT m
                                                LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_LOTS WHERE idEmplacement = :idEmplacement) v ON m.idMaterielCatalogue = v.idMaterielCatalogue
                                            WHERE
                                                m.idEmplacement = :idEmplacement
                                                AND
                                                v.idMaterielCatalogue IS NULL
                                        ;');
                                        $scanAbsent->execute(array(
                                            'idEmplacement' => $emplacement['idEmplacement'],
                                            'idTypeLot'     => $lot['idTypeLot'],
                                        ));
                                        $scanAbsent = $scanAbsent->fetch();

                                        $scanInsuff = $db->prepare('
                                            SELECT
                                                COUNT(*) as nb
                                            FROM
                                                VIEW_SCAN_RESULTS_LOTS v
                                                LEFT OUTER JOIN (SELECT * FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement) e ON v.idMaterielCatalogue = e.idMaterielCatalogue
                                            WHERE
                                                v.quantite <= e.quantiteAlerte
                                                AND
                                                v.idEmplacement = :idEmplacement
                                        ;');
                                        $scanInsuff->execute(array(
                                            'idEmplacement' => $emplacement['idEmplacement'],
                                        ));
                                        $scanInsuff = $scanInsuff->fetch();

                                        $scansPerimes = $db->prepare('
                                            SELECT
                                                COUNT(*) as nb
                                            FROM
                                                VIEW_SCAN_RESULTS_LOTS 
                                            WHERE
                                                peremption <= CURRENT_TIMESTAMP
                                                AND
                                                v.idEmplacement = :idEmplacement
                                        ;');
                                        $scansPerimes->execute(array(
                                            'idEmplacement' => $emplacement['idEmplacement'],
                                        ));
                                        $scansPerimes = $scansPerimes->fetch();

                                        $scanTrop = $db->prepare('
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
                                        $scanTrop->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
                                        $scanTrop = $scanTrop->fetch();

                                        $nberreurs = $scanFailure['nb']+$scanAbsent['nb']+$scanInsuff['nb']+$scanTrop['nb']+$scansPerimes['nb'];

                                        if($nberreurs > 0)
                                        { ?>
                                            <div class="box box-warning collapsed-box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><?= $emplacement['libelleEmplacement'] ?> <i class="fa fa-arrow-right"></i> <?= $nberreurs ?> points d'attention</h3>
                                                    <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i></button></div>
                                                </div>
                                                <div class="box-body">
                                        <?php } else { ?>
                                            <div class="box box-success collapsed-box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title"><?= $emplacement['libelleEmplacement'] ?> <i class="fa fa-arrow-right"></i> OK !</h3>
                                                    <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i></button></div>
                                                </div>
                                                <div class="box-body">
                                        <?php } ?>
                                                    <?php
                                                        if($scanFailure['nb'] > 0)
                                                        {
                                                            echo '<div class="alert alert-warning">';
                                                            echo '<i class="icon fa fa-warning"></i> '.$scanFailure['nb'].' codes ont été scannés mais pas reconnus. Veuillez vérifier l\'inventaire ci-dessous.';
                                                            echo '</div>';
                                                        }
                                                        if($scanTrop['nb'] > 0)
                                                        {
                                                            echo '<div class="alert alert-warning">';
                                                            echo '<i class="icon fa fa-warning"></i> Les éléments suivants ont été scannés dans cet emplacement et doivent être retirés:';
                                                            echo '<ul>';
                                                            $elements = $db->prepare('
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
                                                            $elements->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
                                                            while($element = $elements->fetch())
                                                            {
                                                                echo '<li>'.$element['libelleMateriel'].'</li>';
                                                            }
                                                            echo '</ul>';
                                                            echo '</div>';
                                                        }
                                                    ?>

                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 10px">#</th>
                                                            <th>Libelle du matériel</th>
                                                            <th>Stock d'alerte</th>
                                                            <th>Quantité</th>
                                                            <th>Péremption</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $materiels = $db->prepare('
                                                                    SELECT
                                                                        e.*,
                                                                        c.libelleMateriel,
                                                                        em.idSac,
                                                                        v.peremption as peremptionScan,
                                                                        v.quantite as quantiteScan
                                                                    FROM
                                                                        MATERIEL_ELEMENT e
                                                                        LEFT OUTER JOIN MATERIEL_EMPLACEMENT em ON e.idEmplacement = em.idEmplacement
                                                                        LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue
                                                                        LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_LOTS WHERE idEmplacement = :idEmplacement) v ON e.idMaterielCatalogue = v.idMaterielCatalogue
                                                                    WHERE
                                                                        e.idEmplacement = :idEmplacement
                                                                    ORDER BY
                                                                        c.libelleMateriel
                                                                ;');
                                                                $materiels->execute(array('idEmplacement'=>$emplacement['idEmplacement']));
                                                                while($materiel = $materiels->fetch())
                                                                {
                                                                    if($materiel['quantiteScan']<=$materiel['quantiteAlerte'])
                                                                        {$colorQTT='has-error';}else{$colorQTT='has-success';}
                                                                    if(($materiel['peremptionScan'] != Null AND $materiel['peremptionScan'] <= date('Y-m-d')) OR ($materiel['peremption'] != Null AND $materiel['peremptionScan'] <= date('Y-m-d')))
                                                                        {$colorDATE='has-error';}else{$colorDATE='has-success';}
                                                                    if($colorQTT=='has-success' AND $colorDATE=='has-success')
                                                                        {$colorLINE='bg-success';}else{$colorLINE='bg-danger';}
                                                                    ?>
                                                                    <tr class="<?=$colorLINE?>">
                                                                        <td><?= $materiel['idElement'] ?></td>
                                                                        <td><?= $materiel['libelleMateriel'] ?></td>
                                                                        <td><?= $materiel['quantiteAlerte'] ?></td>
                                                                        <td>
                                                                            <div class="form-group <?= $colorQTT ?>">
                                                                                <input type="text" class="form-control" required value="<?php echo $materiel['quantiteScan']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idSac']; ?>][<?php echo $materiel['idEmplacement']; ?>][<?php echo $materiel['idElement']; ?>][qtt]">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group <?= $colorDATE ?>">
                                                                                <input type="text" class="input-datepicker form-control" value="<?php echo $materiel['peremptionScan']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idSac']; ?>][<?php echo $materiel['idEmplacement']; ?>][<?php echo $materiel['idElement']; ?>][per]" <?php if ($materiel['peremption'] != Null) echo 'required';?>>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                    <?php } ?>                                        
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