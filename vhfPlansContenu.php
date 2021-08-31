<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 902;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vhf_plan_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?php echo $SITECOLOR; ?> sidebar-mini fixed">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>
    <?php require_once 'documentsGetIcone.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM VHF_PLAN WHERE idVhfPlan = :idVhfPlan;');
    $query->execute(array(
        'idVhfPlan' => $_GET['id']));
    $data=$query->fetch();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Détails du plan de programmation : <?php echo $data['libellePlan']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="vhfPlans.php">Plans</a></li>
                <li class="active"><?php echo $data['libellePlan']; ?></li>
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
                                    <td><?= $data['libellePlan'] ?></td>
                                </tr>
                                <tr>
                                    <td>Remarques</td>
                                    <td><?= $data['remarquesPlan'] ?></td>
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
                            <li class="active"><a href="#canaux" data-toggle="tab">Canaux</a></li>
                            <li><a href="#pj" data-toggle="tab">Pièces jointes</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="canaux">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th class="all" style="width: 10px">Canal</th>
                                        <th class="all">Libelle</th>
                                        <th class="not-mobile">Technologie</th>
                                        <th class="not-mobile">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $query = $db->prepare('SELECT * FROM VHF_PLAN_CANAL p LEFT OUTER JOIN VHF_CANAL c ON p.idVhfCanal = c.idVhfCanal LEFT OUTER JOIN VHF_TECHNOLOGIES t  ON c.idVhfTechno = t.idVhfTechno WHERE p.idVhfPlan = :idVhfPlan;');
                                    $query->execute(array('idVhfPlan'=>$_GET['id']));
                                    while ($data = $query->fetch())
                                    {?>
                                        <tr>
                                            <td><?php echo $data['numeroCanal']; ?></td>
                                            <td><?php echo $data['chName']; ?></td>
                                            <td><?php echo $data['libelleTechno']; ?></td>
                                            <td>
                                                <?php if ($_SESSION['vhf_canal_lecture']==1) {?>
                                                    <a href="vhfCanauxContenu.php?id=<?=$data['idVhfCanal']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                                <?php }?>
                                                <?php if ($_SESSION['vhf_plan_modification']==1) {?>
                                                    <a href="modalDeleteConfirm.php?case=vhfPlansCanauxDelete&idVhfPlan=<?=$data['idVhfPlan']?>&idVhfCanal=<?=$data['idVhfCanal']?>" class="btn btn-xs btn-danger modal-form"><i class="fa fa-minus"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query->closeCursor(); ?>
                                    <?php if($_SESSION['vhf_plan_modification']==1){ ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><a href="vhfPlansCanauxForm.php?idVhfPlan=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form"><i class="fa fa-plus"></i></a></td>
                                    <tr>
                                        <?php }?>
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
                                    $query2 = $db->prepare('SELECT * FROM DOCUMENTS_PLAN_VHF c LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument WHERE idVhfPlan = :idVhfPlan ORDER BY nomDocPlanVHF ASC ;');
                                    $query2->execute(array('idVhfPlan' => $_GET['id']));
                                    while ($data2 = $query2->fetch())
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['nomDocPlanVHF'];?></td>
                                            <td><?php echo $data2['libelleTypeDocument'];?></td>
                                            <td><?php echo $data2['dateDocPlanVHF'];?></td>
                                            <td><i class="fa <?php echo documentsGetIcone($data2['formatDocPlanVHF']);?>"></i></td>
                                            <td>
                                                <?php if($_SESSION['vhf_plan_lecture']==1){
                                                    if ($data2['formatDocPlanVHF'] == 'pdf' OR $data2['formatDocPlanVHF'] == 'jpg' OR $data2['formatDocPlanVHF'] == 'jpeg' OR $data2['formatDocPlanVHF'] == 'png'){?>
                                                        <a href="vhfPlansDocView.php?idDoc=<?=$data2['idDocPlanVHF']?>" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                                                    <?php } else { ?>
                                                        <a class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                                    <?php }}?>
                                                <?php if($_SESSION['vhf_plan_lecture']==1){ ?>
                                                    <a href="vhfPlansDocDL.php?idDoc=<?=$data2['idDocPlanVHF']?>" class="btn btn-xs btn-success"><i class="fa fa-download"></i></a>
                                                <?php }?>
                                                <?php if($_SESSION['vhf_plan_suppression']==1){ ?>
                                                    <a href="vhfPlansDocDelete.php?idDoc=<?=$data2['idDocPlanVHF']?>" class="btn btn-xs btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet élément?');"><i class="fa fa-minus"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $query2->closeCursor(); ?>

                                    <?php if($_SESSION['vhf_plan_modification']==1){ ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><a href="vhfPlansDocForm.php?idVhfPlan=<?= $_GET['id'] ?>" class="btn btn-xs btn-success modal-form"><i class="fa fa-plus"></i></a></td>
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



