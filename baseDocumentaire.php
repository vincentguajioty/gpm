<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 507;
require_once('logCheck.php');
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Base documentaire
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Documents</li>
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
                                <th class="all">Module</th>
                                <th class="not-mobile">Nom du document</th>
                                <th class="not-mobile">Elément de rattachement</th>
                                <th class="not-mobile">Type</th>
                                <th class="not-mobile">Format</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
	                        <?php
	                        	if($_SESSION['commande_lecture']==1)
	                        	{
	                        		$query = $db->query('SELECT * FROM VIEW_DOCUMENTS_COMMANDES;');
			                        while ($data = $query->fetch())
			                        {?>
			                        	<tr>
			                                <td>Commandes</td>
			                                <td><?php echo $data['nomDocCommande']; ?></td>
			                                <td>Commande <?php echo $data['idCommande']; ?></td>
			                                <td><?php echo $data['libelleTypeDocument']; ?></td>
			                                <td><i class="fa <?php echo documentsGetIcone($data['formatDocCommande']);?>"></i></td>
			                                <td>
                                                <?php
                                        		if ($data['formatDocCommande'] == 'pdf' OR $data['formatDocCommande'] == 'jpg' OR $data['formatDocCommande'] == 'jpeg' OR $data['formatDocCommande'] == 'png'){?>
                                            		<a href="commandeDocView.php?idDoc=<?=$data['idDocCommande']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                        			<a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                <?php }?>
                                                <a href="commandeDocDL.php?idDoc=<?=$data['idDocCommande']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
                                            </td>
			                            </tr>
			                        <?php
			                        }
	                        	}
	                        ?>
	                        <?php
	                        	if($_SESSION['cout_lecture']==1)
	                        	{
	                        		$query = $db->query('SELECT * FROM VIEW_DOCUMENTS_CENTRE_COUTS;');
			                        while ($data = $query->fetch())
			                        {?>
			                        	<tr>
			                                <td>Centre de couts</td>
			                                <td><?php echo $data['nomDocCouts']; ?></td>
			                                <td>Centre <?php echo $data['idCentreDeCout']; ?></td>
			                                <td><?php echo $data['libelleTypeDocument']; ?></td>
			                                <td><i class="fa <?php echo documentsGetIcone($data['formatDocCouts']);?>"></i></td>
			                                <td>
                                                <?php
                                        		if ($data['formatDocCouts'] == 'pdf' OR $data['formatDocCouts'] == 'jpg' OR $data['formatDocCouts'] == 'jpeg' OR $data['formatDocCouts'] == 'png'){?>
                                            		<a href="centreCoutsDocView.php?idDoc=<?=$data['idDocCouts']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                        			<a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                <?php }?>
                                                <a href="centreCoutsDocDL.php?idDoc=<?=$data['idDocCouts']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
                                            </td>
			                            </tr>
			                        <?php
			                        }
	                        	}
	                        ?>
	                        <?php
	                        	if($_SESSION['vehicules_lecture']==1)
	                        	{
	                        		$query = $db->query('SELECT * FROM VIEW_DOCUMENTS_VEHICULES;');
			                        while ($data = $query->fetch())
			                        {?>
			                        	<tr>
			                                <td>Véhicules</td>
			                                <td><?php echo $data['nomDocVehicule']; ?></td>
			                                <td>Véhicule <?php echo $data['idVehicule']; ?></td>
			                                <td><?php echo $data['libelleTypeDocument']; ?></td>
			                                <td><i class="fa <?php echo documentsGetIcone($data['formatDocVehicule']);?>"></i></td>
			                                <td>
			                                    <?php
                                        		if ($data['formatDocVehicule'] == 'pdf' OR $data['formatDocVehicule'] == 'jpg' OR $data['formatDocVehicule'] == 'jpeg' OR $data['formatDocVehicule'] == 'png'){?>
                                            		<a href="vehiculesDocView.php?idDoc=<?=$data['idDocVehicules']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                        			<a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                <?php }?>
                                                <a href="vehiculesDocDL.php?idDoc=<?=$data['idDocVehicules']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
			                                </td>
			                            </tr>
			                        <?php
			                        }
	                        	}
	                        ?>
	                        <?php
	                        	if($_SESSION['vhf_canal_lecture']==1)
	                        	{
	                        		$query = $db->query('SELECT * FROM VIEW_DOCUMENTS_CANAL_VHF;');
			                        while ($data = $query->fetch())
			                        {?>
			                        	<tr>
			                                <td>VHF - Canal</td>
			                                <td><?php echo $data['nomDocCanalVHF']; ?></td>
			                                <td>Canal <?php echo $data['idVhfCanal']; ?></td>
			                                <td><?php echo $data['libelleTypeDocument']; ?></td>
			                                <td><i class="fa <?php echo documentsGetIcone($data['formatDocCanalVHF']);?>"></i></td>
			                                <td>
			                                    <?php
                                        		if ($data['formatDocCanalVHF'] == 'pdf' OR $data['formatDocCanalVHF'] == 'jpg' OR $data['formatDocCanalVHF'] == 'jpeg' OR $data['formatDocCanalVHF'] == 'png'){?>
                                            		<a href="vhfCanauxDocView.php?idDoc=<?=$data['idDocCanalVHF']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                        			<a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                <?php }?>
                                                <a href="vhfCanauxDocDL.php?idDoc=<?=$data['idDocCanalVHF']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
			                                </td>
			                            </tr>
			                        <?php
			                        }
	                        	}
	                        ?>
	                        <?php
	                        	if($_SESSION['vhf_plan_lecture']==1)
	                        	{
	                        		$query = $db->query('SELECT * FROM VIEW_DOCUMENTS_PLAN_VHF;');
			                        while ($data = $query->fetch())
			                        {?>
			                        	<tr>
			                                <td>VHF - Plan</td>
			                                <td><?php echo $data['nomDocPlanVHF']; ?></td>
			                                <td>Plan <?php echo $data['idVhfPlan']; ?></td>
			                                <td><?php echo $data['libelleTypeDocument']; ?></td>
			                                <td><i class="fa <?php echo documentsGetIcone($data['formatDocPlanVHF']);?>"></i></td>
			                                <td>
			                                    <?php
                                        		if ($data['formatDocPlanVHF'] == 'pdf' OR $data['formatDocPlanVHF'] == 'jpg' OR $data['formatDocPlanVHF'] == 'jpeg' OR $data['formatDocPlanVHF'] == 'png'){?>
                                            		<a href="vhfPlansDocView.php?idDoc=<?=$data['idDocPlanVHF']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                        			<a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                <?php }?>
                                                <a href="vhfPlansDocDL.php?idDoc=<?=$data['idDocPlanVHF']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
			                                </td>
			                            </tr>
			                        <?php
			                        }
	                        	}
	                        ?>
	                        <?php
	                        	if($_SESSION['vhf_equipement_lecture']==1)
	                        	{
	                        		$query = $db->query('SELECT * FROM VIEW_DOCUMENTS_VHF;');
			                        while ($data = $query->fetch())
			                        {?>
			                        	<tr>
			                                <td>VHF - Equipement</td>
			                                <td><?php echo $data['nomDocVHF']; ?></td>
			                                <td>Equipement <?php echo $data['idVhfEquipement']; ?></td>
			                                <td><?php echo $data['libelleTypeDocument']; ?></td>
			                                <td><i class="fa <?php echo documentsGetIcone($data['formatDocVHF']);?>"></i></td>
			                                <td>
			                                    <?php
                                        		if ($data['formatDocVHF'] == 'pdf' OR $data['formatDocVHF'] == 'jpg' OR $data['formatDocVHF'] == 'jpeg' OR $data['formatDocVHF'] == 'png'){?>
                                            		<a href="vhfEquipementsDocView.php?idDoc=<?=$data['idDocVHF']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                        			<a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                <?php }?>
                                                <a href="vhfEquipementsDocDL.php?idDoc=<?=$data['idDocVHF']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
			                                </td>
			                            </tr>
			                        <?php
			                        }
	                        	}
	                        ?>
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



