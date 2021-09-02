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
                            <h3 class="box-title">Détails</h3> <?php if ($_SESSION['vehicules_modification']==1) {?><a href="vehiculesForm.php?id=<?=$_GET['id']?>" class="btn btn-xs modal-form" title="Modifier"><i class="fa fa-pencil"></i></a><?php }?>
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
                                    <td>Notifications</td>
                                    <td><?= $data['libelleEtat'] ?></td>
                                </tr>
                                <tr>
                                    <td>Lieu de parking</td>
                                    <td><?= $data['libelleLieu'] ?></td>
                                </tr>
                                <tr>
                                    <td>Dernier relevé kilométrique</td>
                                    <td>
                                    	<?php
                                    		$kilometrages = $db->prepare('SELECT * FROM VEHICULES_RELEVES WHERE idVehicule = :idVehicule ORDER BY dateReleve DESC;');
                                    		$kilometrages->execute(array('idVehicule'=>$_GET['id']));
                                    		$kilometrage = $kilometrages->fetch();
                                    		if(!(isset($kilometrage['releveKilometrique'])) OR $kilometrage['releveKilometrique'] == Null)
                                    		{
                                    			echo 'Pas de relevé fait';
                                    		}
                                    		else
                                    		{
                                    			echo $kilometrage['releveKilometrique'].'km ('.$kilometrage['dateReleve'].')';
                                    		}
                                    	?>
                                    </td>
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
                                    <td><span class="badge bg-<?php
                                        if($data['assuranceExpiration']==date('Y-m-d'))
                                        {
                                            echo "orange";
                                        }
                                        else
                                        {
                                            if($data['assuranceExpiration']>date('Y-m-d'))
                                            {
                                                echo "green";
                                            }
                                            else
                                            {
                                                echo "red";
                                            }
                                        }
                                        ?>"><?= $data['assuranceExpiration'] ?></span></td>
                                </tr>
								<tr>
                                    <td>Prochaine révision</td>
                                    <td><span class="badge bg-<?php
                                            if($data['dateNextRevision']==date('Y-m-d'))
                                            {
                                                echo "orange";
                                            }
                                            else
                                            {
                                                if($data['dateNextRevision']>date('Y-m-d'))
                                                {
                                                    echo "green";
                                                }
                                                else
                                                {
                                                    echo "red";
                                                }
                                            }
                                        ?>"><?= $data['dateNextRevision'] ?></span>
                                    </td>
                                </tr>
								<tr>
                                    <td>Prochain contrôle technique</td>
                                    <td><span class="badge bg-<?php
                                        if($data['dateNextCT']==date('Y-m-d'))
                                        {
                                            echo "orange";
                                        }
                                        else
                                        {
                                            if($data['dateNextCT']>date('Y-m-d'))
                                            {
                                                echo "green";
                                            }
                                            else
                                            {
                                                echo "red";
                                            }
                                        }
                                        ?>"><?= $data['dateNextCT'] ?></span></td>
                                </tr>
                                <?php
                                	$desinfections = $db->prepare('
                                		SELECT
                                			a.*,
                                			t.libelleVehiculesDesinfectionsType,
                                			MAX(v.dateDesinfection) as dateDesinfection
                                		FROM
                                			VEHICULES_DESINFECTIONS_ALERTES a
                                			LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON a.idVehiculesDesinfectionsType=t.idVehiculesDesinfectionsType
                                			LEFT OUTER JOIN (SELECT * FROM VEHICULES_DESINFECTIONS WHERE idVehicule = :idVehicule) v ON a.idVehiculesDesinfectionsType = v.idVehiculesDesinfectionsType
                                		WHERE
                                			a.idVehicule = :idVehicule
                                		GROUP BY
                                			a.idDesinfectionsAlerte
                                	;');
                                	$desinfections->execute(array('idVehicule'=>$_GET['id']));
                                	while($desinfection = $desinfections->fetch())
                                	{?>
                                		<tr>
		                                    <td>Prochaine désinfection <?=$desinfection['libelleVehiculesDesinfectionsType']?></td>
		                                    <td><?php
		                                    	if($desinfection['dateDesinfection'] == Null)
		                                    	{
		                                    		echo '<span class="badge bg-red">Aucune désinfection enregistrée</span>';
		                                    	}
		                                    	else
		                                    	{
		                                    		$nextDesinf = date('Y-m-d', strtotime($desinfection['dateDesinfection']. ' + '.$desinfection['frequenceDesinfection'].' days'));
		                                    		if($nextDesinf <= date('Y-m-d'))
		                                    		{
		                                    			echo '<span class="badge bg-red">'.$nextDesinf.'</span>';
		                                    		}
		                                    		else
		                                    		{
		                                    			echo '<span class="badge bg-green">'.$nextDesinf.'</span>';
		                                    		}
		                                    	}
		                                    ?></td>
		                                </tr>
                                	<?php }
                                ?>
                                <tr>
                                    <td>Equipements embarqués</td>
                                    <td>
                                    	<?= ($data['pneusAVhivers']==1)? 'Pneus hivers Avant<br/>':'' ?>
                                    	<?= ($data['pneusARhivers']==1)? 'Pneus hivers Arrière<br/>':'' ?>
                                    	<?= ($data['priseAlimentation220']==1)? 'Prise MARECHAL 220V<br/>':'' ?>
                                    	<?= ($data['climatisation']==1)? 'Climatisation<br/>':'' ?>
                                    	<?= ($data['signaletiqueOrange']==1)? 'Feux oranges<br/>':'' ?>
                                    	<?= ($data['signaletiqueBleue']==1)? 'Feux bleux<br/>':'' ?>
                                    	<?= ($data['signaletique2tons']==1)? 'Sirène 2 tons<br/>':'' ?>
                                    	<?= ($data['signaletique3tons']==1)? 'Sirène 3 tons<br/>':'' ?>
                                    	<?= ($data['pmv']==1)? 'Panneau à message variable<br/>':'' ?>
                                    	<?= ($data['fleche']==1)? 'Flèche<br/>':'' ?>
                                    	<?= ($data['nbCones']>0)? $data['nbCones'].' cônes de lubëck<br/>':'' ?>
                                	</td>
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
                            <li><a href="#km" data-toggle="tab">Relevés kilométriques</a></li>
                            <?php if($_SESSION['desinfections_lecture']){ ?><li><a href="#desinfections" data-toggle="tab">Désinfections</a></li><?php } ?>
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
                                        <th><?php if($_SESSION['vehicules_modification']==1){ ?><a href="vehiculesMaintenanceForm.php?idVehicule=<?=$_GET['id']?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?></th>
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
                                                    <a href="vehiculesMaintenanceForm.php?idVehicule=<?=$_GET['id']?>&id=<?=$data2['idMaintenance']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                                <?php }?>
                                                <?php if ($_SESSION['vehicules_suppression']==1) {?>
			                                        <a href="modalDeleteConfirm.php?case=vehiculesMaintenanceDelete&id=<?=$data2['idMaintenance']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
			                                    <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                    
                                </table>
                            </div>
							
							<div class="tab-pane" id="km">
								<table class="table table-hover">
                                    <tr>
                                        <th>Date</th>
                                        <th>Relevé kilométrique</th>
                                        <th>Intervenant</th>
                                        <th><?php if($_SESSION['vehicules_modification']==1){ ?><a href="vehiculesReleveForm.php?idVehicule=<?=$_GET['id']?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM VEHICULES_RELEVES m LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idPersonne = p.idPersonne WHERE idVehicule = :idVehicule ORDER BY dateReleve DESC;');
                                    $query2->execute(array('idVehicule' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['dateReleve'];?></td>
                                            <td><?php echo $data2['releveKilometrique'];?> km</td>
                                            <td><?php echo $data2['identifiant'];?></td>
                                            <td>
                                                <?php if($_SESSION['vehicules_lecture']==1){ ?>
                                                    <a href="vehiculesReleveForm.php?idVehicule=<?=$_GET['id']?>&id=<?=$data2['idReleve']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                                <?php }?>
                                                <?php if ($_SESSION['vehicules_suppression']==1) {?>
			                                        <a href="modalDeleteConfirm.php?case=vehiculesReleveDelete&id=<?=$data2['idReleve']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
			                                    <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                    
                                </table>
							</div>
							
							<?php if($_SESSION['desinfections_lecture']){ ?>
								<div class="tab-pane" id="desinfections">
									<table class="table table-hover">
                                        <tr>
                                            <th>Date</th>
                                            <th>Type de désinfection</th>
                                            <th>Intervenant</th>
                                            <th>
                                                <?php if($_SESSION['desinfections_ajout']==1){ ?><a href="vehiculesDesinfectionsForm.php?idVehicule=<?=$_GET['id']?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?>
                                                <?php if($_SESSION['desinfections_ajout']==1 AND $_SESSION['desinfections_modification']==1){ ?><a href="vehiculesDesinfectionsAlertes.php?idVehicule=<?=$_GET['id']?>" class="btn btn-xs btn-info modal-form" title="Ajouter"><i class="fa fa-bell-o"></i> Gestion des notifications</a><?php } ?>
                                            </th>
                                        </tr>
                                        <?php
                                        $query2 = $db->prepare('SELECT * FROM VEHICULES_DESINFECTIONS d LEFT OUTER JOIN PERSONNE_REFERENTE p ON d.idExecutant = p.idPersonne LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON d.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType WHERE idVehicule = :idVehicule ORDER BY dateDesinfection DESC;');
                                        $query2->execute(array('idVehicule' => $_GET['id']));
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $data2['dateDesinfection'];?></td>
                                                <td><?php echo $data2['libelleVehiculesDesinfectionsType'];?></td>
                                                <td><?php echo $data2['identifiant'];?></td>
                                                <td>
                                                    <?php if($_SESSION['desinfections_modification']==1){ ?>
                                                        <a href="vehiculesDesinfectionsForm.php?id=<?=$data2['idVehiculesDesinfection']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                                    <?php }?>
                                                    <?php if ($_SESSION['desinfections_suppression']==1) {?>
                                                        <a href="modalDeleteConfirm.php?case=vehiculesDesinfectionsDelete&id=<?=$data2['idVehiculesDesinfection']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                        
                                    </table>
								</div>
							<?php } ?>
							
							<div class="tab-pane" id="lots">
								<table class="table table-hover">
                                    <tr>
                                        <th>Lots</th>
                                        <th>Etat</th>
                                        <th></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN ETATS e ON l.idEtat = e.idEtat LEFT OUTER JOIN LOTS_ETATS le ON l.idLotsEtat = le.idLotsEtat WHERE idVehicule = :idVehicule ORDER BY libelleLot ASC;');
                                    $query2->execute(array('idVehicule' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['libelleLot'];?></td>
                                            <td><?php echo $data2['libelleLotsEtat'];?></td>
                                            <td>
                                                <?php if($_SESSION['lots_lecture']==1){ ?>
                                                    <a href="lotsContenu.php?id=<?=$data2['idLot']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
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
                                        <th><?php if($_SESSION['vehicules_modification']==1){ ?><a href="vehiculesDocForm.php?idVehicule=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM VIEW_DOCUMENTS_VEHICULES WHERE idVehicule = :idVehicule ORDER BY nomDocVehicule ASC ;');
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
                                                        <a href="vehiculesDocView.php?idDoc=<?=$data2['idDocVehicules']?>" class="btn btn-xs btn-info" title="Visionner"><i class="fa fa-eye"></i></a>
                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                    <?php }}?>
                                                <?php if($_SESSION['vehicules_lecture']==1){ ?>
                                                    <a href="vehiculesDocDL.php?idDoc=<?=$data2['idDocVehicules']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
                                                <?php }?>
                                                <?php if($_SESSION['vehicules_suppression']==1){ ?>
                                                    <a href="vehiculesDocDelete.php?idDoc=<?=$data2['idDocVehicules']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');" title="Supprimer"><i class="fa fa-minus"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>

                                </table>
                            </div>

                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
                <?php
                    $nb = $db->prepare('SELECT COUNT(*) as nb FROM VEHICULES_RELEVES WHERE idVehicule = :idVehicule;');
                    $nb->execute(array(
                        'idVehicule' => $_GET['id']));
                    $nb=$nb->fetch();
                    $nb = $nb['nb'];
                    if($nb > 1)
                    {
                ?>
                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Evolution kilométrique</h3>
                            </div>
                            <div class="box-body chart-responsive">
                                <div class="chart" id="line-chart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

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

<script>
    $(function () {
        "use strict";
        
        <?php
            $releves = $db->prepare("SELECT dateReleve, releveKilometrique FROM VEHICULES_RELEVES WHERE dateReleve IS NOT Null AND idVehicule = :idVehicule ORDER BY dateReleve DESC;");
            $releves->execute(array('idVehicule'=>$_GET['id']));
        ?>
        
        var line = new Morris.Line({
          element: 'line-chart',
          resize: true,
          data: [
            <?php
                while($releve = $releves->fetch())
                {
                    echo "{x: '".$releve['dateReleve']."', releve: ".$releve['releveKilometrique']."},";
                }
            ?>
          ],
          xkey: 'x',
          ykeys: ['releve'],
          labels: ['Relevé Kilométrique'],
          lineColors: ['#f39c12'],
          hideHover: 'auto'
        });
  });
</script>

</body>
</html>



