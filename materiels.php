<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 104;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['materiel_lecture']==0)
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
                Gestion du matériel et des consommables
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Matériel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['materiel_ajout']==1) {?>
                	<div class="box-header">
                        <h3 class="box-title"><a href="materielsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un materiel</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Emplacement</th>
                                <th class="not-mobile">Sac</th>
                                <th class="not-mobile">Lot</th>
                                <th class="not-mobile">Quantité</th>
                                <th class="not-mobile">Péremption</th>
                                <th class="not-mobile">Etat</th>
                                <th class="not-mobile">Notifications</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN MATERIEL_ETATS me ON m.idMaterielsEtat = me.idMaterielsEtat ORDER BY libelleMateriel ASC;');
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


