<!DOCTYPE html>
<html>
<?php
session_start();
$_SESSION['page'] = 000;
require_once('logCheck.php');
?>
<?php include('header.php'); require_once('config/config.php'); ?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'checkLotsConf.php'; ?>
    <?php require_once 'modal.php'; ?>




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
        <?php include('confirmationBox.php'); ?>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">

            <?php
            $nbLotsNOK = 0;
            $query = $db->query('SELECT * FROM LOTS_LOTS WHERE idTypeLot IS NOT NULL AND idEtat = 1;');
            while ($data = $query->fetch())
            {
                $nbLotsNOK = $nbLotsNOK + checkLotsConf($data['idLot']);
            }
            ?>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <!-- small box -->
                <?php
                $query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremption < CURRENT_DATE OR peremption = CURRENT_DATE) AND idEtat = 1;');
                $data = $query->fetch();

                if ($data['nb']>0)
                { ?>
                    <div class="info-box">
                        <a data-toggle="modal" data-target="#modalAccueilAlertePeremption"><span class="info-box-icon bg-red"><i class="ion ion-alert-circled"></i></span></a>

                        <div class="info-box-content">
                            <span class="info-box-text">Matériel périmé:</span>
                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
                            <span class="info-box-more">Cliquer sur l'icone</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php }
                else
                { ?>
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-checkmark"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Matériel périmé:</span>
                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php }
                $query->closeCursor(); ?>
            </div>
            <!-- ./col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <!-- small box -->
                <?php
                $query = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idEtat = 1;');
                $data = $query->fetch();

                if ($data['nb']>0)
                { ?>

                    <div class="info-box">
                        <a data-toggle="modal" data-target="#modalAccueilAlerteQuantité"><span class="info-box-icon bg-red"><i class="ion ion-alert-circled"></i></span></a>

                        <div class="info-box-content">
                            <span class="info-box-text">Matériel manquant:</span>
                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
                            <span class="info-box-more">Cliquer sur l'icone</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php }
                else
                { ?>
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-checkmark"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Matériel manquant:</span>
                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php }
                $query->closeCursor(); ?>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <!-- small box -->
                <?php
                $query = $db->query('SELECT COUNT(*) as nb FROM LOTS_LOTS WHERE idEtat = 1 AND (frequenceInventaire IS NOT NULL) AND ((DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) < CURRENT_DATE) OR (DATE_ADD(dateDernierInventaire, INTERVAL frequenceInventaire DAY) = CURRENT_DATE));');
                $data = $query->fetch();

                if ($data['nb']>0)
                { ?>

                    <div class="info-box">
                        <a data-toggle="modal" data-target="#modalAccueilAlerteInventaire"><span class="info-box-icon bg-red"><i class="ion ion-alert-circled"></i></span></a>

                        <div class="info-box-content">
                            <span class="info-box-text">Lot en attente d'inventaire:</span>
                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
                            <span class="info-box-more">Cliquer sur l'icone</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php }
                else
                { ?>
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-checkmark"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Lot en attente d'inventaire:</span>
                            <span class="info-box-number"><?php echo $data['nb']; ?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php }
                $query->closeCursor(); ?>
            </div>
            <!-- ./col -->

            <div class="col-md-3 col-sm-6 col-xs-12">
                <?php
                if ($nbLotsNOK>0)
                { ?>

                    <div class="info-box">
                        <a data-toggle="modal" data-target="#modalAccueilAlerteConformite"><span class="info-box-icon bg-red"><i class="ion ion-alert-circled"></i></span></a>

                        <div class="info-box-content">
                            <span class="info-box-text">Lot non-conforme:</span>
                            <span class="info-box-number"><?php echo $nbLotsNOK; ?></span>
                            <span class="info-box-more">Cliquer sur l'icone</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php }
                else
                { ?>
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-checkmark"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Lot non-conforme:</span>
                            <span class="info-box-number"><?php echo $nbLotsNOK; ?></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                <?php } ?>

            </div>
        </div>

		<div class="row">	
	        <div class="col-md-6">
	            <div class="box box-success">
	
	                <div class="box-header with-border">
	                    <i class="fa fa-bullhorn"></i>
	                    <h3 class="box-title">Messages généraux</h3>
	                    <div class="box-tools pull-right">
	                    	<?php if ($_SESSION['messages_ajout']==1) {?><a data-toggle="modal" data-target="#modalMessageGeneralAdd" class="btn btn-sm"><i class="fa fa-plus"></i></a><?php } ?>
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
	        <div class="col-md-6">
	            <div class="box box-success">
	            	Calendrier de l'équipe
	            </div>
	        </div>
        </div>
    </section>
    <section class="content-header">
      <h1>
        Vue personnelle
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">

		<div class="row">
	        <?php include('confirmationBox.php'); ?>
	        <div class="col-md-6">
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
	                                        if (checkLotsConf($data['idLot'])==0)
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
	        <div class="col-md-6">
	            <div class="box box-success">
	            	Calendrier personnel
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
</body>

<div class="modal fade modal-danger" id="modalAccueilAlertePeremption">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de péremption</h4>
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
                <h4 class="modal-title">Alertes de quantité</h4>
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
                <h4 class="modal-title">Alertes d'inventaires</h4>
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
                <h4 class="modal-title">Alertes de conformité</h4>
            </div>
            <div class="modal-body">
                <ul>
                    <?php
                    $query = $db->query('SELECT * FROM LOTS_LOTS WHERE idTypeLot IS NOT NULL AND idEtat = 1;');
                    while ($data = $query->fetch())
                    {
                        if(checkLotsConf($data['idLot'])==1)
                        {
                            echo '<li>' . $data['libelleLot'] . '</li>';
                        }
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

</html>
