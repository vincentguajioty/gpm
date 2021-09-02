<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1003;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['vehiculeHealth_lecture']==0)
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
                Suivi des tâches de maintenance régulières
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Maintenance</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">
            	

            	<div class="col-md-12">
		            <div class="box box-success">
		            	<div class="box-header with-border">
		                    <i class="fa fa-wrench"></i>
		                    <h3 class="box-title">Synthèse par véhicules</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                    <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Véhicule</th>
                                        <?php
                                            $maintenances = $db->query('SELECT * FROM VEHICULES_HEALTH_TYPES WHERE affichageSynthese = 1 ORDER BY libelleHealthType');
                                            while($maintenance = $maintenances->fetch())
                                            {
                                                echo '<th>'.$maintenance['libelleHealthType'].'</th>';
                                            }
                                        ?>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = $db->query('SELECT * FROM VEHICULES WHERE affichageSyntheseHealth = 1 ORDER BY libelleVehicule;');
                                while ($data = $query->fetch())
                                {?>
                                    <tr>
                                        <td><?php echo $data['idVehicule']; ?></td>
                                        <td><?php echo $data['libelleVehicule']; ?></td>
                                        <?php
                                            $maintenances = $db->prepare('
                                                SELECT
                                                    t.*,
                                                    veh.frequenceHealth,
                                                    MAX(cal.dateHealth) as dateHealth
                                                FROM
                                                    VEHICULES_HEALTH_TYPES t
                                                    LEFT OUTER JOIN (SELECT * FROM VEHICULES_HEALTH_ALERTES WHERE idVehicule = :idVehicule)veh ON t.idHealthType = veh.idHealthType
                                                    LEFT OUTER JOIN (SELECT c.*, h.dateHealth FROM VEHICULES_HEALTH_CHECKS c LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth WHERE h.idVehicule = :idVehicule)cal ON t.idHealthType = cal.idHealthType
                                                WHERE
                                                    t.affichageSynthese = 1
                                                GROUP BY
                                                    t.idHealthType
                                                ORDER BY
                                                    libelleHealthType
                                            ');
                                            $maintenances->execute(array('idVehicule'=>$data['idVehicule']));
                                            while($maintenance = $maintenances->fetch())
                                            {
                                                if($maintenance['frequenceHealth'] > 0)
                                                {
                                                    echo '<td>';
                                                    if($maintenance['dateHealth'] == Null)
                                                    {
                                                        echo '<span class="badge bg-red">Aucune maintenance enregistrée</span>';
                                                    }
                                                    else
                                                    {
                                                        if(date('Y-m-d', strtotime($maintenance['dateHealth']. ' + '.$maintenance['frequenceHealth'].' days')) <= date('Y-m-d'))
                                                        {
                                                            echo '<span class="badge bg-red">Dernièrement: '.$maintenance['dateHealth'].'</span>';
                                                        }
                                                        else
                                                        {
                                                            echo '<span class="badge bg-green">Dernièrement: '.$maintenance['dateHealth'].'</span>';
                                                        }
                                                    }
                                                    echo '<br/>(Fréquence: '.$maintenance['frequenceHealth'].' jours)</td>';
                                                }
                                                else
                                                {
                                                    if($maintenance['dateHealth'] != Null)
                                                    {
                                                        echo '<td>Dernièrement: '.$maintenance['dateHealth'].'</td>';
                                                    }
                                                    else
                                                    {
                                                        echo '<td></td>';
                                                    }
                                                }
                                                
                                            }
                                        ?>
                                        <td>
                                            <?php if($_SESSION['vehiculeHealth_ajout']==1){ ?><a href="vehiculesHealthForm.php?idVehicule=<?=$data['idVehicule']?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?>
                                            <?php if ($_SESSION['vehicules_lecture']==1) {?>
                                                <a href="vehiculesContenu.php?id=<?=$data['idVehicule']?>" class="btn btn-xs btn-info" title="Ouvrir"><i class="fa fa-folder-open"></i></a>
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
		        </div>

				<div class="col-md-8">
		            <div class="box box-success">
		            	<div class="box-header with-border">
		                    <i class="fa fa-calendar"></i>
		                    <h3 class="box-title">Calendrier des Maintenances</h3>
		                </div>
		            	<div class="box-body">
		            		<?php
		            			$events = [];
		                        
		                        $query = $db->query('
                                    SELECT
                                        *
                                    FROM
                                        VEHICULES_HEALTH d
                                        LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
                                    WHERE
                                        v.affichageSyntheseHealth = 1
                                ;');
                                while ($data = $query->fetch())
                                {
                                    $events[] = [
                                        'date'     => date_format(date_create($data['dateHealth']), 'Y-m-d'),
                                        'title'    => 'Maintenance faite',
                                        'subTitle' => $data['libelleVehicule'],
                                        'color'    => '#00a65a',
                                        'url'      => 'indexModalCalendrier.php?case=vehiculesMaintenanceFaite&id='.$data['idVehiculeHealth'],
                                    ];
                                }
                                
                                
                                $query = $db->query('
                                    SELECT
                                    	*
                                    FROM
                                    	VEHICULES_MAINTENANCE m
                                    	LEFT OUTER JOIN VEHICULES v on m.idVehicule = v.idVehicule
                                ;');
                                while ($data = $query->fetch())
                                {
                                    $events[] = [
                                        'date'     => date_format(date_create($data['dateMaintenance']), 'Y-m-d'),
                                        'title'    => 'Maintenance ponctuelle',
                                        'subTitle' => $data['libelleVehicule'],
                                        'color'    => '#3c8dbc',
                                        'url'      => 'indexModalCalendrier.php?case=vehiculesMaintenance&id='.$data['idMaintenance'],
                                    ];
                                }


                                $query = $db->query('
                                    SELECT
                                        a.*,
                                        v.libelleVehicule,
                                        t.libelleHealthType,
                                        MAX(cal.dateHealth) as dateHealth,
                                        DATE_ADD(MAX(cal.dateHealth) , INTERVAL a.frequenceHealth DAY) as nextHealth
                                    FROM
                                        VEHICULES_HEALTH_ALERTES a
                                        LEFT OUTER JOIN VEHICULES v ON a.idVehicule=v.idVehicule
                                        LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON a.idHealthType = t.idHealthType
                                        LEFT OUTER JOIN (SELECT c.*, h.dateHealth, h.idVehicule FROM VEHICULES_HEALTH_CHECKS c LEFT OUTER JOIN VEHICULES_HEALTH h ON c.idVehiculeHealth = h.idVehiculeHealth)cal ON a.idHealthType = cal.idHealthType AND cal.idVehicule = a.idVehicule
                                    GROUP BY
                                        a. idHealthAlerte
                                ;');
                                while ($data = $query->fetch())
                                {
                                    $events[] = [
                                        'date'     => date_format(date_create($data['nextHealth']), 'Y-m-d'),
                                        'title'    => $data['libelleVehicule'],
                                        'subTitle' => $data['libelleHealthType'],
                                        'color'    => '#f39c12',
                                        'url'      => 'indexModalCalendrier.php?case=vehiculesMaintenanceAFaire&id='.$data['idHealthAlerte'],
                                    ];
                                }

		                        
	            			?>
	                        <div id="calendar"></div>
		            	</div>
		            </div>
		        </div>

		        <div class="col-md-4">
		        	<div class="box box-success">
		            	<div class="box-header with-border">
		                    <i class="fa fa-hourglass-2"></i>
		                    <h3 class="box-title">Historique des 3 derniers mois</h3>
		                </div>
		                <!-- /.box-header -->
		                <div class="box-body">
		                    <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Véhicule</th>
                                        <th>Taches</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = $db->query('
                                    SELECT
                                        d.idVehiculeHealth,
                                        v.libelleVehicule,
                                        d.dateHealth
                                    FROM
                                        VEHICULES_HEALTH d
                                        LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
                                    WHERE
                                        dateHealth > NOW() - INTERVAL 3 MONTH
                                        AND
                                        v.affichageSyntheseHealth = 1
                                    ORDER BY
                                        dateHealth DESC;');
                                while ($data = $query->fetch())
                                {?>
                                    <tr>
                                        <td><?= $data['libelleVehicule'] ?></td>
                                        <td>
                                            <?php
                                                $taches = $db->prepare('SELECT * FROM VEHICULES_HEALTH_CHECKS c LEFT OUTER JOIN VEHICULES_HEALTH_TYPES t ON c.idHealthType = t.idHealthType WHERE c.idVehiculeHealth = :idVehiculeHealth;');
                                                $taches->execute(array('idVehiculeHealth'=>$data['idVehiculeHealth']));
                                                while($tache = $taches->fetch())
                                                {
                                                    echo $tache['libelleHealthType'].'<br/>';
                                                }
                                            ?>
                                        </td>
                                        <td><?= $data['dateHealth'] ?></td>
                                    </tr>
                                    <?php
                                }
                                $query->closeCursor(); ?>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="plugins/fullcalendar/fr.js"></script>

<script>
    $(function () {
        var events = <?= json_encode($events) ?>;

        var calendarEvents = [];
        $.each(events, function () {
            var dateParts = this.date.split('-');

            calendarEvents.push({
                title: this.title + ' : ' + this.subTitle,
                start: new Date(dateParts[0], parseInt(dateParts[1]) - 1, dateParts[2]),
                backgroundColor: this.color,
                borderColor: this.color,
                allDay: true,
                url: this.url,
            })
        });

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            lang: 'fr',

            eventRender: function(event, element) {
			    if ($(element).is('a')) {
				    $(element).addClass('modal-form');
				}
			},

            events: calendarEvents
        });
    });
</script>
</body>
</html>
