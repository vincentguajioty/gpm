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
                Nouvel inventaire par code barre du lot: <?php echo $lot['libelleLot']; ?>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <?php $autofocus = 1; ?>
            
            <form role="form" class="spinnerAttenteSubmit" action="lotsInventaireCBNewCheckScan.php?id=<?=$_GET['id']?>&methode=1" method="POST">
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
                                    { ?>
                                        <div class="box box-warning box-solid">
                                            <div class="box-header with-border">
                                                <h3 class="box-title"><?= $emplacement['libelleEmplacement'] ?></h3>
                                                <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-minus"></i></button></div>
                                            </div>
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        Ce emplacement est sensé contenir:
                                                        <ul>
                                                            <?php
                                                                $materiels = $db->prepare('SELECT * FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue WHERE e.idEmplacement = :idEmplacement ORDER BY libelleMateriel ASC;');
                                                                $materiels->execute(array('idEmplacement' => $emplacement['idEmplacement']));
                                                                while($materiel = $materiels->fetch())
                                                                {
                                                                    $qtt = $materiel['quantiteAlerte']+1;
                                                                    echo "<li>".$materiel['libelleMateriel'].' (minimum '.$qtt.')</li>';
                                                                }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Scannez ce que vous trouvez dans cet emplacement, article après article, les références doivent s'empiler ci-dessous:</label>
                                                            <textarea class="form-control" rows="10" name="formArray[<?php echo $_GET['id']; ?>][<?php echo $sac['idSac']; ?>][<?php echo $emplacement['idEmplacement']; ?>][barcodes]" <?php if($autofocus == 1){echo "autofocus"; $autofocus = 0;} ?> ></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                ?>
                            </div>
                        </div>
                    <?php }
                ?>

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