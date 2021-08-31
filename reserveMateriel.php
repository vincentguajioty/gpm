<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 702;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['reserve_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
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
                Gestion du matériel et des consommables stockés dans la réserve
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Réserve</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['reserve_ajout']==1) {?>
                	<div class="box-header">
                        <h3 class="box-title"><a href="reserveMaterielForm.php" class="btn btn-sm btn-success modal-form">Ajouter un materiel</a></h3>
                    
                </div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Conteneur</th>
                                <th class="not-mobile">Quantité</th>
                                <th class="not-mobile">Péremption</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue;');
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


