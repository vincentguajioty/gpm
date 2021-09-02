<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 504;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['verrouIP']==0)
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
                IP verrouillées
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">IP</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Date de verrouillage</th>
                                <th>Adresse IP</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM VERROUILLAGE_IP ORDER BY dateVerr DESC;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idIP']; ?></td>
                                <td><?php echo $data['dateVerr']; ?></td>
                                <td><?php echo $data['adresseIPverr']; ?></td>
                                <td>
									<a href="modalDeleteConfirm.php?case=verrouIPDelete&id=<?=$data['idIP']?>" class="btn btn-xs btn-danger modal-form" title="Déverouiller l'adresse IP"><i class="fa fa-trash"></i></a>
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
