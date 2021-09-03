<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 307;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['codeBarre_lecture']==0)
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
                Référentiel de codes barre relatifs aux entrées catalogue
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Codes Barre</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <div class="box-header">
                    <?php if ($_SESSION['codeBarre_ajout']==1) {?>
    					<h3 class="box-title"><a href="codesBarreFormFournisseur.php" class="btn btn-sm btn-success modal-form">Ajouter un code barre fournisseur au référentiel</a></h3>
    					<h3 class="box-title"><a href="codesBarreFormInterne.php" class="btn btn-sm btn-success modal-form">Générer un code barre interne et l'ajouter au référentiel</a></h3>
                    <?php }?>
                    <h3 class="box-title pull-right "><a href="codesBarrePrintRef.php" class="btn btn-sm btn-success">Imprimer le référentiel des codes barre</a></h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri1" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Code Barre</th>
                                <th class="all">Base</th>
                                <th class="not-mobile">Element du catalogue</th>
                                <th class="not-mobile">Péremption spécifiée</th>
                                <th class="not-mobile">Commentaires</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('
                            SELECT
                                c.*,
                                m.libelleMateriel
                            FROM
                                CODES_BARRE c
                                LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue
                            ;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr>
                                <td><?= $data['idCode'] ?></td>
                                <td><?= $data['codeBarre'] ?></td>
                                <td><?php if($data['internalReference']){echo 'Interne';}else{echo 'Fournisseur';} ?></td>
                                <td><?= $data['libelleMateriel'] ?></td>
                                <td><?= $data['peremptionConsommable'] ?></td>
                                <td><?= nl2br($data['commentairesCode']) ?></td>
                                <td>
                                    <a href="codesBarrePrintForm.php?id=<?=$data['idCode']?>" class="btn btn-xs btn-success modal-form" title="Imprimer le code"><i class="fa fa-barcode"></i></a>
                                    <?php if ($_SESSION['codeBarre_modification']==1 AND $data['internalReference']==1 AND $LOTSLOCK==0 AND $RESERVESLOCK==0) {?>
                                        <a href="codesBarreFormInterne.php?id=<?=$data['idCode']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['codeBarre_suppression']==1 AND $LOTSLOCK==0 AND $RESERVESLOCK==0) {?>
                                        <a href="modalDeleteConfirm.php?case=codesBarreDelete&id=<?=$data['idCode']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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



