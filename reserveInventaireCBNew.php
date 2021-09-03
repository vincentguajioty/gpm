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
        $lock = $db->prepare('UPDATE RESERVES_CONTENEUR SET inventaireEnCours = 1 WHERE idConteneur = :idConteneur;');
        $lock->execute(array(
            'idConteneur' => $_GET['id']
        ));

        $clean = $db->prepare('DELETE FROM RESERVES_INVENTAIRES_TEMP WHERE idConteneur = :idConteneur;');
        $clean->execute(array(
            'idConteneur' => $_GET['id']
        ));

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
                Nouvel inventaire du conteneur: <?php echo $data['libelleConteneur']; ?>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            
            <form role="form" class="spinnerAttenteSubmit" action="reserveInventaireCBNewCheckScan.php?id=<?=$_GET['id']?>" method="POST">
                <div class="box box-info box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= $data['libelleConteneur'] ?></h3>
                        <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-minus"></i></button></div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                Ce conteneur est sensé contenir:
                                <ul>
                                    <?php
                                        $materiels = $db->prepare('SELECT * FROM RESERVES_MATERIEL e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue WHERE e.idConteneur = :idConteneur ORDER BY libelleMateriel ASC;');
                                        $materiels->execute(array('idConteneur' => $data['idConteneur']));
                                        while($materiel = $materiels->fetch())
                                        {
                                            $qtt = $materiel['quantiteAlerteReserve']+1;
                                            echo "<li>".$materiel['libelleMateriel'].' (minimum '.$qtt.')</li>';
                                        }
                                    ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Scannez ce que vous trouvez dans cet emplacement, article après article, les références doivent s'empiler ci-dessous:</label>
                                    <textarea class="form-control" rows="10" name="barcodes"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <a href="reserveInventaireNewAbort.php?id=<?=$_GET['id']?>" class="btn btn-default">Annuler l'inventaire</a>
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