<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/version.php'); ?>
<?php
session_start();
$_SESSION['page'] = 501;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['logs_lecture']==0)
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
                Historique des actions
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Logs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box box-info">

                <div class="box-body">
                    <form role="form" action="logsDate.php" method="POST">
                        <h4>Selection de la plage</h4>
                        <!-- text input -->
                        <div class="form-group">
                            <label>Date de début:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateInf" value="<?=$_GET['dateInf']?>">
                            </div>
                            <br/>
                            <label>Date de fin:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="input-datepicker form-control" name="dateSup" value="<?=$_GET['dateSup']?>">
                            </div>
                            <br/>
                            <button type="submit" class="btn btn-info pull-right">Appliquer</button>
                            <!-- /.input group -->
                        </div>
                    </form>
                </div>
            </div>

            <?php
            $query = $db->prepare('SELECT COUNT(*) as nb FROM LOGS l LEFT OUTER JOIN LOGS_LEVEL n ON l.idLogLevel = n.idLogLevel WHERE dateEvt <= :dateSup AND dateEvt >= :dateInf ORDER BY dateEvt DESC;');
            $query->execute(array(
                'dateSup' => $_GET['dateSup'],
                'dateInf' => $_GET['dateInf']));
            $data = $query->fetch();

            if ($data['nb']>1000)
                {
                    $_SESSION['returnMessage'] = "Le nombre d'éléments à afficher est trop important. Veuillez réduire la plage de date ou consulter les logs directement en base.";
                    $_SESSION['returnType'] = 2;
                    include('confirmationBox.php');
                }
                else
                {
                    $query = $db->prepare('SELECT * FROM LOGS l LEFT OUTER JOIN LOGS_LEVEL n ON l.idLogLevel = n.idLogLevel WHERE dateEvt <= :dateSup AND dateEvt >= :dateInf ORDER BY dateEvt DESC;');
                    $query->execute(array(
                        'dateSup' => $_GET['dateSup'],
                        'dateInf' => $_GET['dateInf']
                    ));
                }

            ?>

            <div class="box box-info">
                <div class="box-body">
                    <table id="tri0" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Date/Heure</th>
                            <th>Utilisateur</th>
                            <th>Adresse IP</th>
                            <th>Type d'évènement</th>
                            <th>Détails</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($data = $query->fetch())
                        {?>
                            <tr>
                                <td><?php echo $data['dateEvt']; ?></td>
                                <td><?php echo $data['utilisateurApollonEvt']; ?></td>
                                <td><?php echo $data['adresseIP']; ?></td>
                                <td><?php echo $data['libelleLevel']; ?></td>
                                <td><?php echo $data['detailEvt']; ?></td>
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

<!-- Ion Slider -->
<script src="plugins/ionslider/ion.rangeSlider.min.js"></script>
<!-- Bootstrap slider -->
<script src="plugins/bootstrap-slider/bootstrap-slider.js"></script>
