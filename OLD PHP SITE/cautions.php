<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1103;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['cautions_lecture']==0)
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
                Gestion des cautions pour les tenues
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Cautions</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['cautions_ajout']==1) {?>
	                <div class="box-header">
						<h3 class="box-title"><a href="cautionsForm.php" class="btn btn-sm btn-success modal-form">Nouvelle caution</a></h3>
	                </div>
                <?php }?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Nom Prénom</th>
                                <th class="not-mobile">Emission</th>
                                <th class="not-mobile">Expiration</th>
                                <th class="not-mobile">Montant</th>
                                <th class="not-mobile">Details</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //Ceux qui sont dans la DB en tant que user
                            $query = $db->query('SELECT * FROM CAUTIONS c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne;');
                            while ($data = $query->fetch())
                            {
                               
                            ?>
                                <tr>
                                    <td><?php echo $data['idCaution']; ?></td>
                                    <td><?php echo $data['nomPersonne'].' '.$data['prenomPersonne'].$data['personneNonGPM']; if($data['personneNonGPM'] != ''){echo ' (ext)';}?></td>
                                    <td><?php echo $data['dateEmissionCaution']; ?></td>
                                    <td><?php echo $data['dateExpirationCaution']; ?></td>
                                    <td><?php echo $data['montantCaution']; ?></td>
                                    <td><?php echo $data['detailsMoyenPaiement']; ?></td>
                                    <td>
                                        <?php if ($_SESSION['cautions_modification']==1) {?>
                                            <a href="cautionsForm.php?id=<?=$data['idCaution']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                        <?php }?>
                                        <?php if ($_SESSION['cautions_suppression']==1) {?>
                                            <a href="modalDeleteConfirm.php?case=cautionsDelete&id=<?=$data['idCaution']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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



