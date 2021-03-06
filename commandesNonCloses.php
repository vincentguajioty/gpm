<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 607;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['commande_lecture']==0)
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
                Toutes les commandes non closes et non abandonnées
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Commandes</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2R" class="table table-bordered table-hover" >
                        <thead>
                        <tr>
                            <th class="all" style="width: 10px">#</th>
                            <th class="not-mobile">Date de création</th>
                            <th class="all">Nom</th>
                            <th class="not-mobile">Fournisseur</th>
                            <th class="not-mobile">TTC</th>
                            <th class="not-mobile">Etat</th>
                            <th class="not-mobile">Centre de cout</th>
                            <th class="not-mobile">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT c.idCommande, c.nomCommande, c.dateCreation, f.nomFournisseur, c.numCommandeFournisseur, e.libelleEtat, c.idEtat  FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE c.idEtat <> 8 AND c.idEtat <> 7;');
                        while ($data = $query->fetch())
                        {?>
                            <tr <?php if ($_SESSION['commande_lecture']==1) {?>data-href="commandeView.php?id=<?=$data['idCommande']?>"<?php }?>>
                                <td><?php echo $data['idCommande']; ?></td>
                                <td><?php echo $data['dateCreation']; ?></td>
                                <td><?php echo $data['nomCommande']; ?></td>
                                <td><?php echo $data['nomFournisseur']; ?></td>
                                <td><?php echo cmdTotal($data['idCommande']).'€'; ?></td>
                                <td><span class="badge bg-<?php
                                    switch ($data['idEtat']) {
                                        case 1:
                                            echo "green";
                                            break;
                                        case 2:
                                            echo "orange";
                                            break;
                                        case 3:
                                            echo "green";
                                            break;
                                        case 4:
                                            echo "blue";
                                            break;
                                        case 5:
                                            echo "green";
                                            break;
                                        case 6:
                                            echo "red";
                                            break;
                                        case 7:
                                            echo "grey";
                                            break;
                                        case 8:
                                            echo "grey";
                                            break;
                                    }
                                    ?>"><?php echo $data['idEtat'].' - '.$data['libelleEtat']; ?></span></td>
                                <td>
                                	<?= cmdEtatCentreCouts($data['idCommande']); ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['commande_lecture']==1) {?>
                                    	<a href="commandeView.php?id=<?=$data['idCommande']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                	<?php } ?>
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
