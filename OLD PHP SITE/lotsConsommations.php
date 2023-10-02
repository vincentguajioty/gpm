<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 107;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['consommationLots_lecture']==0)
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
                Matériels et consommables utilisés ces 6 derniers mois
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Consomamtions</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <?php if($CONSOMMATION_BENEVOLES != 1){ ?>
                <div class="alert alert-warning">
                    <i class="icon fa fa-warning"></i> La fonctionnalité est désactivée dans la configuration générale du site. Les bénévoles ne peuvent pas saisir de déclaration de consommation de matériel !
                </div>
            <?php } ?>

            <div class="row">

                <?php
                $query = $db->query('
                    SELECT
                        COUNT(*) as nb
                    FROM
                        LOTS_CONSOMMATION
                    WHERE
                        dateConsommation > DATE_SUB(now(), INTERVAL 6 MONTH)
                ;');
                $data = $query->fetch();
                if($data['nb']==0)
                {
                    echo "<center><i>Aucun rapport enregistré au cours de ces 6 derniers mois</i></center>";
                }

                $query = $db->query('
                    SELECT
                        *
                    FROM
                        LOTS_CONSOMMATION
                    WHERE
                        dateConsommation > DATE_SUB(now(), INTERVAL 6 MONTH)
                    ORDER BY
                        dateConsommation DESC
                ;');
                while ($data = $query->fetch())
                { ?>
                    
                    <?php
                        $query2 = $db->prepare('
                            SELECT
                                MAX(CASE
                                    WHEN conso.idConteneur IS NOT NULL AND r.dateDernierInventaire <= c.dateConsommation THEN true
                                    WHEN conso.idConteneur IS NULL AND l.dateDernierInventaire <= c.dateConsommation THEN true
                                    ELSE false
                                END) as alerte
                            FROM
                                LOTS_CONSOMMATION c
                                LEFT OUTER JOIN LOTS_CONSOMMATION_MATERIEL conso ON c.idConsommation = conso.idConsommation
                                LEFT OUTER JOIN LOTS_LOTS l ON conso.idLot = l.idLot
                                LEFT OUTER JOIN RESERVES_CONTENEUR r ON conso.idConteneur = r.idConteneur
                            WHERE
                                c.idConsommation = :idConsommation
                                AND
                                conso.traiteOperateur = 0
                        ');
                        $query2->execute(array('idConsommation' => $data['idConsommation']));
                        $alerte=$query2->fetch();
                        $alerte=$alerte['alerte'];
                    ?>
                    <div class="col-md-12">
                        <div class="box box-<?php if($alerte){echo "warning";}else{echo "success";} ?> collapsed-box">
                            <div class="box-header with-border">
                                <h3 class="box-title">
                                    <i class="fa fa-heartbeat"></i> <?= $data['dateConsommation'] ?> <?= $data['evenementConsommation'] ?> <?php if($alerte){echo '<span class="badge bg-orange">Action requise</span>';} ?>
                                </h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <b>Date:</b> <?= $data['dateConsommation'] ?>
                                    </div>
                                    <div class="col-md-4">
                                        <b>Déclarant:</b> <?= $data['nomDeclarantConsommation'] ?>
                                    </div>
                                    <div class="col-md-4">
                                        <b>Commentaires:</b> <?= nl2br($data['commentairesConsommation']) ?>
                                    </div>
                                    <br/><br/>
                                    <div class="col-md-12">
                                        <table class="table table-hover">
                                            <tr>
                                                <th>Lot</th>
                                                <th>Matériel</th>
                                                <th>Quantité</th>
                                                <th>Reconditionnement</th>
                                                <th>Actions</th>
                                            </tr>
                                            <?php
                                            $query2 = $db->prepare('
                                                SELECT
                                                    conso.idConsommationMateriel,
                                                    l.libelleLot,
                                                    c.libelleMateriel,
                                                    conso.idConteneur,
                                                    conso.quantiteConsommation,
                                                    conso.traiteOperateur,
                                                    r.libelleConteneur,
                                                    l.inventaireEnCours as inventaireLot,
                                                    r.inventaireEnCours as inventaireReserve,
                                                    l.dateDernierInventaire as dateInventaireLot,
                                                    r.dateDernierInventaire as dateInventaireReserve
                                                FROM
                                                    LOTS_CONSOMMATION_MATERIEL conso
                                                    LEFT OUTER JOIN LOTS_LOTS l ON conso.idLot = l.idLot
                                                    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON conso.idMaterielCatalogue = c.idMaterielCatalogue
                                                    LEFT OUTER JOIN RESERVES_CONTENEUR r ON conso.idConteneur = r.idConteneur
                                                WHERE
                                                    idConsommation = :idConsommation
                                            ;');
                                            $query2->execute(array('idConsommation' => $data['idConsommation']));
                                            while ($data2 = $query2->fetch())
                                            { ?>
                                                <tr>
                                                    <td><?= $data2['libelleLot'] ?></td>
                                                    <td><?= $data2['libelleMateriel'] ?></td>
                                                    <td><?= $data2['quantiteConsommation'] ?></td>
                                                    <td><?= $data2['libelleConteneur'] ?></td>
                                                    <td><?php
                                                        if($data2['traiteOperateur'])
                                                        {
                                                            echo '<span class="badge bg-green"><i class="fa fa-check"></i> Consommation comptabilisée</span><br/>';
                                                        }
                                                        elseif($_SESSION['consommationLots_affectation'])
                                                        {
                                                            if($data2['inventaireLot'] OR $data2['inventaireReserve'])
                                                            {
                                                                echo '<span class="badge bg-orange faa-flash animated">Inventaire en cours</span><br/>';
                                                            }
                                                            else
                                                            {
                                                                if(!(is_null($data2['idConteneur'])))
                                                                {
                                                                    if($data2['dateInventaireReserve']>$data['dateConsommation'])
                                                                    {
                                                                        echo '<span class="badge bg-green"><i class="fa fa-check"></i> La reserve a déjà été inventoriée depuis cette déclaration.</span><br/>';
                                                                    }
                                                                    else
                                                                    {
                                                                        echo '<a href="lotsConsommationsDecompteReserve.php?id='.$data2['idConsommationMateriel'].'" class="btn btn-xs btn-info" title="Décompter"><i class="fa fa-pencil"></i> Je mets à jour le stock de "'.$data2['libelleConteneur'].'".</a>';
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    if($data2['dateInventaireLot']>$data['dateConsommation'])
                                                                    {
                                                                        echo '<span class="badge bg-green"><i class="fa fa-check"></i> Le lot a déjà été inventorié depuis cette déclaration.</span><br/>';
                                                                    }
                                                                    else
                                                                    {
                                                                        echo '<a href="lotsConsommationsDecompteLot.php?id='.$data2['idConsommationMateriel'].'" class="btn btn-xs btn-info" title="Décompter"><i class="fa fa-pencil"></i> Je mets à jour l\'inventaire de "'.$data2['libelleLot'].'".</a>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    ?></td>
                                                </tr>
                                            <?php } ?>
                                            <div>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <?php
                                        if($_SESSION['consommationLots_supression'])
                                        { ?>
                                            <a href="modalDeleteConfirm.php?case=lotsConsommationsDelete&id=<?=$data['idConsommation']?>" class="btn btn-xs btn-danger modal-form" title="Supprimer"><i class="fa fa-trash"></i> Supprimer cette déclaration</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                $query->closeCursor(); ?>
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



