<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 102;
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
                Gestion des sacs
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="sacs.php">Sacs</a></li>
                <li class="active">Ajouter/Modifier un sac</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- general form elements disabled -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Modification d'un sac</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="sacsUpdate.php?id=<?=$_GET['id']?>" method="POST">
                        <?php
                        $query = $db->prepare('SELECT * FROM MATERIEL_SAC s LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN FOURNISSEURS f ON s.idFournisseur = f.idFournisseur WHERE idSac = :idSac;');
                        $query->execute(array('idSac' => $_GET['id']));
                        $data = $query->fetch();
                        ?>
                        <!-- text input -->
                        <div class="form-group">
                            <label>Libellé:</label>
                            <input type="text" class="form-control" value="<?=$data['libelleSac']?>"
                                   name="libelleSac" required>
                        </div>
                        <div class="form-group">
                            <label>Lot d'appartenance: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleLot">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM LOTS_LOTS ORDER BY libelleLot;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idLot']; ?>" <?php if ($data2['idLot'] == $data['idLot']) { echo 'selected'; } ?> ><?php echo $data2['libelleLot']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" value="<?=$data['taille']?>"
                                   name="taille">
                        </div>
                        <div class="form-group">
                            <label>Couleur:</label>
                            <input type="text" class="form-control" value="<?=$data['couleur']?>"
                                   name="couleur">
                        </div>
                        <div class="form-group">
                            <label>Fournisseur: </label>
                            <select class="form-control select2" style="width: 100%;" name="nomFournisseur">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM FOURNISSEURS ORDER BY nomFournisseur;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idFournisseur']; ?>" <?php if ($data2['idFournisseur'] == $data['idFournisseur']) { echo 'selected'; } ?> ><?php echo $data2['nomFournisseur']; ?></option>
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
