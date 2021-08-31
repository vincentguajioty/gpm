<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 103;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['sac2_lecture']==0)
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
                Gestion des emplacements internes des sacs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Emplacements</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="box-header">
                        <?php if ($_SESSION['sac2_ajout']==1) {?>
                            <h3 class="box-title"><a href="emplacementsForm.php?id=0" class="btn btn-sm btn-success">Ajouter un emplacement</a></h3>
                        <?php } else {?>
                            </br>
                        <?php } ?>
                    </div>
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Libelle</th>
                                <th>Sac</th>
                                <th>Lot</th>
                                <th>Quantité de matériel</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot;');
                        while ($data = $query->fetch())
                        {
                            $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT WHERE idEmplacement = :idEmplacement;');
                            $query2->execute(array('idEmplacement' => $data['idEmplacement']));
                            $data2 = $query2->fetch();
                            ?>
                            <tr>
                                <td><?php echo $data['idEmplacement']; ?></td>
                                <td><?php echo $data['libelleEmplacement']; ?></td>
                                <td><?php echo $data['libelleSac']; ?></td>
                                <td><?php echo $data['libelleLot']; ?></td>
                                <td><?php echo $data2['nb']; ?></td>
                                <td>
                                    <?php if ($_SESSION['sac2_lecture']==1) {?>
                                        <a href="emplacementsContenu.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['sac2_modification']==1) {?>
                                        <a href="emplacementsForm.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['sac2_suppression']==1) {?>
                                        <a href="emplacementsDelete.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
                                    <?php }?>
                                </td>
                            </tr>
                            
                        	
                            <?php
                            $query2->closeCursor();
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

