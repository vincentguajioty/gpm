<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 701;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['reserve_lecture']==0)
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
                Gestion des conteneurs de la reserve
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Reserve</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box">
                <div class="box-header">
	                <?php if ($_SESSION['reserve_ajout']==1) {?>
	                    <h3 class="box-title"><a href="reserveConteneurForm.php" class="btn btn-sm btn-success modal-form">Ajouter un conteneur</a></h3>
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
                                <th class="not-mobile">Lieu</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM RESERVES_CONTENEUR c LEFT OUTER JOIN LIEUX l  ON c.idLieu = l.idLieu ORDER BY libelleConteneur;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idConteneur']; ?></td>
								<td><?php echo $data['libelleConteneur']; ?></td>
								<td><?php echo $data['libelleLieu']; ?></td>
                                <td>
                                    <?php if ($_SESSION['reserve_lecture']==1) {?>
                                        <a href="reserveConteneurContenu.php?id=<?=$data['idConteneur']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['reserve_modification']==1) {?>
                                        <a href="reserveConteneurForm.php?id=<?=$data['idConteneur']?>" class="btn btn-xs btn-warning modal-form"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['reserve_suppression']==1) {?>
                                        <a href="reserveConteneurDelete.php?id=<?=$data['idConteneur']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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