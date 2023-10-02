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
        $lock = $db->prepare('UPDATE LOTS_LOTS SET inventaireEnCours = 1 WHERE idLot = :idLot;');
        $lock->execute(array(
            'idLot' => $_GET['id']
        ));

        $clean = $db->prepare('DELETE FROM LOTS_INVENTAIRES_TEMP WHERE idLot = :idLot;');
        $clean->execute(array(
            'idLot' => $_GET['id']
        ));

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
                Nouvel inventaire tout confondu par code barre du lot: <?php echo $lot['libelleLot']; ?>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            
            <form role="form" class="spinnerAttenteSubmit" action="lotsInventaireCBNewCheckScan.php?id=<?=$_GET['id']?>&methode=2" method="POST">
                <div class="box box-info box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Scanner successivement les emplacements puis leur contenu</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Emplacement, contenu, Emplacement, contenu, ...</label>
                            <textarea class="form-control" rows="10" name="barcodes" autofocus></textarea>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-body">
                        <a href="lotsInventaireNewAbort.php?id=<?=$_GET['id']?>" class="btn btn-default">Annuler l'inventaire</a>
                        <button type="submit" class="btn btn-info pull-right">Suite</button>
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