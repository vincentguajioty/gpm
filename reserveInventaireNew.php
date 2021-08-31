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
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
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
                Nouvel inventaire du conteneur: <?php echo $data['libelleConteneur']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="reserveConteneurs.php">Reserve</a></li>
                <li class="active"><?php echo $data['libelleConteneur']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <form role="form" class="spinnerAttenteSubmit" action="reserveInventaireNewAdd.php?id=<?=$_GET['id']?>" method="POST">
                <div class="box">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Personne ayant fait l'inventaire:</label>
                            <select class="form-control select2" style="width: 100%;" name="identifiant" required>
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idPersonne']; ?>" <?php if ($data2['identifiant'] == $_SESSION['identifiant']) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
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
                                <input checked type="checkbox" value="1" name="boolDernier" checked> Marquer cet inventaire comme étant le plus récent
                            </label>
                        </div>
                    </div>
                </div>
				
				<div class="box">
					<div class="box-body">
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
							$query3 = $db->prepare('SELECT * FROM RESERVES_MATERIEL m LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE idConteneur = :idConteneur;');
							$query3->execute(array(
								'idConteneur' => $_GET['id']
							));
							while ($data3 = $query3->fetch()) { ?>

								<tr>
									<td><?php echo $data3['idReserveElement']; ?></td>
									<td><?php echo $data3['libelleMateriel']; ?></td>
									<td><input type="text" class="form-control" value="<?php echo $data3['quantiteReserve']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $data3['idReserveElement']; ?>][qtt]"></td>
									<td><input type="text" class="input-datepicker form-control" value="<?php echo $data3['peremptionReserve']; ?>"name="formArray[<?php echo $_GET['id']; ?>][<?php echo $data3['idReserveElement']; ?>][per]" <?php if ($data3['peremptionReserve'] != Null) echo 'required';?>></td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
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
                        <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                        <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                    </div>
                </div>
            </form>

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