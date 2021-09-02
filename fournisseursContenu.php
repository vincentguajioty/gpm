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
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
    $query = $db->prepare('SELECT *, AES_DECRYPT(aesFournisseur, :aesKey) as aesFournisseurDecode FROM FOURNISSEURS WHERE idFournisseur = :idFournisseur;');
    $query->execute(array(
        'idFournisseur' => $_GET['id'],
        'aesKey'=>$_SESSION['aesFour']
    ));
    $data=$query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Détails du fournisseur : <?php echo $data['nomFournisseur']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="fournisseurs.php">Fournisseurs</a></li>
                <li class="active"><?php echo $data['nomFournisseur']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">

                <div class="col-md-4">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Détails</h3> <?php if ($_SESSION['fournisseurs_modification']==1) {?><a href="fournisseursForm.php?id=<?=$_GET['id']?>" class="btn btn-xs modal-form" title="Modifier"><i class="fa fa-pencil"></i></a><?php }?>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-condensed">
                                <tr>
                                    <td>Nom</td>
                                    <td><?= $data['nomFournisseur'] ?></td>
                                </tr>
                                <tr>
                                    <td>Adresse</td>
                                    <td><?= $data['adresseFournisseur'] ?></td>
                                </tr>
                                <tr>
                                    <td>Téléphone</td>
                                    <td><?= $data['telephoneFournisseur'] ?></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><?= $data['mailFournisseur'] ?></td>
                                </tr>
                                <tr>
                                    <td>Site internet</td>
                                    <td><?= $data['siteWebFournisseur'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
                <div class="col-md-8">
                    <!-- Widget: user widget style 1 -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#produits" data-toggle="tab">Produits référencés</a></li>
                            <li><a href="#commandes" data-toggle="tab">Commandes</a></li>
                            <li><a href="#aes" data-toggle="tab">Informations chiffrées</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="produits">
                                <?php if($_SESSION['catalogue_lecture']){?>
                                    <table class="table table-hover">
                                        <tr>
                                            <th>#</th>
                                            <th>Libellé</th>
                                            <th>Catégorie</th>
                                            <th>Commentaires</th>
                                        </tr>
                                        <?php
                                        $query2 = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE l LEFT OUTER JOIN MATERIEL_CATEGORIES c ON l.idCategorie = c.idCategorie WHERE idFournisseur = :idFournisseur ORDER BY libelleMateriel;');
                                        $query2->execute(array('idFournisseur' => $_GET['id']));
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $data2['idMaterielCatalogue'];?></td>
                                                <td><?php echo $data2['libelleMateriel'];?> <?php if($data['sterilite']){echo '<span class="badge bg-blue">STERILE</span>';} ?></td>
                                                <td><?php echo $data2['libelleCategorie'];?></td>
                                                <td><?php echo $data2['commentairesMateriel'];?></td>
                                            </tr>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </table>
                                <?php } ?>
                            </div>
							
							<div class="tab-pane" id="commandes">
								<?php if($_SESSION['commande_lecture']){?>
                                    <table class="table table-hover">
                                        <tr>
                                            <th>#</th>
                                            <th>Date de création</th>
                                            <th>Nom</th>
                                            <th>TTC</th>
                                            <th>Etat</th>
                                            <th>Référence Fournisseur</th>
                                            <th>Actions</th>
                                        </tr>
                                        <?php
                                        $query2 = $db->prepare('SELECT c.idCommande, c.nomCommande, c.dateCreation, e.libelleEtat, c.idEtat, c.numCommandeFournisseur FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat WHERE idFournisseur = :idFournisseur ORDER BY dateCreation DESC;');
                                        $query2->execute(array('idFournisseur' => $_GET['id']));
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $data2['idCommande']; ?></td>
                                                <td><?php echo $data2['dateCreation']; ?></td>
                                                <td><?php echo $data2['nomCommande']; ?></td>
                                                <td><?php echo cmdTotal($data2['idCommande']).'€'; ?></td>
                                                <td><span class="badge bg-<?php
                                                    switch ($data2['idEtat']) {
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
                                                    ?>"><?php echo $data2['idEtat'].' - '.$data2['libelleEtat']; ?></span></td>
                                                <td><?php echo $data2['numCommandeFournisseur']; ?></td>
                                                <td>
                                                    <?php if ($_SESSION['commande_lecture']==1) {?>
                                                        <a href="commandeView.php?id=<?=$data2['idCommande']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                    </table>
                                <?php } ?>
							</div>

                            <div class="tab-pane" id="aes">
                                <?php if(!isset($_SESSION['aesFour']) AND $AESFOUR){?>
                                    <a href="fournisseursAESgetPWD.php" class="btn btn-sm btn-info modal-form">Accéder aux informations chiffrées</a>
                                <?php } ?>
                                <?php if(isset($_SESSION['aesFour']) AND $AESFOUR){?>
                                    <a href="fournisseursAESlock.php" class="btn btn-sm btn-info">Quitter le mode édition des données chiffrées</a>
                                    <br/><br/>
                                    <form role="form" action="fournisseursAESUpdate.php?id=<?= $_GET['id'] ?>" method="POST">
                                        <div class="form-group">
                                            <textarea class="form-control" rows="3" name="aesFournisseur"><?= isset($data['aesFournisseurDecode']) ? $data['aesFournisseurDecode'] : '' ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Modifier</button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.widget-user -->
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



