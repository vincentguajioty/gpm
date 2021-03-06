<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 701;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['reserve_lecture']==0)
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
                Gestion des conteneurs de la reserve
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Reserve</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box">
                <?php if ($_SESSION['reserve_ajout']==1) {?>
                	<div class="box-header">
	                    <h3 class="box-title"><a href="reserveConteneurForm.php" class="btn btn-sm btn-success modal-form">Ajouter un conteneur</a></h3>
                        <?php if ($_SESSION['reserve_modification']==1 AND $RESERVESLOCK) {?><h3 class="box-title pull-right"><a data-toggle="modal" data-target="#modalSuppressionLocks" class="btn btn-sm btn-danger">Inventaires de réserves - Désactiver les verrouillages</a></h3><?php } ?>
	            	</div>
	            <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Lieu</th>
                                <th class="not-mobile">Prochain inventaire</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('SELECT * FROM RESERVES_CONTENEUR c LEFT OUTER JOIN LIEUX l  ON c.idLieu = l.idLieu ORDER BY libelleConteneur;');
                        while ($data = $query->fetch())
                        {?>
                            <tr <?php if ($_SESSION['reserve_lecture']==1) {?>data-href="reserveConteneurContenu.php?id=<?=$data['idConteneur']?>"<?php }?>>
                                <td><?php echo $data['idConteneur']; ?></td>
								<td><?php echo $data['libelleConteneur']; ?></td>
								<td><?php echo $data['libelleLieu']; ?></td>
								<td><?php
                                    if($data['inventaireEnCours'])
                                    {
                                        echo '<span class="badge bg-orange faa-flash animated">Inventaire en cours</span><br/>';
                                    }
                                    else
                                    {
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
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['reserve_lecture']==1) {?>
                                        <a href="reserveConteneurContenu.php?id=<?=$data['idConteneur']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['reserve_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                        <a href="reserveConteneurForm.php?id=<?=$data['idConteneur']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['reserve_suppression']==1 AND $data['inventaireEnCours']==Null) {?>
                                        <a href="modalDeleteConfirm.php?case=reserveConteneurDelete&id=<?=$data['idConteneur']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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

<div class="modal fade modal-danger" id="modalSuppressionLocks">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Inventaires en cours</h4>
            </div>
            <div class="modal-body">
                Des inventaires de réserves sont en cours. En conséquent, plusieurs fonctionnalités du site sont verouillées en lecture seule. Le verrouillage s'enlèvera automatiquement dès que les inventaires en cours seront validés. Si ce verrouillage est à tors, vous pouvez forcer le déverrouillage manuellement ici.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <a href="reserveInventaireAbortLock.php"><button type="button" class="btn btn-default pull-right">Forcer le déverrouillage</button></a>
            </div>
        </div>
    </div>
</div>

</html>
