<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 402;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['profils_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gestion des profils des utilisateurs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Profils</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
            	<div class="box-header">
                    <?php if ($_SESSION['profils_ajout']==1) {?><h3 class="box-title"><a href="profilsForm.php" class="btn btn-sm btn-success">Ajouter un profil</a></h3> <h3 class="box-title"><a href="profilsFormDupliquer.php" class="btn btn-sm btn-success modal-form">Dupliquer un profil existant</a></h3><?php } ?>
                    <h3 class="box-title pull-right"><a href="profilsRecap.php" class="btn btn-sm btn-default">Tableau des habilitations</a></h3>
            	</div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Libelle</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM PROFILS ORDER BY libelleProfil;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idProfil']; ?></td>
                                <td><?php echo $data['libelleProfil']; ?></td>
                                <td><?php echo $data['descriptifProfil']; ?></td>
                                <td>
                                    <?php if ($_SESSION['profils_modification']==1) {?>
                                        <a href="profilsForm.php?id=<?=$data['idProfil']?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_modification']==1) {?>
                                        <a href="profilsSetDashboard.php?id=<?=$data['idProfil']?>" class="btn btn-xs btn-success" onclick="return confirm('Etes-vous sûr de vouloir réactiver les indicateurs sur la page d\'accueil de tous les utilisateurs liés à ce profil ?');" title="Forcer les indicateurs sur la page d'accueil"><i class="fa fa-dashboard"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_modification']==1) {?>
                                        <a href="profilsSetMail.php?id=<?=$data['idProfil']?>" class="btn btn-xs btn-success" onclick="return confirm('Etes-vous sûr de vouloir réactiver les notifications pour tous les utilisateurs liés à ce profil ?');" title="Forcer l'activation des notifications"><i class="fa fa-envelope"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['profils_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=profilsDelete&id=<?=$data['idProfil']?>" class="btn btn-xs btn-danger modal-form"><i class="fa fa-trash"></i></a>
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
