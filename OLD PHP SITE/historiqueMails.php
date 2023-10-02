<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 501;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['appli_conf']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Historiques des mails envoyés par <?=$APPNAME?> sur les 6 derniers mois
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Mails</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <!-- general form elements disabled -->

           <?php include('confirmationBox.php'); ?>
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2R" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Entrée dans la file</th>
                                <th class="all">Contexte</th>
                                <th class="not-mobile">Destinataire</th>
                                <th class="not-mobile">Envoyé</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('
                            SELECT
                                *
                            FROM
                                NOTIFICATIONS_MAILS
                            WHERE
                                dateEntreeQueue >= (CURRENT_TIMESTAMP() - INTERVAL 6 MONTH)
                            ;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr>
                                <td><?= $data['idMail'] ?></td>
                                <td><?= $data['dateEntreeQueue'] ?></td>
                                <td><?= $data['contexte'] ?></td>
                                <td><?= $data['adresseDest'] ?></td>
                                <td><?php
                                    if(!(is_null($data['dateEnvoiMail'])))
                                    {
                                        echo '<span class="badge bg-green">'.$data['dateEnvoiMail'].'</span>';
                                    }
                                    else
                                    {
                                        if($data['nombreRetry']<4)
                                        {
                                            echo '<span class="badge bg-orange">Envoi en cours</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="badge bg-red">Envoi en échec</span>';
                                        }
                                    }
                                ?></td>
                                <td>
                                    <a href="historiqueMailsVisualiser.php?id=<?=$data['idMail']?>" class="btn btn-xs btn-info modal-form" title="Visualiser"><i class="fa fa-eye"></i></a>
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
