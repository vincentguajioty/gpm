<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 903;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vhf_equipement_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM VHF_EQUIPEMENTS e LEFT OUTER JOIN VHF_ETATS s ON e.idVhfEtat = s.idVhfEtat LEFT OUTER JOIN VHF_TYPES_EQUIPEMENTS t ON e.idVhfType = t.idVhfType LEFT OUTER JOIN VHF_TECHNOLOGIES a ON e.idVhfTechno=a.idVhfTechno LEFT OUTER JOIN VHF_PLAN p ON e.idVhfPlan = p.idVhfPlan LEFT OUTER JOIN PERSONNE_REFERENTE r ON e.idResponsable = r.idPersonne WHERE idVhfEquipement = :idVhfEquipement;');
    $query->execute(array(
        'idVhfEquipement' => $_GET['id']));
    $data=$query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Détails de l'équipement radio : <?php echo $data['vhfIndicatif']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="vhfEquipements.php">Equipements Radio</a></li>
                <li class="active"><?php echo $data['vhfIndicatif']; ?></li>
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
                                    <td>Indicatif</td>
                                    <td><?= $data['vhfIndicatif'] ?></td>
                                </tr>
                                <tr>
                                    <td>Marque/modele</td>
                                    <td><?= $data['vhfMarqueModele'] ?></td>
                                </tr>
                                <tr>
                                    <td>SN</td>
                                    <td><?= $data['vhfSN'] ?></td>
                                </tr>
                                <tr>
                                    <td>Etat</td>
                                    <td><?= $data['libelleVhfEtat'] ?></td>
                                </tr>
                                <tr>
                                    <td>Type</td>
                                    <td><?= $data['libelleType'] ?></td>
                                </tr>
                                <tr>
                                    <td>Technologie</td>
                                    <td><?= $data['libelleTechno'] ?></td>
                                </tr>
                                <tr>
                                    <td>Plan de programmation</td>
                                    <td><?= $data['libellePlan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Date de derniere prog</td>
                                    <td><?= $data['dateDerniereProg'] ?></td>
                                </tr>
                                <tr>
                                    <td>Responsable</td>
                                    <td><?= $data['identifiant'] ?></td>
                                </tr>
                                <tr>
                                    <td>Remarques</td>
                                    <td><?= $data['remarquesVhfEquipement'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
                <div class="col-md-8">
                    <!-- Widget: user widget style 1 -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#canaux" data-toggle="tab">Plan de fréquence</a></li>
                            <li><a href="#pj" data-toggle="tab">Pièces jointes</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="canaux">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th class="all" style="width: 10px">Canal</th>
                                        <th class="all">Libelle</th>
                                        <th class="not-mobile">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM VHF_PLAN_CANAL p LEFT OUTER JOIN VHF_CANAL c ON p.idVhfCanal = c.idVhfCanal WHERE p.idVhfPlan = :idVhfPlan;');
                                    $query2->execute(array('idVhfPlan'=>$data['idVhfPlan']));
                                    while ($data2 = $query2->fetch())
                                    {?>
                                        <tr>
                                            <td><?php echo $data2['numeroCanal']; ?></td>
                                            <td><?php echo $data2['chName']; ?></td>
                                            <td>
                                                <?php if ($_SESSION['vhf_canal_lecture']==1) {?>
                                                    <a href="vhfCanauxContenu.php?id=<?=$data2['idVhfCanal']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>
                                </table>
                            </div>


                            <div class="tab-pane" id="pj">
                                <table class="table table-hover">
                                    <tr>
                                        <th>Nom du document</th>
                                        <th>Type de document</th>
                                        <th>Date de chargement</th>
                                        <th>Format</th>
                                        <th></th>
                                    </tr>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM DOCUMENTS_VHF c LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument WHERE idVhfEquipement = :idVhfEquipement ORDER BY nomDocVHF ASC ;');
                                    $query2->execute(array('idVhfEquipement' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['nomDocVHF'];?></td>
                                            <td><?php echo $data2['libelleTypeDocument'];?></td>
                                            <td><?php echo $data2['dateDocVHF'];?></td>
                                            <td><i class="fa <?php echo documentsGetIcone($data2['formatDocVHF']);?>"></i></td>
                                            <td>
                                                <?php if($_SESSION['vhf_equipement_lecture']==1){
                                                    if ($data2['formatDocVHF'] == 'pdf' OR $data2['formatDocVHF'] == 'jpg' OR $data2['formatDocVHF'] == 'jpeg' OR $data2['formatDocVHF'] == 'png'){?>
                                                        <a href="vhfEquipementsDocView.php?idDoc=<?=$data2['idDocVHF']?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                    <?php }}?>
                                                <?php if($_SESSION['vhf_equipement_lecture']==1){ ?>
                                                    <a href="vhfEquipementsDocDL.php?idDoc=<?=$data2['idDocVHF']?>" class="btn btn-xs btn-success"><i class="fa fa-download"></i></a>
                                                <?php }?>
                                                <?php if($_SESSION['vhf_equipement_suppression']==1){ ?>
                                                    <a href="vhfEquipementsDocDelete.php?idDoc=<?=$data2['idDocVHF']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-minus"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>

                                    <?php if($_SESSION['vhf_equipement_modification']==1){ ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><a href="vhfEquipementsDocForm.php?idVhfEquipement=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form"><i class="fa fa-plus"></i></a></td>
                                    <tr>
                                        <?php }?>
                                </table>
                            </div>

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



