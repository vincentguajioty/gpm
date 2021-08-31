<?php
require_once('logCheck.php');
require_once 'config/bdd.php';
//include('header.php');
//include('scripts.php');
//require_once 'plugins/dompdf/autoload.inc.php';


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


$html = '
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <!-- Widget: user widget style 1 -->
            <div class="box box-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header bg-blue">
                    <!-- /.widget-user-image -->
                    <h3 class="widget-user-username">'.$data['libelleLot'].'</h3>
                </div>
                <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                        <li><a>Référentiel <span class="pull-right">'.$data['libelleTypeLot'].'</span></a></li>
                        <li><a>Conformité au référentiel';
	                            if ($data['libelleTypeLot'] == Null)
	                            {
	                                $html .= '<span class="pull-right">NA</span>';
	                            }
	                            else
	                            {
	                                if ($data['alerteConfRef'] == 1)
	                                {
	                                    $html .= '<span class="pull-right">Lot non-conforme</span>';
	                                }
	                                else
	                                {
	                                    $html .= '<span class="pull-right">Lot conforme</span>';
	                                }
	                            }
	                            $html .= '</a>
                        </li>
                        <li><a>Nombre de sacs <span class="pull-right">'.$data2['nb'].'</span></a></li>
                        <li><a>Nombre d\'emplacements <span class="pull-right">'.$data3['nb'].'</span></a></li>
                        <li><a>Quantite de matériel <span class="pull-right">'.$data4['nb'].'</span></a></li>
                        <li><a>Elements périmés';
                                if ($data5['nb']>0)
                                {
                                    $html .= '<span class="pull-right">' . $data5['nb'] .'</span>';
                                }
                                else
                                {
                                    $html .= '<span class="pull-right">' . $data5['nb'] .'</span>';
                                }
                                $html .= '</span></a></li>
                        <li><a>Matériel manquant';
                                if ($data6['nb']>0)
                                {
                                    $html .= '<span class="pull-right">' . $data6['nb'] .'</span>';
                                }
                                else
                                {
                                    $html .= '<span class="pull-right">' . $data6['nb'] .'</span>';
                                }
                                $html .= '</span></a></li>
                        <li><a>Personne responsable <span class="pull-right">'.$data['identifiant'].'</span></a></li>
                        <li><a>Dernier inventaire <span class="pull-right">'.$data['dateDernierInventaire'].'</span></a></li>
                        <li><a>Fréquence d\'inventaire <span class="pull-right">'.$data['frequenceInventaire'].'</span></a></li>
                        <li><a>Prochain inventaire<span class="pull-right">'.date('Y-m-d', strtotime($data['dateDernierInventaire'] . ' +' . $data['frequenceInventaire'] . ' days')).'</span></a></li>
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
            <tbody>';
            
            $query = $db->prepare('SELECT * FROM MATERIEL_ELEMENT m LEFT OUTER JOIN MATERIEL_EMPLACEMENT e ON m.idEmplacement=e.idEmplacement LEFT OUTER JOIN MATERIEL_SAC s ON e.idSac = s.idSac LEFT OUTER JOIN LOTS_LOTS l ON s.idLot = l.idLot LEFT OUTER JOIN MATERIEL_CATALOGUE c ON m.idMaterielCatalogue = c.idMaterielCatalogue LEFT OUTER JOIN PERSONNE_REFERENTE p ON l.idPersonne = p.idPersonne WHERE s.idLot = :idLot ORDER BY s.libelleSac, e.libelleEmplacement, c.libelleMateriel;');
            $query->execute(array(
                'idLot' => $_GET['id']
            ));
            while ($data = $query->fetch())
            {
                $html .= '<tr>
                    <td>'.$data['libelleSac'].'</td>
                    <td>'.$data['libelleEmplacement'].'</td>
                    <td>'.$data['libelleMateriel'].'</td>
                    <td>'.$data['quantite'].'</span></td>
                    <td>'.$data['peremption'].'</td>
                </tr>';
            }
            $query->closeCursor();
            $html .= '</tbody>
        </table>
    </div>
</section>';


require_once 'plugins/dompdf/lib/html5lib/Parser.php';
require_once 'plugins/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'plugins/dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'plugins/dompdf/src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
//$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();