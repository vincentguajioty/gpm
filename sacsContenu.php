<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 102;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['sac_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'checkLotsConf.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE idSac = :idSac;');
    $query->execute(array(
        'idSac' => $_GET['id']
    ));
    $data = $query->fetch();

    $query3 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac;');
    $query3->execute(array(
        'idSac' => $_GET['id']
    ));
    $data3 = $query3->fetch();

    $query4 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement WHERE idSac = :idSac;');
    $query4->execute(array(
        'idSac' => $_GET['id']
    ));
    $data4 = $query4->fetch();

    $query5 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idSac = :idSac AND (peremption < CURRENT_DATE OR peremption = CURRENT_DATE);');
    $query5->execute(array(
        'idSac' => $_GET['id']
    ));
    $data5 = $query5->fetch();

    $query6 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idSac = :idSac AND (quantite < quantiteAlerte OR quantite = quantiteAlerte);');
    $query6->execute(array(
        'idSac' => $_GET['id']
    ));
    $data6 = $query6->fetch();

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gestion du sac: <?php echo $data['libelleSac']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="sacs.php">Sacs</a></li>
                <li class="active"><?php echo $data['libelleSac']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

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
                            <li><a href="lotsContenu.php?id=<?php echo $data['idLot'];?>">Lot de rattachement <span class="pull-right"><?php echo $data['libelleLot']; ?></span></a></li>
                            <li><a>Personne responsable <span class="pull-right"><?php echo $data['identifiant']; ?></span></a></li>
                            <li><a>Nombre d'emplacements <span class="pull-right"><?php echo $data3['nb']; ?></span></a></li>
                            <li><a>Quantite de matériel <span class="pull-right"><?php echo $data4['nb']; ?></span></a></li>
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
                        </ul>
                    </div>
                </div>
                <!-- /.widget-user -->
            </div>


                <div class="col-md-12">
                    <div class="box box-info box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $data['libelleSac']; ?></h3> <?php if ($_SESSION['sac_modification']==1) {?><a href="sacsForm.php?id=<?=$data['idSac']?>" class="btn btn-xs modal-form"><i class="fa fa-pencil"></i></a><?php }?>
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
                                'idSac' => $data['idSac']
                            ));
                            while ($data8 = $query8->fetch())
                            { ?>
                                <div class="col-md-12">
                                    <div class="box box-warning box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><?php echo $data8['libelleEmplacement']; ?></h3> <?php if ($_SESSION['sac2_modification']==1) {?><a href="emplacementsForm.php?id=<?=$data8['idEmplacement']?>" class="btn btn-xs modal-form"><i class="fa fa-pencil"></i></a><?php }?>
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
                                                                <a href="materielsForm.php?id=<?=$data9['idElement']?>" class="btn btn-xs btn-warning modal-form"><i class="fa fa-pencil"></i></a>
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
            <div class="row"></div>
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



