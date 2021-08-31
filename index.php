<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 000;
require_once('logCheck.php');
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
        <?php include('confirmationBox.php'); ?>
        <div class="row">

            <?php
            
            $query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE alerteConfRef = 1 AND idEtat = 1;');
            $data = $query->fetch();
            $nbLotsNOK = $data['nb'];
            ?>
			
			<?php
				if($_SESSION['conf_indicateur1Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <!-- small box -->
	                <?php
	                $query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremption < CURRENT_DATE OR peremption = CURRENT_DATE) AND idEtat = 1;');
	                $data = $query->fetch();
	
	                if ($data['nb']>0)
	                { ?>
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlertePeremption"><span class="info-box-icon bg-red"><i class="fa fa-stethoscope faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Péremptions:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-stethoscope"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Péremptions:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                $query->closeCursor(); ?>
	            </div>
	        <?php } ?>
	        
	        <?php
	        	if($_SESSION['conf_indicateur2Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <!-- small box -->
	                <?php
	                $query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
	                $data = $query->fetch();
	
	                if ($data['nb']>0)
	                { ?>
	
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlerteQuantité"><span class="info-box-icon bg-red"><i class="fa fa-stethoscope faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Quantités:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-stethoscope"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Quantités:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                $query->closeCursor(); ?>
	            </div>
            <?php } ?>

			<?php
				if($_SESSION['conf_indicateur3Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <!-- small box -->
	                <?php
	                $query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE idEtat = 1 AND (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
	                $data = $query->fetch();
	
	                if ($data['nb']>0)
	                { ?>
	
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlerteInventaire"><span class="info-box-icon bg-red"><i class="fa fa-code-fork faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Inventaires:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-code-fork"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Inventaires:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                $query->closeCursor(); ?>
	            </div>
            <?php } ?>
            <!-- ./col -->

			<?php
				if($_SESSION['conf_indicateur4Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <?php
	                if ($nbLotsNOK>0)
	                { ?>
	
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlerteConformite"><span class="info-box-icon bg-red"><i class="fa fa-bank faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Référentiels:</span>
	                            <span class="info-box-number"><?php echo $nbLotsNOK; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-bank"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Référentiels:</span>
	                            <span class="info-box-number"><?php echo $nbLotsNOK; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php } ?>
	            </div>
            <?php } ?>
            
            <?php
				if($_SESSION['conf_indicateur5Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <!-- small box -->
	                <?php
	                $query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE peremptionReserve < CURRENT_DATE OR peremptionReserve = CURRENT_DATE;');
	                $data = $query->fetch();
	
	                if ($data['nb']>0)
	                { ?>
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlertePeremptionReserve"><span class="info-box-icon bg-red"><i class="fa fa-archive faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Péremption:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-archive"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Péremption:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                $query->closeCursor(); ?>
	            </div>
            <?php } ?>
            
            
            <?php
				if($_SESSION['conf_indicateur6Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <!-- small box -->
	                <?php
	                $query = $db->query('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve;');
	                $data = $query->fetch();
	
	                if ($data['nb']>0)
	                { ?>
	
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlerteQuantitéReserve"><span class="info-box-icon bg-red"><i class="fa fa-archive faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Quantités:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-archive"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Quantités:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                $query->closeCursor(); ?>
	            </div>
            <?php } ?>
            
            <?php
				if($_SESSION['conf_indicateur7Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <!-- small box -->
	                <?php
	                $query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND (assuranceExpiration IS NOT NULL) AND ((assuranceExpiration < CURRENT_DATE) OR (assuranceExpiration = CURRENT_DATE));');
	                $data = $query->fetch();
	
	                if ($data['nb']>0)
	                { ?>
	
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlerteAssurance"><span class="info-box-icon bg-red"><i class="fa fa-ambulance faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Assurances:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-ambulance"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Assurances:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                $query->closeCursor(); ?>
	            </div>
            <?php } ?>
            
            <?php
				if($_SESSION['conf_indicateur8Accueil']==1)
				{ ?>
	            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	                <!-- small box -->
	                <?php
	                $query = $db->query('SELECT COUNT(*) as nb FROM VEHICULES WHERE idEtat = 1 AND ((dateNextRevision IS NOT NULL) AND ((dateNextRevision < CURRENT_DATE) OR (dateNextRevision = CURRENT_DATE))OR((dateNextCT IS NOT NULL) AND ((dateNextCT < CURRENT_DATE) OR (dateNextCT = CURRENT_DATE))));');
	                $data = $query->fetch();
	
	                if ($data['nb']>0)
	                { ?>
	
	                    <div class="info-box">
	                        <a data-toggle="modal" data-target="#modalAccueilAlerteCT"><span class="info-box-icon bg-red"><i class="fa fa-ambulance faa-flash animated"></i></span></a>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Révisions / CT:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                            <span class="info-box-more">Cliquer sur l'icone</span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                else
	                { ?>
	                    <div class="info-box">
	                        <span class="info-box-icon bg-green"><i class="fa fa-ambulance"></i></span>
	
	                        <div class="info-box-content">
	                            <span class="info-box-text">Révisions / CT:</span>
	                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
	                        </div>
	                        <!-- /.info-box-content -->
	                    </div>
	                <?php }
	                $query->closeCursor(); ?>
	            </div>
            <?php } ?>
            
        </div>

		<div class="row">	
	        <div class="col-md-12">
	            <div class="box box-success">
	                <div class="box-header with-border">
	                    <i class="fa fa-ambulance"></i>
	
	                    <h3 class="box-title">Lots dont j'ai la charge</h3>
	                </div>
	
	                <!-- /.box-header -->
	                <div class="box-body">
	                    <table class="table table-bordered">
	                        <tr>
	                            <th>Libelle</th>
	                            <th>Etat</th>
	                            <th>Référentiel</th>
	                            <th>Prochain inventaire</th>
	                            <th></th>
	                        </tr>
	                        <?php
	                        $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne WHERE l.idPersonne = :idPersonne;');
	                        $query->execute(array('idPersonne' => $_SESSION['idPersonne']));
	                        while ($data = $query->fetch())
	                        {
	                            ?>
	                            <tr>
	                                <td><?php echo $data['libelleLot']; ?></td>
	                                <td><?php echo $data['libelleEtat']; ?></td>
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
	                                <td>
		                                <?php if ($_SESSION['lots_lecture']==1) {?>
	                                        <a href="lotsContenu.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
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
	        <div class="col-md-4">
	            <div class="box box-success">
	
	                <div class="box-header with-border">
	                    <i class="fa fa-bullhorn"></i>
	                    <h3 class="box-title">Messages généraux</h3>
	                    <div class="box-tools pull-right">
	                    	<?php if ($_SESSION['messages_ajout']==1) {?><a href="messagesForm.php" class="btn btn-sm modal-form"><i class="fa fa-plus"></i></a><?php } ?>
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
	                            echo '<div class="callout callout-info">';
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
	        <div class="col-md-8">
	            <div class="box box-success">
	            	<div class="box-body">
	            		<?php
	            			$events = [];

	            			$query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE peremptionNotification IS NOT NULL AND idEtat = 1;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => $data['peremption'],
                                    'title'    => 'Peremption',
                                    'subTitle' => $data['libelleLot'] . ' > ' . $data['libelleSac'] . ' > ' . $data['libelleEmplacement'] . ' > ' . $data['libelleMateriel'],
                                    'color'    => '#dd4b39',
                                ];
	                        }

	                        $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR c ON m.idConteneur=c.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE r ON m.idMaterielCatalogue = r.idMaterielCatalogue WHERE peremptionReserve IS NOT NULL;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => $data['peremptionReserve'],
                                    'title'    => 'Peremption',
                                    'subTitle' => $data['libelleConteneur'] . ' > ' . $data['libelleMateriel'],
                                    'color'    => '#dd4b39',
                                ];
	                        }

	                        $query = $db->query('SELECT * FROM LOTS_LOTS WHERE idEtat = 1;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +'.$data['frequenceInventaire'].' day')),
                                    'title'    => 'Inventaire',
                                    'subTitle' => $data['libelleLot'],
                                    'color'    => '#00c0ef',
                                ];
	                        }

	                        $query = $db->query('SELECT * FROM COMMANDES WHERE idEtat = 4 AND dateLivraisonPrevue IS NOT NULL;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => date_format(date_create($data['dateLivraisonPrevue']), 'Y-m-d'),
                                    'title'    => 'Livraison commande',
                                    'subTitle' => $data['idCommande'],
                                    'color'    => '#00a65a',
                                ];
	                        }

	                        $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND dateNextRevision IS NOT NULL;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => date_format(date_create($data['dateNextRevision']), 'Y-m-d'),
                                    'title'    => 'Révision',
                                    'subTitle' => $data['libelleVehicule'],
                                    'color'    => '#f39c12',
                                ];
	                        }

	                        $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND dateNextCT IS NOT NULL;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => date_format(date_create($data['dateNextCT']), 'Y-m-d'),
                                    'title'    => 'CT',
                                    'subTitle' => $data['libelleVehicule'],
                                    'color'    => '#f39c12',
                                ];
	                        }

	                        $query = $db->query('SELECT * FROM VEHICULES WHERE idEtat = 1 AND assuranceExpiration IS NOT NULL;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => date_format(date_create($data['assuranceExpiration']), 'Y-m-d'),
                                    'title'    => 'Assurance',
                                    'subTitle' => $data['libelleVehicule'],
                                    'color'    => '#f39c12',
                                ];
	                        }

	                        $query = $db->query('SELECT * FROM RESERVES_CONTENEUR;');
	                        while ($data = $query->fetch())
	                        {
	                        	$events[] = [
                                    'date'     => date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +'.$data['frequenceInventaire'].' day')),
                                    'title'    => 'Inventaire',
                                    'subTitle' => $data['libelleConteneur'],
                                    'color'    => '#3c8dbc',
                                ];
	                        }
            			?>
                        <div id="calendar"></div>
	            	</div>
	            </div>
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
        console.log("test");
        var events = <?= json_encode($events) ?>;

        var calendarEvents = [];
        $.each(events, function () {
            var dateParts = this.date.split('-');

            calendarEvents.push({
                title: this.title + ' : ' + this.subTitle,
                start: new Date(dateParts[0], parseInt(dateParts[1]) - 1, dateParts[2]),
                backgroundColor: this.color,
                borderColor: this.color,
                allDay: true
            })
        });

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            lang: 'fr',
            //Random default events
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
                $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremption < CURRENT_DATE OR peremption = CURRENT_DATE) AND idEtat = 1;');
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
                $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE peremptionReserve < CURRENT_DATE OR peremptionReserve = CURRENT_DATE;');
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
                <?php if ($_SESSION['reserve_lecture']==1){ ?><a href="vehicules.php"><button type="button" class="btn btn-default pull-right">Accéder aux véhicules</button></a><? } ?>
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
                <?php if ($_SESSION['reserve_lecture']==1){ ?><a href="vehicules.php"><button type="button" class="btn btn-default pull-right">Accéder aux véhicules</button></a><? } ?>
            </div>
        </div>
    </div>
</div>

</html>
