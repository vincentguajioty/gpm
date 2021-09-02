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
    
    <?php
	    $query = $db->prepare('SELECT * FROM LOTS_TYPES WHERE idTypeLot = :idTypeLot;');
	    $query->execute(array(
	        'idTypeLot' => $_GET['id']
	    ));
	    $data = $query->fetch();
	?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Référentiel: <?php echo $data['libelleTypeLot'];?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="referentiels.php">Référentiels</a></li>
                <li class="active">Modifier un référentiel</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <form role="form" action="referentielsContenuUpdate.php?id=<?= $_GET['id'] ?>" method="POST">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">Contenu du référentiel</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Catérogie</th>
                                <th>Matériel</th>
                                <th>Quantité exigée</th>
                                <th>Obligation exigée</th>
                                <th>Commentaires</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = $db->prepare('SELECT * FROM REFERENTIELS WHERE idTypeLot = :idTypeLot;');
                            $query->execute(array(
                                'idTypeLot' => $_GET['id']
                            ));
                            $referentiel = $query->fetchall();

                            $query = $db->query('SELECT c.*, s.libelleCategorie FROM MATERIEL_CATALOGUE c LEFT OUTER JOIN MATERIEL_CATEGORIES s ON c.idCategorie = s.idCategorie ORDER BY libelleCategorie, libelleMateriel;');
                            while ($catalogue = $query->fetch())
                            {
                                $key = -1;
                                if(array_search($catalogue['idMaterielCatalogue'],array_column($referentiel, 'idMaterielCatalogue')) !== FALSE){$key = array_search($catalogue['idMaterielCatalogue'],array_column($referentiel, 'idMaterielCatalogue'));}
                                ?>
                                <tr>
                                    <td><?php echo $catalogue['idMaterielCatalogue']; ?></td>
                                    <td><?php echo $catalogue['libelleCategorie']; ?></td>
                                    <td><?php echo $catalogue['libelleMateriel']; ?></td>
                                    <td>
                                        <input
                                            type="number"
                                            min="1"
                                            class="form-control"
                                            value="<?=$referentiel[$key][2]?>"
                                            name="formArray[<?php echo $catalogue['idMaterielCatalogue']; ?>][qtt]"
                                        >
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control" style="width: 100%;" name="formArray[<?php echo $catalogue['idMaterielCatalogue']; ?>][obligatoire]">
                                                <option value="1">Obligatoire</option>
                                                <option value="0" <?php if($referentiel[$key][3] != Null AND $referentiel[$key][3]==0){echo 'selected';} ?>>Facultatif</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea
                                            class="form-control"
                                            rows="1"
                                            name="formArray[<?php echo $catalogue['idMaterielCatalogue']; ?>][commentairesReferentiel]"><?=$referentiel[$key][4]?></textarea>
                                    </td>
                                </tr>
                                <?php
                            }
                            $query->closeCursor(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="box box-warning">
                    <div class="box-footer">
                        <button type="submit" class="btn btn-warning pull-right">Enregistrer et vérifier tous les lots</button>
                    </div>
                </div>
            </form>
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
