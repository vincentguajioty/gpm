<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 106;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lots_lecture']==0)
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
                Comparaison de lots opérationnels
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="lots.php">Lots</a></li>
                <li class="active">Comparaisons</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            
            <?php
                if(isset($_POST['idLot1']) AND isset($_POST['idLot2']))
                { ?>
                    <div class="box box-info collapsed-box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Comparaison des éléments de matériel</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Matériel</th>
                                    <th>Quantités lot 1 | lot 2</th>
                                    <th>Quantités alerte lot 1 | lot 2</th>
                                    <th>Péremptions d'alerte lot 1 | lot 2</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $comparaisons = $db->prepare('
                                            SELECT
                                                lot1.idMaterielCatalogue,
                                                c.libelleMateriel,
                                                lot1.qttLot1,
                                                lot1.qttAlerteLot1,
                                                lot1.peremptionAnticipationLot1,
                                                lot2.qttLot2,
                                                lot2.qttAlerteLot2,
                                                lot2.peremptionAnticipationLot2
                                            FROM
                                                (SELECT
                                                        e.idMaterielCatalogue as idMaterielCatalogue,
                                                        SUM(e.quantite) as qttLot1,
                                                        SUM(e.quantiteAlerte) as qttAlerteLot1,
                                                        MIN(e.peremption) as peremptionLot1,
                                                        MIN(e.peremptionAnticipation) as peremptionAnticipationLot1
                                                    FROM
                                                        MATERIEL_ELEMENT e
                                                        LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON e.idEmplacement = emp.idEmplacement
                                                        LEFT OUTER JOIN MATERIEL_SAC s ON emp.idSac = s.idSac
                                                    WHERE
                                                        s.idLot = :idLot1
                                                    GROUP BY
                                                        e.idMaterielCatalogue
                                                ) lot1
                                                LEFT OUTER JOIN
                                                    (SELECT
                                                        e.idMaterielCatalogue as idMaterielCatalogue,
                                                        SUM(e.quantite) as qttLot2,
                                                        SUM(e.quantiteAlerte) as qttAlerteLot2,
                                                        MIN(e.peremption) as peremptionLot2,
                                                        MIN(e.peremptionAnticipation) as peremptionAnticipationLot2
                                                    FROM
                                                        MATERIEL_ELEMENT e
                                                        LEFT OUTER JOIN MATERIEL_EMPLACEMENT emp ON e.idEmplacement = emp.idEmplacement
                                                        LEFT OUTER JOIN MATERIEL_SAC s ON emp.idSac = s.idSac
                                                    WHERE
                                                        s.idLot = :idLot2
                                                    GROUP BY
                                                        e.idMaterielCatalogue
                                                    ) lot2 ON lot1.idMaterielCatalogue = lot2.idMaterielCatalogue
                                                LEFT OUTER JOIN
                                                    MATERIEL_CATALOGUE c ON lot1.idMaterielCatalogue = c.idMaterielCatalogue
                                            ORDER BY
                                                c.libelleMateriel ASC
                                        ');
                                        $comparaisons->execute(array(
                                            'idLot1'=>$_POST['idLot1'],
                                            'idLot2'=>$_POST['idLot2'],
                                        ));
                                        while($comparaison = $comparaisons->fetch())
                                        {
                                            if($comparaison['qttLot1'] == $comparaison['qttLot2'])
                                                { $colorQtt = 'bg-success'; }
                                            else
                                            {
                                                if($comparaison['qttLot1'] > $comparaison['qttLot2'])
                                                    { $colorQtt = 'bg-danger'; }
                                                else
                                                    { $colorQtt = 'bg-warning'; }
                                            }

                                            if($comparaison['qttAlerteLot1'] == $comparaison['qttAlerteLot2'])
                                                { $colorQttAlerte = 'bg-success'; }
                                            else
                                            {
                                                if($comparaison['qttAlerteLot1'] > $comparaison['qttAlerteLot2'])
                                                    { $colorQttAlerte = 'bg-danger'; }
                                                else
                                                    { $colorQttAlerte = 'bg-warning'; }
                                            }

                                            if($comparaison['peremptionAnticipationLot1'] == $comparaison['peremptionAnticipationLot2'])
                                                { $colorPerAlerte = 'bg-success'; }
                                            else
                                            {
                                                if($comparaison['peremptionAnticipationLot1'] > $comparaison['peremptionAnticipationLot2'])
                                                    { $colorPerAlerte = 'bg-danger'; }
                                                else
                                                    { $colorPerAlerte = 'bg-warning'; }
                                            }
                                            ?>
                                            <tr>
                                                <td><?=$comparaison['libelleMateriel']?></td>
                                                <td class="<?=$colorQtt?>"><?=$comparaison['qttLot1']?> | <?=$comparaison['qttLot2']?></td>
                                                <td class="<?=$colorQttAlerte?>"><?=$comparaison['qttAlerteLot1']?> | <?=$comparaison['qttAlerteLot2']?></td>
                                                <td class="<?=$colorPerAlerte?>"><?=$comparaison['peremptionAnticipationLot1']?> | <?=$comparaison['peremptionAnticipationLot2']?></td>
                                            </tr>
                                        <?php }
                                    ?>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>

                    <?php
                    unset($_POST['idLot1']);
                    unset($_POST['idLot2']);
                }
                else
                { ?>
                    <div class="box box-info">
                        <div class="box-header">
                            <h3 class="box-title">Selectionner les lots à comparer</h3>
                        </div>
                        <form role="form" class="spinnerAttenteSubmit" action="lotsCompare.php" method="POST">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Lot de référence:</label>
                                            <select class="form-control select2" style="width: 100%;" name="idLot1" required>
                                                <?php
                                                $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                                while ($data2 = $query2->fetch())
                                                {
                                                    ?>
                                                    <option value ="<?php echo $data2['idLot']; ?>" ><?php echo $data2['libelleLot']; ?></option>
                                                    <?php
                                                }
                                                $query2->closeCursor(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Lot à comparer:</label>
                                            <select class="form-control select2" style="width: 100%;" name="idLot2" required>
                                                <?php
                                                $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                                while ($data2 = $query2->fetch())
                                                {
                                                    ?>
                                                    <option value ="<?php echo $data2['idLot']; ?>" ><?php echo $data2['libelleLot']; ?></option>
                                                    <?php
                                                }
                                                $query2->closeCursor(); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button name="button" type="submit" class="btn btn-info pull-right">Comparer</button>
                            </div>
                        </form>
                    </div>
                    <?php
                }
            ?>

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



