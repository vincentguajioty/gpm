<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['materiel_ajout']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot WHERE idLot = :idLot;');
    $query->execute(array(
        'idLot' => $_GET['id']
    ));
    $data = $query->fetch();

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Import du référentiel <?= $data['libelleTypeLot'] ?> dans le lot <?= $data['libelleLot'] ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="lots.php">Lots</a></li>
                <li class="active"><?php echo $data['libelleLot']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">
                <form role="form" class="spinnerAttenteSubmit" action="lotsImportRefAdd.php?id=<?=$_GET['id']?>" method="POST">
                    <div class="col-md-12">
                       <div class="box">
                            <div class="box-body">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Libelle du matériel</th>
                                        <th>Quantité</th>
                                        <th>Quantité d'alerte</th>
                                        <th>Emplacement</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $query2 = $db->prepare('SELECT r.*, c.libelleMateriel FROM REFERENTIELS r LEFT OUTER JOIN MATERIEL_CATALOGUE c ON r.idMaterielCatalogue = c.idMaterielCatalogue WHERE idTypeLot = :idTypeLot;');
                                    $query2->execute(array(
                                        'idTypeLot' => $data['idTypeLot']
                                    ));
                                    while ($data2 = $query2->fetch()) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $data2['idMaterielCatalogue']; ?>
                                            </td>
                                            <td>
                                                <?php echo $data2['libelleMateriel']; ?>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" required value="<?php echo $data2['quantiteReferentiel']; ?>"name="formArray[<?php echo $data2['idMaterielCatalogue']; ?>][qtt]">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" required value="<?php echo $data2['quantiteReferentiel']-1; ?>"name="formArray[<?php echo $data2['idMaterielCatalogue']; ?>][qttAlerte]">
                                            </td>
                                            <td>
                                                <select class="form-control select2" style="width: 100%;" name="formArray[<?php echo $data2['idMaterielCatalogue']; ?>][idEmplacement]">
                                                    <?php
                                                    $query3 = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE idLot = :idLot ORDER BY libelleSac, libelleEmplacement;');
                                                    $query3->execute(array('idLot'=>$_GET['id']));
                                                    while ($data3 = $query3->fetch())
                                                    {
                                                        ?>
                                                        <option value="<?php echo $data3['idEmplacement']; ?>" ><?php echo $data3['libelleSac']; ?> > <?php echo $data3['libelleEmplacement']; ?></option>
                                                        <?php
                                                    }
                                                    $query3->closeCursor(); ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-body">
                                <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                <button type="submit" class="btn btn-info pull-right">Lancer l'import</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include('footer.php'); ?>


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php include('scripts.php'); ?>
</body>
</html>



