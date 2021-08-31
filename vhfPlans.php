<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 902;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['vhf_plan_lecture']==0)
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
                Gestion des plans de fréquence
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
                <?php if ($_SESSION['vhf_plan_ajout']==1) {?>
                	<div class="box-header">
                        <h3 class="box-title"><a href="vhfPlansForm.php" class="btn btn-sm btn-success modal-form">Ajouter un plan</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="all" style="width: 10px">#</th>
                            <th class="all">Libelle</th>
                            <th class="not-mobile">Nombre de canaux</th>
                            <th class="not-mobile">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM VHF_PLAN;');
                        while ($data = $query->fetch())
                        {
                            $query2 = $db->prepare('SELECT COUNT(*) as nb FROM VHF_PLAN_CANAL WHERE idVhfPlan = :idVhfPlan');
                            $query2->execute(array(
                                'idVhfPlan' => $data['idVhfPlan']));
                            $data2 = $query2->fetch();
                            ?>
                            <tr>
                                <td><?php echo $data['idVhfPlan']; ?></td>
                                <td><?php echo $data['libellePlan']; ?></td>
                                <td><?php echo $data2['nb']; ?></td>
                                <td>
                                    <?php if ($_SESSION['vhf_plan_lecture']==1) {?>
                                        <a href="vhfPlansContenu.php?id=<?=$data['idVhfPlan']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['vhf_plan_modification']==1) {?>
                                        <a href="vhfPlansForm.php?id=<?=$data['idVhfPlan']?>" class="btn btn-xs btn-warning modal-form"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['vhf_plan_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=vhfPlansDelete&id=<?=$data['idVhfPlan']?>" class="btn btn-xs btn-danger modal-form"><i class="fa fa-trash"></i></a>
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
