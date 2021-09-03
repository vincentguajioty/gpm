<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lots_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>

    <?php
    $query = $db->prepare('
        SELECT
            *
        FROM
            LOTS_LOTS l
            LEFT OUTER JOIN LIEUX w ON l.idLieu = w.idLieu
            LEFT OUTER JOIN VEHICULES v ON l.idVehicule = v.idVehicule
            LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot
            LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne
            LEFT OUTER JOIN LOTS_ETATS et ON l.idLotsEtat = et.idLotsEtat
        WHERE
            idLot = :idLot;');
    $query->execute(array(
        'idLot' => $_GET['id']
    ));
    $data = $query->fetch();
    $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_SAC WHERE idLot = :idLot;');
    $query2->execute(array(
        'idLot' => $_GET['id']
    ));
    $data2 = $query2->fetch();
    $query3 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE idLot = :idLot;');
    $query3->execute(array(
        'idLot' => $_GET['id']
    ));
    $data3 = $query3->fetch();
    $query4 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE idLot = :idLot;');
    $query4->execute(array(
        'idLot' => $_GET['id']
    ));
    $data4 = $query4->fetch();
    $query5 = $db->prepare('
        SELECT
            COUNT(*) as nb
        FROM
            MATERIEL_ELEMENT m
            LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
            LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
            LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
            LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
        WHERE
            s.idLot = :idLot
            AND
            (peremption < CURRENT_DATE OR peremption = CURRENT_DATE)
        ;');
    $query5->execute(array(
        'idLot' => $_GET['id']
    ));
    $data5 = $query5->fetch();
    $query6 = $db->prepare('
        SELECT
            COUNT(*) as nb
        FROM
            MATERIEL_ELEMENT m
            LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
            LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac
            LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot
            LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
        WHERE
            s.idLot = :idLot
            AND
            (quantite < quantiteAlerte OR quantite = quantiteAlerte)
        ;');
    $query6->execute(array(
        'idLot' => $_GET['id']
    ));
    $data6 = $query6->fetch();
    $query13 = $db->prepare('
        SELECT
            e.*,
            COUNT(idLot) as nb
        FROM
            LOTS_ALERTES_ETATS e
            LEFT OUTER JOIN (SELECT * FROM LOTS_ALERTES WHERE idLot = :idLot) a ON e.idLotsAlertesEtat = a.idLotsAlertesEtat
        GROUP BY
            e.idLotsAlertesEtat
        ORDER BY
            e.idLotsAlertesEtat DESC
        ;');
    $query13->execute(array(
        'idLot' => $_GET['id']
    ));

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gestion du lot: <?php echo $data['libelleLot']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="lots.php">Lots</a></li>
                <li class="active"><?php echo $data['libelleLot']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <?php
                if($data['inventaireEnCours'])
                {
                    echo '<div class="alert alert-warning alert-dismissible">';
                    echo '<i class="icon fa fa-warning"></i> Ce lot est en cours d\'inventaire et est donc verrouillé en lecture seule.';
                    echo '</div>';
                }
            ?>
            <div class="row">

                <div class="col-md-6">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-blue">
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Informations générales</h3>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li>
                                    <a>Référentiel <span class="pull-right"><?php echo $data['libelleTypeLot']; ?></span></a>
                                </li>
                                <li>
                                    <a>Nombre de sacs <span class="pull-right"><?php echo $data2['nb']; ?></span></a>
                                </li>
                                <li>
                                    <a>Nombre d'emplacements <span class="pull-right"><?php echo $data3['nb']; ?></span></a>
                                </li>
                                <li>
                                    <a>Quantite de matériel <span class="pull-right"><?php echo $data4['nb']; ?></span></a>
                                </li>
                                <li>
                                    <a <?php if($_SESSION['annuaire_lecture'] AND isset($data['idPersonne'])){echo 'href="annuaireContenu.php?id='.$data['idPersonne'].'"';} ?>>Personne responsable <span class="pull-right"><?php echo $data['identifiant']; ?></span></a>
                                </li>
                                <li>
                                    <a>Lieu <span class="pull-right"><?php echo $data['libelleLieu']; ?></span></a>
                                </li>
                                <li>
                                    <a <?php if($_SESSION['vehicules_lecture'] AND isset($data['idVehicule'])){echo 'href="vehiculesContenu.php?id='.$data['idVehicule'].'"';} ?>>Véhicule <span class="pull-right"><?php echo $data['libelleVehicule']; ?></span></a>
                                </li>
                                <li>
                                    <a>Dernier inventaire <span class="pull-right"><?php echo $data['dateDernierInventaire']; ?></span></a>
                                </li>
                                <li>
                                    <a>Fréquence d'inventaire <span class="pull-right"><?php echo $data['frequenceInventaire']; ?></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>

                <div class="col-md-6">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-blue">
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Verifications</h3>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li><a>Conformité au référentiel<?php
                                        if ($data['libelleTypeLot'] == Null)
                                        {
                                            echo '<span class="pull-right badge bg-orange">NA</span>';
                                        }
                                        else
                                        {
                                            if ($data['alerteConfRef'] == 1)
                                            {
                                                echo '<span class="pull-right badge bg-red">Lot non-conforme</span>';
                                            }
                                            else
                                            {
                                                echo '<span class="pull-right badge bg-green">Lot conforme</span>';
                                            }
                                        }
                                        ?></a></li>
                                <li><a <?php if ($data5['nb']>0){echo 'data-toggle="modal" data-target="#modalLotAlertePeremption"';}?>>Elements périmés<?php
                                        if ($data5['nb']>0)
                                        {
                                            echo '<span class="pull-right badge bg-red">' . $data5['nb'] .'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="pull-right badge bg-green">' . $data5['nb'] .'</span>';
                                        }
                                        ?></a></li>
                                <li><a <?php if ($data6['nb']>0){echo 'data-toggle="modal" data-target="#modalLotAlerteQuantité"';}?>>Matériel manquant<?php
                                        if ($data6['nb']>0)
                                        {
                                            echo '<span class="pull-right badge bg-red">' . $data6['nb'] .'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="pull-right badge bg-green">' . $data6['nb'] .'</span>';
                                        }
                                        ?></a></li>
                                <li><a>Prochain inventaire<?php
                                    if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) <= date('Y-m-d'))
                                    {
                                        echo '<span class="pull-right badge bg-red">' . date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) . '</span>';
                                    }
                                    else
                                    {
                                        echo '<span class="pull-right badge bg-green">' . date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) . '</span>';
                                    }
                                        ?></a></li>
                                <li><a>Etat <span class="pull-right"><?php echo $data['libelleLotsEtat']; ?></span></a></li>
                                <li><a>Notifications<?php
                                    if ($data[3]==1) /*Attention, indice passé en fonction de la requete -> si la requete change vérifier que l'indice soit toujours en même position.*/
                                    {
                                        echo '<span class="pull-right badge bg-green" title="Notifications activées"><i class="fa fa-bell-o"></i></span>';
                                    }
                                    else
                                    {
                                        echo '<span class="pull-right badge bg-orange" title="Notifications désactivées"><i class="fa fa-bell-slash-o"></i></span>';
                                    }
                                        ?></a></li>
                                <li><a>Alertes bénévoles<?php
                                    while($data13 = $query13->fetch())
                                    { ?>
                                        <span class="pull-right badge bg-<?=$data13['couleurLotsAlertesEtat']?>" title="<?=$data13['libelleLotsAlertesEtat']?>"><?=$data13['nb']?></span>
                                    <?php } ?>
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>

                <div class="col-md-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Contenu de: <?php echo $data['libelleLot']; ?></h3>
                            <?php if ($_SESSION['lots_modification']==1 AND $data['inventaireEnCours']==Null) {?><a href="lotsForm.php?id=<?=$_GET['id']?>" class="btn btn-xs modal-form" title="Modifier"><i class="fa fa-pencil"></i></a><?php }?>
                            <?php if ($_SESSION['lots_lecture']==1 AND $data['inventaireEnCours']==Null) {?><a href="lotsInventaire.php?id=<?=$data['idLot']?>" target="_blank" class="btn btn-xs" title="Imprimer le dernier inventaire"><i class="fa fa-print"></i> Impresion inventaire</a><?php }?>
                            <?php if ($_SESSION['codeBarre_lecture']==1) {?><a href="lotsCBPrintForm.php?id=<?=$data['idLot']?>" class="btn btn-xs modal-form" title="Imprimer tous les codes barre emplacement de ce lot"><i class="fa fa-barcode"></i> Impression codes barre emplacements</a><?php }?>
                            <?php if ($_SESSION['materiel_ajout']==1 AND $data3['nb']>0 AND $data['idTypeLot'] != Null AND $data['inventaireEnCours']==Null) {?><a href="lotsImportRef.php?id=<?=$data['idLot']?>" class="btn btn-xs" title="Importer dans ce lot le matériel du référentiel indiqué" <?php if($data4['nb']!=0){?>onClick="javascript: return confirm('Ce lot n\'est pas vide. Etes vous sur de vouloir faire un import ?');"<?php } ?>><i class="fa fa-bank"></i> Importer référentiel</a><?php }?>
                            <div class="box-tools pull-right">
                            	<?php if ($_SESSION['sac_ajout']==1 AND $data['inventaireEnCours']==Null) {?><a href="sacsForm.php?idParent=<?= $_GET['id'] ?>" class="btn btn-sm btn-success modal-form">Ajouter un sac</a><?php } ?>
                                <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <?php
                            $query7 = $db->prepare('SELECT * FROM MATERIEL_SAC WHERE idLot = :idLot;');
                            $query7->execute(array(
                                'idLot' => $_GET['id']
                            ));
                            while ($data7 = $query7->fetch())
                            { ?>
                                <div class="col-md-12">
                                    <div class="box box-info box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><?php echo $data7['libelleSac']; ?></h3>
                                            <?php if ($_SESSION['sac_modification']==1 AND $data['inventaireEnCours']==Null) {?><a href="sacsForm.php?id=<?=$data7['idSac']?>" class="btn btn-xs modal-form" title="Modifier"><i class="fa fa-pencil"></i></a><?php }?>
                                            <?php if ($_SESSION['codeBarre_lecture']==1) {?><a href="sacsCBPrintForm.php?id=<?=$data7['idSac']?>" class="btn btn-xs modal-form" title="Imprimer tous les codes barre emplacement de ce sac"><i class="fa fa-barcode"></i></a><?php }?>
                                            <div class="box-tools pull-right">
                                            	<?php if ($_SESSION['sac2_ajout']==1 AND $data['inventaireEnCours']==Null) {?><a href="emplacementsForm.php?idParent=<?= $data7['idSac'] ?>" class="btn btn-sm btn-info modal-form">Ajouter un emplacement</a><?php } ?>
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <!-- /.box-tools -->
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">
                                            <?php
                                            $query8 = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac;');
                                            $query8->execute(array(
                                                'idSac' => $data7['idSac']
                                            ));
                                            while ($data8 = $query8->fetch())
                                            { ?>
                                                <div class="col-md-12">
                                                    <div class="box box-warning collapsed-box box-solid">
                                                        <div class="box-header with-border">
                                                            <h3 class="box-title"><?php echo $data8['libelleEmplacement']; ?></h3>
                                                            <?php if ($_SESSION['sac2_modification']==1 AND $data['inventaireEnCours']==Null) {?><a href="emplacementsForm.php?id=<?=$data8['idEmplacement']?>" class="btn btn-xs modal-form" title="Modifier"><i class="fa fa-pencil"></i></a><?php }?>
                                                            <?php if ($_SESSION['codeBarre_lecture']==1) {?><a href="emplacementsCBPrintForm.php?id=<?=$data8['idEmplacement']?>" class="btn btn-xs modal-form" title="Imprimer le code barre de cet emplacement"><i class="fa fa-barcode"></i></a><?php }?>
                                                            <div class="box-tools pull-right">
                                                            	<?php if ($_SESSION['materiel_ajout']==1 AND $data['inventaireEnCours']==Null) {?><a href="materielsForm.php?idParent=<?= $data8['idEmplacement'] ?>" class="btn btn-sm btn-warning modal-form">Ajouter un materiel</a><?php } ?>
                                                                <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i>
                                                                </button>
                                                            </div>
                                                            <!-- /.box-tools -->
                                                        </div>
                                                        <!-- /.box-header -->
                                                        <div class="box-body">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                <tr>
                                                                    <th style="width: 10px">#</th>
                                                                    <th>Libelle</th>
                                                                    <th>Quantité</th>
                                                                    <th>Péremption</th>
                                                                    <th>Etat</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                $query9 = $db->prepare('SELECT * FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN MATERIEL_ETATS me ON e.idMaterielsEtat = me.idMaterielsEtat WHERE idEmplacement = :idEmplacement;');
                                                                $query9->execute(array(
                                                                    'idEmplacement' => $data8['idEmplacement']
                                                                ));
                                                                while ($data9 = $query9->fetch())
                                                                { ?>
                                                                    <tr>
                                                                        <td><?php echo $data9['idElement']; ?></td>
                                                                        <td><?php echo $data9['libelleMateriel']; ?></td>
                                                                        <td><?php
                                                                            if ($data9['quantite'] < $data9['quantiteAlerte'])
                                                                            {
                                                                                ?><span class="badge bg-red"><?php echo $data9['quantite']; ?></span><?php
                                                                            }
                                                                            else if ($data9['quantite'] == $data9['quantiteAlerte'])
                                                                            {
                                                                                ?><span class="badge bg-orange"><?php echo $data9['quantite']; ?></span><?php
                                                                            }
                                                                            else
                                                                            {
                                                                                ?><span class="badge bg-green"><?php echo $data9['quantite']; ?></span><?php
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td><?php
                                                                            if ($data9['peremption'] <= date('Y-m-d'))
                                                                            {
                                                                                ?><span class="badge bg-red"><?php echo $data9['peremption']; ?></span><?php
                                                                            }
                                                                            else if ($data9['peremptionNotification'] <= date('Y-m-d'))
                                                                            {
                                                                                ?><span class="badge bg-orange"><?php echo $data9['peremption']; ?></span><?php
                                                                            }
                                                                            else
                                                                            {
                                                                                ?><span class="badge bg-green"><?php echo $data9['peremption']; ?></span><?php
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td><?= $data9['libelleMaterielsEtat'] ?></td>
                                                                        <td>
                                                                            <?php if ($_SESSION['reserve_ReserveVersLot']==1 AND $data['inventaireEnCours']==Null) {?>
                                                                            	<a href="transfertResLotsFromLots.php?idElement=<?=$data9['idElement']?>&idMaterielCatalogue=<?=$data9['idMaterielCatalogue']?>" class="btn btn-xs btn-success modal-form" title="Approvisionner depuis la réserve"><i class="fa fa-exchange"></i></a>
                                                                            <?php }?>
                                                                            <?php if ($_SESSION['materiel_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                                                            	<a href="materielsForm.php?id=<?=$data9['idElement']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                                                            <?php }?>
                                                                            <?php if ($_SESSION['materiel_suppression']==1 AND $data['inventaireEnCours']==Null) {?>
                                                                                <a href="modalDeleteConfirm.php?case=materielsDelete&id=<?=$data9['idElement']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                                                            <?php }?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                 ?>
                                                                </tbody>


                                                            </table>

                                                        </div>
                                                        <!-- /.box-body -->
                                                    </div>
                                                    <!-- /.box -->
                                                </div>
                                            <?php }
                                            ?>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                    <!-- /.box -->
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>

                <?php
                    if (($data['libelleTypeLot'] != Null)) {
                        ?>
                        <div class="col-md-12">
                            <div class="box box-success collapsed-box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Détails de l'analyse de conformité au référentiel <a href="lotsCheckConfOneManu.php?id=<?= $_GET['id'] ?>" class="btn btn-xs" title="Analyser à nouveau ce lot"><i class="fa fa-refresh"></i></a></h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i
                                                    class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table id="tri1" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th class="all">Matériel</th>
                                            <th class="not-mobile">Stérilité requise</th>
                                            <th class="not-mobile">Quantité requise par le référentiel</th>
                                            <th class="not-mobile">Quantité présente dans le lot</th>
                                            <th class="not-mobile">Péremption dans le lot</th>
                                            <th class="all">Analyse de conformité</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $query10 = $db->prepare('SELECT * FROM REFERENTIELS r LEFT OUTER JOIN MATERIEL_CATALOGUE c ON r.idMaterielCatalogue = c.idMaterielCatalogue WHERE r.idTypeLot = :idTypeLot AND r.obligatoire = 1;');
                                        $query10->execute(array(
                                            'idTypeLot' => $data['idTypeLot']
                                        ));
                                        while ($data10 = $query10->fetch()) {
                                            ?>
                                            <tr>
                                                <td><?php echo $data10['libelleMateriel']; ?></td>
                                                <td><?php
                                                    if ($data10['sterilite'] == 0) {
                                                        echo "Non-Stérile";
                                                    } else {
                                                        echo "Stérile";
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $data10['quantiteReferentiel']; ?></td>
                                                <?php
                                                $query11 = $db->prepare('SELECT SUM(quantite) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE s.idLot = :idLot AND idMaterielCatalogue = :idMaterielCatalogue;');
                                                $query11->execute(array(
                                                    'idLot' => $_GET['id'],
                                                    'idMaterielCatalogue' => $data10['idMaterielCatalogue']
                                                ));
                                                $data11 = $query11->fetch();
                                                ?>
                                                <td><?php
                                                    if ($data11['nb'] == 0) {
                                                        echo "0";
                                                    } else {
                                                        echo $data11['nb'];
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                                $query12 = $db->prepare('SELECT MIN(peremption) as peremption FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE s.idLot = :idLot AND idMaterielCatalogue = :idMaterielCatalogue;');
                                                $query12->execute(array(
                                                    'idLot' => $_GET['id'],
                                                    'idMaterielCatalogue' => $data10['idMaterielCatalogue']
                                                ));
                                                $data12 = $query12->fetch();
                                                ?>
                                                <td><?php echo $data12['peremption']; ?></td>
                                                <td><?php
                                                    if ($data10['sterilite'] == 0) {
                                                        if ($data11['nb'] >= $data10['quantiteReferentiel']) {
                                                            ?><span class="badge bg-green">OK</span><?php
                                                        } else {
                                                            ?><span class="badge bg-red">Quantité</span><?php
                                                        }
                                                    } else {
                                                        if (($data11['nb'] < $data10['quantiteReferentiel']) AND ($data12['peremption'] <= date('Y-m-d'))) {
                                                            ?><span class="badge bg-red">Péremption</span><span
                                                                    class="badge bg-red">Quantité</span><?php
                                                        } else {
                                                            if ($data11['nb'] < $data10['quantiteReferentiel']) {
                                                                ?><span class="badge bg-red">Quantité</span><?php
                                                            } else {
                                                                if ($data12['peremption'] <= date('Y-m-d')) {
                                                                    ?><span class="badge bg-red">Péremption</span><?php
                                                                } else {
                                                                    ?><span class="badge bg-green">OK</span><?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $query10->closeCursor(); ?>
                                        </tbody>


                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
                
                <div class="col-md-12">
                    <div class="box box-success collapsed-box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Inventaires</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php if ($_SESSION['lots_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                <a href="lotsInventaireNew.php?id=<?php echo $_GET['id']; ?>" class="btn btn-app spinnerAttenteClick">
                                    <i class="fa fa-hand-paper-o"></i> Inventaire manuel
                                </a>
                                <a href="lotsInventaireCBNew.php?id=<?php echo $_GET['id']; ?>" class="btn btn-app spinnerAttenteClick">
                                    <i class="fa fa-barcode"></i> Inventaire scanné - Emplacement par Emplacement
                                </a>
                                <a href="lotsInventaireCBVNew.php?id=<?php echo $_GET['id']; ?>" class="btn btn-app spinnerAttenteClick">
                                    <i class="fa fa-barcode"></i> Inventaire scanné - A la chaine
                                </a>
                            <?php }?>

                            <a href="lotsInventaireHelp.php?id=<?php echo $_GET['id']; ?>" class="btn btn-app spinnerAttenteClick pull-right">
                                <i class="fa fa-book"></i> Fiche d'assistance
                            </a>
                            <br/><br/>

                            <?php if ($_SESSION['lots_modification']==1 AND $data['inventaireEnCours']==1) {?>
                                <a data-toggle="modal" data-target="#modalSuppressionLockUnite" class="btn btn-sm btn-danger">Désactiver le verrouillage</a>
                                <br/><br/>
                            <?php } ?>



                            <table id="tri2R" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Date de l'inventaire</th>
                                    <th>Personne ayant réalisé l'inventaire</th>
                                    <th>Commentaires</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM INVENTAIRES i LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne WHERE idLot = :idLot;');
                                    $query2->execute(array(
                                        'idLot' => $_GET['id']
                                    ));
                                    while ($data2 = $query2->fetch()) {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['idInventaire']; ?></td>
                                            <td><?php echo $data2['dateInventaire']; ?></td>
                                            <td><?php echo $data2['identifiant']; ?></td>
                                            <td><?php echo nl2br($data2['commentairesInventaire']); ?></td>
                                            <td>
                                                <a href="lotsInventaireShow.php?id=<?=$data2['idInventaire']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                                <?php if ($_SESSION['lots_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                                    <a href="modalDeleteConfirm.php?case=lotsInventaireDelete&id=<?=$data2['idInventaire']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <?php
                                    }
                                    ?>
                                </tbody>


                            </table>
                        </div>
                    </div>
                </div>

                <?php
                    if ($_SESSION['alertesBenevolesLots_lecture']==1){ ?>
                        <div class="col-md-12">
                            <div class="box box-success collapsed-box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Alertes des bénévoles</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table id="tri3R" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="all" style="width: 10px">#</th>
                                                <th class="all">Bénévole</th>
                                                <th class="not-mobile">Date d'ouverture</th>
                                                <th class="not-mobile">Message</th>
                                                <th class="not-mobile">Traitement</th>
                                                <th class="not-mobile">Affectation</th>
                                                <th class="not-mobile">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $query14 = $db->prepare('
                                            SELECT
                                                a.*,
                                                e.libelleLotsAlertesEtat,
                                                e.couleurLotsAlertesEtat,
                                                p.identifiant
                                            FROM
                                                LOTS_ALERTES a
                                                LEFT OUTER JOIN LOTS_LOTS l ON a.idLot = l.idLot
                                                LEFT OUTER JOIN LOTS_ALERTES_ETATS e on a.idLotsAlertesEtat = e.idLotsAlertesEtat
                                                LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
                                            WHERE
                                                a.idLot = :idLot
                                            ORDER BY
                                                idLotsAlertesEtat
                                            ;');
                                        $query14->execute(array('idLot' => $_GET['id']));
                                        while ($data14 = $query14->fetch())
                                        {
                                            ?>
                                            <tr>
                                                <td><?= $data14['idAlerte'] ?></td>
                                                <td><?= $data14['nomDeclarant'] ?></td>
                                                <td><?= $data14['dateCreationAlerte'] ?></td>
                                                <td><?= nl2br($data14['messageAlerteLot']) ?></td>
                                                <td><span class="badge bg-<?= $data14['couleurLotsAlertesEtat'] ?>"><?= $data14['libelleLotsAlertesEtat'] ?></span></td>
                                                <td>
                                                    <?php if($data14['idLotsAlertesEtat']==1){?>
                                                        <a href="lotsAlerteBenevoleAffectation.php?id=<?=$data14['idAlerte']?>" class="btn btn-xs btn-success" title="S'affecter cette alerte">Je prends en charge cette alerte</a>
                                                        <br/>
                                                        <a href="lotsAlerteBenevoleAffectationTiers.php?id=<?=$data14['idAlerte']?>" class="btn btn-xs btn-success modal-form" title="Affecter cette alerte à une personne de l'équipe">Affecter l'alerte à quelqu'un</a>
                                                    <?php } ?>
                                                    <?= $data14['identifiant'] ?>
                                                </td>
                                                <td>
                                                    <?php if($data14['idLotsAlertesEtat']==1){?><a href="lotsAlerteBenevoleDoublon.php?id=<?=$data14['idAlerte']?>" class="btn btn-xs btn-warning modal-form" title="Cette alerte bénévole fait doublon à une alerte déjà remontée">Signaler un doublon</a><?php } ?>
                                                    <?php if($data14['idLotsAlertesEtat']==1){?><a href="lotsAlerteBenevoleLockIpConfirmation.php?id=<?=$data14['idAlerte']?>" class="btn btn-xs btn-danger modal-form" title="Cette entrée est frauduleuse">Fraude</a><?php } ?>
                                                    <?php if($data14['idLotsAlertesEtat']==2 OR $data14['idLotsAlertesEtat']==3){?><a href="lotsAlerteBenevoleCloture.php?id=<?=$data14['idAlerte']?>" class="btn btn-xs btn-success" title="Cette alerte est traitée et doit être close">Clôturer cette alerte</a><?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $query->closeCursor(); ?>
                                        </tbody>
                                    </table>
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
</body>

<div class="modal fade modal-danger" id="modalLotAlertePeremption">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de péremption</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (peremptionNotification < CURRENT_DATE OR peremptionNotification = CURRENT_DATE OR peremption < CURRENT_DATE OR peremption = CURRENT_DATE) AND idLot = :idLot;');
                $query->execute(array('idLot'=>$_GET['id']));
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
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-danger" id="modalLotAlerteQuantité">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Alertes de quantité</h4>
            </div>
            <div class="modal-body">
                <?php
                $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE (quantite < quantiteAlerte OR quantite = quantiteAlerte) AND idLot = :idLot;');
                $query->execute(array('idLot'=>$_GET['id']));
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
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-danger" id="modalSuppressionLockUnite">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Inventaires en cours</h4>
            </div>
            <div class="modal-body">
                L'inventaire de ce lot est en cours. En conséquent, plusieurs fonctionnalités du site sont verouillées en lecture seule. Le verrouillage s'enlèvera automatiquement dès que tous les inventaires en cours seront validés. Si ce verrouillage est à tors, vous pouvez forcer le déverrouillage manuellement ici.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <a href="lotsInventaireNewAbort.php?id=<?=$_GET['id']?>"><button type="button" class="btn btn-default pull-right">Forcer le déverrouillage</button></a>
            </div>
        </div>
    </div>
</div>

</html>



