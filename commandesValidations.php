<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 602;
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
                Commandes en attente de validation de <?php echo $_SESSION['identifiant']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="commandesToutes.php">Commandes</a></li>
                <li class="active">Commandes à suivre</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2R" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="all" style="width: 10px">#</th>
                            <th class="not-mobile">Date de création</th>
                            <th class="all">Nom</th>
                            <th class="not-mobile">Fournisseur</th>
                            <th class="not-mobile">TTC</th>
                            <th class="not-mobile">Référence fournisseur</th>
                            <th class="not-mobile">Etat</th>
                            <th class="not-mobile">Etat centre de cout</th>
                            <th class="not-mobile">Demandeur</th>
                            <th class="not-mobile">Gerant</th>
                            <th class="not-mobile">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->prepare('SELECT c.idCommande, c.nomCommande, c.dateCreation, f.nomFournisseur, c.numCommandeFournisseur, e.libelleEtat, c.idEtat FROM COMMANDES_VALIDEURS j LEFT OUTER JOIN COMMANDES c ON j.idCommande = c.idCommande LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE (idValideur = :idPersonne) AND (c.idEtat = 2);');
                        $query->execute(array(
                            'idPersonne' => $_SESSION['idPersonne']
                        ));
                        while ($data = $query->fetch())
                        {?>
                            <tr <?php if ($_SESSION['commande_lecture']==1) {?>data-href="commandeView.php?id=<?=$data['idCommande']?>"<?php }?>>
                                <td><?php echo $data['idCommande']; ?></td>
                                <td><?php echo $data['dateCreation']; ?></td>
                                <td><?php echo $data['nomCommande']; ?></td>
                                <td><?php echo $data['nomFournisseur']; ?></td>
                                <td><?php echo cmdTotal($data['idCommande']).'€'; ?></td>
                                <td><?php echo $data['numCommandeFournisseur']; ?></td>
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
                                    ?>"><?php echo $data['libelleEtat']; ?></span></td>
                                <td><?= cmdEtatCentreCouts($data['idCommande']); ?></td>
                                <td>
                                	<?php
                                		$query2 = $db->prepare('SELECT * FROM COMMANDES_DEMANDEURS c JOIN PERSONNE_REFERENTE p ON c.idDemandeur = p.idPersonne WHERE idCommande = :idCommande');
                                		$query2->execute(array('idCommande'=>$data['idCommande']));
                                		while($data2 = $query2->fetch())
                                		{
                                			echo $data2['identifiant'].'<br/>';
                                		}
                                	?>
                                </td>
                                <td>
	                                <?php
	                            		$query2 = $db->prepare('SELECT * FROM COMMANDES_AFFECTEES c JOIN PERSONNE_REFERENTE p ON c.idAffectee = p.idPersonne WHERE idCommande = :idCommande');
	                            		$query2->execute(array('idCommande'=>$data['idCommande']));
	                            		while($data2 = $query2->fetch())
	                            		{
	                            			echo $data2['identifiant'].'<br/>';
	                            		}
	                            	?>
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