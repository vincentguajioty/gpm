
<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 901;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vhf_canal_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
        $query = $db->prepare('SELECT * FROM VHF_CANAL c LEFT OUTER JOIN VHF_TECHNOLOGIES t ON c.idVhfTechno = t.idVhfTechno WHERE idVhfCanal = :idVhfCanal;');
        $query->execute(array(
                'idVhfCanal' => $_GET['id']));
        $data=$query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Détails du canal: <?php echo $data['chName']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="vhfCanaux.php">Canaux</a></li>
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
                            <h3 class="box-title">Détails</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-condensed">
                                <tr>
                                    <td>Libellé</td>
                                    <td><?= $data['chName'] ?></td>
                                </tr>
                                <tr>
                                    <td>Technologie</td>
                                    <td><?= $data['libelleTechno'] ?></td>
                                </tr>
                                <tr>
                                    <td>Rx</td>
                                    <td><?= $data['rxFreq'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tx</td>
                                    <td><?= $data['txFreq'] ?></td>
                                </tr>
                                <tr>
                                    <td>CTCSS Rx</td>
                                    <td><?= $data['rxCtcss'] ?></td>
                                </tr>
                                <tr>
                                    <td>CTCSS Tx</td>
                                    <td><?= $data['txCtcss'] ?></td>
                                </tr>
                                <tr>
                                    <td>CTCSS Porteuse</td>
                                    <td><?= $data['niveauCtcss'] ?></td>
                                </tr>
                                <tr>
                                    <td>Puissance d'émission</td>
                                    <td><?= $data['txPower'] ?></td>
                                </tr>
                                <tr>
                                    <td>Appel Selectif (code)</td>
                                    <td><?= $data['appelSelectifCode'] ?></td>
                                </tr>
                                <tr>
                                    <td>Appel Selectif (porteuse)</td>
                                    <td><?= $data['appelSelectifPorteuse'] ?></td>
                                </tr>
                                <tr>
                                    <td>Let</td>
                                    <td><?= $data['let'] ?></td>
                                </tr>
                                <tr>
                                    <td>NoTone</td>
                                    <td><?= $data['notone'] ?></td>
                                </tr>
                                <tr>
                                    <td>Remarques</td>
                                    <td><?= $data['remarquesCanal'] ?></td>
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
                            <h3 class="box-title">Pièces jointes</h3>
                        </div>

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-hover">
                                <tr>
                                    <th>Nom du document</th>
                                    <th>Type de document</th>
                                    <th>Date de chargement</th>
                                    <th>Format</th>
                                    <th></th>
                                </tr>
                                <?php
                                $query2 = $db->prepare('SELECT * FROM DOCUMENTS_CANAL_VHF c LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument WHERE idVhfCanal = :idVhfCanal ORDER BY nomDocCanalVHF ASC ;');
                                $query2->execute(array('idVhfCanal' => $_GET['id']));
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <tr>
                                        <td><?php echo $data2['nomDocCanalVHF'];?></td>
                                        <td><?php echo $data2['libelleTypeDocument'];?></td>
                                        <td><?php echo $data2['dateDocCanalVHF'];?></td>
                                        <td><i class="fa <?php echo documentsGetIcone($data2['formatDocCanalVHF']);?>"></i></td>
                                        <td>
                                            <?php if($_SESSION['vhf_canal_lecture']==1){
                                                if ($data2['formatDocCanalVHF'] == 'pdf' OR $data2['formatDocCanalVHF'] == 'jpg' OR $data2['formatDocCanalVHF'] == 'jpeg' OR $data2['formatDocCanalVHF'] == 'png'){?>
                                                    <a href="vhfCanauxDocView.php?idDoc=<?=$data2['idDocCanalVHF']?>" class="btn btn-xs btn-info" title="Visualiser"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                                    <a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                <?php }}?>
                                            <?php if($_SESSION['vhf_canal_lecture']==1){ ?>
                                                <a href="vhfCanauxDocDL.php?idDoc=<?=$data2['idDocCanalVHF']?>" class="btn btn-xs btn-success" title="Télécharger"><i class="fa fa-download"></i></a>
                                            <?php }?>
                                            <?php if($_SESSION['vhf_canal_suppression']==1){ ?>
                                                <a href="vhfCanauxDocDelete.php?idDoc=<?=$data2['idDocCanalVHF']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');" title="Supprimer"><i class="fa fa-minus"></i></a>
                                            <?php }?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $query2->closeCursor(); ?>

                                <?php if($_SESSION['vhf_canal_modification']==1){ ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><a href="vhfCanauxDocForm.php?idVhfCanal=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a></td>
                                <tr>
                                    <?php }?>
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



