<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['lots_lecture']==0)
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
                Gestion des lots
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Lots</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="box">
                <?php if ($_SESSION['lots_ajout']==1) {?>
	                <div class="box-header">
						<h3 class="box-title"><a href="lotsForm.php" class="btn btn-sm btn-success modal-form">Ajouter un lot</a></h3>
						<h3 class="box-title"><a href="lotsDuplicateForm.php" class="btn btn-sm btn-success modal-form">Dupliquer un lot existant</a></h3>
                        <?php if ($_SESSION['lots_modification']==1 AND $LOTSLOCK) {?><h3 class="box-title pull-right"><a data-toggle="modal" data-target="#modalSuppressionLocks" class="btn btn-sm btn-danger">Inventaires de lots - Désactiver les verrouillages</a></h3><?php } ?>
	                </div>
                <?php }?>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="tri2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="all" style="width: 10px">#</th>
                                <th class="all">Libelle</th>
                                <th class="not-mobile">Etat</th>
                                <th class="not-mobile">Référentiel <a href="lotsCheckConfTotalManu.php" class="btn btn-xs spinnerAttenteClick" title="Recalculer la conformité au référentiel"><i class="fa fa-refresh"></i></a></th>
                                <th class="not-mobile">Référent</th>
                                <th class="not-mobile">Quantité Matériel</th>
                                <th class="not-mobile">Prochain Inventaire</th>
                                <?php if($_SESSION['alertesBenevolesLots_lecture']){ ?><th class="not-mobile">Alertes Bénévoles</th><?php } ?>
                                <th class="not-mobile">Notifications</th>
                                <th class="not-mobile">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $db->query('
                            SELECT
                                *
                            FROM
                                LOTS_LOTS l
                                LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot
                                LEFT OUTER JOIN ETATS s on l.idEtat = s.idEtat
                                LEFT OUTER JOIN LIEUX e ON l.idLieu = e.idLieu
                                LEFT OUTER JOIN PERSONNE_REFERENTE p on l.idPersonne = p.idPersonne
                                LEFT OUTER JOIN LOTS_ETATS et ON l.idLotsEtat = et.idLotsEtat
                            ;');
                        while ($data = $query->fetch())
                        {
                            ?>
                            <tr <?php if ($_SESSION['lots_lecture']==1) {?>data-href="lotsContenu.php?id=<?=$data['idLot']?>"<?php }?>>
                                <td><?= $data['idLot'] ?></td>
                                <td><?= $data['libelleLot'] ?></td>
                                <td><?= $data['libelleLotsEtat'] ?></td>
                                <td>
                                    <?php
                                    if ($data['libelleTypeLot'] == Null)
                                    {
                                        ?><span class="badge bg-orange">NA</span><?php
                                    }
                                    else
                                    {
                                        if ($data['alerteConfRef']==0)
                                        {
                                            ?><span class="badge bg-green"><?php echo $data['libelleTypeLot']; ?></span><?php
                                        }
                                        else
                                        {
                                            ?><span class="badge bg-red"><?php echo $data['libelleTypeLot']; ?></span><?php
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $data['identifiant']; ?></td>
                                <td>
                                    <?php
                                        $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                                            WHERE idLot = :idLot AND
                                            (
                                                quantite > quantiteAlerte AND
                                                (
                                                    peremptionNotification > CURRENT_DATE
                                                    OR
                                                    peremptionNotification IS NULL
                                                )
                                            );');
                                        $query2->execute(array(
                                            'idLot' => $data['idLot']
                                        ));
                                        $data2 = $query2->fetch();
                                        if($data2['nb']>0)
                                        {
                                            ?><span class="badge bg-green"><?= $data2['nb'] ?></span><?php
                                        }
                                    ?>
                                    <?php
                                    $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                                            WHERE idLot = :idLot AND
                                            (
                                                (
                                                    quantite = quantiteAlerte
                                                    AND
                                                    peremptionNotification = CURRENT_DATE
                                                )
                                                OR
                                                (
                                                    quantite = quantiteAlerte
                                                    AND
                                                    (
                                                        peremptionNotification > CURRENT_DATE
                                                        OR
                                                        peremptionNotification IS NULL
                                                    )
                                                )
                                                OR
                                                (
                                                    quantite > quantiteAlerte
                                                    AND
                                                    peremptionNotification = CURRENT_DATE
                                                )
                                            );');
                                    $query2->execute(array(
                                        'idLot' => $data['idLot']
                                    ));
                                    $data2 = $query2->fetch();
                                    if($data2['nb']>0)
                                    {
                                        ?><span class="badge bg-orange"><?= $data2['nb'] ?></span><?php
                                    }
                                    ?>
                                    <?php
                                    $query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT e LEFT OUTER JOIN MATERIEL_EMPLACEMENT p ON e.idEmplacement=p.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON p.idSac=s.idSac
                                            WHERE idLot = :idLot AND
                                            (
                                                quantite < quantiteAlerte OR
                                                peremptionNotification < CURRENT_DATE
                                            );');
                                    $query2->execute(array(
                                        'idLot' => $data['idLot']
                                    ));
                                    $data2 = $query2->fetch();
                                    if($data2['nb']>0)
                                    {
                                        ?><span class="badge bg-red"><?= $data2['nb'] ?></span><?php
                                    }
                                    ?>
                                </td>
                                <td><?php
                                    if($data['inventaireEnCours'])
                                    {
                                        echo '<span class="badge bg-orange faa-flash animated">Inventaire en cours</span><br/>';
                                    }
                                    else
                                    {
                                        if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) < date('Y-m-d'))
                                        {
                                            ?><span class="badge bg-red"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
                                        }
                                        else if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) == date('Y-m-d'))
                                        {
                                            ?><span class="badge bg-orange"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
                                        }
                                        else
                                        {
                                            ?><span class="badge bg-green"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span><?php
                                        }
                                    }
                                    ?>
                                </td>
                                <?php if($_SESSION['alertesBenevolesLots_lecture']){ ?>
                                	<td>
                                		<?php
	                                        $query2 = $db->prepare('SELECT COUNT(*) as nb FROM LOTS_ALERTES WHERE idLot = :idLot AND idLotsAlertesEtat = 1;');
	                                        $query2->execute(array(
	                                            'idLot' => $data['idLot']
	                                        ));
	                                        $data2 = $query2->fetch();
	                                        if($data2['nb']>0)
	                                        {
	                                            ?><span class="badge bg-red"><?= $data2['nb'] ?></span><?php
	                                        }
	                                    ?>
	                                    <?php
	                                        $query2 = $db->prepare('SELECT COUNT(*) as nb FROM LOTS_ALERTES WHERE idLot = :idLot AND (idLotsAlertesEtat = 2 OR idLotsAlertesEtat = 3);');
	                                        $query2->execute(array(
	                                            'idLot' => $data['idLot']
	                                        ));
	                                        $data2 = $query2->fetch();
	                                        if($data2['nb']>0)
	                                        {
	                                            ?><span class="badge bg-blue"><?= $data2['nb'] ?></span><?php
	                                        }
	                                    ?>
                                	</td>
                                <?php } ?>
                                <td><?php echo $data['libelleEtat']; ?> (<?php if($data['idEtat']!=1){echo '<i class="fa fa-bell-slash-o"></i>';}else{echo '<i class="fa fa-bell-o"></i>';} ?>)</td>
                                <td>
                                    <?php if ($_SESSION['lots_lecture']==1) {?>
                                        <a href="lotsContenu.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['lots_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                        <a href="lotsForm.php?id=<?=$data['idLot']?>" class="btn btn-xs btn-warning modal-form" title="Modifier"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <?php if ($_SESSION['lots_suppression']==1 AND $data['inventaireEnCours']==Null) {?>
                                        <a href="modalDeleteConfirm.php?case=lotsDelete&id=<?=$data['idLot']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i></a>
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


<div class="modal fade modal-danger" id="modalSuppressionLocks">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Inventaires en cours</h4>
            </div>
            <div class="modal-body">
                Des inventaires de lots sont en cours. En conséquent, plusieurs fonctionnalités du site sont verouillées en lecture seule. Le verrouillage s'enlèvera automatiquement dès que les inventaires en cours seront validés. Si ce verrouillage est à tors, vous pouvez forcer le déverrouillage manuellement ici.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <a href="lotsInventaireAbortLock.php"><button type="button" class="btn btn-default pull-right">Forcer le déverrouillage</button></a>
            </div>
        </div>
    </div>
</div>


</html>



