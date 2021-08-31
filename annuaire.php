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
                Gestion des utilisateurs
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
                <?php if ($_SESSION['annuaire_ajout']==1) {?>
                	<div class="box-header">
	                    <h3 class="box-title"><a href="annuaireForm.php" class="btn btn-sm btn-success modal-form">Ajouter un utilisateur</a></h3>
	            	</div>
	            <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Identifiant de connexion</th>
                                <th class="not-mobile">Nom</th>
                                <th class="not-mobile">Prénom</th>
                                <th class="not-mobile">Fonction</th>
                                <th class="not-mobile">Profil</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE person LEFT OUTER JOIN PROFILS profil ON person.idProfil = profil.idProfil ORDER BY identifiant;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idPersonne']; ?></td>
                                <td><?php
                                	if($data['connexion_connexion']==1 AND (date('Y-m-d') > date('Y-m-d', strtotime($data['derniereConnexion'] . ' + 60 days'))))
                                	{ ?>
                                		<span class="badge bg-red"><?php echo $data['identifiant']; ?></span>
                                	<?php }else{
                                		echo $data['identifiant'];
                                	} ?>
                            	</td>
                                <td><?php echo $data['nomPersonne']; ?></td>
                                <td><?php echo $data['prenomPersonne']; ?></td>
                                <td><?php echo $data['fonction']; ?></td>
                                <td><?php echo $data['libelleProfil']; ?></td>
                                <td>
                                    <?php if ($_SESSION['annuaire_modification']==1) {?>
                                        <a href="annuaireForm.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_modification']==1) {?>
                                        <a href="annuaireSetDashboard.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-success" onclick="return confirm('Etes-vous sûr de vouloir réactiver les indicateurs sur la page d\'accueil de l\'utilisateur ?');" title="Forcer les indicateurs sur la page d'accueil"><i class="fa fa-dashboard"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_modification']==1) {?>
                                        <a href="annuaireSetMail.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-success" onclick="return confirm('Etes-vous sûr de vouloir réactiver les notifications pour cet utilisateur ?');" title="Forcer l'activation des notifications"><i class="fa fa-envelope"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_mdp']==1) {?>
                                        <a href="annuaireRAZ.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-info" onclick="return confirm('Etes-vous sûr de vouloir réinitialiser ce mot de passe (le nouveau mot de passe prendra la valeur de l\'identifiant) ?');" title="Réinitialiser le mot de passe"><i class="fa fa-lock"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=annuaireDelete&id=<?=$data['idPersonne']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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
