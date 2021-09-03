<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 701;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['reserve_modification']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php require_once 'config/bdd.php'; ?>

    <?php
        $query = $db->prepare('SELECT * FROM RESERVES_CONTENEUR WHERE idConteneur = :idConteneur;');
        $query->execute(array(
            'idConteneur' => $_GET['id']
        ));
        $data = $query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Validation de l'inventaire du conteneur: <?php echo $data['libelleConteneur']; ?>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <?php
            if(isset($_POST['barcodes']) AND $_POST['barcodes'] != '' AND $_POST['barcodes'] != Null)
                $codes = preg_split('/\r\n|[\r\n]/', $_POST['barcodes']);
                foreach ($codes as $idLigneSaisie => $code){
                    if ($code == '')
                    {
                        unset($codes[$idLigneSaisie]);
                    }
                }

                foreach ($codes as $idLigneSaisie => $code){
                    $insertTemp = $db->prepare('
                        INSERT INTO
                            RESERVES_INVENTAIRES_TEMP
                        SET
                            idConteneur = :idConteneur,
                            codeBarre   = :codeBarre
                    ');
                    $insertTemp->execute(array(
                        'idConteneur' => $_GET['id'],
                        'codeBarre' => $code,
                    ));
                }
            }
            ?>


            <form role="form" class="spinnerAttenteSubmit" action="reserveInventaireCBNewFinalize.php?id=<?=$_GET['id']?>" method="POST">
                <?php
                    $scanFailure = $db->prepare('
                        SELECT
                            SUM(quantite) as nb
                        FROM
                            VIEW_SCAN_RESULTS_RESERVES 
                        WHERE
                            idConteneur = :idConteneur
                            AND
                            idMaterielCatalogue IS NULL
                    ;');
                    $scanFailure->execute(array('idConteneur'=>$data['idConteneur']));
                    $scanFailure = $scanFailure->fetch();

                    $manque = $db->prepare('
                        SELECT
                            COUNT(*) as nb
                        FROM
                            RESERVES_MATERIEL m
                            LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur
                            LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                            LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_RESERVES WHERE idConteneur = :idConteneur) v ON m.idMaterielCatalogue = v.idMaterielCatalogue
                        WHERE
                            m.idConteneur = :idConteneur
                            AND
                            v.idMaterielCatalogue IS NULL
                        ORDER BY
                            c.libelleMateriel;
                    ;');
                    $manque->execute(array(
                        'idConteneur' => $data['idConteneur'],
                        'idTypeLot'     => $lot['idTypeLot'],
                    ));
                    $manque = $manque->fetch();

                    $surplus = $db->prepare('
                        SELECT
                            COUNT(*) as nb
                        FROM
                            VIEW_SCAN_RESULTS_RESERVES s
                            LEFT OUTER JOIN (SELECT * FROM RESERVES_MATERIEL WHERE idConteneur = :idConteneur) e ON e.idMaterielCatalogue = s.idMaterielCatalogue
                        WHERE
                            s.idConteneur = :idConteneur
                            AND
                            e.idConteneur IS NULL
                            AND
                            s.idMaterielCatalogue IS NOT NULL
                    ;');
                    $surplus->execute(array('idConteneur'=>$data['idConteneur']));
                    $surplus = $surplus->fetch();

                    $nberreurs = $scanFailure['nb']+$manque['nb']+$surplus['nb'];

                    if($nberreurs > 0)
                    { ?>
                        <div class="box box-danger collapsed-box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= $data['libelleConteneur'] ?> <i class="fa fa-arrow-right"></i> <?= $nberreurs ?> points d'attention</h3>
                                <div class="box-tools pull-right"><button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/Réduire"><i class="fa fa-plus"></i></button></div>
                            </div>
                            <div class="box-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <?php if($scanFailure['nb']>0){?><th>Nombre de codes barre non-reconnus</th><?php } ?>
                                            <?php if($manque['nb']>0){?><th>Elements manquants (non-scannés)</th><?php } ?>
                                            <?php if($surplus['nb']>0){?><th>Elements en surplus qu'il faut enlever</th><?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php if($scanFailure['nb']>0){?><td><?=$scanFailure['nb']?></td><?php } ?>
                                            <?php if($manque['nb']>0){?><td>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 10px">#</th>
                                                        <th>Libelle du matériel</th>
                                                        <th>Quantité</th>
                                                        <th>Péremption</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $materiels = $db->prepare('
                                                            SELECT
                                                                m.*,
                                                                e.*,
                                                                c.*
                                                            FROM
                                                                RESERVES_MATERIEL m
                                                                LEFT OUTER JOIN RESERVES_CONTENEUR e ON m.idConteneur=e.idConteneur
                                                                LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                                                                LEFT OUTER JOIN (SELECT * FROM VIEW_SCAN_RESULTS_RESERVES WHERE idConteneur = :idConteneur) v ON m.idMaterielCatalogue = v.idMaterielCatalogue
                                                            WHERE
                                                                m.idConteneur = :idConteneur
                                                                AND
                                                                v.idMaterielCatalogue IS NULL
                                                            ORDER BY
                                                                c.libelleMateriel;');
                                                        $materiels->execute(array(
                                                            'idConteneur' => $data['idConteneur'],
                                                        ));
                                                        while ($materiel = $materiels->fetch()) { ?>
                            
                                                            <tr>
                                                                <td><?php echo $materiel['idReserveElement']; ?></td>
                                                                <td><?php echo $materiel['libelleMateriel']; ?></td>
                                                                <td><input type="text" class="form-control" value="<?php echo $materiel['quantiteReserve']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idReserveElement']; ?>][qtt]"></td>
                                                                <td><input type="text" class="input-datepicker form-control" value="<?php echo $materiel['peremptionReserve']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idReserveElement']; ?>][per]" <?php if ($materiel['peremptionReserve'] != Null) echo 'required';?>></td>
                                                            </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </td><?php } ?>
                                            <?php if($surplus['nb']>0){?><td>
                                                <ul>
                                                    <?php
                                                        $surplus = $db->prepare('
                                                            SELECT
                                                                s.*
                                                            FROM
                                                                VIEW_SCAN_RESULTS_RESERVES s
                                                                LEFT OUTER JOIN (SELECT * FROM RESERVES_MATERIEL WHERE idConteneur = :idConteneur) e ON e.idMaterielCatalogue = s.idMaterielCatalogue
                                                            WHERE
                                                                s.idConteneur = :idConteneur
                                                                AND
                                                                e.idConteneur IS NULL
                                                                AND
                                                                s.idMaterielCatalogue IS NOT NULL
                                                        ');
                                                        $surplus->execute(array('idConteneur'=>$data['idConteneur']));
                                                        while($surplu = $surplus->fetch())
                                                        {
                                                            echo '<li>'.$surplu['libelleMateriel'].'</li>';
                                                        }
                                                    ?>
                                                </ul>
                                            </td><?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php }
                    else
                    { ?>
                        <div class="box box-success box-solid">
                            <div class="box-header">
                                <h3 class="box-title"><?= $data['libelleConteneur'] ?> <i class="fa fa-arrow-right"></i> Inventaire terminé !</h3>
                            </div>
                        </div>
                    <?php }
                ?>

                <div class="box">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Remarques</label>
                            <textarea class="form-control" rows="3" name="commentairesInventaire"></textarea>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-body">
                        <a href="reserveInventaireNewAbort.php?id=<?=$_GET['id']?>" class="btn btn-default">Annuler l'inventaire</a>
                        <button type="submit" class="btn btn-info pull-right">Valider mon inventaire</button>
                    </div>
                </div>
            </form>


            <div class="row"></div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<?php include('scripts.php'); ?>
</body>
</html>