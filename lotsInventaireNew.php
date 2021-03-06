<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lots_modification']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php require_once 'config/bdd.php'; ?>

    <?php
        $lock = $db->prepare('UPDATE LOTS_LOTS SET inventaireEnCours = 1 WHERE idLot = :idLot;');
        $lock->execute(array(
            'idLot' => $_GET['id']
        ));

        $lot = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE idLot = :idLot;');
        $lot->execute(array(
            'idLot' => $_GET['id']
        ));
        $lot = $lot->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Nouvel inventaire du lot: <?php echo $lot['libelleLot']; ?>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <form role="form" class="spinnerAttenteSubmit" action="lotsInventaireNewAdd.php?id=<?=$_GET['id']?>" method="POST">
                <div class="box box-success">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Personne ayant fait l'inventaire:</label>
                            <input type="text" class="form-control" name="dateInventaire" value="<?= $_SESSION['identifiant'] ?>" disabled>
                        </div>
                        <div class="form-group" id="dateInventaire">
                            <label>Date de l'inventaire:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="input-datepicker form-control" name="dateInventaire" value="<?php echo date('Y-m-d');?>" required>
                            </div>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input checked type="checkbox" value="1" name="boolDernier" checked> Marquer cet inventaire comme ??tant le plus r??cent
                            </label>
                        </div>
                    </div>
                </div>
                <?php
                $sacs = $db->prepare('SELECT * FROM MATERIEL_SAC WHERE idLot = :idLot;');
                $sacs->execute(array(
                    'idLot' => $_GET['id']
                ));
                while ($sac=$sacs->fetch())
                {
                ?>
	                <div class="box box-info box-solid">
	                    <div class="box-header with-border">
	                    	<h3 class="box-title"><?php echo $sac['libelleSac']; ?></h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/R??duire"><i class="fa fa-minus"></i></button>
                            </div>
	                    </div>
	                    <div class="box-body">
                            <?php
                            $emplacements = $db->prepare('SELECT * FROM MATERIEL_EMPLACEMENT WHERE idSac = :idSac ORDER BY libelleEmplacement ASC;');
                            $emplacements->execute(array('idSac'=>$sac['idSac']));
                            while($emplacement = $emplacements->fetch())
                            {
                            ?>
    	                        <div class="box box-warning collapsed-box box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><?= $emplacement['libelleEmplacement'] ?></h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse" title="Agrandir/R??duire"><i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-striped">
            	                            <thead>
            	                            <tr>
            	                                <th style="width: 10px">#</th>
            	                                <th>Libelle du mat??riel</th>
            	                                <th>Stock d'alerte</th>
            	                                <?php if($lot['idTypeLot'] != Null){echo '<th>Requis par le r??f??rentiel</th>' ;} ?>
            	                                <th>Quantit??</th>
            	                                <th>P??remption</th>
            	                            </tr>
            	                            </thead>
            	                            <tbody>
                	                            <?php
                	                            $materiels = $db->prepare('
                	                            	SELECT
                	                            		*
                	                            	FROM
                	                            		MATERIEL_ELEMENT m
                	                            		LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement
                	                            		LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue
                	                            		LEFT OUTER JOIN (SELECT * FROM REFERENTIELS WHERE idTypeLot = :idTypeLot)ref ON m.idMaterielCatalogue = ref.idMaterielCatalogue
                	                            	WHERE
                	                            		m.idEmplacement = :idEmplacement
                	                            	ORDER BY
                	                            		libelleMateriel;');
                	                            $materiels->execute(array(
                	                                'idEmplacement' => $emplacement['idEmplacement'],
                	                                'idTypeLot'     => $lot['idTypeLot'],
                	                            ));
                	                            while ($materiel = $materiels->fetch()) { ?>
                	
                	                                <tr>
                	                                    <td><?php echo $materiel['idElement']; ?></td>
                	                                    <td><?php echo $materiel['libelleMateriel']; ?></td>
                	                                    <td><?php echo $materiel['quantiteAlerte']; ?></td>
                	                                    <?php if($lot['idTypeLot'] != Null){echo '<td>'.$materiel['quantiteReferentiel'].'</td>' ;} ?>
                	                                    <td><input type="text" class="form-control" required value="<?php echo $materiel['quantite']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idSac']; ?>][<?php echo $materiel['idEmplacement']; ?>][<?php echo $materiel['idElement']; ?>][qtt]"></td>
                	                                    <td><input type="text" class="input-datepicker form-control" value="<?php echo $materiel['peremption']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $materiel['idSac']; ?>][<?php echo $materiel['idEmplacement']; ?>][<?php echo $materiel['idElement']; ?>][per]" <?php if ($materiel['peremption'] != Null) echo 'required';?>></td>
                	                                </tr>
                	                            <?php
                	                            }
                	                            ?>
            	                            </tbody>
            	                        </table>
                                    </div>
                                </div>
                            <?php } ?>
	                    </div>
	                </div>
                <?php
                }
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
                        <a href="lotsInventaireNewAbort.php?id=<?=$_GET['id']?>" class="btn btn-default">Retour</a>
                        <button type="submit" class="btn btn-info pull-right">Ajouter</button>
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