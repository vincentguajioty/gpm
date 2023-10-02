
<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1102;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['tenues_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
    	    
    	if($_GET['case']=='int')
    	{
    		$infoPersonne = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
    		$infoPersonne->execute(array('idPersonne'=>$_GET['id']));
    		$infoPersonne = $infoPersonne->fetch();

    		$affectations = $db->prepare('SELECT * FROM TENUES_AFFECTATION a JOIN TENUES_CATALOGUE c ON a.idCatalogueTenue = c.idCatalogueTenue WHERE idPersonne = :idPersonne;');
    		$affectations->execute(array('idPersonne'=>$_GET['id']));
    	}
    	else
    	{
    		if ($_GET['case']=='ext')
    		{
    			$infoPersonne['idPersonne'] = 'Ext';
    			$infoPersonne['nomPersonne'] = $_GET['personneNonGPM'];
    			$infoPersonne['prenomPersonne'] = '(externe)';

    			$affectations = $db->prepare('SELECT * FROM TENUES_AFFECTATION a JOIN TENUES_CATALOGUE c ON a.idCatalogueTenue = c.idCatalogueTenue WHERE personneNonGPM = :personneNonGPM;');
    			$affectations->execute(array('personneNonGPM'=>$_GET['personneNonGPM']));
    		}
    		else
    		{
				//Pour ne pas afficher une erreur trop moche
    			echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
    			exit;
    		}
    	}
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Affectations de <?php echo $infoPersonne['nomPersonne'] . ' ' . $infoPersonne['prenomPersonne'];?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="tenuesAffectations.php">Affectations des tenues</a></li>
                <li class="active"><?php echo $infoPersonne['nomPersonne'] . ' ' . $infoPersonne['prenomPersonne']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">
                <div class="col-md-12">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-success">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="tri2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="all" style="width: 10px">#</th>
                                        <th class="all">Element de tenue</th>
                                        <th class="not-mobile">Taille</th>
                                        <th class="not-mobile">Date d'affectation</th>
                                        <th class="not-mobile">Date de retour prévue</th>
                                        <th class="not-mobile"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                while ($data = $affectations->fetch())
                                {
                                    ?>
                                    <tr>
                                        <td><?php echo $data['idTenue']; ?></td>
                                        <td><?php echo $data['libelleCatalogueTenue']; ?></td>
                                        <td><?php echo $data['tailleCatalogueTenue']; ?></td>
                                        <td><?php echo $data['dateAffectation']; ?></td>
                                        <td><?php echo $data['dateRetour']; ?></td>
                                        <td>
                                            <?php if ($_SESSION['tenues_modification']==1) {?>
                                                <a href="tenuesAffectationsForm.php?id=<?=$data['idTenue']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                            <?php }?>
                                            <?php if ($_SESSION['tenues_suppression']==1) {?>
                                                <a href="modalDeleteConfirm.php?case=tenuesAffectationsDelete&id=<?=$data['idTenue']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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
                    <!-- /.widget-user -->
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



