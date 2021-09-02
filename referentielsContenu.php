<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 201;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['typesLots_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    
    <?php
	    $query = $db->prepare('SELECT * FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot;');
	    $query->execute(array(
	        'idTypeLot' => $_GET['id']
	    ));
	    $data = $query->fetch();
	?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Référentiel: <?php echo $data['libelleTypeLot'];?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="referentiels.php">Référentiels</a></li>
                <li class="active">Modifier un référentiel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">Contenu du référentiel</h3>
                        <div class="box-tools pull-right">
	                    	<?php if ($_SESSION['typesLots_modification']==1) {?><a href="referentielsContenuForm.php?id=<?= $_GET['id'] ?>" class="btn btn-warning btn-sm">Modifier le référentiel</a><?php } ?>
	                    </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="tri2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Matériel</th>
                                <th>Quantité</th>
                                <th>Obligation</th>
                                <th>Catérogie</th>
                                <th>Commentaires</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = $db->prepare('SELECT * FROM REFERENTIELS r LEFT OUTER JOIN MATERIEL_CATALOGUE c ON r.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN MATERIEL_CATEGORIES b ON c.idCategorie = b.idCategorie WHERE idTypeLot = :idTypeLot ORDER BY libelleMateriel;');
                            $query->execute(array(
                                'idTypeLot' => $_GET['id']
                            ));
                            while ($data = $query->fetch())
                            {?>
                                <tr>
                                    <td><?php echo $data['idMaterielCatalogue']; ?></td>
                                    <td><?php echo $data['libelleMateriel']; ?></td>
                                    <td><?php echo $data['quantiteReferentiel']; ?></td>
                                    <td><?php
                                        if ($data['obligatoire'] == 0)
                                        {
                                            ?><span class="badge bg-green">Facultatif</span><?php
                                        }
                                        else
                                        {
                                            ?><span class="badge bg-blue">Obligatoire</span><?php
                                        }
                                        ?></td>
                                    <td><?php echo $data['libelleCategorie']; ?></td>
                                    <td><?php echo $data['commentairesReferentiel']; ?></td>
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
