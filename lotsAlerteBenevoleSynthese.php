<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 105;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['alertesBenevolesLots_lecture']==0)
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
                Alertes remontées par les bénévoles sur les lots opérationnels
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Alertes</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri3R" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Bénévole</th>
                                <th class="not-mobile">Date d'ouverture</th>
                                <th class="not-mobile">Lot</th>
                                <th class="not-mobile">Message</th>
                                <th class="not-mobile">Traitement</th>
                                <th class="not-mobile">Affectation</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('
                            SELECT
                                a.*,
                                l.libelleLot,
                                e.libelleLotsAlertesEtat,
                                e.couleurLotsAlertesEtat,
                                p.identifiant
                            FROM
                                LOTS_ALERTES a
                                LEFT OUTER JOIN LOTS_LOTS l ON a.idLot = l.idLot
                                LEFT OUTER JOIN LOTS_ALERTES_ETATS e on a.idLotsAlertesEtat = e.idLotsAlertesEtat
                                LEFT OUTER JOIN PERSONNE_REFERENTE p ON a.idTraitant = p.idPersonne
                            WHERE
                                a.idLotsAlertesEtat = 1
                                OR
                                a.idLotsAlertesEtat = 2
                                OR
                                a.idLotsAlertesEtat = 3
                            ;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr>
                                <td><?= $data['idAlerte'] ?></td>
                                <td><?= $data['nomDeclarant'] ?></td>
                                <td><?= $data['dateCreationAlerte'] ?></td>
                                <td><?= $data['libelleLot'] ?></td>
                                <td><?= nl2br($data['messageAlerteLot']) ?></td>
                                <td><span class="badge bg-<?= $data['couleurLotsAlertesEtat'] ?>"><?= $data['libelleLotsAlertesEtat'] ?></span></td>
                                <td>
                                	<?php if($data['idLotsAlertesEtat']==1){?>
                                		<a href="lotsAlerteBenevoleAffectation.php?id=<?=$data['idAlerte']?>" class="btn btn-xs btn-success" title="S'affecter cette alerte">Je prends en charge cette alerte</a>
                                		<br/>
                                		<a href="lotsAlerteBenevoleAffectationTiers.php?id=<?=$data['idAlerte']?>" class="btn btn-xs btn-success modal-form" title="Affecter cette alerte à une personne de l'équipe">Affecter l'alerte à quelqu'un</a>
                                	<?php } ?>
                                	<?= $data['identifiant'] ?>
                                </td>
                                <td>
                                    <?php if($data['idLotsAlertesEtat']==1){?><a href="lotsAlerteBenevoleDoublon.php?id=<?=$data['idAlerte']?>" class="btn btn-xs btn-warning modal-form" title="Cette alerte bénévole fait doublon à une alerte déjà remontée">Signaler un doublon</a><?php } ?>
                                    <?php if($data['idLotsAlertesEtat']==1){?><a href="lotsAlerteBenevoleLockIpConfirmation.php?id=<?=$data['idAlerte']?>" class="btn btn-xs btn-danger modal-form" title="Cette entrée est frauduleuse">Fraude</a><?php } ?>
                                    <?php if($data['idLotsAlertesEtat']==2 OR $data['idLotsAlertesEtat']==3){?><a href="lotsAlerteBenevoleCloture.php?id=<?=$data['idAlerte']?>" class="btn btn-xs btn-success" title="Cette alerte est traitée et doit être close">Clôturer cette alerte</a><?php } ?>
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



