
<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['tenuesCatalogue_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
        $query = $db->prepare('SELECT * FROM TENUES_CATALOGUE t LEFT OUTER JOIN FOURNISSEURS f ON t.idFournisseur = f.idFournisseur WHERE idCatalogueTenue = :idCatalogueTenue;');
        $query->execute(array(
                'idCatalogueTenue' => $_GET['id']));
        $data=$query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $data['libelleCatalogueTenue']; if(isset($data['tailleCatalogueTenue']) AND $data['tailleCatalogueTenue'] != Null){ echo ' ('.$data['tailleCatalogueTenue'].')'; }?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="tenuesCatalogue.php">Catalogue des tenues</a></li>
                <li class="active"><?php echo $data['chName']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">

                <div class="col-md-4">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Détails</h3> <?php if ($_SESSION['tenuesCatalogue_modification']==1) {?><a href="tenuesCatalogueForm.php?id=<?=$_GET['id']?>" class="btn btn-xs modal-form" title="Modifier"><i class="fa fa-pencil"></i></a><?php }?>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-condensed">
                                <tr>
                                    <td>Libellé</td>
                                    <td><?= $data['libelleCatalogueTenue'] ?></td>
                                </tr>
                                <tr>
                                    <td>Taille</td>
                                    <td><?= $data['tailleCatalogueTenue'] ?></td>
                                </tr>
                                <tr>
                                    <td>Sérigraphie</td>
                                    <td><?= $data['serigraphieCatalogueTenue'] ?></td>
                                </tr>
                                <tr>
                                    <td>Fournisseur</td>
                                    <td><?= $data['nomFournisseur'] ?></td>
                                </tr>
                                <tr>
                                    <td>Stock</td>
                                    <td><?php
                                            if ($data['stockAlerteCatalogueTenue'] == $data['stockCatalogueTenue'])
                                            {
                                                echo '<span class="badge bg-orange">'.$data['stockCatalogueTenue'].'</span>';
                                            }
                                            else
                                            {
                                                if ($data['stockAlerteCatalogueTenue'] > $data['stockCatalogueTenue'])
                                                {
                                                    echo '<span class="badge bg-red">'.$data['stockCatalogueTenue'].'</span>';
                                                }
                                                else
                                                {
                                                    echo '<span class="badge bg-green">'.$data['stockCatalogueTenue'].'</span>';
                                                }
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alerte de stock</td>
                                    <td><?= $data['stockAlerteCatalogueTenue'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
                <div class="col-md-8">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Affectations</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="tri2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="all" style="width: 10px">#</th>
                                        <th class="all">Personne</th>
                                        <th class="not-mobile">Date d'affectation</th>
                                        <th class="not-mobile">Date de retour prévue</th>
                                        <th class="not-mobile"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query2 = $db->prepare('SELECT ta.*, p.nomPersonne, p.prenomPersonne FROM TENUES_AFFECTATION ta LEFT OUTER JOIN PERSONNE_REFERENTE p ON ta.idPersonne = p.idPersonne WHERE idCatalogueTenue = :idCatalogueTenue;');
                                $query2->execute(array('idCatalogueTenue'=>$_GET['id']));
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <tr>
                                        <td><?php echo $data2['idTenue']; ?></td>
                                        <td><?php
                                                if (isset($data2['idPersonne']) AND $data2['idPersonne'] != Null)
                                                {
                                                    echo $data2['nomPersonne'] . ' ' . $data2['prenomPersonne'];
                                                }
                                                else
                                                {
                                                    echo $data2['personneNonGPM'] . ' (externe)';
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo $data2['dateAffectation']; ?></td>
                                        <td><?php echo $data2['dateRetour']; ?></td>
                                        <td>
                                            <?php if ($_SESSION['tenues_modification']==1) {?>
                                                <a href="tenuesAffectationsForm.php?id=<?=$data2['idTenue']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                            <?php }?>
                                            <?php if ($_SESSION['tenues_suppression']==1) {?>
                                                <a href="modalDeleteConfirm.php?case=tenuesAffectationsDelete&id=<?=$data2['idTenue']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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
                    <!-- /.widget-user -->
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



