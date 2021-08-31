<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/version.php'); ?>
<?php
session_start();
$_SESSION['page'] = 304;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lieux_lecture']==0)
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
                Lieux de stockage des lots
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Lieux de stockage</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="box-header">
                        <?php if ($_SESSION['lieux_ajout']==1) {?>
                            <h3 class="box-title"><a href="lieuxForm.php?id=0" class="btn btn-sm btn-success">Ajouter un lieu</a></h3>
                        <?php } else {?>
                            </br>
                        <?php } ?>
                    </div>
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Libelle</th>
                                <th>Adresse</th>
                                <th>Accès sécurisé</th>
                                <th>Détails</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM LIEUX;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idLieu']; ?></td>
                                <td><?php echo $data['libelleLieu']; ?></td>
                                <td><?php echo $data['adresseLieu']; ?></td>
                                <td><?php
                                    if ($data['accesReserve'] == 0)
                                    {
                                        ?><span class="badge bg-green">Accès libre</span><?php
                                    }
                                    else
                                    {
                                        ?><span class="badge bg-red">Accès réglementé</span><?php
                                    }
                                ?></td>
                                <td><?php echo $data['detailsLieu']; ?></td>
                                <td>
                                    <?php if ($_SESSION['lieux_modification']==1) {?>
                                        <a href="lieuxForm.php?id=<?=$data['idLieu']?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['lieux_suppression']==1) {?>
                                        <a href="lieuxDelete.php?id=<?=$data['idLieu']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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
