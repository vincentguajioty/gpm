<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 102;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['sac_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gestion des sacs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Sacs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <div class="box-header">
                    <?php if ($_SESSION['sac_ajout']==1) {?>
                        <h3 class="box-title"><a href="sacsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un sac</a></h3>
                    <?php } else {?>
                        </br>
                    <?php } ?>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Lot</th>
                                <th class="not-mobile">Quantité d'emplacements</th>
                                <th class="not-mobile">Quantité de matériel</th>
                                <th class="not-mobile not-tablet">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr <?php if ($_SESSION['sac_lecture']==1) {?>data-href="sacsContenu.php?id=<?=$data['idSac']?>"<?php }?>>
                                <td><?php echo $data['idSac']; ?></td>
                                <td><?php echo $data['libelleSac']; ?></td>
                                <td><?php echo $data['libelleLot']; ?></td>

                                <?php
                                    $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_EMPLACEMENT WHERE idSac=:idSac');
                                    $query2->execute(array('idSac' => $data['idSac']));
                                    $data2 = $query2->fetch();
                                ?>

                                <td><?php echo $data2['nb']; ?></td>

                                <?php
                                    $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement WHERE idSac = :idSac;');
                                    $query2->execute(array('idSac' => $data['idSac']));
                                    $data2 = $query2->fetch();
                                ?>

                                <td><?php echo $data2['nb']; ?></td>

                                <td>
                                    <?php if ($_SESSION['sac_lecture']==1) {?>
                                        <a href="sacsContenu.php?id=<?=$data['idSac']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['sac_modification']==1) {?>
                                        <a href="sacsForm.php?id=<?=$data['idSac']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['sac_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=sacsDelete&id=<?=$data['idSac']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                    <?php }?>
                                </td>
                            </tr>
                            <?php
                        }
                        $query->closeCursor(); ?>
                        </tbody>


                    </table>
                </div>
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


