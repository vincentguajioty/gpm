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
                Gestion des utilisateurs (avec droit de connexion)
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
                	<?php if ($_SESSION['annuaire_ajout']==1) {?>
	                    <h3 class="box-title"><a href="annuaireForm.php" class="btn btn-sm btn-success modal-form">Ajouter un utilisateur</a></h3>
	                    <h3 class="box-title"><a href="annuaireImport.php" class="btn btn-sm btn-success">Import d'utilisateurs</a></h3>
	            	<?php } ?>
	            	<h3 class="box-title pull-right"><a href="annuaireOff.php" class="btn btn-sm btn-default">Voir les utilisateurs sans droits de connexion</a></h3>
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
                                <th class="not-mobile">Mail</th>
                                <th class="not-mobile">Fonction</th>
                                <th class="not-mobile">Profil</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM VIEW_HABILITATIONS WHERE connexion_connexion = 1 ORDER BY identifiant;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?= $data['idPersonne'] ?></td>
                                <td><?= $data['identifiant'] ?></td>
                                <td><?= $data['nomPersonne'] ?></td>
                                <td><?= $data['prenomPersonne'] ?></td>
                                <td><?= $data['mailPersonne'] ?></td>
                                <td><?= $data['fonction'] ?></td>
                                <td><?php
                                	$query2 = $db->prepare('SELECT * FROM PROFILS_PERSONNES person LEFT OUTER JOIN PROFILS profil ON person.idProfil = profil.idProfil WHERE idPersonne = :idPersonne;');
                                	$query2->execute(array('idPersonne'=>$data['idPersonne']));
                                	while($data2 = $query2->fetch())
                                	{
                                		echo $data2['libelleProfil'].'<br/>';
                                	}
                                	?>
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
