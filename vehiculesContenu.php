<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1001;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vehicules_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM VEHICULES v LEFT OUTER JOIN ETATS e ON v.idEtat = e.idEtat LEFT OUTER JOIN PERSONNE_REFERENTE p ON v.idResponsable = p.idPersonne LEFT OUTER JOIN VEHICULES_TYPES t ON v.idVehiculesType = t.idVehiculesType LEFT OUTER JOIN LIEUX l ON v.idLieu = l.idLieu WHERE idVehicule = :idVehicule;');
    $query->execute(array(
        'idVehicule' => $_GET['id']));
    $data=$query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Détails du véhicule : <?php echo $data['libelleVehicule']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="vehicules.php">Véhicules</a></li>
                <li class="active"><?php echo $data['libelleVehicule']; ?></li>
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
                            <h3 class="box-title">Détails</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-condensed">
                                <tr>
                                    <td>Libellé/Indicatif</td>
                                    <td><?= $data['libelleVehicule'] ?></td>
                                </tr>
                                <tr>
                                    <td>Marque/modele</td>
                                    <td><?= $data['marqueModele'] ?></td>
                                </tr>
                                <tr>
                                    <td>Immatriculation</td>
                                    <td><?= $data['immatriculation'] ?></td>
                                </tr>
								<tr>
                                    <td>Type</td>
                                    <td><?= $data['libelleType'] ?></td>
                                </tr>
                                <tr>
                                    <td>Etat</td>
                                    <td><?= $data['libelleEtat'] ?></td>
                                </tr>
                                <tr>
                                    <td>Lieu de parking</td>
                                    <td><?= $data['libelleLieu'] ?></td>
                                </tr>
                                <tr>
                                    <td>Kilmetrage</td>
                                    <td><?= $data['kilometrage'] ?></td>
                                </tr>
                                <tr>
                                    <td>Nombre de places</td>
                                    <td><?= $data['nbPlaces'] ?></td>
                                </tr>
                                <tr>
                                    <td>Dimensions</td>
                                    <td><?= $data['dimensions'] ?></td>
                                </tr>
                                <tr>
                                    <td>Responsable</td>
                                    <td><?= $data['identifiant'] ?></td>
                                </tr>
								<tr>
                                    <td>Date d'achat</td>
                                    <td><?= $data['dateAchat'] ?></td>
                                </tr>
								<tr>
                                    <td>Numéro d'assurance</td>
                                    <td><?= $data['assuranceNumero'] ?></td>
                                </tr>
								<tr>
                                    <td>Expiration d'assurance</td>
                                    <td><?= $data['assuranceExpiration'] ?></td>
                                </tr>
								<tr>
                                    <td>Prochaine révision</td>
                                    <td><?= $data['dateNextRevision'] ?></td>
                                </tr>
								<tr>
                                    <td>Prochain contrôle technique</td>
                                    <td><?= $data['dateNextCT'] ?></td>
                                </tr>
                                <tr>
                                    <td>Remarques</td>
                                    <td><?= $data['remarquesVehicule'] ?></td>
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
                            <li class="active"><a href="#maintenance" data-toggle="tab">Taches de maintenance</a></li>
							<li><a href="#lots" data-toggle="tab">Lots opérationnels affetés</a></li>
                            <li><a href="#pj" data-toggle="tab">Pièces jointes</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="maintenance">
                                <table class="table table-hover">
                                    <tr>
                                        <th>Date</th>
                                        <th>Intervention</th>
                                        <th>Intervenant</th>
                                        <th></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM VEHICULES_MAINTENANCE m LEFT OUTER JOIN VEHICULES_MAINTENANCE_TYPES t ON m.idTypeMaintenance = t.idTypeMaintenance LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idExecutant = p.idPersonne WHERE idVehicule = :idVehicule ORDER BY dateMaintenance DESC;');
                                    $query2->execute(array('idVehicule' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['dateMaintenance'];?></td>
                                            <td><?php echo $data2['libelleTypeMaintenance'];?></td>
                                            <td><?php echo $data2['identifiant'];?></td>
                                            <td>
                                                <?php if($_SESSION['vehicules_lecture']==1){ ?>
                                                    <a href="vehiculesMaintenanceForm.php?idVehicule=<?=$_GET['id']?>&id=<?=$data2['idMaintenance']?>" class="btn btn-xs btn-warning modal-form"><i class="fa fa-pencil"></i></a>
                                                <?php }?>
                                                <?php if ($_SESSION['vehicules_suppression']==1) {?>
			                                        <a href="vehiculesMaintenanceDelete.php?id=<?=$data2['idMaintenance']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
			                                    <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                    
                                    <?php if($_SESSION['vehicules_modification']==1){ ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><a href="vehiculesMaintenanceForm.php?idVehicule=<?=$_GET['id']?>" class="btn btn-xs btn-success modal-form"><i class="fa fa-plus"></i></a></td>
                                    <tr>
                                        <?php }?>
                                </table>
                            </div>
							
							<div class="tab-pane" id="lots">
								<table class="table table-hover">
                                    <tr>
                                        <th>Lots</th>
                                        <th>Etat</th>
                                        <th></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN ETATS e ON l.idEtat = e.idEtat WHERE idVehicule = :idVehicule ORDER BY libelleLot ASC;');
                                    $query2->execute(array('idVehicule' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['libelleLot'];?></td>
                                            <td><?php echo $data2['libelleEtat'];?></td>
                                            <td>
                                                <?php if($_SESSION['lots_lecture']==1){ ?>
                                                    <a href="lotsContenu.php?id=<?=$data2['idLot']?>" class="btn btn-xs btn-info" ><i class="fa fa-folder-open"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </table>
							</div>


                            <div class="tab-pane" id="pj">
                                <table class="table table-hover">
                                    <tr>
                                        <th>Nom du document</th>
                                        <th>Type de document</th>
                                        <th>Date de chargement</th>
                                        <th>Format</th>
                                        <th></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM DOCUMENTS_VEHICULES c LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument WHERE idVehicule = :idVehicule ORDER BY nomDocVehicule ASC ;');
                                    $query2->execute(array('idVehicule' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['nomDocVehicule'];?></td>
                                            <td><?php echo $data2['libelleTypeDocument'];?></td>
                                            <td><?php echo $data2['dateDocVehicule'];?></td>
                                            <td><i class="fa <?php echo documentsGetIcone($data2['formatDocVehicule']);?>"></i></td>
                                            <td>
                                                <?php if($_SESSION['vehicules_lecture']==1){
                                                    if ($data2['formatDocVehicule'] == 'pdf' OR $data2['formatDocVehicule'] == 'jpg' OR $data2['formatDocVehicule'] == 'jpeg' OR $data2['formatDocVehicule'] == 'png'){?>
                                                        <a href="vehiculesDocView.php?idDoc=<?=$data2['idDocVehicules']?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                    <?php }}?>
                                                <?php if($_SESSION['vehicules_lecture']==1){ ?>
                                                    <a href="vehiculesDocDL.php?idDoc=<?=$data2['idDocVehicules']?>" class="btn btn-xs btn-success"><i class="fa fa-download"></i></a>
                                                <?php }?>
                                                <?php if($_SESSION['vehicules_suppression']==1){ ?>
                                                    <a href="vehiculesDocDelete.php?idDoc=<?=$data2['idDocVehicules']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-minus"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>

                                    <?php if($_SESSION['vehicules_modification']==1){ ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><a href="vehiculesDocForm.php?idVehicule=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form"><i class="fa fa-plus"></i></a></td>
                                    <tr>
                                        <?php }?>
                                </table>
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



