<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 701;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['reserve_lecture']==0)
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
?>
<body class="hold-transition skin-<?= $SITECOLOR ?> sidebar-mini <?= $_SESSION['layout'] ?>">
<div class="wrapper">
    <?php include('bandeausup.php'); ?>
    <?php include('navbar.php'); ?>
    <?php require_once 'config/bdd.php'; ?>

    <?php
    $query = $db->prepare('SELECT * FROM RESERVES_CONTENEUR c LEFT OUTER JOIN LIEUX l ON c.idLieu = l.idLieu WHERE idConteneur = :idConteneur;');
    $query->execute(array(
        'idConteneur' => $_GET['id']
    ));
    $data = $query->fetch();
    $query4 = $db->prepare('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL WHERE idConteneur = :idConteneur;');
    $query4->execute(array(
        'idConteneur' => $_GET['id']
    ));
    $data4 = $query4->fetch();
    $query5 = $db->prepare('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL m WHERE idConteneur = :idConteneur AND (peremptionReserve < CURRENT_DATE OR peremptionReserve = CURRENT_DATE);');
    $query5->execute(array(
        'idConteneur' => $_GET['id']
    ));
    $data5 = $query5->fetch();
    $query6 = $db->prepare('SELECT COUNT(*) as nb FROM RESERVES_MATERIEL m WHERE idConteneur = :idConteneur AND (quantiteReserve < quantiteAlerteReserve OR quantiteReserve = quantiteAlerteReserve);');
    $query6->execute(array(
        'idConteneur' => $_GET['id']
    ));
    $data6 = $query6->fetch();

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <section class="content-header">
            <h1>
                Gestion du conteneur de réserve: <?php echo $data['libelleConteneur']; ?>
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li><a href="reserveConteneurs.php">Conteneurs</a></li>
                <li class="active"><?php echo $data['libelleConteneur']; ?></li>
            </ol>
        </section>

        <!-- Main content -->
        
    	<section class="content">
    		<?php include('confirmationBox.php'); ?>
            <?php
                if($data['inventaireEnCours'])
                {
                    echo '<div class="alert alert-warning alert-dismissible">';
                    echo '<i class="icon fa fa-warning"></i> Ce conteneur est en cours d\'inventaire et est donc verrouillé en lecture seule.';
                    echo '</div>';
                }
            ?>
            <div class="row">
                <div class="col-md-6">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-blue">
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Informations générales</h3>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li><a>Lieu <span class="pull-right"><?php echo $data['libelleLieu']; ?></span></a></li>
                                <li><a>Quantite de matériel <span class="pull-right"><?php echo $data4['nb']; ?></span></a></li>
                                <li><a>Dernier inventaire <span class="pull-right"><?php echo $data['dateDernierInventaire']; ?></span></a></li>
                                <li><a>Fréquence d'inventaire <span class="pull-right"><?php echo $data['frequenceInventaire']; ?></span></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>
                <div class="col-md-6">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-blue">
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Verifications</h3>
                        </div>
                        <div class="box-footer no-padding">
                            <ul class="nav nav-stacked">
                                <li><a>Elements périmés<?php
                                        if ($data5['nb']>0)
                                        {
                                            echo '<span class="pull-right badge bg-red">' . $data5['nb'] .'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="pull-right badge bg-green">' . $data5['nb'] .'</span>';
                                        }
                                        ?></span></a></li>
                                <li><a>Matériel manquant<?php
                                        if ($data6['nb']>0)
                                        {
                                            echo '<span class="pull-right badge bg-red">' . $data6['nb'] .'</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="pull-right badge bg-green">' . $data6['nb'] .'</span>';
                                        }
                                        ?></span></a></li>
                                <li><a>Prochain inventaire<?php
                                    if (date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) <= date('Y-m-d'))
                                    {
                                        echo '<span class="pull-right badge bg-red">' . date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) . '</span>';
                                    }
                                    else
                                    {
                                        echo '<span class="pull-right badge bg-green">' . date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')) . '</span>';
                                    }
                                        ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.widget-user -->
                </div>

                <div class="col-md-12">
                    <div class="box box-warning box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $data['libelleConteneur']; ?></h3>
                            <?php if ($_SESSION['reserve_modification']==1 AND $data['inventaireEnCours']==Null) {?><a href="reserveConteneurForm.php?id=<?=$data['idConteneur']?>" class="btn btn-xs modal-form"><i class="fa fa-pencil"></i></a><?php }?>
                            <?php if ($_SESSION['codeBarre_lecture']==1) {?><a href="reserveConteneurCBPrintForm.php?id=<?=$data['idConteneur']?>" class="btn btn-xs modal-form" title="Imprimer le code barre de ce conteneur de réserve"><i class="fa fa-barcode"></i> Impression code barre conteneur</a><?php }?>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Catégorie</th>
                                    <th>Libelle</th>
                                    <th>Quantité</th>
                                    <th>Péremption</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query9 = $db->prepare('SELECT * FROM RESERVES_MATERIEL e LEFT OUTER JOIN MATERIEL_CATALOGUE c ON e.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN MATERIEL_CATEGORIES cc ON c.idCategorie = cc.idCategorie WHERE idConteneur = :idConteneur ORDER BY libelleCategorie ASC, libelleMateriel ASC;');
                                $query9->execute(array(
                                    'idConteneur' => $data['idConteneur']
                                ));
                                while ($data9 = $query9->fetch())
                                { ?>
                                    <tr>
                                        <td><?php echo $data9['idReserveElement']; ?></td>
                                        <td><?php echo $data9['libelleCategorie']; ?></td>
                                        <td><?php echo $data9['libelleMateriel']; ?></td>
                                        <td><?php
                                            if ($data9['quantiteReserve'] < $data9['quantiteAlerteReserve'])
                                            {
                                                ?><span class="badge bg-red"><?php echo $data9['quantiteReserve']; ?></span><?php
                                            }
                                            else if ($data9['quantiteReserve'] == $data9['quantiteAlerteReserve'])
                                            {
                                                ?><span class="badge bg-orange"><?php echo $data9['quantiteReserve']; ?></span><?php
                                            }
                                            else
                                            {
                                                ?><span class="badge bg-green"><?php echo $data9['quantiteReserve']; ?></span><?php
                                            }
                                            ?>
                                        </td>
                                        <td><?php
                                            if ($data9['peremptionReserve'] <= date('Y-m-d'))
                                            {
                                                ?><span class="badge bg-red"><?php echo $data9['peremptionReserve']; ?></span><?php
                                            }
                                            else if ($data9['peremptionNotificationReserve'] <= date('Y-m-d'))
                                            {
                                                ?><span class="badge bg-orange"><?php echo $data9['peremptionReserve']; ?></span><?php
                                            }
                                            else
                                            {
                                                ?><span class="badge bg-green"><?php echo $data9['peremptionReserve']; ?></span><?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($_SESSION['reserve_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                                <a href="reserveMaterielForm.php?id=<?=$data9['idReserveElement']?>" class="btn btn-xs btn-warning modal-form"><i class="fa fa-pencil"></i></a>
                                            <?php }?>
                                            <?php if ($_SESSION['reserve_suppression']==1 AND $data['inventaireEnCours']==Null) {?>
                                                <a href="modalDeleteConfirm.php?case=reserveMaterielDelete&id=<?=$data9['idReserveElement']?>" class="btn btn-xs btn-danger modal-form"><i class="fa fa-trash"></i></a>
                                            <?php }?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>


                            </table>

                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                
                <div class="col-md-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Inventaires</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <?php if ($_SESSION['reserve_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                <a href="reserveInventaireNew.php?id=<?php echo $_GET['id']; ?>" class="btn btn-app spinnerAttenteClick">
                                    <i class="fa fa-hand-paper-o"></i> Nouvel inventaire traditionnel
                                </a>
                                <a href="reserveInventaireCBNew.php?id=<?php echo $_GET['id']; ?>" class="btn btn-app spinnerAttenteClick">
                                    <i class="fa fa-barcode"></i> Nouvel inventaire - Scanner à la chaine
                                </a>
                                <br/><br/>
                            <?php }?>

                            <?php if ($_SESSION['reserve_modification']==1 AND $data['inventaireEnCours']==1) {?>
                                <a data-toggle="modal" data-target="#modalSuppressionLockUnite" class="btn btn-sm btn-danger">Désactiver le verrouillage</a>
                                <br/><br/>
                            <?php } ?>

                            <br/>
                            <table id="tri2R" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Date de l'inventaire</th>
                                    <th>Personne ayant réalisé l'inventaire</th>
                                    <th>Commentaires</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query2 = $db->prepare('SELECT * FROM RESERVES_INVENTAIRES i LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne WHERE idConteneur = :idConteneur;');
                                    $query2->execute(array(
                                        'idConteneur' => $_GET['id']
                                    ));
                                    while ($data2 = $query2->fetch()) {
                                        ?>
                                        <tr>
                                            <td><?php echo $data2['idReserveInventaire']; ?></td>
                                            <td><?php echo $data2['dateInventaire']; ?></td>
                                            <td><?php echo $data2['identifiant']; ?></td>
                                            <td><?php echo nl2br($data2['commentairesInventaire']); ?></td>
                                            <td>
                                                <a href="reserveInventaireShow.php?id=<?=$data2['idReserveInventaire']?>" class="btn btn-xs btn-info"><i class="fa fa-folder-open"></i></a>
                                                <?php if ($_SESSION['reserve_modification']==1 AND $data['inventaireEnCours']==Null) {?>
                                                    <a href="modalDeleteConfirm.php?case=reserveInventaireDelete&id=<?=$data2['idReserveInventaire']?>" class="btn btn-xs btn-danger modal-form"><i class="fa fa-trash"></i></a>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <?php
                                    }
                                    ?>
                                </tbody>


                            </table>
                        </div>
                    </div>
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


<div class="modal fade modal-danger" id="modalSuppressionLockUnite">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Inventaires en cours</h4>
            </div>
            <div class="modal-body">
                L'inventaire de ce conteneur est en cours. En conséquent, plusieurs fonctionnalités du site sont verouillées en lecture seule. Le verrouillage s'enlèvera automatiquement dès que tous les inventaires en cours seront validés. Si ce verrouillage est à tors, vous pouvez forcer le déverrouillage manuellement ici.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                <a href="reserveInventaireNewAbort.php?id=<?=$_GET['id']?>"><button type="button" class="btn btn-default pull-right">Forcer le déverrouillage</button></a>
            </div>
        </div>
    </div>
</div>

</html>



