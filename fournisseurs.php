<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 305;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['fournisseurs_lecture']==0)
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
                Fournisseurs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Fournisseurs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
            	<?php if ($_SESSION['fournisseurs_ajout']==1) {?>
            		<div class="box-header">
                        <h3 class="box-title"><a href="fournisseursForm.php" class="btn btn-sm btn-success modal-form">Ajouter un fournisseur</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Nom</th>
                                <th>Site Web</th>
                                <th>Téléphone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idFournisseur']; ?></td>
                                <td><?php echo $data['nomFournisseur']; ?></td>
                                <td><?php echo $data['siteWebFournisseur']; ?></td>
                                <td><?php echo $data['telephoneFournisseur']; ?></td>
                                <td>
                                    <?php
                                        if($data['siteWebFournisseur'] != Null AND $data['siteWebFournisseur'] != ''){?>
                                            <a href="<?=$data['siteWebFournisseur']?>" class="btn btn-xs btn-info" title="Aller sur le site du fournisseur" target="_blank"><i class="fa fa-internet-explorer"></i></a>
                                    <?php } ?>
                                    <?php if ($_SESSION['lieux_modification']==1) {?>
                                        <a href="fournisseursForm.php?id=<?=$data['idFournisseur']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['lieux_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=fournisseursDelete&id=<?=$data['idFournisseur']?>" class="btn btn-xs btn-danger modal-form" title="Suppimer"><i class="fa fa-trash"></i></a>
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
