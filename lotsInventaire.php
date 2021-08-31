<?php
session_start();
$_SESSION['page'] = 101;
require_once('logCheck.php');
require_once 'config/bdd.php';
include('header.php');
include('scripts.php');

$query = $db->prepare('SELECT * FROM LOTS_LOTS l LEFT OUTER JOIN LOTS_TYPES t ON l.idTypeLot = t.idTypeLot LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE idLot = :idLot;');
$query->execute(array(
    'idLot' => $_GET['id']
));
$data = $query->fetch();
$query2 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_SAC WHERE idLot = :idLot;');
$query2->execute(array(
    'idLot' => $_GET['id']
));
$data2 = $query2->fetch();
$query3 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_EMPLACEMENT e LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE idLot = :idLot;');
$query3->execute(array(
    'idLot' => $_GET['id']
));
$data3 = $query3->fetch();
$query4 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement = e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac WHERE idLot = :idLot;');
$query4->execute(array(
    'idLot' => $_GET['id']
));
$data4 = $query4->fetch();
$query5 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE s.idLot = :idLot AND (peremption < CURRENT_DATE OR peremption = CURRENT_DATE);');
$query5->execute(array(
    'idLot' => $_GET['id']
));
$data5 = $query5->fetch();
$query6 = $db->prepare('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue WHERE s.idLot = :idLot AND (quantite < quantiteAlerte OR quantite = quantiteAlerte);');
$query6->execute(array(
    'idLot' => $_GET['id']
));
$data6 = $query6->fetch();
?>

<section class="content">
    <div class="row">
        <div class="col-md-6">
            <!-- Widget: user widget style 1 -->
            <div class="box box-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header bg-blue">
                    <!-- /.widget-user-image -->
                    <h3 class="widget-user-username"><?php echo $data['libelleLot']; ?></h3>
                </div>
                <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                        <li><a>Référentiel <span class="pull-right"><?php echo $data['libelleTypeLot']; ?></span></a></li>
                        <li><a>Conformité au référentiel<?php
                                if (checkLotsConf($_GET['id']))
                                {
                                    echo '<span class="pull-right">Lot non-conforme</span>';
                                }
                                else
                                {
                                    echo '<span class="pull-right">Lot conforme</span>';
                                }
                                ?></span></a></li>
                        <li><a>Nombre de sacs <span class="pull-right"><?php echo $data2['nb']; ?></span></a></li>
                        <li><a>Nombre d'emplacements <span class="pull-right"><?php echo $data3['nb']; ?></span></a></li>
                        <li><a>Quantite de matériel <span class="pull-right"><?php echo $data4['nb']; ?></span></a></li>
                        <li><a>Elements périmés<?php
                                if ($data5['nb']>0)
                                {
                                    echo '<span class="pull-right">' . $data5['nb'] .'</span>';
                                }
                                else
                                {
                                    echo '<span class="pull-right">' . $data5['nb'] .'</span>';
                                }
                                ?></span></a></li>
                        <li><a>Matériel manquant<?php
                                if ($data6['nb']>0)
                                {
                                    echo '<span class="pull-right">' . $data6['nb'] .'</span>';
                                }
                                else
                                {
                                    echo '<span class="pull-right">' . $data6['nb'] .'</span>';
                                }
                                ?></span></a></li>
                        <li><a>Personne responsable <span class="pull-right"><?php echo $data['identifiant']; ?></span></a></li>
                        <li><a>Dernier inventaire <span class="pull-right"><?php echo $data['dateDernierInventaire']; ?></span></a></li>
                        <li><a>Fréquence d'inventaire <span class="pull-right"><?php echo $data['frequenceInventaire']; ?></span></a></li>
                        <li><a>Prochain inventaire<span class="pull-right"><?php echo date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')); ?></span></a></li>
                    </ul>
                </div>
            </div>
            <!-- /.widget-user -->
        </div>
        <p style="page-break-before:always">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Sac</th>
                <th>Emplacement</th>
                <th>Matériel</th>
                <th>Quantité</th>
                <th>Péremption</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE s.idLot = :idLot ORDER BY s.libelleSac, e.libelleEmplacement, c.libelleMateriel;');
            $query->execute(array(
                'idLot' => $_GET['id']
            ));
            while ($data = $query->fetch())
            {?>
                <tr>
                    <td><?php echo $data['libelleSac']; ?></td>
                    <td><?php echo $data['libelleEmplacement']; ?></td>
                    <td><?php echo $data['libelleMateriel']; ?></td>
                    <td><?php echo $data['quantite']; ?></span></td>
                    <td><?php echo $data['peremption']; ?></td>
                </tr>
                <?php
            }
            $query->closeCursor(); ?>
            </tbody>
        </table>
    </div>
</section>

<script type="text/javascript">
    window.print() ;
</script>