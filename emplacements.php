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
            <?php
                if($LOTSLOCK)
                {
                    echo '<div class="alert alert-warning alert-dismissible">';
                    echo '<i class="icon fa fa-warning"></i> Des inventaires de lots sont en cours, cette section est donc verrouillée en lecture seule.';
                    echo '</div>';
                }
            ?>
            <div class="box">
                <?php if ($_SESSION['sac2_ajout']==1 AND $LOTSLOCK==0) {?>
                	<div class="box-header">
                        <h3 class="box-title"><a href="emplacementsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un emplacement</a></h3>
                	</div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Sac</th>
                                <th class="not-mobile">Lot</th>
                                <th class="not-mobile">Quantité de matériel</th>
                                <th class="not-mobile">Actions</th>
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
                            <tr <?php if ($_SESSION['sac2_lecture']==1) {?>data-href="emplacementsContenu.php?id=<?=$data['idEmplacement']?>"<?php }?>>
                                <td><?php echo $data['idEmplacement']; ?></td>
                                <td><?php echo $data['libelleEmplacement']; ?></td>
                                <td><?php echo $data['libelleSac']; ?></td>
                                <td><?php echo $data['libelleLot']; ?></td>
                                <td><?php echo $data2['nb']; ?></td>
                                <td>
                                    <?php if ($_SESSION['sac2_lecture']==1) {?>
                                        <a href="emplacementsContenu.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['sac2_lecture']==1 AND $_SESSION['codeBarre_lecture']==1) {?>
                                        <a href="emplacementsCBPrintForm.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-success modal-form" title="Imprimer le code barre de cet emplacement"><i class="fa fa-barcode"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['sac2_modification']==1 AND $LOTSLOCK==0) {?>
                                        <a href="emplacementsForm.php?id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['sac2_suppression']==1 AND $LOTSLOCK==0) {?>
                                        <a href="modalDeleteConfirm.php?case=emplacementsDelete&id=<?=$data['idEmplacement']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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

