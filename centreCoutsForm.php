<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/version.php'); ?>
<?php
session_start();
$_SESSION['page'] = 606;
require_once('logCheck.php');
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Centres de couts
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="centreCouts.php">Centres de côuts</a></li>
                <li class="active">Ajouter/Modifier un centre</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php
                if ($_GET['id'] == 0) {
                    ?>
                    <!-- general form elements disabled -->
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ajout d'un centre de cout</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" action="centreCoutsAdd.php" method="POST">
                                <div class="form-group">
									<label>Libellé:</label>
									<input type="text" class="form-control" name="libelleCentreDecout" required>
								</div>
								<div class="form-group">
									<label>Personne référente</label>
									<select class="form-control" name="idResponsable">
										<option></option>
										<?php
										$query = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE cout_etreEnCharge=1 ORDER BY identifiant;');
										while ($data = $query->fetch())
										{
											?>
											<option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
											<?php
										}
										$query->closeCursor(); ?>
									</select>
								</div>
                                <div class="box-footer">
                                    <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                    <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.box-body -->

                    </div>

                    <?php
                }
                else {
                    ?>

                    <!-- general form elements disabled -->
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Modification d'un centre de cout</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form role="form" action="centreCoutsUpdate.php?id=<?=$_GET['id']?>" method="POST">
                                <?php
                                $query = $db->prepare('SELECT * FROM CENTRE_COUTS WHERE idCentreDeCout=:idCentreDeCout;');
                                $query->execute(array('idCentreDeCout' => $_GET['id']));
                                $data = $query->fetch();
                                ?>
                                <div class="form-group">
									<label>Libellé:</label>
									<input type="text" class="form-control" name="libelleCentreDecout" required value="<?php echo $data['libelleCentreDecout']; ?>">
								</div>
								<div class="form-group">
									<label>Personne référente</label>
									<select class="form-control" name="idResponsable">
										<option></option>
										<?php
										$query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE cout_etreEnCharge=1 ORDER BY identifiant;');
										while ($data2 = $query2->fetch())
										{
											?>
											<option value="<?php echo $data2['idPersonne']; ?>" <?php if ($data2['idPersonne'] == $data['idResponsable']){echo 'selected'; }?>><?php echo $data2['identifiant']; ?></option>
											<?php
										}
										$query2->closeCursor(); ?>
									</select>
								</div>
                                <div class="box-footer">
                                    <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                                    <button type="submit" class="btn btn-info pull-right">Modifier</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.box-body -->

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
