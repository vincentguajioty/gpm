<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 201;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['typesLots_lecture']==0)
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
                Référentiels des lots opérationnels
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="referentiels.php">Référentiels</a></li>
                <li class="active">Modifier un référentiel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
                <?php if ($_SESSION['typesLots_modification']==1)
                { ?>
                    <?php include('confirmationBox.php'); ?>
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ajouter un matériel au référentiel</h3>
                        </div>
                        <div class="box-body">
                            <form role="form" action="referentielsAddItem.php?idLot=<?=$_GET['id']?>" method="POST">
                                <!-- select -->
                                <div class="form-group">
                                    <label>Matériel: </label>
                                    <select class="form-control select2" style="width: 100%;" name="libelleMateriel">
                                        <?php
                                        $query = $db->prepare('SELECT c.idMaterielCatalogue, c.libelleMateriel FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN (SELECT idMaterielCatalogue FROM REFERENTIELS WHERE idTypeLot= :idTypeLot) r ON c.idMaterielCatalogue = r.idMaterielCatalogue WHERE r.idMaterielCatalogue IS NULL ORDER BY libelleMateriel;');
                                        $query->execute(array('idTypeLot' => $_GET['id']));
                                        while ($data = $query->fetch())
                                        {
                                            ?>
                                            <option value="<?php echo $data['idMaterielCatalogue']; ?>"><?php echo $data['libelleMateriel']; ?></option>
                                            <?php
                                        }
                                        $query->closeCursor(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Quantité:</label>
                                    <input type="text" class="form-control" placeholder="1-2-3 ..."
                                           name="quantiteReferentiel">
                                </div>
                                <div class="form-group">
                                    <label>Obligation:</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="obligatoire" id="optionsRadios1" value="option1" checked>
                                            Ce matériel est obligatoire.
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="obligatoire" id="optionsRadios2" value="option2">
                                            Ce matériel est facultatif.
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Commentaires:</label>
                                    <textarea class="form-control" rows="3" placeholder="Spécifiez d'autres détails"
                                              name="commentairesReferentiel"></textarea>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-info pull-right">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>


                <div class="box box-warning">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="box-header">
                            <h3 class="box-title">Contenu du référentiel</h3>
                        </div>

                        <table id="tri2" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Matériel</th>
                                <th>Quantité</th>
                                <th>Obligation</th>
                                <th>Catérogie</th>
                                <th>Commentaires</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = $db->prepare('SELECT * FROM REFERENTIELS r LEFT OUTER JOIN MATERIEL_CATALOGUE c ON r.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN MATERIEL_CATEGORIES b ON c.idCategorie = b.idCategorie WHERE idTypeLot = :idTypeLot ORDER BY libelleMateriel;');
                            $query->execute(array(
                                'idTypeLot' => $_GET['id']
                            ));
                            while ($data = $query->fetch())
                            {?>
                                <tr>
                                    <td><?php echo $data['idMaterielCatalogue']; ?></td>
                                    <td><?php echo $data['libelleMateriel']; ?></td>
                                    <td><?php echo $data['quantiteReferentiel']; ?></td>
                                    <td><?php
                                        if ($data['obligatoire'] == 0)
                                        {
                                            ?><span class="badge bg-green">Facultatif</span><?php
                                        }
                                        else
                                        {
                                            ?><span class="badge bg-blue">Obligatoire</span><?php
                                        }
                                        ?></td>
                                    <td><?php echo $data['libelleCategorie']; ?></td>
                                    <td><?php echo $data['commentairesReferentiel']; ?></td>
                                    <td>
                                        <?php if ($_SESSION['typesLots_modification']==1) {?>
                                            <a href="referentielsDeleteItem.php?idLot=<?=$_GET['id']?>&idMateriel=<?=$data['idMaterielCatalogue']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-trash"></i></a>
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
