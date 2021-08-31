<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1001;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vehicules_lecture']==0)
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
                Véhicules
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Véhicules</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
            	<?php if ($_SESSION['vehicules_ajout']==1) {?>
            		<div class="box-header">
                        <h3 class="box-title"><a href="vehiculesForm.php" class="btn btn-sm btn-success modal-form">Ajouter un véhicule</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Type</th>
                                <th class="not-mobile">Immatriculation</th>
                                <th class="not-mobile">Marque/Modele</th>
                                <th class="not-mobile">Contrôles</th>
                                <th class="not-mobile">Etat</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM VEHICULES v LEFT OUTER JOIN ETATS e ON v.idEtat = e.idEtat LEFT OUTER JOIN VEHICULES_TYPES t ON v.idVehiculesType = t.idVehiculesType ;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idVehicule']; ?></td>
                                <td><?php echo $data['libelleVehicule']; ?></td>
                                <td><?php echo $data['libelleType']; ?></td>
                                <td><?php echo $data['immatriculation']; ?></td>
                                <td><?php echo $data['marqueModele']; ?></td>
                                <td>
                                    <span class="badge bg-<?php
                                            if($data['dateNextRevision']==date('Y-m-d'))
                                            {
                                                echo "orange";
                                            }
                                            else
                                            {
                                                if($data['dateNextRevision']>date('Y-m-d'))
                                                {
                                                    echo "green";
                                                }
                                                else
                                                {
                                                    echo "red";
                                                }
                                            }
                                        ?>">Révision</span>
                                    <span class="badge bg-<?php
                                        if($data['dateNextCT']==date('Y-m-d'))
                                        {
                                            echo "orange";
                                        }
                                        else
                                        {
                                            if($data['dateNextCT']>date('Y-m-d'))
                                            {
                                                echo "green";
                                            }
                                            else
                                            {
                                                echo "red";
                                            }
                                        }
                                        ?>">CT</span>
                                    <span class="badge bg-<?php
                                        if($data['assuranceExpiration']==date('Y-m-d'))
                                        {
                                            echo "orange";
                                        }
                                        else
                                        {
                                            if($data['assuranceExpiration']>date('Y-m-d'))
                                            {
                                                echo "green";
                                            }
                                            else
                                            {
                                                echo "red";
                                            }
                                        }
                                        ?>">Assurance</span>
                                </td>
                                <td><?php echo $data['libelleEtat']; ?> (<?php if($data['idEtat']!=1){echo '<i class="fa fa-bell-slash-o"></i>';}else{echo '<i class="fa fa-bell-o"></i>';} ?>)</td>
                                <td>
                                    <?php if ($_SESSION['vehicules_lecture']==1) {?>
                                        <a href="vehiculesContenu.php?id=<?=$data['idVehicule']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['vehicules_modification']==1) {?>
                                        <a href="vehiculesForm.php?id=<?=$data['idVehicule']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['vehicules_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=vehiculesDelete&id=<?=$data['idVehicule']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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
