<!DOCTYPE html>
<html>
<?php include('header.php'); ?>
<?php
session_start();
$_SESSION['page'] = 401;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['annuaire_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-blue sidebar-mini fixed">
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
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="box-header">
                        <?php if ($_SESSION['annuaire_ajout']==1) {?>
                            <h3 class="box-title"><a href="annuaireForm.php?id=0" class="btn btn-sm btn-success">Ajouter un utilisateur</a></h3>
                        <?php } else {?>
                            </br>
                        <?php } ?>
                    </div>
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Identifiant de connexion</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Fonction</th>
                                <th>Profil Apollon</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM PERSONNE_REFERENTE person LEFT OUTER JOIN PROFILS profil ON person.idProfil = profil.idProfil ORDER BY identifiant;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idPersonne']; ?></td>
                                <td><?php echo $data['identifiant']; ?></td>
                                <td><?php echo $data['nomPersonne']; ?></td>
                                <td><?php echo $data['prenomPersonne']; ?></td>
                                <td><?php echo $data['fonction']; ?></td>
                                <td><?php echo $data['libelleProfil']; ?></td>
                                <td>
                                    <?php if ($_SESSION['annuaire_modification']==1) {?>
                                        <a href="annuaireForm.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_mdp']==1) {?>
                                        <a href="annuaireRAZ.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-info" onclick="return confirm('Etes-vous sûr de vouloir réinitialiser ce mot de passe (le nouveau mot de passe prendra la valeur de l\'identifiant)?');"><i class="fa fa-lock"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['annuaire_suppression']==1) {?>
                                        <a href="annuaireDelete.php?id=<?=$data['idPersonne']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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
