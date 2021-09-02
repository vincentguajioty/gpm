<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 000;
include('logCheck.php');
require_once('loginReloadHabilitation.php');
include('logCheck.php');
/*Laisser le touble logCheck.php pour la prise en compte du retrait de connexion_connexion*/
?>
<?php include('header.php'); require_once('config/config.php'); ?>
<meta http-equiv="refresh" content="<?= $_SESSION['conf_accueilRefresh'] ?>"; url="mapage.php" />
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
        Vue générale du parc matériel
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i>Accueil</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    	<?php
    		if ($MAINTENANCE)
    		{
    			echo '<div class="alert alert-warning alert-dismissible">';
		        echo '<i class="icon fa fa-wrench"></i> Mode maintenance activé.';
		        echo '</div>';
    		}
    	?>
        <?php include('confirmationBox.php'); ?>
        <div class="row">
        	
        	
        	<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
		
				<div class="row">	
					<?php
						if ($_SESSION['lots_lecture'] OR $_SESSION['lots_ajout'] OR $_SESSION['lots_modification'] OR $_SESSION['lots_suppression'] OR $_SESSION['sac_lecture'] OR $_SESSION['sac_ajout'] OR $_SESSION['sac_modification'] OR $_SESSION['sac_suppression'] OR $_SESSION['sac2_lecture'] OR $_SESSION['sac2_ajout'] OR $_SESSION['sac2_modification'] OR $_SESSION['sac2_suppression'] OR $_SESSION['materiel_lecture'] OR $_SESSION['materiel_ajout'] OR $_SESSION['materiel_modification'] OR $_SESSION['materiel_suppression'])
						{
					?>
				        <div class="col-md-12">
				            <div class="box box-success">
				                <div class="box-header with-border">
				                    <i class="fa fa-medkit"></i>
				
				                    <h3 class="box-title">Lots dont j'ai la charge</h3>
				                </div>
				
				                <!-- /.box-header -->
				                <div class="box-body">
				                    <table class="table table-bordered">
				                        <tr>
				                            <th>Libelle</th>
				                            <th>Référentiel</th>
				                            <th>Prochain inventaire</th>
				                            <th>Notifications</th>
				                            <th></th>
				                        </tr>
				                        <?php
				                        $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne WHERE l.idPersonne = :idPersonne;');
				                        $query->execute(array('idPersonne' => $_SESSION['idPersonne']));
				                        while ($data = $query->fetch())
				                        {
				                            ?>
				                            <tr <?php if ($_SESSION['lots_lecture']==1) {?>data-href="lotsContenu.php?id=<?=$data['idLot']?>"<?php }?>>
				                                <td><?php echo $data['libelleLot']; ?></td>
				                                <td>
					                                <?php
				                                    if ($data['libelleTypeLot'] == Null)
				                                    {
				                                        ?><span class="badge bg-orange">NA</span><?php
				                                    }
				                                    else
				                                    {
				                                        if ($data['alerteConfRef']==0)
				                                        {
				                                            ?><span class="badge bg-green"><?php echo $data['libelleTypeLot']; ?></span><?php
				                                        }
				                                        else
				                                        {
				                                            ?><span class="badge bg-red"><?php echo $data['libelleTypeLot']; ?></span><?php
				                                        }
				                                    }
				                                    ?>
				                                </td>
				                                <td><?php
				                                    if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) < date('Y-m-d'))
				                                    {
				                                        ?><span class="badge bg-red"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
				                                    }
				                                    else if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) == date('Y-m-d'))
				                                    {
				                                        ?><span class="badge bg-orange"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
				                                    }
				                                    else
				                                    {
				                                        ?><span class="badge bg-green"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
				                                    }
				                                    ?>
				                                </td>
				                                <td><?php echo $data['libelleEtat']; ?> (<?php if($data['idEtat']!=1){echo '<i class="fa fa-bell-slash-o"></i>';}else{echo '<i class="fa fa-bell-o"></i>';} ?>)</td>
				                                <td>
					                                <?php if ($_SESSION['lots_lecture']==1) {?>
				                                        <a href="lotsContenu.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
				                                    <?php }?>
				                                </td>
				                            </tr>
				                            <?php
				                        }
				                        $query->closeCursor(); ?>
				                    </table>
				                </div>
				            </div>
				        </div>
				    <?php } ?>
				    
				    
			        <div class="col-md-6">
			            <div class="box box-success">
			
			                <div class="box-header with-border">
			                    <i class="fa fa-bullhorn"></i>
			                    <h3 class="box-title">Messages généraux</h3>
			                    <div class="box-tools pull-right">
			                    	<?php if ($_SESSION['messages_ajout']==1) {?><a href="messagesForm.php" class="btn btn-sm modal-form" title="Ajouter un message"><i class="fa fa-plus"></i></a><?php } ?>
			                    </div>
			                </div>
			
			                <!-- /.box-header -->
			                <div class="box-body">
			                    <?php
			                    $query = $db->query('SELECT COUNT(*) as nb FROM MESSAGES m LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idPersonne = p.idPersonne;');
			                    $data = $query->fetch();
			
			                    if ($data['nb'] == 0)
			                    {
			                        echo '<center>Aucun message à afficher.</center>';
			                    }
			                    else
			                    {
			                        $query = $db->query('SELECT * FROM MESSAGES m LEFT OUTER JOIN PERSONNE_REFERENTE p ON m.idPersonne = p.idPersonne;');
			                        while ($data = $query->fetch())
			                        {
			                            echo '<div class="callout callout-default">';
			                            echo '<h4>' . $data['titreMessage'] . '<small> '. $data['prenomPersonne'] . '</small></h4>';
			                            echo '<p>' . $data['corpsMessage'] . '</p>';
			                            echo '</div>';
			                        }
			                    }
			                    ?>
			
			                </div>
			            </div>
			            <!-- /.box-body -->
			        </div>
			        
			        <div class="col-md-6">
					    <div class="box box-success">
							<div class="box-header">
								<i class="fa fa-check-square-o"></i>
								<h3 class="box-title">To Do List</h3>
								<div class="box-tools pull-right">
			                    	<?php if ($_SESSION['todolist_perso']==1) {?><a href="todolistForm.php?idCreateur=<?= $_SESSION['idPersonne'] ?>&idExecutant=<?= $_SESSION['idPersonne'] ?>" class="btn btn-sm modal-form" title="Ajouter un tache"><i class="fa fa-plus"></i></a><?php } ?>
			                    </div>
						    </div>
						    <!-- /.box-header -->
							<div class="box-body">
								<ul class="todo-list">
									<?php
										$query = $db->prepare('SELECT * FROM TODOLIST_PERSONNES tp JOIN TODOLIST tdl ON tp.idTache = tdl.idTache LEFT OUTER JOIN TODOLIST_PRIORITES prio ON tdl.idTDLpriorite = prio.idTDLpriorite WHERE idExecutant = :idExecutant AND realisee = 0');
										$query->execute(array('idExecutant'=>$_SESSION['idPersonne']));
										while ($data = $query->fetch())
										{ ?>
											<li>
												<!-- todo text -->
												<span class="text"><?= $data['titre'] ?></span>
												<!-- Emphasis label -->
												<small class="label label-<?= $data['couleurPriorite'] ?>"><?= $data['libellePriorite'] ?></small><small class="label label-<?= getTDLdateColor($data['idTache']) ?>"><?= $data['dateExecution'] ?></small>
												<!-- General tools such as edit or delete-->
												<div class="tools">
													<a href="todolistForm.php?id=<?= $data['idTache'] ?>" class="modal-form"><i class="fa fa-edit"></i></a>
												</div>
											</li>
										<?php }
									?>
								</ul>
							</div>
						    <!-- /.box-body -->
						</div>
					</div>
					
					
			        <div class="col-md-12">
			            <div class="box box-success">
			            	<div class="box-body">
			            		<?php
			            			$events = [];
		
		                    		$lots = $_SESSION['lots_lecture'] OR $_SESSION['sac_lecture'] OR $_SESSION['sac2_lecture'] OR $_SESSION['materiel_lecture'];
		                    		$reserves = $_SESSION['reserve_lecture'];
		                    		$vehicules = $_SESSION['vehicules_lecture'];
		                    		$desinfections = $_SESSION['desinfections_lecture'];
		                    		$commandes = $_SESSION['commande_lecture'];
		                    		$tenues = $_SESSION['tenues_lecture'];
		
		                    		if($lots)
		                    		{
				            			$query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE peremptionNotification IS NOT NULL AND idEtat = 1;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => $data['peremption'],
			                                    'title'    => 'Peremption',
			                                    'subTitle' => $data['libelleLot'] . ' > ' . $data['libelleSac'] . ' > ' . $data['libelleEmplacement'] . ' > ' . $data['libelleMateriel'],
			                                    'color'    => '#dd4b39',
			                                    'url'      => 'indexModalCalendrier.php?case=premptionLot&id='.$data['idElement'],
			                                ];
				                        }
		                    		}
		
		                    		if ($reserves)
		                    		{
				                        $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE peremptionReserve IS NOT NULL;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => $data['peremptionReserve'],
			                                    'title'    => 'Peremption',
			                                    'subTitle' => $data['libelleConteneur'] . ' > ' . $data['libelleMateriel'],
			                                    'color'    => '#dd4b39',
			                                    'url'      => 'indexModalCalendrier.php?case=premptionReserve&id='.$data['idReserveElement'],
			                                ];
				                        }
				                    }
		
			                        if ($lots)
			                        {
			                        	$query = $db->query('SELECT * FROM LOTS_LOTS WHERE idEtat = 1;');
		    	                        while ($data = $query->fetch())
		    	                        {
		    	                        	$events[] = [
		                                        'date'     => date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +'.$data['frequenceInventaire'].' day')),
		                                        'title'    => 'Inventaire à faire',
		                                        'subTitle' => $data['libelleLot'],
		                                        'color'    => '#00c0ef',
		                                        'url'      => 'indexModalCalendrier.php?case=inventaireLot&id='.$data['idLot'],
		                                    ];
		    	                        }
		    	                        $query = $db->query('SELECT * FROM INVENTAIRES i LEFT OUTER JOIN LOTS_LOTS l ON i.idLot = l.idLot WHERE idEtat = 1;');
		    	                        while ($data = $query->fetch())
		    	                        {
		    	                        	$events[] = [
		                                        'date'     => $data['dateInventaire'],
		                                        'title'    => 'Inventaire fait',
		                                        'subTitle' => $data['libelleLot'],
		                                        'color'    => '#00c0ef',
		                                        'url'      => 'indexModalCalendrier.php?case=inventaireLot&id='.$data['idLot'],
		                                    ];
		    	                        }
		        	            	}
		
		        	            	if ($commandes)
		        	            	{
		        	            		$query = $db->query('SELECT * FROM COMMANDES WHERE idEtat = 4 AND dateLivraisonPrevue IS NOT NULL;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date_format(date_create($data['dateLivraisonPrevue']), 'Y-m-d'),
			                                    'title'    => 'Livraison commande',
			                                    'subTitle' => $data['idCommande'],
			                                    'color'    => '#00a65a',
			                                    'url'      => 'indexModalCalendrier.php?case=commandes&id='.$data['idCommande'],
			                                ];
				                        }
				                    }
		
			                        if ($vehicules)
			                        {
				                        $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND dateNextRevision IS NOT NULL;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date_format(date_create($data['dateNextRevision']), 'Y-m-d'),
			                                    'title'    => 'Révision',
			                                    'subTitle' => $data['libelleVehicule'],
			                                    'color'    => '#f39c12',
			                                    'url'      => 'indexModalCalendrier.php?case=vehiculesRev&id='.$data['idVehicule'],
			                                ];
				                        }
			                        }
		
			                        if ($vehicules)
			                        {
				                        $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND dateNextCT IS NOT NULL;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date_format(date_create($data['dateNextCT']), 'Y-m-d'),
			                                    'title'    => 'CT',
			                                    'subTitle' => $data['libelleVehicule'],
			                                    'color'    => '#f39c12',
			                                    'url'      => 'indexModalCalendrier.php?case=vehiculesCT&id='.$data['idVehicule'],
			                                ];
				                        }
			                        }
		
			                        if ($vehicules)
			                        {
				                        $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND assuranceExpiration IS NOT NULL;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date_format(date_create($data['assuranceExpiration']), 'Y-m-d'),
			                                    'title'    => 'Assurance',
			                                    'subTitle' => $data['libelleVehicule'],
			                                    'color'    => '#f39c12',
			                                    'url'      => 'indexModalCalendrier.php?case=vehiculesAssu&id='.$data['idVehicule'],
			                                ];
				                        }
			                        }

			                        if ($vehicules)
			                        {
				                        $query = $db->query('SELECT * FROM VEHICULES_MAINTENANCE m LEFT OUTER JOIN VEHICULES v on m.idVehicule = v.idVehicule WHERE idEtat = 1;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date_format(date_create($data['dateMaintenance']), 'Y-m-d'),
			                                    'title'    => 'Maintenance de',
			                                    'subTitle' => $data['libelleVehicule'],
			                                    'color'    => '#f39c12',
			                                    'url'      => 'indexModalCalendrier.php?case=vehiculesMaintenance&id='.$data['idMaintenance'],
			                                ];
				                        }
			                        }
			                        
			                        if ($desinfections)
			                        {
				                        $query = $db->query('
				                        	SELECT
	                                            *
	                                        FROM
	                                            VEHICULES_DESINFECTIONS d
	                                            LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
				                        ;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date_format(date_create($data['dateDesinfection']), 'Y-m-d'),
			                                    'title'    => 'Désinfection faite',
			                                    'subTitle' => $data['libelleVehicule'],
			                                    'color'    => '#f39c12',
			                                    'url'      => 'indexModalCalendrier.php?case=vehiculesDesinfectionFaite&id='.$data['idVehiculesDesinfection'],
			                                ];
				                        }
			                        }
			                        
			                        if ($desinfections)
			                        {
				                        $query = $db->query('
				                        	SELECT
	                                            a.*,
	                                            vv.libelleVehicule,
	                                            t.libelleVehiculesDesinfectionsType,
	                                            MAX(v.dateDesinfection) as dateDesinfection
	                                        FROM
	                                            VEHICULES_DESINFECTIONS_ALERTES a
	                                            LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON a.idVehiculesDesinfectionsType=t.idVehiculesDesinfectionsType
	                                            LEFT OUTER JOIN VEHICULES_DESINFECTIONS v ON a.idVehiculesDesinfectionsType = v.idVehiculesDesinfectionsType AND a.idVehicule=v.idVehicule
	                                            LEFT OUTER JOIN VEHICULES vv ON a.idVehicule = vv.idVehicule
	                                        GROUP BY
	                                            a.idDesinfectionsAlerte
				                        ;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date('Y-m-d', strtotime($data['dateDesinfection']. ' + '.$data['frequenceDesinfection'].' days')),
			                                    'title'    => 'Désinfection à faire',
			                                    'subTitle' => $data['libelleVehicule'],
			                                    'color'    => '#f39c12',
			                                    'url'      => 'indexModalCalendrier.php?case=vehiculesDesinfectionAFaire&id='.$data['idDesinfectionsAlerte'],
			                                ];
				                        }
			                        }
		
			                        if ($reserves)
			                        {
				                        $query = $db->query('SELECT * FROM RESERVES_CONTENEUR;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +'.$data['frequenceInventaire'].' day')),
			                                    'title'    => 'Inventaire à faire',
			                                    'subTitle' => $data['libelleConteneur'],
			                                    'color'    => '#3c8dbc',
			                                    'url'      => 'indexModalCalendrier.php?case=inventaireReserve&id='.$data['idConteneur'],
			                                ];
				                        }
				                        $query = $db->query('SELECT * FROM RESERVES_INVENTAIRES i LEFT OUTER JOIN RESERVES_CONTENEUR c ON i.idConteneur = c.idConteneur;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => $data['dateInventaire'],
			                                    'title'    => 'Inventaire fait',
			                                    'subTitle' => $data['libelleConteneur'],
			                                    'color'    => '#3c8dbc',
			                                    'url'      => 'indexModalCalendrier.php?case=inventaireReserve&id='.$data['idConteneur'],
			                                ];
				                        }
			                        }
			                        
			                        if ($tenues)
			                        {
				                        $query = $db->query('SELECT * FROM TENUES_AFFECTATION ta JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne WHERE dateRetour < CURRENT_DATE OR dateRetour = CURRENT_DATE;');
				                        while ($data = $query->fetch())
				                        {
				                        	$events[] = [
			                                    'date'     => date_format(date_create($data['dateRetour']), 'Y-m-d'),
			                                    'title'    => 'Tenues',
			                                    'subTitle' => $data['nomPersonne'] . ' ' . $data['prenomPersonne'] . $data['personneNonGPM'] . ' - ' . $data['libelleCatalogueTenue'],
			                                    'color'    => '#00a65a',
			                                    'url'      => 'indexModalCalendrier.php?case=tenuesRecup&id='.$data['idTenue'],
			                                ];
				                        }
			                        }
		
		                         	$query = $db->prepare('SELECT * FROM TODOLIST_PERSONNES tp LEFT OUTER JOIN TODOLIST t ON tp.idTache=t.idTache WHERE idExecutant = :idExecutant AND realisee=0;');
		                         	$query->execute(array('idExecutant'=>$_SESSION['idPersonne']));
			                        while ($data = $query->fetch())
			                        {
			                        	$events[] = [
		                                    'date'     => date_format(date_create($data['dateExecution']), 'Y-m-d'),
		                                    'title'    => 'ToDoList',
		                                    'subTitle' => $data['titre'],
		                                    'color'    => '#3c8dbc',
		                                    'url'      => 'todolistForm.php?id='.$data['idTache'],
		                                ];
			                        }
			                        
		            			?>
		                        <div id="calendar"></div>
			            	</div>
			            </div>
			        </div>
			    </div>
			</div>
			
			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        		<ul class="timeline">
					<li class="time-label">
						<span class="bg-blue">
							CheckList du parc matériel
						</span>
					</li>
					
					<?php if($_SESSION['conf_indicateur1Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE) AND idEtat = 1;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlertePeremption">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Péremption dans les lots: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur2Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteQuantité">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Matériel manquant dans les lots: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur3Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE idEtat = 1 AND (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteInventaire">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Inventaires de lots à faire: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur4Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE alerteConfRef = 1 AND idEtat = 1;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteConformite">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Lots non-conformes à leur référentiel: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur5Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE peremptionNotificationReserve < CURRENT_DATE OR peremptionNotificationReserve = CURRENT_DATE;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlertePeremptionReserve">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Péremption dans la réserve: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur6Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteQuantitéReserve">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Matériel manquant dans la réserve: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur7Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND (assuranceExpiration IS NOT NULL) AND ((assuranceExpiration < CURRENT_DATE) OR (assuranceExpiration = CURRENT_DATE));');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteAssurance">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Assurances périmées: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur8Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND ((dateNextRevision IS NOT NULL) AND ((dateNextRevision < CURRENT_DATE) OR (dateNextRevision = CURRENT_DATE))OR((dateNextCT IS NOT NULL) AND ((dateNextCT < CURRENT_DATE) OR (dateNextCT = CURRENT_DATE))));');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteCT">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Révisions et CT: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>

					<?php if($_SESSION['conf_indicateur11Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND alerteDesinfection = 1;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteDesinfections">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Désinfections de véhicules: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur9Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM TENUES_CATALOGUE WHERE stockCatalogueTenue < stockAlerteCatalogueTenue OR stockCatalogueTenue = stockAlerteCatalogueTenue;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteTenuesStock">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Tenues manquantes dans le stock: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					<?php if($_SESSION['conf_indicateur10Accueil']==1){ ?>
						<li>
							<?php
								$query = $db->query('SELECT COUNT(*) as nb FROM TENUES_AFFECTATION WHERE dateRetour < CURRENT_DATE OR dateRetour = CURRENT_DATE;');
			                	$data = $query->fetch();
							?>
							<?php
								if ($data['nb']>0)
								{
									echo '<i class="fa bg-red"></i>';
									echo '<i class="fa fa-warning bg-red faa-flash animated"></i>';
								}
								else
								{
									echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item">
								<?php
									if ($data['nb']>0)
									{
										echo '<span class="time"><a data-toggle="modal" data-target="#modalAccueilAlerteTenuesRetours">Voir le détail</a></span>';
									}
								?>
								<h3 class="timeline-header no-border">Tenues en attente de retour: <?= $data['nb'] ?></h3>
							</div>
						</li>
					<?php } ?>
					
					
					
		            <li>
						<i class="fa fa-sliders bg-gray"></i>
		            </li>
				</ul>
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="plugins/fullcalendar/fr.js"></script>

<script>
    $(function () {
        var events = <?= json_encode($events) ?>;

        var calendarEvents = [];
        $.each(events, function () {
            var dateParts = this.date.split('-');

            calendarEvents.push({
                title: this.title + ' : ' + this.subTitle,
                start: new Date(dateParts[0], parseInt(dateParts[1]) - 1, dateParts[2]),
                backgroundColor: this.color,
                borderColor: this.color,
                allDay: true,
                url: this.url,
            })
        });

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            lang: 'fr',

            eventRender: function(event, element) {
			    if ($(element).is('a')) {
				    $(element).addClass('modal-form');
				}
			},

            events: calendarEvents
        });
    });
</script>
</body>

<div class="modal fade modal-danger" id="modalAccueilAlertePeremption">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de péremption (lots)</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE) AND idEtat = 1;');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleMateriel'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['materiel_lecture']==1){ ?><a href="materiels.php"><button type="button" class="btn btn-default pull-right">Accéder aux matériels</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteQuantité">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de quantité (lots)</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleMateriel'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['materiel_lecture']==1){ ?><a href="materiels.php"><button type="button" class="btn btn-default pull-right">Accéder aux matériels</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteInventaire">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes d'inventaires (lots)</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM LOTS_LOTS WHERE idEtat = 1 AND dateDernierInventaire IS NOT NULL AND frequenceInventaire != 0 AND CURRENT_DATE >= DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY);');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleLot'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['lots_lecture']==1){ ?><a href="lots.php"><button type="button" class="btn btn-default pull-right">Accéder aux lots</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteConformite">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de conformité (lots)</h4>
            </div>
            <div class="modal-body">
                <ul>
                    <?php
                    $query = $db->query('SELECT * FROM LOTS_LOTS WHERE alerteConfRef = 1 AND idEtat = 1;');
                    while ($data = $query->fetch())
                    {
                        echo '<li>' . $data['libelleLot'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['lots_lecture']==1){ ?><a href="lots.php"><button type="button" class="btn btn-default pull-right">Accéder aux lots</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlertePeremptionReserve">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de péremption (réserve)</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE peremptionNotificationReserve < CURRENT_DATE OR peremptionNotificationReserve = CURRENT_DATE;');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleMateriel'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['reserve_lecture']==1){ ?><a href="reserveMateriel.php"><button type="button" class="btn btn-default pull-right">Accéder à la réserve</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteQuantitéReserve">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de quantité (réserve)</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleMateriel'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['reserve_lecture']==1){ ?><a href="reserveMateriel.php"><button type="button" class="btn btn-default pull-right">Accéder à la réserve</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteAssurance">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de fin d'assurance</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND (assuranceExpiration IS NOT NULL) AND ((assuranceExpiration < CURRENT_DATE) OR (assuranceExpiration = CURRENT_DATE));');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleVehicule'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['vehicules_lecture']==1){ ?><a href="vehicules.php"><button type="button" class="btn btn-default pull-right">Accéder aux véhicules</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteCT">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de révisions / Contrôle technique</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND ((dateNextRevision IS NOT NULL) AND ((dateNextRevision < CURRENT_DATE) OR (dateNextRevision = CURRENT_DATE))OR((dateNextCT IS NOT NULL) AND ((dateNextCT < CURRENT_DATE) OR (dateNextCT = CURRENT_DATE))));');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleVehicule'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['vehicules_lecture']==1){ ?><a href="vehicules.php"><button type="button" class="btn btn-default pull-right">Accéder aux véhicules</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteDesinfections">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes des désinfections des véhicules</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND alerteDesinfection = 1;');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleVehicule'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['vehicules_lecture']==1){ ?><a href="vehicules.php"><button type="button" class="btn btn-default pull-right">Accéder aux véhicules</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteTenuesStock">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tenues manquantes dans le stock</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM TENUES_CATALOGUE WHERE stockCatalogueTenue < stockAlerteCatalogueTenue OR stockCatalogueTenue = stockAlerteCatalogueTenue;');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['libelleCatalogueTenue'] . ' (' . $data['tailleCatalogueTenue'] . ')</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['tenues_lecture']==1){ ?><a href="tenuesCatalogue.php"><button type="button" class="btn btn-default pull-right">Accéder aux tenues</button></a><? } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalAccueilAlerteTenuesRetours">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tenues non-récupérées</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->query('SELECT * FROM TENUES_AFFECTATION ta JOIN TENUES_CATALOGUE tc ON ta.idCatalogueTenue = tc.idCatalogueTenue LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne WHERE dateRetour < CURRENT_DATE OR dateRetour = CURRENT_DATE;');
                ?>
                <ul>
                    <?php
                    while($data=$query->fetch())
                    {
                        echo '<li>' . $data['nomPersonne'] . ' ' . $data['prenomPersonne'] . $data['personneNonGPM'] . ' - ' . $data['libelleCatalogueTenue'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <?php if ($_SESSION['tenues_lecture']==1){ ?><a href="tenuesAffectations.php"><button type="button" class="btn btn-default pull-right">Accéder aux affectations</button></a><? } ?>
            </div>
        </div>
    </div>
</div>

</html>
