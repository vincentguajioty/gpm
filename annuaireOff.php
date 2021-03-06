<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 401;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['annuaire_lecture']==0)
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
                Gestion des utilisateurs sans droit de connexion
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Annuaire</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box">
	            <div class="box-header">
	            	<h3 class="box-title pull-right"><a href="annuaire.php" class="btn btn-sm btn-default">Retour</a></h3>
	            </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Identifiant de connexion</th>
                                <th class="not-mobile">Nom</th>
                                <th class="not-mobile">Prénom</th>
                                <th class="not-mobile">CNIL</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM VIEW_HABILITATIONS WHERE connexion_connexion Is Null OR connexion_connexion = 0 ORDER BY identifiant;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idPersonne']; ?></td>
                                <td><?php echo $data['identifiant']; ?></td>
                                <td><?php echo $data['nomPersonne']; ?></td>
                                <td><?php echo $data['prenomPersonne']; ?></td>
                                <td>
                                	<?php if($data['cnil_anonyme']){echo '<span class="badge bg-green">Anonymisation 3 ans</span>';}else{echo '<span class="badge bg-orange">Anonymisation 3 ans</span>';} ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['annuaire_lecture']==1) {?>
                                        <a href="annuaireContenu.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-info" title="Ouvrir la fiche"><i class="fa fa-folder"></i></a>
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
