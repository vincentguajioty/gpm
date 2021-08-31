<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 601;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['commande_ajout']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
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
                Gestion des commandes
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="commandesToutes.php">Commandes</a></li>
                <li class="active">Nouvelle commande</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Nouvelle Commande</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="commandesAdd.php" method="POST">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Demandeur: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idDemandeur">
                                <?php
                                $query = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idPersonne']; ?>" <?php if ($_SESSION['idPersonne'] == $data['idPersonne']) { echo 'selected'; } ?> ><?php echo $data['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Observateur: </label>
                            <select class="form-control select2" style="width: 100%;" name="idObservateur">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Validation de la demande de commande par: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idValideur" required>
                                <?php
                                $query = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1 AND commande_valider = 1;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Affectation (commande traitée par): <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idAffectee" required>
                                <?php
                                $query = $db->query('SELECT * FROM PERSONNE_REFERENTE p LEFT OUTER JOIN PROFILS h ON p.idProfil = h.idProfil WHERE commande_lecture = 1 AND commande_etreEnCharge = 1;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idPersonne']; ?>"><?php echo $data['identifiant']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Centre de cout: </label>
                            <select class="form-control select2" style="width: 100%;" name="idCentreDeCout">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM CENTRE_COUTS;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idCentreDeCout']; ?>"><?php echo $data['libelleCentreDecout']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lieu de livraison:</label>
                            <select class="form-control select2" style="width: 100%;" name="idLieuLivraison">
                                <option></option>
                                <?php
                                $query = $db->query('SELECT * FROM LIEUX;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idLieu']; ?>"><?php echo $data['libelleLieu']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fournisseur: <small style="color:grey;">Requis</small></label>
                            <select class="form-control select2" style="width: 100%;" name="idFournisseur" required>
                                <?php
                                $query = $db->query('SELECT * FROM FOURNISSEURS;');
                                while ($data = $query->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data['idFournisseur']; ?>"><?php echo $data['nomFournisseur']; ?></option>
                                    <?php
                                }
                                $query->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Remarques:</label>
                            <textarea class="form-control" rows="3" name="remarquesGenerales"></textarea>
                        </div>
                        <div class="box-footer">
                            <a href="javascript:history.go(-1)" class="btn btn-default">Retour</a>
                            <button type="submit" class="btn btn-info pull-right">Suite</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->

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
