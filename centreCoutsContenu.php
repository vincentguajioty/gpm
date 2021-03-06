<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 606;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['cout_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
        $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout = :idCentreDeCout');
        $query->execute(array('idCentreDeCout' => $_GET['id']));
        $data = $query->fetch();

        $query2 = $db->prepare('SELECT COALESCE(SUM(montantEntrant),0)-COALESCE(SUM(montantSortant),0) as total FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = :idCentreDeCout');
        $query2->execute(array('idCentreDeCout'=>$_GET['id']));
        $enCours = $query2->fetch();
        $enCours = round($enCours['total'],2);

        $query2 = $db->prepare('SELECT idCommande FROM COMMANDES WHERE idCentreDeCout = :idCentreDeCout AND integreCentreCouts = 0 AND idEtat != 8;');
        $query2->execute(array('idCentreDeCout'=>$_GET['id']));
        $previsions = 0;
        while($commande = $query2->fetch())
        {
            $previsions += cmdTotal($commande['idCommande']);
        }

        $query2 = $db->prepare('SELECT idCommande FROM COMMANDES WHERE idCentreDeCout = :idCentreDeCout AND integreCentreCouts = 0 AND idEtat != 8 AND idEtat != 1 AND idEtat != 2;');
        $query2->execute(array('idCentreDeCout'=>$_GET['id']));
        $validees = 0;
        while($commande = $query2->fetch())
        {
            $validees += cmdTotal($commande['idCommande']);
        }

        $query2 = $db->prepare('SELECT COUNT(*) as nb FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat WHERE idCentreDeCout = :idCentreDeCout AND integreCentreCouts = 0 AND c.idEtat > 3 AND c.idEtat < 8;');
        $query2->execute(array('idCentreDeCout'=>$_GET['id']));
        $nbIntergrationAttente = $query2->fetch();

		if($data['dateFermeture'] == Null)
		{
			if($data['dateOuverture'] <= date('Y-m-d'))
			{
				$couleur = 'green';
				$text = 'Ouvert';
			}
			else
			{
				$couleur = 'blue';
				$text = 'Futur';
			}
		}
		else
		{
			if($data['dateFermeture'] < date('Y-m-d'))
			{
				$couleur = 'grey';
				$text = 'Clos';
			}
			else
			{
				if($data['dateOuverture'] <= date('Y-m-d'))
    			{
    				$couleur = 'green';
					$text = 'Ouvert';
    			}
    			else
    			{
    				$couleur = 'blue';
					$text = 'Futur';
    			}
			}
		}
    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Centre de co??ts: <?php echo $data['libelleCentreDecout']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="centreCouts.php">Centres de couts</a></li>
                <li class="active"><?php echo $data['libelleCentreDecout']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">

                <div class="col-md-3">
                    <div class="row">
                    	<div class="col-md-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-<?=$couleur?>"><i class="fa fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?=$data['dateOuverture']?> <i class="fa  fa-arrow-right"></i> <?=$data['dateFermeture']?></span>
                                    <span class="info-box-number">
                                    	<?=$text?>
                                    	<br/>
                                    	<?php
                                    		if($text=='Clos' AND $enCours>0 AND centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])==1)
                                    		{ ?>
                                    			<a href="centreCoutsOperationsSolderForm.php?idCentreDeCout=<?=$_GET['id']?>" class="btn btn-sm btn-warning modal-form"><i class="fa fa-battery-quarter"></i> Solder ce centre de couts</a>
                                    		<?php }
                                    	?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-<?php if($enCours==0){echo 'orange';}elseif($enCours>0){echo 'green';}else{echo 'red';} ?>"><i class="fa fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Solde actuel</span>
                                    <span class="info-box-number"><?= $enCours ?> ???</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-<?php if($enCours-$validees==0){echo 'orange';}elseif($enCours-$validees>0){echo 'green';}else{echo 'red';} ?>"><i class="fa fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Commandes ?? venir valid??es</span>
                                    <span class="info-box-number"><?= $validees ?> ???<br/>(Solde cible: <?= round($enCours-$validees,2) ?>???)</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-<?php if($enCours-$previsions==0){echo 'orange';}elseif($enCours-$previsions>0){echo 'green';}else{echo 'red';} ?>"><i class="fa fa-cart-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Potentielles commandes ?? venir</span>
                                    <span class="info-box-number"><?= $previsions ?> ???</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <!-- Widget: user widget style 1 -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#livre" data-toggle="tab">Livre de comptes</a></li>
                            <li><a href="#cmd" data-toggle="tab">Commandes ?? int??grer <?php if($nbIntergrationAttente['nb']>0){echo '<span class="badge bg-yellow">'.$nbIntergrationAttente['nb'].'</span>';} ?></a></li>
                            <li><a href="#cmdKO" data-toggle="tab">Commandes refus??es</a></li>
                            <li><a href="#pj" data-toggle="tab">Pi??ces jointes</a></li>
                            <?php if ($_SESSION['cout_ajout']==1) {?><li><a href="#personnes" data-toggle="tab">Gestionnaires</a></li><?php } ?>
                            <li><a href="#export" data-toggle="tab">Exports</a></li>
                        </ul>
                        <div class="tab-content">

                            <div class="active tab-pane" id="livre">
                                
                                <?php
                                    if($text == 'Clos')
                                    { ?>
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <i class="icon fa fa-warning"></i> Ce centre de co??t est clos. Aucune action ne devrait y ??tre men??e !
                                            </div>
                                        </div>
                                    <?php }
                                    if($text == 'Futur')
                                    { ?>
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                <i class="icon fa fa-warning"></i> Ce centre de co??t n'est pas encore ouvert. Aucune action ne devrait y ??tre men??e !
                                            </div>
                                        </div>
                                    <?php }
                                ?>

                                <table id="tri2R" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="all" style="width: 10px">#</th>
                                            <th class="all">Date</th>
                                            <th class="not-mobile">Titre transaction</th>
                                            <th class="not-mobile">Montant Entrant</th>
                                            <th class="not-mobile">Montant Sortant</th>
                                            <th class="not-mobile">D??tails</th>
                                            <th class="not-mobile">Responsable</th>
                                            <th class="not-mobile">Commande</th>
                                            <th class="not-mobile"><?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])==1){ ?><a href="centreCoutsOperationsForm.php?idCentreDeCout=<?=$_GET['id']?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $query2 = $db->prepare('SELECT op.*, p.identifiant FROM CENTRE_COUTS_OPERATIONS op LEFT OUTER JOIN PERSONNE_REFERENTE p ON op.idPersonne = p.idPersonne WHERE idCentreDeCout = :idCentreDeCout ORDER BY dateOperation DESC;');
                                            $query2->execute(array('idCentreDeCout'=>$_GET['id']));
                                            while ($data2 = $query2->fetch())
                                            { ?>
                                                <tr>
                                                    <td><?= $data2['idOperations'] ?></td>
                                                    <td><?= $data2['dateOperation'] ?></td>
                                                    <td><?= $data2['libelleOperation'] ?></td>
                                                    <td><?php if($data2['montantEntrant'] != Null){echo $data2['montantEntrant'].' ???';} ?></td>
                                                    <td><?php if($data2['montantSortant'] != Null){echo $data2['montantSortant'].' ???';} ?></td>
                                                    <td><?= $data2['detailsMoyenTransaction'] ?></td>
                                                    <td><?= $data2['identifiant'] ?></td>
                                                    <td>
                                                        <?php
                                                            if($data2['idCommande'] != Null AND $data2['idCommande'] != '')
                                                            {
                                                                if ($_SESSION['commande_lecture']==1)
                                                                {
                                                                	echo '<a href="commandeView.php?id='.$data2['idCommande'].'" class="btn btn-xs btn-info" title="Acc??der ?? la commande"><i class="fa fa-folder"></i></a>';
                                                                }
                                                                else
                                                                {
                                                                	echo '<a href="#" class="btn btn-xs btn-info" disabled><i class="fa fa-folder"></i></a>';
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])==1){ ?>
                                                            <a href="centreCoutsOperationsForm.php?id=<?=$data2['idOperations']?>&idCentreDeCout=<?=$_GET['id']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                                        <?php } ?>
                                                        <?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])==1){ ?>
                                                            <a href="centreCoutsOperationsDelete.php?id=<?=$data2['idOperations']?>&idCentreDeCout=<?=$_GET['id']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer" onClick="javascript: return confirm('Etes vous sur de vouloir supprimer cette entr??e ?');"><i class="fa fa-trash"></i></a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane table-responsive no-padding" id="cmd">
                                
                                <?php
                                	if($text == 'Clos')
                                	{ ?>
                                		<div class="col-md-12">
	                                		<div class="alert alert-warning">
							                    <i class="icon fa fa-warning"></i> Ce centre de co??t est clos. Aucune action ne devrait y ??tre men??e !
							                </div>
	                                	</div>
                                	<?php }
                                	if($text == 'Futur')
                                	{ ?>
                                		<div class="col-md-12">
	                                		<div class="alert alert-warning">
							                    <i class="icon fa fa-warning"></i> Ce centre de co??t n'est pas encore ouvert. Aucune action ne devrait y ??tre men??e !
							                </div>
	                                	</div>
                                	<?php }
                                ?>
                                
                                <table class="table table-hover">
                                    <tr>
                                        <th>#</th>
                                        <th>Date de cr??ation</th>
                                        <th>Nom</th>
                                        <th>Montant</th>
                                        <th>Etat</th>
                                        <th>Gerant</th>
                                        <th>Actions</th>
                                    </tr>
                                    <?php
                                        $query2 = $db->prepare('SELECT c.*, e.libelleEtat FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat WHERE idCentreDeCout = :idCentreDeCout AND integreCentreCouts = 0 AND c.idEtat > 3 AND c.idEtat != 8;');
                                        $query2->execute(array('idCentreDeCout'=>$_GET['id']));
                                        while($data2 = $query2->fetch())
                                        { 
                                            $query3 = $db->prepare('SELECT * FROM COMMANDES_MATERIEL c WHERE idCommande = :idCommande;');
                                            $query3->execute(array('idCommande' => $data2['idCommande']));
                                            $totalCMD = 0;
                                            while ($data3 = $query3->fetch())
                                            {
                                                $totalCMD = $totalCMD + ($data3['prixProduitTTC']*$data3['quantiteCommande']);
                                            }
                                            ?>
                                            <tr>
                                                <td><?= $data2['idCommande'] ?></td>
                                                <td><?= $data2['dateCreation'] ?></td>
                                                <td><?= $data2['nomCommande'] ?></td>
                                                <td><?= $totalCMD ?> ???</td>
                                                <td>
                                                    <span class="badge bg-<?php
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
                                                        ?>"><?php echo $data2['libelleEtat']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                        $query3 = $db->prepare('SELECT * FROM COMMANDES_AFFECTEES c JOIN PERSONNE_REFERENTE p ON c.idAffectee = p.idPersonne WHERE idCommande = :idCommande');
                                                        $query3->execute(array('idCommande'=>$data2['idCommande']));
                                                        while($data3 = $query3->fetch())
                                                        {
                                                            echo $data3['identifiant'].'<br/>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                	<?php if($_SESSION['commande_lecture']==1){ ?>
                                                    	<a href="commandeView.php?id=<?= $data2['idCommande'] ?>" class="btn btn-xs btn-info" title="Acc??der ?? la commande"><i class="fa fa-folder"></i></a>
                                                    <?php } ?>
                                                    <?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])){ ?>
                                                    	<a href="centreCoutsCommandeOK.php?idCommande=<?= $data2['idCommande'] ?>&idCentreDeCout=<?= $_GET['id'] ?>" class="btn btn-xs btn-success" title="Cr??er une ligne dans ce centre de couts pour int??grer la commande"><i class="fa fa-check"></i> Int??grer la commande</a>
                                                    	<a href="centreCoutsCommandeNOK.php?idCommande=<?= $data2['idCommande'] ?>&idCentreDeCout=<?= $_GET['id'] ?>" class="btn btn-xs btn-danger" title="Rejeter la commande de ce centre de couts"><i class="fa fa-close"></i> Rejeter la commande</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    ?>
                                </table>
                            </div>

							<div class="tab-pane table-responsive no-padding" id="cmdKO">
                                <table class="table table-hover">
                                    <tr>
                                        <th>#</th>
                                        <th>Date de cr??ation</th>
                                        <th>Nom</th>
                                        <th>Montant</th>
                                        <th>Etat</th>
                                        <th>Gerant</th>
                                        <th>Actions</th>
                                    </tr>
                                    <?php
                                        $query2 = $db->prepare('SELECT c.*, e.libelleEtat FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat LEFT OUTER JOIN CENTRE_COUTS_OPERATIONS op ON c.idCommande = op.idCommande WHERE c.idCentreDeCout = :idCentreDeCout AND integreCentreCouts = 1 AND c.idEtat > 3 AND c.idEtat != 8 AND op.idOperations IS Null;');
                                        $query2->execute(array('idCentreDeCout'=>$_GET['id']));
                                        while($data2 = $query2->fetch())
                                        { 
                                            $query3 = $db->prepare('SELECT * FROM COMMANDES_MATERIEL c WHERE idCommande = :idCommande;');
                                            $query3->execute(array('idCommande' => $data2['idCommande']));
                                            $totalCMD = 0;
                                            while ($data3 = $query3->fetch())
                                            {
                                                $totalCMD = $totalCMD + ($data3['prixProduitTTC']*$data3['quantiteCommande']);
                                            }
                                            ?>
                                            <tr>
                                                <td><?= $data2['idCommande'] ?></td>
                                                <td><?= $data2['dateCreation'] ?></td>
                                                <td><?= $data2['nomCommande'] ?></td>
                                                <td><?= $totalCMD ?> ???</td>
                                                <td>
                                                    <span class="badge bg-<?php
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
                                                        ?>"><?php echo $data2['libelleEtat']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                        $query3 = $db->prepare('SELECT * FROM COMMANDES_AFFECTEES c JOIN PERSONNE_REFERENTE p ON c.idAffectee = p.idPersonne WHERE idCommande = :idCommande');
                                                        $query3->execute(array('idCommande'=>$data2['idCommande']));
                                                        while($data3 = $query3->fetch())
                                                        {
                                                            echo $data3['identifiant'].'<br/>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                	<?php if($_SESSION['commande_lecture']==1){ ?>
                                                    	<a href="commandeView.php?id=<?= $data2['idCommande'] ?>" class="btn btn-xs btn-info" title="Acc??der ?? la commande"><i class="fa fa-folder"></i></a>
                                                    <?php } ?>
                                                    <?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])){ ?>
                                                    	<a href="centreCoutsCommandeRecycle.php?idCommande=<?= $data2['idCommande'] ?>&idCentreDeCout=<?= $_GET['id'] ?>" class="btn btn-xs btn-warning" title="Remettre la commande en attente d'int??gration"><i class="fa fa-refresh"></i> Recycler la commande</a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                    <?php }
                                    ?>
                                </table>
                            </div>

                            <div class="tab-pane table-responsive no-padding" id="pj">
                                <table class="table table-hover">
                                    <tr>
                                        <th>Nom du document</th>
                                        <th>Type de document</th>
                                        <th>Date de chargement</th>
                                        <th>Format</th>
                                        <th><?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])==1){ ?><a href="centreCoutsDocForm.php?idCentreDeCout=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM VIEW_DOCUMENTS_CENTRE_COUTS_COMMANDES WHERE idCentreDeCout = :idCentreDeCout ORDER BY dateDocCouts DESC;');
                                    $query2->execute(array('idCentreDeCout' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['nomDocCouts']; ?></td>
                                            <td><?php
                                                echo $data2['libelleTypeDocument'];
                                                if(is_null($data2['idDocCouts'])){echo ' <i class="fa fa-shopping-cart"></i>';}
                                            ?></td>
                                            <td><?php echo $data2['dateDocCouts'];?></td>
                                            <td><i class="fa <?php echo documentsGetIcone($data2['formatDocCouts']);?>"></i></td>
                                            <td>
                                                <?php
                                                    if(is_null($data2['idDocCommande']))
                                                    { ?>
                                                        <?php if($_SESSION['cout_lecture']==1){
                                                            if ($data2['formatDocCouts'] == 'pdf' OR $data2['formatDocCouts'] == 'jpg' OR $data2['formatDocCouts'] == 'jpeg' OR $data2['formatDocCouts'] == 'png'){?>
                                                                <a href="centreCoutsDocView.php?idDoc=<?=$data2['idDocCouts']?>" class="btn btn-xs btn-info" title="Visionner"><i class="fa fa-eye"></i></a>
                                                            <?php } else { ?>
                                                                <a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                            <?php }}?>
                                                        <?php if($_SESSION['cout_lecture']==1){ ?>
                                                            <a href="centreCoutsDocDL.php?idDoc=<?=$data2['idDocCouts']?>" class="btn btn-xs btn-success" title="T??l??charger"><i class="fa fa-download"></i></a>
                                                        <?php }?>
                                                        <?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])==1){ ?>
                                                            <a href="centreCoutsDocDelete.php?idDoc=<?=$data2['idDocCouts']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous s??r de vouloir supprimer cet ??l??ment?');" title="Supprimer"><i class="fa fa-minus"></i></a>
                                                        <?php }?>
                                                    <?php }else{ ?>
                                                        <?php if($_SESSION['commande_lecture']==1){
                                                                if ($data2['formatDocCouts'] == 'pdf' OR $data2['formatDocCouts'] == 'jpg' OR $data2['formatDocCouts'] == 'jpeg' OR $data2['formatDocCouts'] == 'png'){?>
                                                                    <a href="commandeDocView.php?idDoc=<?=$data2['idDocCommande']?>" class="btn btn-xs btn-info" title="Visualiser"><i class="fa fa-eye"></i></a>
                                                        <?php } else { ?>
                                                                    <a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                        <?php }}?>
                                                        <?php if($_SESSION['commande_lecture']==1){ ?>
                                                            <a href="commandeDocDL.php?idDoc=<?=$data2['idDocCommande']?>" class="btn btn-xs btn-success" title="T??l??charger"><i class="fa fa-download"></i></a>
                                                        <?php }?>
                                                        <a class="btn btn-xs btn-default"><i class="fa fa-minus"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>

                                </table>
                            </div>
                            
                            <?php if ($_SESSION['cout_ajout']==1) {?>
                                <div class="tab-pane" id="personnes">
                                    <table class="table table-hover">
                                    <tr>
                                        <th>Personne</th>
                                        <th>Seuil de validation des commandes</th>
                                        <th>Droits ??tendus</th>
                                        <th><a href="centreCoutsGerantForm.php?idCentreDeCout=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT c.*, p.identifiant FROM CENTRE_COUTS_PERSONNES c LEFT OUTER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE c.idCentreDeCout = :idCentreDeCout ORDER BY identifiant ASC;');
                                    $query2->execute(array('idCentreDeCout' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['identifiant'];?><?php if(centreCoutsEstCharge($_SESSION['idPersonne'],$_GET['id'])==0){echo ' <span class="badge bg-blue">Expir??</span>';}?></td>
                                            <td><?php if($data2['montantMaxValidation']!=Null AND $data2['montantMaxValidation']>=0){echo $data2['montantMaxValidation'].' ???';}else{echo '<span class="badge bg-yellow">Illimit??</span>';}?></td>
                                            <td><?php if($data2['depasseBudget']){echo '<span class="badge bg-yellow">D??passement de budget autoris??</span><br/>';}?><?php if($data2['validerClos']){echo '<span class="badge bg-yellow">Op??rer sur le centre clos</span>';}?></td>
                                            <td>
                                                <a href="centreCoutsGerantForm.php?id=<?= $data2['idGerant'] ?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                                <a href="centreCoutsGerantDelete.php?id=<?=$data2['idGerant']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous s??r de vouloir supprimer ce g??rant?');" title="Supprimer"><i class="fa fa-minus"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </table>
                                </div>
                            <?php } ?>

                            <div class="tab-pane" id="export">
                                <a href="centreCoutsExport.php?id=<?=$_GET['id']?>" class="btn btn-xs btn-success"><i class="fa fa-download"></i> T??l??charger un export CSV des op??rations enregistr??es</a>
                            </div>

                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
				<div class="col-md-12">
					<div class="box box-success">
						<div class="box-header with-border">
							<h3 class="box-title">Activit?? mensuelle du centre de cout (somme des d??penses et des gains par mois)</h3>
						</div>
						<div class="box-body chart-responsive">
							<div class="chart" id="bar-chart" style="height: 300px;"></div>
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
<script>
	$(function () {
		"use strict";
		
		<?php
			$entrants = $db->prepare("
				SELECT
					YEAR(dateOperation) as annee,
					MONTH(dateOperation) as mois,
					COALESCE(SUM(montantEntrant), 0) as montantEntrant,
					COALESCE(SUM(montantSortant), 0) as montantSortant
				FROM
					CENTRE_COUTS_OPERATIONS
				WHERE
					idCentreDeCout = :idCentreDeCout
				GROUP BY
					YEAR(dateOperation),
					MONTH(dateOperation)
				;"
			);
			$entrants->execute(array('idCentreDeCout'=>$_GET['id']));
		?>
		
	    var line = new Morris.Bar({
	      element: 'bar-chart',
	      resize: true,
	      data: [
	        <?php
	        	while($entrant = $entrants->fetch())
	        	{
	        		echo "{x: '".$entrant['annee'].'-'.$entrant['mois']."', montantEntrant: ".$entrant['montantEntrant'].", montantSortant: ".round($entrant['montantSortant'],2)."},";
	        	}
	        ?>
	      ],
	      xkey: 'x',
	      ykeys: ['montantEntrant','montantSortant'],
	      labels: ['Entrant','Sortant'],
	      barColors: ['#5cc709','#ff6b00'],
	      hideHover: 'auto'
	    });
  });
</script>
</body>
</html>



