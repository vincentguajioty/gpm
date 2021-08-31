<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lots_lecture']==0)
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
                Gestion des lots
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Lots</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['lots_ajout']==1) {?>
	                <div class="box-header">
						<h3 class="box-title"><a href="lotsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un lot</a></h3>
	                </div>
                <?php }?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Référentiel <a href="lotsCheckConfTotalManu.php" class="btn btn-xs spinnerAttenteClick"><i class="fa fa-refresh"></i></a></th>
                                <th class="not-mobile">Etat</th>
                                <th class="not-mobile">Référent</th>
                                <th class="not-mobile">Prochain Inventaire</th>
                                <th class="not-mobile">Notifications</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr>
                                <td><?php echo $data['idLot']; ?></td>
                                <td><?php echo $data['libelleLot']; ?></td>
                                <td>
                                    <?php
                                    //echo $data['libelleTypeLot'];
                                    if ($data['libelleTypeLot'] == Null)
                                    {
                                        ?><span class="badge bg-orange">NA</span><?php
                                    }
                                    else
                                    {
                                        if ($data['alerteConfRef']==0)
                                        {
                                            ?><span class="badge bg-green"><?php echo $data['libelleTypeLot']; ?></span><?php
                                        }
                                        else
                                        {
                                            ?><span class="badge bg-red"><?php echo $data['libelleTypeLot']; ?></span><?php
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $data['libelleEtat']; ?></td>
                                <td><?php echo $data['identifiant']; ?></td>
                                <td><?php
                                    if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) < date('Y-m-d'))
                                    {
                                        ?><span class="badge bg-red"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
                                    }
                                    else if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) == date('Y-m-d'))
                                    {
                                        ?><span class="badge bg-orange"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
                                    }
                                    else
                                    {
                                        ?><span class="badge bg-green"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
                                    }
                                    ?>
                                </td>
                                <td>
                                	<?php if($data['idEtat']!=1){echo '<i class="fa fa-bell-slash-o"></i>';}else{echo '<i class="fa fa-bell-o"></i>';} ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['lots_lecture']==1) {?>
                                        <a href="lotsContenu.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['lots_modification']==1) {?>
                                        <a href="lotsForm.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-warning modal-form"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['lots_suppression']==1) {?>
                                        <a href="lotsDelete.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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



