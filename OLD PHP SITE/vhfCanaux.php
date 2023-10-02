<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 901;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['vhf_canal_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gestion des canaux
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
                <?php if ($_SESSION['vhf_canal_ajout']==1) {?>
                	<div class="box-header">
                        <h3 class="box-title"><a href="vhfCanauxForm.php" class="btn btn-sm btn-success modal-form">Ajouter un canal</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="all" style="width: 10px">#</th>
                            <th class="all">Libelle</th>
                            <th class="not-mobile">Technologie</th>
                            <th class="not-mobile">Rx</th>
                            <th class="not-mobile">Tx</th>
                            <th class="not-mobile">CTCSS Rx</th>
                            <th class="not-mobile">CTCSS Tx</th>
                            <th class="not-mobile">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM VHF_CANAL c LEFT OUTER JOIN VHF_TECHNOLOGIES t  ON c.idVhfTechno = t.idVhfTechno ORDER BY chName;');
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
