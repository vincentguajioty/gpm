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
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
  <?php include('bandeausup.php'); ?>
  <?php include('navbar.php'); ?>
  <?php require_once 'config/bdd.php'; ?>


  <?php $_POST['search'] = str_replace($XSS_SECURITY, "", $_POST['search']); ?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Moteur de recherche inter-modules: <?= $_POST['search'] ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i>Accueil</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <?php include('confirmationBox.php'); ?>        
        
        <?php
        if($_SESSION['lots_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Lots</h3>
    		    </div>
            <div class="box-body">
        			<table class="table table-bordered">
                  <thead>
                      <tr>
                          <th style="width: 10px">#</th>
                          <th>Libelle</th>
                          <th>Etat</th>
                          <th>Référentiel</th>
                          <th>Référent</th>
                          <th>Quantité Matériel</th>
                          <th>Prochain Inventaire</th>
                          <th>Notifications</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                  $query = $db->query('
                      SELECT
                          *
                      FROM
                          LOTS_LOTS l
                          LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot
                          LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat
                          LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu
                          LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne
                          LEFT OUTER JOIN LOTS_ETATS et ON l.idLotsEtat = et.idLotsEtat
                      WHERE
                          libelleLot LIKE "%'.$_POST['search'].'%"
                      ;');
                  while ($data = $query->fetch())
                  {
                      ?>
                      <tr>
                          <td><?= $data['idLot'] ?></td>
                          <td><?= $data['libelleLot'] ?></td>
                          <td><?= $data['libelleLotsEtat'] ?></td>
                          <td>
                              <?php
                              //echo $data['libelleTypeLot'];
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
                          <td><?php echo $data['identifiant']; ?></td>
                          <td>
                              <?php
                                  $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                                      WHERE idLot = :idLot AND
                                      (
                                          quantite > quantiteAlerte AND
                                          (
                                              peremptionNotification > CURRENT_DATE
                                              OR
                                              peremptionNotification IS NULL
                                          )
                                      );');
                                  $query2->execute(array(
                                      'idLot' => $data['idLot']
                                  ));
                                  $data2 = $query2->fetch();
                                  if($data2['nb']>0)
                                  {
                                      ?><span class="badge bg-green"><?= $data2['nb'] ?></span><?php
                                  }
                              ?>
                              <?php
                              $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                                      WHERE idLot = :idLot AND
                                      (
                                          (
                                              quantite = quantiteAlerte
                                              AND
                                              peremptionNotification = CURRENT_DATE
                                          )
                                          OR
                                          (
                                              quantite = quantiteAlerte
                                              AND
                                              (
                                                  peremptionNotification > CURRENT_DATE
                                                  OR
                                                  peremptionNotification IS NULL
                                              )
                                          )
                                          OR
                                          (
                                              quantite > quantiteAlerte
                                              AND
                                              peremptionNotification = CURRENT_DATE
                                          )
                                      );');
                              $query2->execute(array(
                                  'idLot' => $data['idLot']
                              ));
                              $data2 = $query2->fetch();
                              if($data2['nb']>0)
                              {
                                  ?><span class="badge bg-orange"><?= $data2['nb'] ?></span><?php
                              }
                              ?>
                              <?php
                              $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                                      WHERE idLot = :idLot AND
                                      (
                                          quantite < quantiteAlerte OR
                                          peremptionNotification < CURRENT_DATE
                                      );');
                              $query2->execute(array(
                                  'idLot' => $data['idLot']
                              ));
                              $data2 = $query2->fetch();
                              if($data2['nb']>0)
                              {
                                  ?><span class="badge bg-red"><?= $data2['nb'] ?></span><?php
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
                              <?php if ($_SESSION['lots_modification']==1) {?>
                                  <a href="lotsForm.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                              <?php }?>
                              <?php if ($_SESSION['lots_suppression']==1) {?>
                                  <a href="modalDeleteConfirm.php?case=lotsDelete&id=<?=$data['idLot']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                              <?php }?>
                          </td>
                      </tr>
                      <?php
                  }
                  $query->closeCursor(); ?>
                  </tbody>
              </table>
            </div>
    		  </div>
        <?php } ?>

        <?php
        if($_SESSION['sac_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Sacs</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th style="width: 10px">#</th>
                              <th>Libelle</th>
                              <th>Lot</th>
                              <th>Quantité d'emplacements</th>
                              <th>Quantité de matériel</th>
                              <th class="not-mobile not-tablet">Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                      <?php
                      $query = $db->query('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE libelleSac LIKE "%'.$_POST['search'].'%";');
                      while ($data = $query->fetch())
                      {
                          ?>
                          <tr>
                              <td><?php echo $data['idSac']; ?></td>
                              <td><?php echo $data['libelleSac']; ?></td>
                              <td><?php echo $data['libelleLot']; ?></td>

                              <?php
                                  $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_EMPLACEMENT WHERE idSac=:idSac');
                                  $query2->execute(array('idSac' => $data['idSac']));
                                  $data2 = $query2->fetch();
                              ?>

                              <td><?php echo $data2['nb']; ?></td>

                              <?php
                                  $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement = p.idEmplacement WHERE idSac = :idSac;');
                                  $query2->execute(array('idSac' => $data['idSac']));
                                  $data2 = $query2->fetch();
                              ?>

                              <td><?php echo $data2['nb']; ?></td>

                              <td>
                                  <?php if ($_SESSION['sac_lecture']==1) {?>
                                      <a href="sacsContenu.php?id=<?=$data['idSac']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                  <?php }?>
                                  <?php if ($_SESSION['sac_modification']==1) {?>
                                      <a href="sacsForm.php?id=<?=$data['idSac']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                  <?php }?>
                                  <?php if ($_SESSION['sac_suppression']==1) {?>
                                      <a href="modalDeleteConfirm.php?case=sacsDelete&id=<?=$data['idSac']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                  <?php }?>
                              </td>
                          </tr>
                          <?php
                      }
                      $query->closeCursor(); ?>
                      </tbody>
                  </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['sac2_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Emplacements</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                      <tr>
                          <th style="width: 10px">#</th>
                          <th>Libelle</th>
                          <th>Sac</th>
                          <th>Lot</th>
                          <th>Quantité de matériel</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                  $query = $db->query('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot WHERE libelleEmplacement LIKE "%'.$_POST['search'].'%";');
                  while ($data = $query->fetch())
                  {
                      $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement;');
                      $query2->execute(array('idEmplacement' => $data['idEmplacement']));
                      $data2 = $query2->fetch();
                      ?>
                      <tr>
                          <td><?php echo $data['idEmplacement']; ?></td>
                          <td><?php echo $data['libelleEmplacement']; ?></td>
                          <td><?php echo $data['libelleSac']; ?></td>
                          <td><?php echo $data['libelleLot']; ?></td>
                          <td><?php echo $data2['nb']; ?></td>
                          <td>
                              <?php if ($_SESSION['sac2_lecture']==1) {?>
                                  <a href="emplacementsContenu.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                              <?php }?>
                              <?php if ($_SESSION['sac2_modification']==1) {?>
                                  <a href="emplacementsForm.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                              <?php }?>
                              <?php if ($_SESSION['sac2_suppression']==1) {?>
                                  <a href="modalDeleteConfirm.php?case=emplacementsDelete&id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                              <?php }?>
                          </td>
                      </tr>
                      
                    
                      <?php
                      $query2->closeCursor();
                  }
                  $query->closeCursor(); ?>
                  </tbody>

              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['materiel_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Matériels</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Libelle</th>
                            <th>Emplacement</th>
                            <th>Sac</th>
                            <th>Lot</th>
                            <th>Quantité</th>
                            <th>Péremption</th>
                            <th>Etat</th>
                            <th>Notifications</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN MATERIEL_ETATS me ON m.idMaterielsEtat = me.idMaterielsEtat WHERE libelleMateriel LIKE "%'.$_POST['search'].'%";');
                    while ($data = $query->fetch())
                    {?>
                        <tr>
                            <td><?php echo $data['idElement']; ?></td>
                            <td><?php echo $data['libelleMateriel']; ?></td>
                            <td><?php echo $data['libelleEmplacement']; ?></td>
                            <td><?php echo $data['libelleSac']; ?></td>
                            <td><?php echo $data['libelleLot']; ?></td>
                            <td><?php
                                if ($data['quantite'] < $data['quantiteAlerte'])
                                {
                                    ?><span class="badge bg-red"><?php echo $data['quantite']; ?></span><?php
                                }
                                else if ($data['quantite'] == $data['quantiteAlerte'])
                                {
                                    ?><span class="badge bg-orange"><?php echo $data['quantite']; ?></span><?php
                                }
                                else
                                {
                                    ?><span class="badge bg-green"><?php echo $data['quantite']; ?></span><?php
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($data['peremption'] <= date('Y-m-d'))
                                {
                                    ?><span class="badge bg-red"><?php echo $data['peremption']; ?></span><?php
                                }
                                else if ($data['peremptionNotification'] <= date('Y-m-d'))
                                {
                                    ?><span class="badge bg-orange"><?php echo $data['peremption']; ?></span><?php
                                }
                                else
                                {
                                    ?><span class="badge bg-green"><?php echo $data['peremption']; ?></span><?php
                                }
                                ?>
                            </td>
                            <td><?php echo $data['libelleMaterielsEtat']; ?></td>
                            <td>
                              <?php if($data['idEtat']!=1){echo '<i class="fa fa-bell-slash-o"></i>';}else{echo '<i class="fa fa-bell-o"></i>';} ?>
                            </td>
                            <td>
                                <?php if ($_SESSION['reserve_ReserveVersLot']==1) {?>
                                  <a href="transfertResLotsFromLots.php?idElement=<?=$data['idElement']?>&idMaterielCatalogue=<?=$data['idMaterielCatalogue']?>" class="btn btn-xs btn-success modal-form" title="Approvisionner depuis la réserve"><i class="fa fa-exchange"></i></a>
                                <?php }?>
                                <?php if ($_SESSION['materiel_modification']==1) {?>
                                    <a href="materielsForm.php?id=<?=$data['idElement']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                <?php }?>
                                <?php if ($_SESSION['materiel_suppression']==1) {?>
                                    <a href="modalDeleteConfirm.php?case=materielsDelete&id=<?=$data['idElement']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                <?php }?>
                            </td>
                        </tr>
                        <?php
                    }
                    $query->closeCursor(); ?>
                    </tbody>


              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['reserve_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Conteneurs de réserve</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                      <tr>
                          <th style="width: 10px">#</th>
                          <th>Libelle</th>
                          <th>Lieu</th>
                          <th>Prochain inventaire</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php
                  $query = $db->query('SELECT * FROM RESERVES_CONTENEUR c LEFT OUTER JOIN LIEUX l  ON c.idLieu = l.idLieu WHERE libelleConteneur LIKE "%'.$_POST['search'].'%" ORDER BY libelleConteneur;');
                  while ($data = $query->fetch())
                  {?>
                      <tr <?php if ($_SESSION['reserve_lecture']==1) {?>data-href="reserveConteneurContenu.php?id=<?=$data['idConteneur']?>"<?php }?>>
                          <td><?php echo $data['idConteneur']; ?></td>
                          <td><?php echo $data['libelleConteneur']; ?></td>
                          <td><?php echo $data['libelleLieu']; ?></td>
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
                              <?php if ($_SESSION['reserve_lecture']==1) {?>
                                  <a href="reserveConteneurContenu.php?id=<?=$data['idConteneur']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                              <?php }?>
                              <?php if ($_SESSION['reserve_modification']==1) {?>
                                  <a href="reserveConteneurForm.php?id=<?=$data['idConteneur']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                              <?php }?>
                              <?php if ($_SESSION['reserve_suppression']==1) {?>
                                  <a href="modalDeleteConfirm.php?case=reserveConteneurDelete&id=<?=$data['idConteneur']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                              <?php }?>
                          </td>
                      </tr>
                      <?php
                  }
                  $query->closeCursor(); ?>
                  </tbody>

              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['reserve_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Matériels des réserves</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Libelle</th>
                        <th>Conteneur</th>
                        <th>Quantité</th>
                        <th>Péremption</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE libelleMateriel LIKE "%'.$_POST['search'].'%";');
                while ($data = $query->fetch())
                {?>
                    <tr>
                        <td><?php echo $data['idReserveElement']; ?></td>
                        <td><?php echo $data['libelleMateriel']; ?></td>
                        <td><?php echo $data['libelleConteneur']; ?></td>
                        <td><?php
                            if ($data['quantiteReserve'] < $data['quantiteAlerteReserve'])
                            {
                                ?><span class="badge bg-red"><?php echo $data['quantiteReserve']; ?></span><?php
                            }
                            else if ($data['quantiteReserve'] == $data['quantiteAlerteReserve'])
                            {
                                ?><span class="badge bg-orange"><?php echo $data['quantiteReserve']; ?></span><?php
                            }
                            else
                            {
                                ?><span class="badge bg-green"><?php echo $data['quantiteReserve']; ?></span><?php
                            }
                            ?>
                        </td>
                        <td><?php
                            if ($data['peremptionReserve'] <= date('Y-m-d'))
                            {
                                ?><span class="badge bg-red"><?php echo $data['peremptionReserve']; ?></span><?php
                            }
                            else if ($data['peremptionNotificationReserve'] <= date('Y-m-d'))
                            {
                                ?><span class="badge bg-orange"><?php echo $data['peremptionReserve']; ?></span><?php
                            }
                            else
                            {
                                ?><span class="badge bg-green"><?php echo $data['peremptionReserve']; ?></span><?php
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($_SESSION['reserve_modification']==1) {?>
                                <a href="reserveMaterielForm.php?id=<?=$data['idReserveElement']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                            <?php }?>
                            <?php if ($_SESSION['reserve_suppression']==1) {?>
                                <a href="modalDeleteConfirm.php?case=reserveMaterielDelete&id=<?=$data['idReserveElement']?>" class="btn btn-xs btn-danger modal-form"><i class="fa fa-trash" title="Supprimer"></i></a>
                            <?php }?>
                        </td>
                    </tr>
                    <?php
                }
                $query->closeCursor(); ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['commande_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Commandes</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                      <th style="width: 10px">#</th>
                      <th>Date de création</th>
                      <th>Nom</th>
                      <th>Fournisseur</th>
                      <th>TTC</th>
                      <th>Référence fournisseur</th>
                      <th>Etat</th>
                      <th>Etat centre de cout</th>
                      <th>Demandeur</th>
                      <th>Gerant</th>
                      <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $query = $db->query('SELECT c.idCommande, c.nomCommande, c.dateCreation, f.nomFournisseur, c.numCommandeFournisseur, e.libelleEtat, c.idEtat FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat LEFT OUTER JOIN FOURNISSEURS f ON c.idFournisseur = f.idFournisseur WHERE nomCommande LIKE "%'.$_POST['search'].'%" OR numCommandeFournisseur LIKE "%'.$_POST['search'].'%" OR nomFournisseur LIKE "%'.$_POST['search'].'%";');
                  while ($data = $query->fetch())
                  {?>
                      <tr <?php if ($_SESSION['commande_lecture']==1) {?>data-href="commandeView.php?id=<?=$data['idCommande']?>"<?php }?>>
                          <td><?php echo $data['idCommande']; ?></td>
                          <td><?php echo $data['dateCreation']; ?></td>
                          <td><?php echo $data['nomCommande']; ?></td>
                          <td><?php echo $data['nomFournisseur']; ?></td>
                          <td>
                            <?php
                              $query2 = $db->prepare('SELECT IFNULL(SUM(prixProduitTTC*quantiteCommande),0) AS total FROM COMMANDES_MATERIEL c LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue WHERE idCommande = :idCommande;');
                              $query2->execute(array('idCommande' => $data['idCommande']));
                              $total = $query2->fetch();
                              echo floor($total['total']*100)/100; echo ' €';
                            ?>
                          </td>
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
        <?php } ?>

        <?php
        if($_SESSION['commande_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Contenu des Commandes</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th>Matériel</th>
                  <th>Référence</th>
                  <th>Prix unitaire TTC</th>
                  <th>Quantité</th>
                  <th>Sous-Total</th>
                  <th>Commande</th>
                  <th>Accéder à la commande</th>
                </tr>
              </thead>
              <tbody>
                <?php
                      $query2 = $db->prepare('SELECT * FROM COMMANDES_MATERIEL c LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue LEFT OUTER JOIN COMMANDES g ON c.idCommande = g.idCommande WHERE libelleMateriel LIKE "%'.$_POST['search'].'%" ORDER BY libelleMateriel ASC ;');
                      $query2->execute(array('idCommande' => $_GET['id']));
                      $totalCMD = 0;
                      while ($data2 = $query2->fetch())
                      {
                          ?>
                            <tr>
                        <td><?php echo $data2['libelleMateriel'];?></td>
                        <td><?php echo $data2['referenceProduitFournisseur'];?></td>
                        <td><?php echo $data2['prixProduitTTC'];?> €</td>
                        <td><?php echo $data2['quantiteCommande'];?></td>
                        <td><?php echo $data2['prixProduitTTC']*$data2['quantiteCommande']; $totalCMD = $totalCMD + ($data2['prixProduitTTC']*$data2['quantiteCommande']);?> €</td>
                        <td><?php echo $data2['nomCommande'];?></td>
                        <td>
                          <?php if ($_SESSION['commande_lecture']==1) {?>
                                <a href="commandeView.php?id=<?=$data2['idCommande']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                            <?php } ?>
                        </td>
                      </tr>
                        <?php
                    }
                    $query2->closeCursor(); ?>
                  </tbody>
              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['vhf_canal_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Transmissions - Canaux</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Libelle</th>
                        <th>Technologie</th>
                        <th>Rx</th>
                        <th>Tx</th>
                        <th>CTCSS Rx</th>
                        <th>CTCSS Tx</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $query = $db->query('SELECT * FROM VHF_CANAL c LEFT OUTER JOIN VHF_TECHNOLOGIES t  ON c.idVhfTechno = t.idVhfTechno WHERE chName LIKE "%'.$_POST['search'].'%" OR rxFreq LIKE "%'.$_POST['search'].'%" OR txFreq LIKE "%'.$_POST['search'].'%" ORDER BY chName;');
                    while ($data = $query->fetch())
                    {?>
                        <tr <?php if ($_SESSION['vhf_canal_lecture']==1) {?>data-href="vhfCanauxContenu.php?id=<?=$data['idVhfCanal']?>"<?php }?>>
                            <td><?php echo $data['idVhfCanal']; ?></td>
                            <td><?php echo $data['chName']; ?></td>
                            <td><?php echo $data['libelleTechno']; ?></td>
                            <td><?php echo $data['rxFreq']; ?></td>
                            <td><?php echo $data['txFreq']; ?></td>
                            <td><?php echo $data['rxCtcss']; ?></td>
                            <td><?php echo $data['txCtcss']; ?></td>
                            <td>
                                <?php if ($_SESSION['vhf_canal_lecture']==1) {?>
                                    <a href="vhfCanauxContenu.php?id=<?=$data['idVhfCanal']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                <?php }?>
                                <?php if ($_SESSION['vhf_canal_modification']==1) {?>
                                    <a href="vhfCanauxForm.php?id=<?=$data['idVhfCanal']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                <?php }?>
                                <?php if ($_SESSION['vhf_canal_suppression']==1) {?>
                                    <a href="modalDeleteConfirm.php?case=vhfCanauxDelete&id=<?=$data['idVhfCanal']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                <?php }?>
                            </td>
                        </tr>
                        <?php
                    }
                    $query->closeCursor(); ?>
                    </tbody>
              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['vhf_plan_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Transmissions - Plans de fréquence</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Libelle</th>
                        <th>Nombre de canaux</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $query = $db->query('SELECT * FROM VHF_PLAN WHERE libellePlan LIKE "%'.$_POST['search'].'%";');
                    while ($data = $query->fetch())
                    {
                        $query2 = $db->prepare('SELECT COUNT(*) as nb FROM VHF_PLAN_CANAL WHERE idVhfPlan = :idVhfPlan');
                        $query2->execute(array(
                            'idVhfPlan' => $data['idVhfPlan']));
                        $data2 = $query2->fetch();
                        ?>
                        <tr <?php if ($_SESSION['vhf_plan_lecture']==1) {?>data-href="vhfPlansContenu.php?id=<?=$data['idVhfPlan']?>"<?php }?>>
                            <td><?php echo $data['idVhfPlan']; ?></td>
                            <td><?php echo $data['libellePlan']; ?></td>
                            <td><?php echo $data2['nb']; ?></td>
                            <td>
                                <?php if ($_SESSION['vhf_plan_lecture']==1) {?>
                                    <a href="vhfPlansContenu.php?id=<?=$data['idVhfPlan']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                <?php }?>
                                <?php if ($_SESSION['vhf_plan_modification']==1) {?>
                                    <a href="vhfPlansForm.php?id=<?=$data['idVhfPlan']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                <?php }?>
                                <?php if ($_SESSION['vhf_plan_suppression']==1) {?>
                                    <a href="modalDeleteConfirm.php?case=vhfPlansDelete&id=<?=$data['idVhfPlan']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                <?php }?>
                            </td>
                        </tr>
                        <?php
                    }
                    $query->closeCursor(); ?>
                    </tbody>
              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['vhf_equipement_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Transmissions - Equipements</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                      <th style="width: 10px">#</th>
                      <th>Indicatif</th>
                      <th>Type</th>
                      <th>Etat</th>
                      <th>Technologie</th>
                      <th>Plan de fréquence</th>
                      <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $query = $db->query('SELECT * FROM VHF_EQUIPEMENTS e LEFT OUTER JOIN VHF_PLAN p ON e.idVhfPlan = p.idVhfPlan LEFT OUTER JOIN VHF_TECHNOLOGIES t ON e.idVhfTechno = t.idVhfTechno LEFT OUTER JOIN VHF_ETATS s ON e.idVhfEtat=s.idVhfEtat LEFT OUTER JOIN VHF_TYPES_EQUIPEMENTS c ON e.idVhfType = c.idVhfType WHERE vhfIndicatif LIKE "%'.$_POST['search'].'%" ORDER BY vhfIndicatif;');
                  while ($data = $query->fetch())
                  {?>
                      <tr <?php if ($_SESSION['vhf_equipement_lecture']==1) {?>data-href="vhfEquipementsContenu.php?id=<?=$data['idVhfEquipement']?>"<?php }?>>
                          <td><?php echo $data['idVhfEquipement']; ?></td>
                          <td><?php echo $data['vhfIndicatif']; ?></td>
                          <td><?php echo $data['libelleType']; ?></td>
                          <td><?php echo $data['libelleVhfEtat']; ?></td>
                          <td><?php echo $data['libelleTechno']; ?></td>
                          <td><?php echo $data['libellePlan']; ?></td>
                          <td>
                              <?php if ($_SESSION['vhf_equipement_lecture']==1) {?>
                                  <a href="vhfEquipementsContenu.php?id=<?=$data['idVhfEquipement']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                              <?php }?>
                              <?php if ($_SESSION['vhf_equipement_modification']==1) {?>
                                  <a href="vhfEquipementsForm.php?id=<?=$data['idVhfEquipement']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                              <?php }?>
                              <?php if ($_SESSION['vhf_equipement_suppression']==1) {?>
                                  <a href="modalDeleteConfirm.php?case=vhfEquipementsDelete&id=<?=$data['idVhfEquipement']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                              <?php }?>
                          </td>
                      </tr>
                      <?php
                  }
                  $query->closeCursor(); ?>
                  </tbody>
              </table>
            </div>
          </div>
        <?php } ?>

        <?php
        if($_SESSION['vehicules_lecture']==1)
        { ?>
          <div class="box box-success">
            <div class="box-header">
              <i class="fa fa-search"></i>
              <h3 class="box-title">Transmissions - Equipements</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Libelle</th>
                        <th>Type</th>
                        <th>Etat</th>
                        <th>Responsable</th>
                        <th>Immatriculation</th>
                        <th>Marque/Modele</th>
                        <th>Contrôles</th>
                        <th>Notifications</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = $db->query('SELECT * FROM VEHICULES v LEFT OUTER JOIN ETATS e ON v.idEtat = e.idEtat LEFT OUTER JOIN VEHICULES_TYPES t ON v.idVehiculesType = t.idVehiculesType LEFT OUTER JOIN VEHICULES_ETATS ve ON v.idVehiculesEtat = ve.idVehiculesEtat LEFT OUTER JOIN PERSONNE_REFERENTE p ON v.idResponsable = p.idPersonne WHERE libelleVehicule LIKE "%'.$_POST['search'].'%" OR immatriculation LIKE "%'.$_POST['search'].'%";');
                while ($data = $query->fetch())
                {?>
                    <tr <?php if ($_SESSION['vehicules_lecture']==1) {?>data-href="vehiculesContenu.php?id=<?=$data['idVehicule']?>"<?php }?>>
                        <td><?php echo $data['idVehicule']; ?></td>
                        <td><?php echo $data['libelleVehicule']; ?></td>
                        <td><?php echo $data['libelleType']; ?></td>
                        <td><?php echo $data['libelleVehiculesEtat']; ?></td>
                        <td><?php echo $data['identifiant']; ?></td>
                        <td><?php echo $data['immatriculation']; ?></td>
                        <td><?php echo $data['marqueModele']; ?></td>
                        <td>
                            <span class="badge bg-<?php
                                if($data['dateNextRevision']<date('Y-m-d'))
                                {
                                    echo "red";
                                }
                                else
                                {
                                    if(date('Y-m-d')>=date('Y-m-d', strtotime($data['dateNextRevision'] . ' - '.$VEHICULES_REVISION_DELAIS_NOTIF.' days')))
                                    {
                                        echo "orange";
                                    }
                                    else
                                    {
                                        echo "green";
                                    }
                                }
                                ?>">Révision</span>
                            <span class="badge bg-<?php
                                if($data['dateNextCT']<date('Y-m-d'))
                                {
                                    echo "red";
                                }
                                else
                                {
                                    if(date('Y-m-d')>=date('Y-m-d', strtotime($data['dateNextCT'] . ' - '.$VEHICULES_CT_DELAIS_NOTIF.' days')))
                                    {
                                        echo "orange";
                                    }
                                    else
                                    {
                                        echo "green";
                                    }
                                }
                                ?>">CT</span>
                            <span class="badge bg-<?php
                                if($data['assuranceExpiration']<date('Y-m-d'))
                                {
                                    echo "red";
                                }
                                else
                                {
                                    if(date('Y-m-d')>=date('Y-m-d', strtotime($data['assuranceExpiration'] . ' - '.$VEHICULES_ASSURANCE_DELAIS_NOTIF.' days')))
                                    {
                                        echo "orange";
                                    }
                                    else
                                    {
                                        echo "green";
                                    }
                                }
                                ?>">Assurance</span>
                            <span class="badge bg-<?php
                                if($data['alerteDesinfection'] == Null)
                                {
                                    echo "grey";
                                }
                                else
                                {
                                    if($data['alerteDesinfection'] == 0)
                                    {
                                        echo "green";
                                    }
                                    else
                                    {
                                        echo "red";
                                    }
                                }
                                ?>">Désinfections</span>
                        </td>
                        <td><?php echo $data['libelleEtat']; ?> (<?php if($data['idEtat']!=1){echo '<i class="fa fa-bell-slash-o"></i>';}else{echo '<i class="fa fa-bell-o"></i>';} ?>)</td>
                        <td>
                            <?php if ($_SESSION['vehicules_lecture']==1) {?>
                                <a href="vehiculesContenu.php?id=<?=$data['idVehicule']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                            <?php }?>
                            <?php if ($_SESSION['vehicules_modification']==1) {?>
                                <a href="vehiculesForm.php?id=<?=$data['idVehicule']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                            <?php }?>
                            <?php if ($_SESSION['vehicules_suppression']==1) {?>
                                <a href="modalDeleteConfirm.php?case=vehiculesDelete&id=<?=$data['idVehicule']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                            <?php }?>
                        </td>
                    </tr>
                    <?php
                }
                $query->closeCursor(); ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php } ?>


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
