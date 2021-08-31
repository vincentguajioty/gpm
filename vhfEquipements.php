<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 903;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['vhf_equipement_lecture']==0)
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
                Gestion des équipements radio
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Transmission</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box">
                <?php if ($_SESSION['vhf_equipement_ajout']==1) {?>
                	<div class="box-header">
                        <h3 class="box-title"><a href="vhfEquipementsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un équipement</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="all" style="width: 10px">#</th>
                            <th class="all">Indicatif</th>
                            <th class="not-mobile">Type</th>
                            <th class="not-mobile">Etat</th>
                            <th class="not-mobile">Technologie</th>
                            <th class="not-mobile">Plan de fréquence</th>
                            <th class="not-mobile">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM VHF_EQUIPEMENTS e LEFT OUTER JOIN VHF_PLAN p ON e.idVhfPlan = p.idVhfPlan LEFT OUTER JOIN VHF_TECHNOLOGIES t ON e.idVhfTechno = t.idVhfTechno LEFT OUTER JOIN VHF_ETATS s ON e.idVhfEtat=s.idVhfEtat LEFT OUTER JOIN VHF_TYPES_EQUIPEMENTS c ON e.idVhfType = c.idVhfType ORDER BY vhfIndicatif;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
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
