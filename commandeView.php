<!DOCTYPE html>
<html>
<?php include('header.php');?>
<?php
session_start();
$_SESSION['page'] = 601;
require_once('logCheck.php');
require_once('config/config.php');
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
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE idCommande=:idCommande;');
    $query->execute(array('idCommande' => $_GET['id']));
    $data = $query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php if($data['idEtat']<3){echo 'DEMANDE D\'ACHAT';}else{echo 'COMMANDE';} echo ' '.$_GET['id'].' - '.$data['libelleEtat']; if($data['nomCommande']!='' AND $data['nomCommande']!=Null){echo ' - '.$data['nomCommande'];}?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="commandesNonCloses.php">Commandes</a></li>
                <li class="active"><?php if($data['idEtat']<3){echo 'DEMANDE D\'ACHAT';}else{echo 'COMMANDE';} echo ' '.$_GET['id']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <?php
            	$nbArticles = $db->prepare('SELECT COUNT(*) AS nb FROM COMMANDES_MATERIEL WHERE idCommande = :idCommande;');
            	$nbArticles->execute(array('idCommande'=>$_GET['id']));
            	$nbArticles = $nbArticles->fetch();
            	$nbArticles = $nbArticles['nb'];

            	if($nbArticles == 0)
            	{ ?>
            		<div class="alert alert-warning">
	                    <i class="icon fa fa-warning"></i> Attention cette commande ne contient aucun article
	                </div>
            	<?php }
            ?>
			<div class="row">
	            <div class="col-md-3">
	                <ul class="timeline">
			            <li class="time-label"><span class="bg-blue">Processus</span></li>
			            <li>
							<?php
								switch($data['idEtat'])
								{
									case 1:
										echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
									break;

									case 8:
										echo '<i class="fa fa-close bg-red"></i>';
									break;

									default:
										echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item"><h3 class="timeline-header no-border">Création de la commande</h3></div>
			            </li>
			            <li>
							<?php
								switch($data['idEtat'])
								{
									case 2:
										echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
									break;

									case 1:
										echo '<i class="fa fa-hourglass-half bg-gray"></i>';
									break;

									case 8:
										echo '<i class="fa fa-close bg-red"></i>';
									break;

									default:
										echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item"><h3 class="timeline-header no-border"><?php if($data['idEtat']>=3){echo 'Validée';}else{echo 'En attente de validation';}?></h3></div>
			            </li>
			            <li>
							<?php
								switch($data['idEtat'])
								{
									case 3:
										echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
									break;

									case 1:
									case 2:
										echo '<i class="fa fa-hourglass-half bg-gray"></i>';
									break;

									case 8:
										echo '<i class="fa fa-close bg-red"></i>';
									break;

									default:
										echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item"><h3 class="timeline-header no-border"><?php if($data['idEtat']>=4){echo 'Passée chez le fournisseur';}else{echo 'Doit être passée chez le fournisseur';}?></h3></div>
			            </li>
			            <li>
							<?php
								switch($data['idEtat'])
								{
									case 4:
										echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
									break;

									case 1:
									case 2:
									case 3:
									case 4:
										echo '<i class="fa fa-hourglass-half bg-gray"></i>';
									break;

									case 6:
									case 8:
										echo '<i class="fa fa-close bg-red"></i>';
									break;

									default:
										echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item"><h3 class="timeline-header no-border"><?php if($data['idEtat']>=5){echo 'Livrée';}else{echo 'En attente de livraison';}?></h3></div>
			            </li>
			            <?php
			            	if($data['savHistorique']==1)
			            	{ ?>
					            <li>
									<?php
										switch($data['idEtat'])
										{
											case 6:
												echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
											break;

											case 1:
											case 2:
											case 3:
											case 4:
												echo '<i class="fa fa-hourglass-half bg-gray"></i>';
											break;

											case 8:
												echo '<i class="fa fa-close bg-red"></i>';
											break;

											default:
												echo '<i class="fa fa-check bg-green"></i>';
										}
									?>
									<div class="timeline-item"><h3 class="timeline-header no-border"><?php if($data['idEtat']==6){echo 'SAV en cours';}else{echo 'SAV terminé';}?></h3></h3></div>
					            </li>
					        <?php } ?>
			            <li>
							<?php
								switch($data['idEtat'])
								{
									case 5:
										echo '<i class="fa fa-spinner fa-spin bg-blue"></i>';
									break;

									case 1:
									case 2:
									case 3:
									case 4:
									case 5:
									case 6:
										echo '<i class="fa fa-hourglass-half bg-gray"></i>';
									break;

									case 8:
										echo '<i class="fa fa-close bg-red"></i>';
									break;

									default:
										echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item"><h3 class="timeline-header no-border">Intégration dans le stock</h3></div>
			            </li>
			            <li>
							<?php
								switch($data['idEtat'])
								{
									case 1:
									case 2:
									case 3:
									case 4:
									case 5:
									case 6:
										echo '<i class="fa fa-hourglass-half bg-gray"></i>';
									break;

									case 8:
										echo '<i class="fa fa-close bg-red"></i>';
									break;

									default:
										echo '<i class="fa fa-check bg-green"></i>';
								}
							?>
							<div class="timeline-item"><h3 class="timeline-header no-border">Commande clôturée</h3></div>
			            </li>
			            <?php
			            	if($data['idEtat']!=7)
			            	{ ?>
				            <li>
								<?php
									switch($data['idEtat'])
									{
										case 1:
										case 2:
										case 3:
										case 4:
										case 5:
										case 6:
										case 7:
											echo '<i class="fa fa-hourglass-half bg-gray"></i>';
										break;

										default:
											echo '<i class="fa fa-check bg-green"></i>';
									}
								?>
								<div class="timeline-item"><h3 class="timeline-header no-border">Commande abandonnée</h3></div>
				            </li>
				        	<?php } ?>
			            <li>
			              <i class="fa fa-clock-o bg-gray"></i>
			            </li>
			          </ul>
	            </div>

	            <div class="col-md-9">
	                <div class="nav-tabs-custom">
	                    <ul class="nav nav-tabs">
	                        <li class="active"><a href="#general" data-toggle="tab">Informations générales</a></li>
	                        <li><a href="#contenu" data-toggle="tab">Contenu</a></li>
	                        <li><a href="#pj" data-toggle="tab">Pièces jointes</a></li>
	                        <?php if ($data['idEtat']>1) { ?><li><a href="#validation" data-toggle="tab">Validation</a></li> <?php } ?>
	                        <?php if ($data['idEtat']>2) { ?><li><a href="#fournisseur" data-toggle="tab">Passage de la commande</a></li> <?php } ?>
	                        <?php if ($data['idEtat']>3) { ?><li><a href="#livraison" data-toggle="tab">Livraison</a></li> <?php } ?>
	                        <?php if ($data['idEtat']>4 AND $_SESSION['codeBarre_lecture']) { ?><li><a href="#codebarre" data-toggle="tab">Vérification des codes barre</a></li> <?php } ?>
	                        <li class="pull-right"><a href="#timeline" data-toggle="tab">Historique</a></li>
	                    </ul>
	                    <div class="tab-content">
	
	                        <div class="active tab-pane" id="general">
	                            <form role="form" class="spinnerAttenteSubmit" action="commandesUpdate.php?id=<?php echo $_GET['id'];?>" method="POST">
	                                <div class="row">
	                                	<div class="col-md-12">
	                                		<div class="form-group">
				                                <label>Nom de la commande:</label>
				                                <input <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> type="text" class="form-control" name="nomCommande" value="<?php echo $data['nomCommande']; ?>">
				                            </div>
				                        </div>
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Fournisseur: </label>
	                                            <?php if($data['siteWebFournisseur'] != Null AND $data['siteWebFournisseur'] != ''){ ?><a href="<?=$data['siteWebFournisseur']?>" title="Aller sur le site du fournisseur" target="_blank"><label class="pull-right"><i class="fa fa-internet-explorer"></i></label></a><?php } ?>
	                                            <select class="form-control select2" style="width: 100%;" <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> name="idFournisseur">
	                                                <option value="">--- Non-spécifié - Obligatoire pour valider la commande ---</option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if ($data['idFournisseur'] == $data2['idFournisseur']) { echo 'selected'; } ?>><?php echo $data2['nomFournisseur']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Etat: </label>
	                                            <select disabled class="form-control" name="idEtat">
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM COMMANDES_ETATS;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idEtat']; ?>" <?php if ($data['idEtat'] == $data2['idEtat']) { echo 'selected'; } ?> ><?php echo $data2['idEtat'].' - '.$data2['libelleEtat']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="row">
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Demandeur: </label>
	                                            <select class="form-control select2" style="width: 100%;" <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> name="idDemandeur[]" multiple>
	                                                <?php
	                                                if($data['idEtat']<3)
	                                                {
		                                                $query2 = $db->prepare('
		                                                	SELECT
		                                                		ao.*,
		                                                		(SELECT idCommande FROM COMMANDES_DEMANDEURS aop WHERE ao.idPersonne = aop.idDemandeur AND aop.idCommande = :idCommande) as idCommande
		                                                	FROM
		                                                		PERSONNE_REFERENTE ao
		                                                		JOIN VIEW_HABILITATIONS h ON ao.idPersonne = h.idPersonne
		                                                		
		                                                	WHERE
		                                                		commande_lecture = 1
		                                                	ORDER BY
		                                                		identifiant;');
										                $query2->execute(array('idCommande' => $_GET['id']));
						
						                                while ($data2 = $query2->fetch())
						                                {
						                                    
						                                    echo '<option value=' . $data2['idPersonne'];
						
											                if (isset($data2['idCommande']) AND $data2['idCommande'])
											                {
											                    echo " selected ";
											                }
											                echo '>' . $data2['identifiant'] . '</option>';
						                                }
	                                                }
	                                                else
	                                                {
	                                                	$query2 = $db->prepare('SELECT * FROM COMMANDES_DEMANDEURS o JOIN PERSONNE_REFERENTE p ON o.idDemandeur = p.idPersonne WHERE idCommande = :idCommande;');
	                                                	$query2->execute(array('idCommande' => $_GET['id']));
	                                                	while($data2 = $query2->fetch())
	                                                	{
	                                                	?>
	                                                		<option value="<?php echo $data2['idPersonne']; ?>" selected ><?php echo $data2['identifiant']; ?></option>
	                                                	<?php
	                                                	}
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Observateur: </label>
	                                            <select class="form-control select2" style="width: 100%;" <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> name="idObservateur[]" multiple>
	                                                <?php
	                                                if($data['idEtat']<3)
	                                                {
		                                                $query2 = $db->prepare('
		                                                	SELECT
		                                                		ao.*,
		                                                		(SELECT idCommande FROM COMMANDES_OBSERVATEURS aop WHERE ao.idPersonne = aop.idObservateur AND aop.idCommande = :idCommande) as idCommande
		                                                	FROM
		                                                		PERSONNE_REFERENTE ao
		                                                		JOIN VIEW_HABILITATIONS h ON ao.idPersonne = h.idPersonne
		                                                	WHERE
		                                                		commande_lecture = 1
		                                                	ORDER BY
		                                                		identifiant;');
										                $query2->execute(array('idCommande' => $_GET['id']));
						
						                                while ($data2 = $query2->fetch())
						                                {
						                                    
						                                    echo '<option value=' . $data2['idPersonne'];
						
											                if (isset($data2['idCommande']) AND $data2['idCommande'])
											                {
											                    echo " selected ";
											                }
											                echo '>' . $data2['identifiant'] . '</option>';
						                                }
	                                                }
	                                                else
	                                                {
	                                                	$query2 = $db->prepare('SELECT * FROM COMMANDES_OBSERVATEURS o JOIN PERSONNE_REFERENTE p ON o.idObservateur = p.idPersonne WHERE idCommande = :idCommande;');
	                                                	$query2->execute(array('idCommande' => $_GET['id']));
	                                                	while($data2 = $query2->fetch())
	                                                	{
	                                                	?>
	                                                		<option value="<?php echo $data2['idPersonne']; ?>" selected ><?php echo $data2['identifiant']; ?></option>
	                                                	<?php
	                                                	}
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-6">
	                                    	<div class="form-group">
	                                            <label>Affectation: </label>
	                                            <select class="form-control select2" style="width: 100%;" <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> name="idAffectee[]" multiple>
	                                                <?php
	                                                if($data['idEtat']<3)
	                                                {
		                                                $query2 = $db->prepare('
		                                                	SELECT
		                                                		ao.*,
		                                                		(SELECT idCommande FROM COMMANDES_AFFECTEES aop WHERE ao.idPersonne = aop.idAffectee AND aop.idCommande = :idCommande) as idCommande
	                                                		FROM
	                                                			PERSONNE_REFERENTE ao
	                                                			JOIN VIEW_HABILITATIONS h ON ao.idPersonne = h.idPersonne
	                                                		WHERE
	                                                			commande_lecture = 1
	                                                			AND commande_etreEnCharge = 1
	                                                		ORDER BY
	                                                			identifiant;');
										                $query2->execute(array('idCommande' => $_GET['id']));
						
						                                while ($data2 = $query2->fetch())
						                                {
						                                    
						                                    echo '<option value=' . $data2['idPersonne'];
						
											                if (isset($data2['idCommande']) AND $data2['idCommande'])
											                {
											                    echo " selected ";
											                }
											                echo '>' . $data2['identifiant'] . '</option>';
						                                }
	                                                }
	                                                else
	                                                {
	                                                	$query2 = $db->prepare('SELECT * FROM COMMANDES_AFFECTEES o JOIN PERSONNE_REFERENTE p ON o.idAffectee = p.idPersonne WHERE idCommande = :idCommande;');
	                                                	$query2->execute(array('idCommande' => $_GET['id']));
	                                                	while($data2 = $query2->fetch())
	                                                	{
	                                                	?>
	                                                		<option value="<?php echo $data2['idPersonne']; ?>" selected ><?php echo $data2['identifiant']; ?></option>
	                                                	<?php
	                                                	}
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group">
	                                            <label>Valideurs potentiels de la demande:</label>
	                                            <select class="form-control select2" style="width: 100%;" disabled name="idValideur[]" multiple>
	                                                <?php
	                                                	if($data['idCentreDeCout'] != Null)
	                                                	{
	                                                		$query2 = $db->prepare('SELECT c.idPersonne, p.identifiant FROM CENTRE_COUTS_PERSONNES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE c.idCentreDeCout = :idCentreDeCout;');
	                                                		$query2->execute(array('idCentreDeCout'=>$data['idCentreDeCout']));
	                                                		$qttValideurs = 0;
	                                                		while($data2 = $query2->fetch())
	                                                		{
	                                                			if(cmdEstValideur($data2['idPersonne'], $_GET['id'])==1)
	                                                			{
	                                                				$qttValideurs += 1;
	                                                				?>
	                                                				<option selected ><?php echo $data2['identifiant']; ?></option>
	                                                			<?php }
	                                                		}

	                                                		if($qttValideurs == 0)
	                                                		{
	                                                			$query2 = $db->prepare('SELECT idPersonne, identifiant FROM VIEW_HABILITATIONS WHERE commande_valider_delegate = 1;');
	                                                			$query2->execute(array('idCentreDeCout'=>$data['idCentreDeCout']));
	                                                			while($data2 = $query2->fetch())
	                                                			{ ?>
	                                                				<option selected ><?php echo $data2['identifiant']; ?></option>
	                                                			<?php }
	                                                		}
	                                                	}
	                                                ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="row">
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Centre de cout: </label>
	                                            <label class="pull-right"><?= cmdEtatCentreCouts($_GET['id']); ?></label>
	                                            <select class="form-control select2" style="width: 100%;" <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> name="idCentreDeCout">
	                                                <option value="">--- Non-spécifié ---</option>
	                                                <optgroup label="Ouverts">
	                                                	<?php
		                                                $query2 = $db->query('
		                                                	SELECT
															    *
															FROM
															    CENTRE_COUTS
															WHERE
															    (
															    	dateFermeture IS NULL
															    	AND dateOuverture <= CURRENT_DATE
															    )
															    OR
															    (
															    	dateFermeture >= CURRENT_DATE
															    	AND
															    	dateOuverture <= CURRENT_DATE
															    )
															    OR
															    (
															    	dateOuverture IS NULL
															    	AND dateFermeture IS NULL
															    )
															ORDER BY
																libelleCentreDecout
		                                                ');
		                                                while ($data2 = $query2->fetch())
		                                                {
		                                                    ?>
		                                                    <option value="<?php echo $data2['idCentreDeCout']; ?>" <?php if ($data['idCentreDeCout'] == $data2['idCentreDeCout']) { echo 'selected'; } ?>><?php echo $data2['libelleCentreDecout']; ?></option>
		                                                    <?php
		                                                }
		                                                $query2->closeCursor(); ?>
	                                                </optgroup>
	                                                
	                                                <optgroup label="Ouverture future">
	                                                	<?php
		                                                $query2 = $db->query('
		                                                	SELECT
															    *
															FROM
															    CENTRE_COUTS
															WHERE
															    (
															    	dateFermeture IS NULL
															    	AND dateOuverture > CURRENT_DATE
															    )
															    OR
															    (
															    	dateFermeture >= CURRENT_DATE
															    	AND
															    	dateOuverture > CURRENT_DATE
															    )
															ORDER BY
																libelleCentreDecout
		                                                ');
		                                                while ($data2 = $query2->fetch())
		                                                {
		                                                    ?>
		                                                    <option value="<?php echo $data2['idCentreDeCout']; ?>" <?php if ($data['idCentreDeCout'] == $data2['idCentreDeCout']) { echo 'selected'; } ?>><?php echo $data2['libelleCentreDecout']; ?></option>
		                                                    <?php
		                                                }
		                                                $query2->closeCursor(); ?>
	                                                </optgroup>
	                                                
	                                                <optgroup label="Fermés">
	                                                	<?php
		                                                $query2 = $db->query('
		                                                	SELECT
															    *
															FROM
															    CENTRE_COUTS
															WHERE
															    dateFermeture < CURRENT_DATE
															ORDER BY
																libelleCentreDecout
		                                                ');
		                                                while ($data2 = $query2->fetch())
		                                                {
		                                                    ?>
		                                                    <option value="<?php echo $data2['idCentreDeCout']; ?>" <?php if ($data['idCentreDeCout'] == $data2['idCentreDeCout']) { echo 'selected'; } ?>><?php echo $data2['libelleCentreDecout']; ?></option>
		                                                    <?php
		                                                }
		                                                $query2->closeCursor(); ?>
	                                                </optgroup>
	                                            </select>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-6">
	                                    	<div class="form-group">
	                                            <label>Lieu de livraison: </label>
	                                            <select class="form-control select2" style="width: 100%;" <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> name="idLieuLivraison">
	                                                <option value="">--- Non-spécifié ---</option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM LIEUX;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idLieu']; ?>" <?php if ($data['idLieuLivraison'] == $data2['idLieu']) { echo 'selected'; } ?>><?php echo $data2['libelleLieu']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="row">
	                                    <div class="col-md-6">
	                                        <div class="form-group" id="dateCreation">
	                                            <label>Date de création:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateCreation" value="<?php echo $data['dateCreation']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-6">
	                                        <div class="form-group" id="dateCloture">
	                                            <label>Date de clôture:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateCreation" value="<?php echo $data['dateCloture']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="form-group">
	                                    <label>Remarques:</label>
	                                    <textarea <?php if(($data['idEtat']>2) OR (($_SESSION['commande_ajout']==0) AND ($_SESSION['commande_etreEnCharge']==0))){echo 'disabled';}?> class="form-control" rows="3" name="remarquesGenerales"><?php echo $data['remarquesGenerales']; ?></textarea>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']<7) AND ($_SESSION['commande_abandonner']==1)){ ?><a href="commandesGo8.php?id=<?=$_GET['id']?>" class="btn btn-danger spinnerAttenteClick" onclick="return confirm('Etes-vous sûr de vouloir abandonner la commande (action irreversible) ?');">Abandon de la commande</a> <?php } ?>
	                                    <?php if ($_SESSION['commande_abandonner']==1){ ?><a href="modalDeleteConfirm.php?case=commandesDelete&id=<?=$_GET['id']?>" class="btn btn-danger modal-form">Suppression de la commande</a><?php } ?>
	                                    <?php if (($data['idEtat']==1 OR ($data['idEtat']==2 AND (cmdEstValideur($_SESSION['idPersonne'], $_GET['id'])==1 OR $_SESSION['commande_valider_delegate']))) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))) { ?><button type="submit" class="btn btn-warning pull-right">Modifier</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>
	
	
	                        <div class="tab-pane" id="contenu">
	                            <div class="table-responsive no-padding">
					              <table class="table table-hover">
					                <tr>
					                  <th>Matériel</th>
					                  <th>Référence</th>
					                  <th>Prix unitaire TTC</th>
					                  <th>Quantité</th>
					                  <th>Sous-Total</th>
					                  <th></th>
					                </tr>
					                <?php
                                        $query2 = $db->prepare('SELECT * FROM COMMANDES_MATERIEL c LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue WHERE idCommande = :idCommande ORDER BY libelleMateriel ASC ;');
                                        $query2->execute(array('idCommande' => $_GET['id']));
                                        $totalCMD = 0;
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            	<tr>
								                  <td><?php
								                  	echo $data2['libelleMateriel'];
								                  	if($data2['idFournisseur'] != NULL AND $data2['idFournisseur'] != $data['idFournisseur'])
								                  	{
								                  		echo ' <i class="fa fa-warning" title="Cet article n\'est pas habituellement acheté chez ce fournisseur."></i>';
								                  	}
								                  ?></td>
								                  <td><?php echo $data2['referenceProduitFournisseur'];?></td>
								                  <td><?php echo $data2['prixProduitTTC'];?> €</td>
								                  <td><?php echo $data2['quantiteCommande'];?></td>
								                  <td><?php echo $data2['prixProduitTTC']*$data2['quantiteCommande']; $totalCMD = $totalCMD + ($data2['prixProduitTTC']*$data2['quantiteCommande']);?> €</td>
								                  <td>
								                	<?php if(($data['idEtat']==1) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))){ ?>
								                		<?php if ($data2['idMaterielCatalogue'] != Null){ ?><a href="commandeItemForm.php?idCommande=<?= $_GET['id'] ?>&idElement=<?= $data2['idMaterielCatalogue'] ?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a><?php }else{ ?><a href="#" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a><?php } ?>
								                		<a href="commandeItemDelete.php?idCommande=<?=$data2['idCommande']?>&idMaterielCatalogue=<? if($data2['idMaterielCatalogue'] == Null){echo '-1';}else{echo $data2['idMaterielCatalogue'];}?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');" title="Supprimer"><i class="fa fa-minus"></i></a>
								                	<?php }?>
								                	<?php if(($data['idEtat']>1) AND ($_SESSION['commande_lecture']==1)){ ?>
								                		<a href="commandeItemForm.php?idCommande=<?= $_GET['id'] ?>&idElement=<?= $data2['idMaterielCatalogue'] ?>" class="btn btn-xs btn-info modal-form" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
								                	<?php }?>
								                	<?php if(($data['idEtat']==5) AND ($_SESSION['reserve_cmdVersReserve']==1)){ ?>
                                                        <?php
                                                            if(($data2['idMaterielCatalogue'] != Null) AND ($data2['quantiteAtransferer'] > 0))
                                                            { ?>
                                                                <a href="transfertCmdResFromCmd.php?idCommande=<?= $_GET['id'] ?>&idMaterielCatalogue=<?= $data2['idMaterielCatalogue'] ?>" class="btn btn-xs btn-success modal-form" title="Transférer vers la réserve"><i class="fa fa-exchange"> <?= $data2['quantiteAtransferer'] ?></i></a>
                                                            <?php }else{ ?>
                                                                <a href="#" class="btn btn-xs btn-default modal-form"><i class="fa fa-exchange"></i></a>
                                                            <?php } ?>

								                	<?php }?>
								                  </td>
								                </tr>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>
                                        <tr>
								        	<td></td>
								        	<td></td>
								        	<td></td>
								        	<td><b>Total:</b></td>
								        	<td><b><?php echo $totalCMD;?> €</b></td>
								        	<td></td>
								        <tr>
                                        <?php if(($data['idEtat']==1) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))){ ?>
										        <tr>
										        	<td></td>
										        	<td></td>
										        	<td></td>
										        	<td></td>
										        	<td></td>
										        	<td><a href="commandeItemForm.php?idCommande=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a></td>
										        <tr>
										  <?php }?>
					              </table>
					              
					            </div>
	                        </div>
	                        

	                        <div class="tab-pane" id="pj">
	                            <div class="table-responsive no-padding">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Nom du document</th>
                                            <th>Type de document</th>
                                            <th>Date de chargement</th>
                                            <th>Format</th>
                                            <th></th>
                                        </tr>
                                        <?php
                                        $query2 = $db->prepare('SELECT * FROM VIEW_DOCUMENTS_COMMANDES WHERE idCommande = :idCommande ORDER BY dateDocCommande DESC;');
                                        $query2->execute(array('idCommande' => $_GET['id']));
                                        while ($data2 = $query2->fetch())
                                        {
                                            ?>
                                            <tr>
                                                <td><?php echo $data2['nomDocCommande'];?></td>
                                                <td><?php echo $data2['libelleTypeDocument'];?></td>
                                                <td><?php echo $data2['dateDocCommande'];?></td>
                                                <td><i class="fa <?php echo documentsGetIcone($data2['formatDocCommande']);?>"></i></td>
                                                <td>
                                                    <?php if($_SESSION['commande_lecture']==1){
                                                    		if ($data2['formatDocCommande'] == 'pdf' OR $data2['formatDocCommande'] == 'jpg' OR $data2['formatDocCommande'] == 'jpeg' OR $data2['formatDocCommande'] == 'png'){?>
                                                        		<a href="commandeDocView.php?idDoc=<?=$data2['idDocCommande']?>" class="btn btn-xs btn-info" title="Visualiser"><i class="fa fa-eye"></i></a>
                                                    <?php } else { ?>
                                                    			<a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                    <?php }}?>
                                                    <?php if($_SESSION['commande_lecture']==1){ ?>
                                                        <a href="commandeDocDL.php?idDoc=<?=$data2['idDocCommande']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
                                                    <?php }?>
                                                    <?php if(($data['idEtat']==1) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))){ ?>
                                                        <a href="commandeDocDelete.php?idDoc=<?=$data2['idDocCommande']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');" title="Supprimer"><i class="fa fa-minus"></i></a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $query2->closeCursor(); ?>

                                        <?php if(($data['idEtat']<7) AND (($_SESSION['commande_ajout']==1) OR ($_SESSION['commande_etreEnCharge']==1))){ ?>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><a href="commandeDocForm.php?idCommande=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a></td>
                                            <tr>
                                        <?php }?>
                                    </table>
					            </div>
	                        </div>
	
	
	                        <div class="tab-pane" id="validation">
	                            <form role="form" class="spinnerAttenteSubmit" action="commandesGo13.php?id=<?=$_GET['id']?>" method="POST">
	                                <div class="row">
	                                	<?php
	                                		$query = $db->prepare('SELECT COUNT(*) as nb FROM COMMANDES_MATERIEL cm LEFT OUTER JOIN COMMANDES c ON cm.idCommande = c.idCommande LEFT OUTER JOIN MATERIEL_CATALOGUE mc ON cm.idMaterielCatalogue = mc.idMaterielCatalogue WHERE cm.idCommande = :idCommande AND mc.idFournisseur IS NOT NULL AND mc.idFournisseur <> c.idFournisseur;');
	                                		$query->execute(array('idCommande'=>$_GET['id']));
	                                		$nbErreurFou = $query->fetch();
	                                		if($data['idEtat'] == 2 AND $nbErreurFou['nb']>0)
	                                		{
		                                	?>
			                                	<div class="col-md-12">
			                                		<div class="alert alert-warning">
									                    <i class="icon fa fa-warning"></i> Attention cette commande contient des articles habituellement commandés chez d'autres fournisseurs.
									                </div>
			                                	</div>
		                                <?php } ?>
		                                <?php
	                                		if($data['idEtat'] == 2 AND $data['idFournisseur'] == Null)
	                                		{
		                                	?>
			                                	<div class="col-md-12">
			                                		<div class="alert alert-warning">
									                    <i class="icon fa fa-warning"></i> Aucun fournisseur renseigné pour cette commande.
									                </div>
			                                	</div>
		                                <?php } ?>
		                                <?php
	                                		if($data['idEtat'] == 2 AND cmdEstValideur($_SESSION['idPersonne'], $_GET['id']) == -1)
	                                		{
		                                	?>
			                                	<div class="col-md-12">
			                                		<div class="alert alert-warning">
									                    <i class="icon fa fa-warning"></i> Votre profil ne vous permet pas de valider cette commande (seuil du centre de cout atteint, montant de la commande suppérieur à votre seuil, centre de cout clôs, ...).
									                </div>
			                                	</div>
		                                <?php } ?>
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateDemandeValidation">
	                                            <label>Date de demande:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateDemandeValidation" value="<?php echo $data['dateDemandeValidation']; ?>">
	                                            </div>
	                                        </div>
	
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group">
	                                            <label>Valideurs potentiels:</label>
	                                            <select class="form-control select2" style="width: 100%;" disabled name="idValideur[]" multiple>
	                                                <?php
	                                                	if($data['idCentreDeCout'] != Null)
	                                                	{
	                                                		$query2 = $db->prepare('SELECT c.idPersonne, p.identifiant FROM CENTRE_COUTS_PERSONNES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE c.idCentreDeCout = :idCentreDeCout;');
	                                                		$query2->execute(array('idCentreDeCout'=>$data['idCentreDeCout']));
	                                                		$qttValideurs = 0;
	                                                		while($data2 = $query2->fetch())
	                                                		{
	                                                			if(cmdEstValideur($data2['idPersonne'], $_GET['id'])==1)
	                                                			{
	                                                				$qttValideurs += 1;
	                                                				?>
	                                                				<option selected ><?php echo $data2['identifiant']; ?></option>
	                                                			<?php }
	                                                		}

	                                                		if($qttValideurs == 0)
	                                                		{
	                                                			$query2 = $db->prepare('SELECT idPersonne, identifiant FROM VIEW_HABILITATIONS WHERE commande_valider_delegate = 1;');
	                                                			$query2->execute(array('idCentreDeCout'=>$data['idCentreDeCout']));
	                                                			while($data2 = $query2->fetch())
	                                                			{ ?>
	                                                				<option selected ><?php echo $data2['identifiant']; ?></option>
	                                                			<?php }
	                                                		}
	                                                	}
	                                                ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateValidation">
	                                            <label>Date de validation:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateValidation" value="<?php echo $data['dateValidation']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="form-group">
	                                    <label>Remarques:</label>
	                                    <textarea <?php if(($data['idEtat']>2) OR ((cmdEstValideur($_SESSION['idPersonne'], $_GET['id'])!=1) AND ($_SESSION['commande_valider_delegate']==0))){echo 'disabled';}?> class="form-control" rows="3" name="remarquesValidation" required><?php echo $data['remarquesValidation']; ?></textarea>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']==2) AND cmdEstValideur($_SESSION['idPersonne'], $_GET['id'])==1){ ?><button name="button" value="nok" type="submit" class="btn btn-danger pull-left">Refuser</button> <?php } ?>
	                                    <?php if (($data['idEtat']==2) AND ($_SESSION['commande_valider_delegate']==1) AND cmdEstValideur($_SESSION['idPersonne'], $_GET['id'])!=1){ ?><button name="button" value="nokdelegate" type="submit" class="btn btn-danger pull-left">Refuser en tant que délégué</button> <?php } ?>
	                                    
	                                    <?php if (($data['idEtat']==2) AND ($data['idFournisseur']!=Null) AND cmdEstValideur($_SESSION['idPersonne'], $_GET['id'])==1) { ?><button name="button" value="ok" type="submit" class="btn btn-success pull-right">Valider</button> <?php } ?>
	                                    <?php if (($data['idEtat']==2) AND ($data['idFournisseur']!=Null) AND ($_SESSION['commande_valider_delegate']==1) AND cmdEstValideur($_SESSION['idPersonne'], $_GET['id'])!=1) { ?><button name="button" value="okdelegate" type="submit" class="btn btn-success pull-right">Valider en tant que délégué</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>
	                        
	                        
	                        <div class="tab-pane" id="fournisseur">
	                            <form role="form" class="spinnerAttenteSubmit" action="commandesGo4.php?id=<?=$_GET['id']?>" method="POST">
	                                <div class="row">
	                                    <div class="col-md-6">
	                                        <div class="form-group">
	                                            <label>Fournisseur: </label>
	                                            <select disabled class="form-control" name="idFournisseur">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM FOURNISSEURS;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if ($data['idFournisseur'] == $data2['idFournisseur']) { echo 'selected'; } ?>><?php echo $data2['nomFournisseur']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                        <div class="form-group" id="datePassage">
	                                            <label>Date de passage de la commande:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input <?php if(($data['idEtat']>3) OR ($_SESSION['commande_etreEnCharge']==0) OR cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])==0){echo 'disabled';}?> type="text" class="input-datetimepicker form-control" name="datePassage" value="<?php echo $data['datePassage']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	
	                                    <div class="col-md-6">
	                                        <div class="form-group">
				                                <label>Référence de la commande fournisseur:</label>
				                                <input <?php if(($data['idEtat']>3) OR ($_SESSION['commande_etreEnCharge']==0) OR cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])==0){echo 'disabled';}?> type="text" class="form-control" name="numCommandeFournisseur" value="<?php echo $data['numCommandeFournisseur']; ?>">
				                            </div>
	                                        <div class="form-group" id="dateLivraisonPrevue">
	                                            <label>Date prévue de livraison finale:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input <?php if(($data['idEtat']>3) OR ($_SESSION['commande_etreEnCharge']==0) OR cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])==0){echo 'disabled';}?> type="text" class="input-datetimepicker form-control" name="dateLivraisonPrevue" value="<?php echo $data['dateLivraisonPrevue']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']==3) AND ($_SESSION['commande_etreEnCharge']==1) AND cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])) { ?><button type="submit" class="btn btn-info pull-right">Commande passée > En attente de livraison</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>
	                        
	                        
	                        <div class="tab-pane" id="livraison">
	                            <form role="form" class="spinnerAttenteSubmit" action="commandesGo56.php?id=<?=$_GET['id']?>" method="POST">
	                                <div class="row">
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateLivraisonPrevue">
	                                            <label>Date prévue de livraison finale:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input disabled type="text" class="input-datepicker form-control" name="dateLivraisonPrevue" value="<?php echo $data['dateLivraisonPrevue']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group">
	                                            <label>Lieu de livraison: </label>
	                                            <select disabled class="form-control" name="idLieuLivraison">
	                                                <option></option>
	                                                <?php
	                                                $query2 = $db->query('SELECT * FROM LIEUX;');
	                                                while ($data2 = $query2->fetch())
	                                                {
	                                                    ?>
	                                                    <option value="<?php echo $data2['idLieu']; ?>" <?php if ($data['idLieuLivraison'] == $data2['idLieu']) { echo 'selected'; } ?>><?php echo $data2['libelleLieu']; ?></option>
	                                                    <?php
	                                                }
	                                                $query2->closeCursor(); ?>
	                                            </select>
	                                        </div>
	                                    </div>
	                                    <div class="col-md-4">
	                                        <div class="form-group" id="dateLivraisoneffective">
	                                            <label>Date de livraison finale effective:</label>
	                                            <div class="input-group">
	                                                <div class="input-group-addon">
	                                                    <i class="fa fa-calendar"></i>
	                                                </div>
	                                                <input <?php if(($data['idEtat']>4) OR ($_SESSION['commande_etreEnCharge']==0) OR cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])==0){echo 'disabled';}?> type="text" class="input-datetimepicker form-control" name="dateLivraisoneffective" value="<?php echo $data['dateLivraisoneffective']; ?>">
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="form-group">
	                                    <label>Remarques:</label>
	                                    <textarea <?php if(($data['idEtat']>4) OR ($_SESSION['commande_etreEnCharge']==0) OR cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])==0){echo 'disabled';}?> class="form-control" rows="3" name="remarquesLivraison"><?php echo $data['remarquesLivraison']; ?></textarea>
	                                </div>
	                                <div class="box-footer">
	                                    <?php if (($data['idEtat']==4) AND ($_SESSION['commande_etreEnCharge']==1) AND cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])){ ?><button name="button" value="nok" type="submit" class="btn btn-danger pull-left">Commande reçue > SAV</button> <?php } ?>
	                                    <?php if (($data['idEtat']==4) AND ($_SESSION['commande_etreEnCharge']==1) AND cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])) { ?><button name="button" value="ok" type="submit" class="btn btn-success pull-right">Commande reçue > OK</button> <?php } ?>
	                                </div>
	                            </form>
	                        </div>


	                        <div class="tab-pane" id="codebarre">
	                            <table id="tri1" class="table table-bordered table-hover">
			                        <thead>
			                            <tr>
			                                <th class="all" style="width: 10px">#</th>
			                                <th class="all">Code Barre</th>
			                                <th class="all">Base</th>
			                                <th class="not-mobile">Element du catalogue</th>
			                                <th class="not-mobile">Péremption spécifiée</th>
			                                <th class="not-mobile">Commentaires</th>
			                                <th class="not-mobile">Actions</th>
			                            </tr>
			                        </thead>
			                        <tbody>
			                        <?php
			                        $query2 = $db->prepare('
			                            SELECT
			                                c.*,
			                                m.libelleMateriel
			                            FROM
			                                CODES_BARRE c
			                                LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue
			                            WHERE
			                            	c.idMaterielCatalogue IN
			                            	(
			                            		SELECT idMaterielCatalogue FROM COMMANDES_MATERIEL WHERE idCommande = :idCommande
			                            	)
			                            ;');
			                        $query2->execute(array('idCommande'=>$_GET['id']));
			                        while ($data2 = $query2->fetch())
			                        {
			                            ?>
			                            <tr>
			                                <td><?= $data2['idCode'] ?></td>
			                                <td><?= $data2['codeBarre'] ?></td>
			                                <td><?php if($data2['internalReference']){echo 'Interne';}else{echo 'Fournisseur';} ?></td>
			                                <td><?= $data2['libelleMateriel'] ?></td>
			                                <td><?= $data2['peremptionConsommable'] ?></td>
			                                <td><?= nl2br($data2['commentairesCode']) ?></td>
			                                <td>
			                                    <a href="codesBarrePrintForm.php?id=<?=$data2['idCode']?>" class="btn btn-xs btn-success modal-form" title="Imprimer le code"><i class="fa fa-barcode"></i></a>
			                                    <?php if ($_SESSION['codeBarre_modification']==1 AND $data2['internalReference']==1 AND $LOTSLOCK==0 AND $RESERVESLOCK==0) {?>
			                                        <a href="codesBarreFormInterne.php?id=<?=$data2['idCode']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
			                                    <?php }?>
			                                    <?php if ($_SESSION['codeBarre_suppression']==1 AND $LOTSLOCK==0 AND $RESERVESLOCK==0) {?>
			                                        <a href="modalDeleteConfirm.php?case=codesBarreDelete&id=<?=$data2['idCode']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
			                                    <?php }?>
			                                </td>
			                            </tr>
			                            <?php
			                        }
			                        $query->closeCursor(); ?>
			                        </tbody>
			                    </table>
			                    <?php if ($_SESSION['codeBarre_ajout']==1) {?>
									<a href="codesBarreFormFournisseur.php?idCommande=<?=$_GET['id']?>" class="btn btn-success modal-form">Enregistrer un nouveau code barre fournisseur</a>
									<a href="codesBarreFormInterne.php?idCommande=<?=$_GET['id']?>" class="btn btn-success modal-form">Générer un nouveau code barre, l'imprimer, et le coller sur les produits</a>
				                <?php }?>
	                        </div>

	                        <div class="tab-pane" id="timeline">
	                            <div class="table-responsive no-padding">
					             	<ul class="timeline">
							            <li class="time-label">
							                  <span class="bg-blue">
							                    Historique
							                  </span>
							            </li>
							            <?php
							                    $query2 = $db->prepare('SELECT * FROM COMMANDES_TIMELINE c LEFT OUTER JOIN COMMANDES_TIMELINE_ICON i ON c.idComIcon = i.idComIcon WHERE idCommande=:idCommande ORDER BY dateEvtCommande DESC;');
							                    $query2->execute(array('idCommande' => $_GET['id']));
							                    while($data2 = $query2->fetch())
							                    { ?>
							                    	<li>
										              <i class="fa <?php echo $data2['iconFontAsw'];?> bg-<?php echo $data2['iconColor'];?>"></i>
										              <div class="timeline-item">
										                <span class="time"><i class="fa fa-clock-o"></i> <?php echo $data2['dateEvtCommande'];?></span>
										                <h3 class="timeline-header no-border"><?php echo $data2['detailsEvtCommande'];?></h3>
										              </div>
										            </li>
							                    <?php }
							             ?>
							            <li>
							              <i class="fa fa-clock-o bg-gray"></i>
							            </li>
							        </ul>
					            </div>
	                        </div>
	
	
	                    </div>
	                </div>
	                
	                <?php
	                	if($data['idEtat']!=7 AND $data['idEtat']!=8)
	                	{
	                	?>
			                <div class="box box-warning">
			                    <div class="box-body">
			                        <?php if (($data['idEtat']==1) AND(($_SESSION['commande_ajout']==1)OR($_SESSION['commande_etreEnCharge']==1))) { ?><a href="commandesGo2.php?id=<?=$_GET['id']?>" class="btn btn-info pull-right spinnerAttenteClick">Soumettre à validation</a> <?php } ?>
			                        <?php if ($data['idEtat']==5 AND cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])) { ?><a href="commandesGo7.php?id=<?=$_GET['id']?>" class="btn btn-info pull-right spinnerAttenteClick">Cloturer la commande</a> <?php } ?>
			                        <?php if ($data['idEtat']==6 AND cmdEstAffectee($_SESSION['idPersonne'], $_GET['id'])) { ?><a href="commandesGo5.php?id=<?=$_GET['id']?>" class="btn btn-info pull-right spinnerAttenteClick">SAV terminé > Commande OK</a> <?php } ?>
			                        <?php if ((cmdEstAffectee($_SESSION['idPersonne'], $_GET['id']) OR cmdEstValideur($_SESSION['idPersonne'], $_GET['id'])==1 OR cmdEstObservateur($_SESSION['idPersonne'], $_GET['id']) OR cmdEstDemandeur($_SESSION['idPersonne'], $_GET['id'])) AND ($data['idEtat']<7)){ ?>
						            	<a href="commandesNotesForm.php?id=<?=$_GET['id']?>" class="btn btn-success modal-form"><i class="fa fa-plus"></i> Note</a>
					                <?php } ?>
			                    </div>
			                </div>
			        <?php } ?>
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
