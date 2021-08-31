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
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'checkLotsConf.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE idLot = :idLot;');
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
    $query5 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE s.idLot = :idLot AND (peremption < CURRENT_DATE OR peremption = CURRENT_DATE);');
    $query5->execute(array(
        'idLot' => $_GET['id']
    ));
    $data5 = $query5->fetch();
    $query6 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE s.idLot = :idLot AND (quantite < quantiteAlerte OR quantite = quantiteAlerte);');
    $query6->execute(array(
        'idLot' => $_GET['id']
    ));
    $data6 = $query6->fetch();

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
                                <li><a>Référentiel <span class="pull-right"><?php echo $data['libelleTypeLot']; ?></span></a></li>
                                <li><a>Nombre de sacs <span class="pull-right"><?php echo $data2['nb']; ?></span></a></li>
                                <li><a>Nombre d'emplacements <span class="pull-right"><?php echo $data3['nb']; ?></span></a></li>
                                <li><a>Quantite de matériel <span class="pull-right"><?php echo $data4['nb']; ?></span></a></li>
                                <li><a>Personne responsable <span class="pull-right"><?php echo $data['identifiant']; ?></span></a></li>
                                <li><a>Dernier inventaire <span class="pull-right"><?php echo $data['dateDernierInventaire']; ?></span></a></li>
                                <li><a>Fréquence d'inventaire <span class="pull-right"><?php echo $data['frequenceInventaire']; ?></span></a></li>
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
                                            if (checkLotsConf($_GET['id']))
                                            {
                                                echo '<span class="pull-right badge bg-red">Lot non-conforme</span>';
                                            }
                                            else
                                            {
                                                echo '<span class="pull-right badge bg-green">Lot conforme</span>';
                                            }
                                        }
                                        ?></span></a></li>
                                <li><a>Elements périmés<?php
                                        if ($data5['nb']>0)
                                        {
                                            echo '<span class="pull-right badge bg-red">' . $data5['nb'] .'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="pull-right badge bg-green">' . $data5['nb'] .'</span>';
                                        }
                                        ?></span></a></li>
                                <li><a>Matériel manquant<?php
                                        if ($data6['nb']>0)
                                        {
                                            echo '<span class="pull-right badge bg-red">' . $data6['nb'] .'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="pull-right badge bg-green">' . $data6['nb'] .'</span>';
                                        }
                                        ?></span></a></li>
                                <li><a>Inventaire<?php
                                    if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) <= date('Y-m-d'))
                                    {
                                        echo '<span class="pull-right badge bg-red">En retard</span>';
                                    }
                                    else
                                    {
                                        echo '<span class="pull-right badge bg-green">A jour</span>';
                                    }
                                        ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>

                <div class="col-md-12">
                    <div class="box box-success collapsed-box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Contenu de: <?php echo $data['libelleLot']; ?></h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
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
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
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
                                                    <div class="box box-warning box-solid">
                                                        <div class="box-header with-border">
                                                            <h3 class="box-title"><?php echo $data8['libelleEmplacement']; ?></h3>
                                                            <div class="box-tools pull-right">
                                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
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
                                                                    <th>Actions</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                $query9 = $db->prepare('SELECT * FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue WHERE idEmplacement = :idEmplacement;');
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
                                                                        <td>
                                                                            <?php if ($_SESSION['materiel_modification']==1) {?>
                                                                                <a href="materielsForm.php?id=<?=$data9['idElement']?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                                                                            <?php }?>
                                                                            <?php if ($_SESSION['materiel_suppression']==1) {?>
                                                                                <a href="materielsDelete.php?id=<?=$data9['idElement']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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
                                    <h3 class="box-title">Détails de l'analyse de conformité au référentiel</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table id="tri1" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Matériel</th>
                                            <th>Stérilité requise</th>
                                            <th>Quantité requise par le référentiel</th>
                                            <th>Quantité présente dans le lot</th>
                                            <th>Péremption dans le lot</th>
                                            <th>Analyse de conformité</th>
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
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php if ($_SESSION['lots_modification']==1) {?>
                                <a href="lotsInventaireNew.php?id=<?php echo $_GET['id']; ?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Faire un nouvel inventaire</a>
                            <?php }?>
                            <?php if ($_SESSION['lots_lecture']==1) {?>
                                <a href="lotsInventaire.php?id=<?=$data['idLot']?>" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-print"></i> Imprimer un état des lieux</a>
                            <?php }?>
                        </div>
                        <div class="box-body">

                            <br/>
                            <table id="tri2R" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Date de l'inventaire</th>
                                    <th>Personne ayant réalisé l'inventaire</th>
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
                                            <td>
                                                <a href="lotsInventaireShow.php?id=<?=$data2['idInventaire']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                                <?php if ($_SESSION['lots_modification']==1) {?>
                                                    <a href="lotsInventaireDelete.php?id=<?php echo $data2['idInventaire']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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



