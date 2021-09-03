<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 701;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['reserve_modification']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php require_once 'config/bdd.php'; ?>

    <?php
        $query = $db->prepare('SELECT * FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur;');
        $query->execute(array(
            'idConteneur' => $_GET['id']
        ));
        $data = $query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Validation de l'inventaire du conteneur: <?php echo $data['libelleConteneur']; ?>
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

                foreach ($codes as $idLigneSaisie => $code){
                    $insertTemp = $db->prepare('
                        INSERT INTO
                            RESERVES_INVENTAIRES_TEMP
                        SET
                            idConteneur = :idConteneur,
                            codeBarre   = :codeBarre
                    ');
                    $insertTemp->execute(array(
                        'idConteneur' => $_GET['id'],
                        'codeBarre' => $code,
                    ));
                }
            }
            ?>


            <form role="form" class="spinnerAttenteSubmit" action="reserveInventaireCBNewFinalize.php?id=<?=$_GET['id']?>" method="POST">
                <?php
                    $scanFailure = $db->prepare('
                        SELECT
                            SUM(quantite) as nb
                        FROM
                            VIEW_SCAN_RESULTS_RESERVES 
                        WHERE
                            idConteneur = :idConteneur
                            AND
                            idMaterielCatalogue IS NULL
                    ;');
                    $scanFailure->execute(array('idConteneur'=>$data['idConteneur']));
                    $scanFailure = $scanFailure->fetch();

                    $scanAbsent = $db->prepare('
                        SELECT
                            COUNT(*) as nb
                        FROM
                            RESERVES_MATERIEL m
                            LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_RESERVES WHERE idConteneur = :idConteneur) v ON m.idMaterielCatalogue = v.idMaterielCatalogue
                        WHERE
                            m.idConteneur = :idConteneur
                            AND
                            v.idMaterielCatalogue IS NULL
                    ;');
                    $scanAbsent->execute(array(
                        'idConteneur' => $data['idConteneur'],
                        'idTypeLot'     => $lot['idTypeLot'],
                    ));
                    $scanAbsent = $scanAbsent->fetch();

                    $scansPerimes = $db->prepare('
                        SELECT
                            COUNT(*) as nb
                        FROM
                            VIEW_SCAN_RESULTS_RESERVES 
                        WHERE
                            peremption <= CURRENT_TIMESTAMP
                            AND
                            idConteneur = :idConteneur
                    ;');
                    $scansPerimes->execute(array(
                        'idConteneur' => $data['idConteneur'],
                    ));
                    $scansPerimes = $scansPerimes->fetch();

                    $scanTrop = $db->prepare('
                        SELECT
                            COUNT(*) as nb
                        FROM
                            VIEW_SCAN_RESULTS_RESERVES s
                            LEFT OUTER JOIN (SELECT * FROM RESERVES_MATERIEL WHERE idConteneur = :idConteneur) e ON e.idMaterielCatalogue = s.idMaterielCatalogue
                        WHERE
                            s.idConteneur = :idConteneur
                            AND
                            e.idConteneur IS NULL
                            AND
                            s.idMaterielCatalogue IS NOT NULL
                    ;');
                    $scanTrop->execute(array('idConteneur'=>$data['idConteneur']));
                    $scanTrop = $scanTrop->fetch();

                    $nberreurs = $scanFailure['nb']+$scanAbsent['nb']+$scanTrop['nb']+$scansPerimes['nb'];


                    if($nberreurs > 0)
                    { ?>
                        <div class="box box-warning collapsed-box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= $data['libelleConteneur'] ?> <i class="fa fa-arrow-right"></i> <?= $nberreurs ?> points d'attention</h3>
                                <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i></button></div>
                            </div>
                            <div class="box-body">
                    <?php } else { ?>
                        <div class="box box-success collapsed-box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= $data['libelleConteneur'] ?> <i class="fa fa-arrow-right"></i> OK !</h3>
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
                                        echo '<i class="icon fa fa-warning"></i> Les éléments suivants ont été scannés dans ce conteneur et doivent être retirés:';
                                        echo '<ul>';
                                        $elements = $db->prepare('
                                            SELECT
                                                s.*
                                            FROM
                                                VIEW_SCAN_RESULTS_RESERVES s
                                                LEFT OUTER JOIN (SELECT * FROM RESERVES_MATERIEL WHERE idConteneur = :idConteneur) e ON e.idMaterielCatalogue = s.idMaterielCatalogue
                                            WHERE
                                                s.idConteneur = :idConteneur
                                                AND
                                                e.idConteneur IS NULL
                                                AND
                                                s.idMaterielCatalogue IS NOT NULL
                                        ');
                                        $elements->execute(array('idConteneur'=>$data['idConteneur']));
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
                                                    v.peremption as peremptionScan,
                                                    v.quantite as quantiteScan
                                                FROM
                                                    RESERVES_MATERIEL e
                                                    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue
                                                    LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_RESERVES WHERE idConteneur = :idConteneur) v ON e.idMaterielCatalogue = v.idMaterielCatalogue
                                                WHERE
                                                    e.idConteneur = :idConteneur
                                            ;');
                                            $materiels->execute(array('idConteneur'=>$data['idConteneur']));
                                            while($materiel = $materiels->fetch())
                                            {
                                                if(is_null($materiel['quantiteScan']))
                                                    {$colorQTT='has-error';}else{$colorQTT='has-success';}
                                                if(($materiel['peremptionScan'] != Null AND $materiel['peremptionScan'] <= date('Y-m-d')) OR ($materiel['peremptionReserve'] != Null AND $materiel['peremptionScan'] <= date('Y-m-d')))
                                                    {$colorDATE='has-error';}else{$colorDATE='has-success';}
                                                if($colorQTT=='has-success' AND $colorDATE=='has-success')
                                                    {$colorLINE='bg-success';}else{$colorLINE='bg-danger';}
                                                ?>
                                                <tr class="<?=$colorLINE?>">
                                                    <td><?= $materiel['idReserveElement'] ?></td>
                                                    <td><?= $materiel['libelleMateriel'] ?></td>
                                                    <td>
                                                        <div class="form-group <?= $colorQTT ?>">
                                                            <input type="text" class="form-control" required value="<?php echo $materiel['quantiteScan']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idReserveElement']; ?>][qtt]">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group <?= $colorDATE ?>">
                                                            <input type="text" class="input-datepicker form-control" value="<?php echo $materiel['peremptionScan']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idReserveElement']; ?>][per]" <?php if ($materiel['peremptionReserve'] != Null) echo 'required';?>>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>


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
                        <a href="reserveInventaireNewAbort.php?id=<?=$_GET['id']?>" class="btn btn-default">Annuler l'inventaire</a>
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