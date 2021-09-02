<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 606;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['cout_lecture']==0)
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
                Centres de couts
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Centre de couts</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['cout_ajout']==1) {?>
                	<div class="box-header">
                        <h3 class="box-title"><a href="centreCoutsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un centre de couts</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri3" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Etat d'ouverture</th>
                                <th class="not-mobile">Dates</th>
                                <th class="not-mobile">Responsable</th>
                                <th class="not-mobile">Solde actuel</th>
                                <th class="not-mobile">Commandes à intégrer</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM CENTRE_COUTS;');
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idCentreDeCout']; ?></td>
                                <td><?php echo $data['libelleCentreDecout']; ?></td>
                                <td>
                                	<?php
                                		if($data['dateFermeture'] == Null)
                                		{
                                			if($data['dateOuverture'] <= date('Y-m-d'))
                                			{
                                				echo '<span class="badge bg-green">1 - Ouvert</span>';
                                			}
                                			else
                                			{
                                				echo '<span class="badge bg-blue">2 - Futur</span>';
                                			}
                                		}
                                		else
                                		{
                                			if($data['dateFermeture'] < date('Y-m-d'))
                                			{
                                				echo '<span class="badge bg-gray">3 - Clos</span>';
                                			}
                                			else
                                			{
                                				if($data['dateOuverture'] <= date('Y-m-d'))
	                                			{
	                                				echo '<span class="badge bg-green">1 - Ouvert</span>';
	                                			}
	                                			else
	                                			{
	                                				echo '<span class="badge bg-blue">2 - Futur</span>';
	                                			}
                                			}
                                		}
                                	?>
                                </td>
                                <td><?=$data['dateOuverture']?> <i class="fa  fa-arrow-right"></i> <?=$data['dateFermeture']?></td>
                                <td>
                                	<?php
                                		$query2 = $db->prepare('SELECT p.* FROM CENTRE_COUTS_PERSONNES cc LEFT OUTER JOIN PERSONNE_REFERENTE p ON cc.idPersonne = p.idPersonne WHERE cc.idCentreDeCout = :idCentreDeCout');
                                		$query2->execute(array('idCentreDeCout'=>$data['idCentreDeCout']));
                                		while($data2 = $query2->fetch())
                                		{
                                			echo $data2['identifiant'].'<br/>';
                                		}
                                	?>
                                </td>
                                <td>
                                	<?php
                                		$query2 = $db->prepare('SELECT COALESCE(SUM(montantEntrant),0)-COALESCE(SUM(montantSortant),0) as total FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = :idCentreDeCout');
                                		$query2->execute(array('idCentreDeCout'=>$data['idCentreDeCout']));
                                		$total = $query2->fetch();
                                        $total = round($total['total'],2);
                                		if($total == 0)
                                        {
                                            echo '<span class="badge bg-orange">'.$total.' €</span>';
                                        }
                                        else if($total < 0)
                                        {
                                            echo '<span class="badge bg-red">'.$total.' €</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="badge bg-green">'.$total.' €</span>';
                                        }
                                	?>
                                </td>
                                <td>
                                    <?php
                                        $query2 = $db->prepare('SELECT COUNT(*) as nb FROM COMMANDES c LEFT OUTER JOIN COMMANDES_ETATS e ON c.idEtat = e.idEtat WHERE idCentreDeCout = :idCentreDeCout AND integreCentreCouts = 0 AND c.idEtat > 3 AND c.idEtat < 8;');
                                        $query2->execute(array('idCentreDeCout'=>$data['idCentreDeCout']));
                                        $nb = $query2->fetch();
                                        $nb = $nb['nb'];
                                        if($nb == 0)
                                        {
                                            echo '<span class="badge bg-green">0</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="badge bg-orange">'.$nb.'</span>';
                                        }

                                    ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['cout_lecture']==1) {?>
                                        <a href="centreCoutsContenu.php?id=<?=$data['idCentreDeCout']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['cout_ajout']==1) {?>
                                        <a href="centreCoutsForm.php?id=<?=$data['idCentreDeCout']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['cout_supprimer']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=centreCoutsDelete&id=<?=$data['idCentreDeCout']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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
