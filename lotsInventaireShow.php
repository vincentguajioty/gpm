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

    <?php
    $query = $db->prepare('SELECT * FROM INVENTAIRES i LEFT OUTER JOIN LOTS_LOTS l ON i.idLot = l.idLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne WHERE idInventaire = :idInventaire;');
    $query->execute(array(
        'idInventaire' => $_GET['id']
    ));
    $data = $query ->fetch();
    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $data['dateInventaire']; ?> - Inventaire de <?php echo $data['libelleLot']; ?> - Réalisé par <?php echo $data['identifiant']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="lots.php">Lots</a></li>
                <li class="active"><?php echo $data['libelleLot']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-header">
                        </div>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Matériel</th>
                                <th>Quantité</th>
                                <th>Péremption</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query2 = $db->prepare('SELECT * FROM INVENTAIRES_CONTENUS c LEFT OUTER JOIN INVENTAIRES i ON c.idInventaire = i.idInventaire LEFT OUTER JOIN MATERIEL_CATALOGUE m ON c.idMaterielCatalogue = m.idMaterielCatalogue WHERE c.idInventaire = :idInventaire ORDER BY libelleMateriel;');
                            $query2->execute(array(
                                'idInventaire' => $_GET['id']
                            ));
                            while ($data2 = $query2->fetch())
                            {?>
                                <tr>
                                    <td><?php echo $data2['libelleMateriel']; ?></td>
                                    <td><?php echo $data2['quantiteInventaire']; ?></td>
                                    <td><?php if ($data2['peremptionInventaire'] != Null) echo $data2['peremptionInventaire']; ?></td>
                                </tr>
                                <?php
                            }
                            $query2->closeCursor(); ?>
                            </tbody>


                        </table>
                    </div>
                </div>
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-header">
                            Commentaires:
                        </div>
                        <?php echo $data['commentairesInventaire']; ?>
                    </div>
                </div>
            <div class="row"></div>
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



