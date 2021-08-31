<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 301;
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
                Ajout d'un élément dans le catalogue
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="catalogue.php">Catalogue</a></li>
                <li class="active">Ajouter/Modifier le catalogue</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- general form elements disabled -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Modification d'un item du catalogue</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <form role="form" action="catalogueUpdate.php?id=<?=$_GET['id']?>" method="POST">
                        <?php
                        $query = $db->prepare('SELECT * FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN MATERIEL_CATEGORIES b ON c.idCategorie = b.idCategorie WHERE idMaterielCatalogue=:idMaterielCatalogue;');
                        $query->execute(array('idMaterielCatalogue' => $_GET['id']));
                        $data = $query->fetch();
                        ?>

                        <div class="form-group">
                            <label>Libellé:</label>
                            <input type="text" class="form-control" value="<?=$data['libelleMateriel']?>"
                                   name="libelleMateriel" required>
                        </div>
                        <div class="form-group">
                            <label>Catégorie: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleCategorie">
                                <option></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM MATERIEL_CATEGORIES ORDER BY libelleCategorie;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idCategorie']; ?>" <?php if ($data2['libelleCategorie'] == $data['libelleCategorie']) { echo 'selected'; } ?> ><?php echo $data2['libelleCategorie']; ?></option>
                                    <?php
                                }
                                $query->closeCursor();
                                $query2->closeCursor();?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Taille:</label>
                            <input type="text" class="form-control" value="<?=$data['taille']?>"
                                   name="taille">
                        </div>
                        <div class="form-group">
                            <label>Stérilité:</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios1" value="option1" <?php if ($data['sterilite']==0)
                                        echo 'checked'?>
                                    >
                                    Matériel non-stérile
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="sterilite" id="optionsRadios2" value="option2"<?php if ($data['sterilite']==1)
                                            echo 'checked'?>>
                                    Matériel stérile
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Conditionnement:</label>
                            <input type="text" class="form-control" value="<?=$data['conditionnementMultiple']?>"
                                   name="conditionnementMultiple">
                        </div>
                        <div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                      name="commentairesMateriel"><?php echo $data['commentairesMateriel']?></textarea>
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
