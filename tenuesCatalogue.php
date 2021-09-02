<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['tenuesCatalogue_lecture']==0)
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
                Catalogue des tenues
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Catalogue des tenues</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['tenuesCatalogue_ajout']==1) {?>
	                <div class="box-header">
						<h3 class="box-title"><a href="tenuesCatalogueForm.php" class="btn btn-sm btn-success modal-form">Ajouter une tenue</a></h3>
	                </div>
                <?php }?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Taille</th>
                                <th class="not-mobile">Stock</th>
                                <th class="not-mobile">Affectations</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM TENUES_CATALOGUE;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr <?php if ($_SESSION['tenuesCatalogue_lecture']==1) {?>data-href="tenuesCatalogueContenu.php?id=<?=$data['idCatalogueTenue']?>"<?php }?>>
                                <td><?php echo $data['idCatalogueTenue']; ?></td>
                                <td><?php echo $data['libelleCatalogueTenue']; ?></td>
                                <td><?php echo $data['tailleCatalogueTenue']; ?></td>
                                <td>
                                    <?php
                                    if ($data['stockAlerteCatalogueTenue'] == $data['stockCatalogueTenue'])
                                    {
                                        echo '<span class="badge bg-orange">'.$data['stockCatalogueTenue'].'</span>';
                                    }
                                    else
                                    {
                                        if ($data['stockAlerteCatalogueTenue'] > $data['stockCatalogueTenue'])
                                        {
                                            echo '<span class="badge bg-red">'.$data['stockCatalogueTenue'].'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="badge bg-green">'.$data['stockCatalogueTenue'].'</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $query2 = $db->prepare('SELECT COUNT(*) AS nb FROM TENUES_AFFECTATION WHERE idCatalogueTenue = :idCatalogueTenue;');
                                        $query2->execute(array('idCatalogueTenue'=>$data['idCatalogueTenue']));
                                        $data2 = $query2->fetch();
                                        echo '<span class="badge bg-blue">'.$data2['nb'].'</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['tenuesCatalogue_lecture']==1) {?>
                                        <a href="tenuesCatalogueContenu.php?id=<?=$data['idCatalogueTenue']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['tenuesCatalogue_modification']==1) {?>
                                        <a href="tenuesCatalogueForm.php?id=<?=$data['idCatalogueTenue']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['tenuesCatalogue_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=tenuesCatalogueDelete&id=<?=$data['idCatalogueTenue']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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



