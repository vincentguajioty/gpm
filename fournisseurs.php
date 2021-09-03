<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 605;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['fournisseurs_lecture']==0)
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
                Fournisseurs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Fournisseurs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
            	
        		<div class="box-header">
        			<?php if ($_SESSION['fournisseurs_ajout']==1) {?>
                    	<h3 class="box-title"><a href="fournisseursForm.php" class="btn btn-sm btn-success modal-form">Ajouter un fournisseur</a></h3>
                    <?php } ?>
                    
                    <?php if(!isset($_SESSION['aesFour']) AND $AESFOUR){?>
                		<h3 class="box-title pull-right"><a href="fournisseursAESgetPWD.php" class="btn btn-sm btn-info modal-form">Accéder aux informations chiffrées</a></h3>
                	<?php } ?>
                    <?php if(isset($_SESSION['aesFour']) AND $AESFOUR){?>
                		<a href="fournisseursAESlock.php" class="btn btn-sm btn-info pull-right">Quitter le mode édition des données chiffrées</a>
                	<?php } ?>
            	</div>
                
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Nom</th>
                                <th>Site Web</th>
                                <th>Téléphone</th>
                                <th>Liens</th>
                                <?php if(isset($_SESSION['aesFour'])){echo '<th>Informations chiffrées</th>';} ?>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->prepare('SELECT *, AES_DECRYPT(aesFournisseur, :aesKey) as aesFournisseurDecode FROM FOURNISSEURS ORDER BY nomFournisseur;');
                        $query->execute(array('aesKey'=>$_SESSION['aesFour']));
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['idFournisseur']; ?></td>
                                <td><?php echo $data['nomFournisseur']; ?></td>
                                <td><?php echo $data['siteWebFournisseur']; ?></td>
                                <td><?php echo $data['telephoneFournisseur']; ?></td>
                                <td>
                                	<?php
                                        if($data['siteWebFournisseur'] != Null AND $data['siteWebFournisseur'] != ''){?>
                                            <a href="<?=$data['siteWebFournisseur']?>" class="btn btn-xs btn-info" title="Aller sur le site du fournisseur" target="_blank"><i class="fa fa-internet-explorer"></i></a>
                                    <?php } ?>
                                </td>
                                <?php if(isset($_SESSION['aesFour'])){echo '<td>'.nl2br($data['aesFournisseurDecode']).'</td>';} ?>
                                <td>
                                    <?php if ($_SESSION['fournisseurs_lecture']==1) {?>
                                        <a href="fournisseursContenu.php?id=<?=$data['idFournisseur']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['fournisseurs_modification']==1) {?>
                                        <a href="fournisseursForm.php?id=<?=$data['idFournisseur']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if (isset($_SESSION['aesFour'])) {?>
                                    	<a href="fournisseursAESForm.php?id=<?=$data['idFournisseur']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i> <i class="fa fa-lock"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['fournisseurs_suppression']==1) {?>
                                        <a href="modalDeleteConfirm.php?case=fournisseursDelete&id=<?=$data['idFournisseur']?>" class="btn btn-xs btn-danger modal-form" title="Suppimer"><i class="fa fa-trash"></i></a>
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
