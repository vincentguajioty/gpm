<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 1002;
require_once('logCheck.php');
?>
<?php
if ($_SESSION['desinfections_lecture']==0)
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
                Suivi des désinfections
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Désinfections</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>
            <div class="row">
            	

            	<div class="col-md-12">
		            <div class="box box-success">
		            	<div class="box-header with-border">
		                    <i class="fa fa-recycle"></i>
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
		                                	$desinfections = $db->query('SELECT * FROM VEHICULES_DESINFECTIONS_TYPES WHERE affichageSynthese = 1 ORDER BY libelleVehiculesDesinfectionsType');
		                                	while($desinfection = $desinfections->fetch())
		                                	{
		                                		echo '<th>'.$desinfection['libelleVehiculesDesinfectionsType'].'</th>';
		                                	}
		                                ?>
		                                <th>Actions</th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        <?php
		                        $query = $db->query('SELECT * FROM VEHICULES WHERE affichageSyntheseDesinfections = 1 ORDER BY libelleVehicule;');
		                        while ($data = $query->fetch())
		                        {?>
		                            <tr>
		                                <td><?php echo $data['idVehicule']; ?></td>
		                                <td><?php echo $data['libelleVehicule']; ?></td>
		                                <?php
		                                	$desinfections = $db->prepare('
		                                		SELECT
		                                			t.*,
		                                			veh.frequenceDesinfection,
		                                			MAX(cal.dateDesinfection) as dateDesinfection
		                                		FROM
		                                			VEHICULES_DESINFECTIONS_TYPES t
		                                			LEFT OUTER JOIN (SELECT * FROM VEHICULES_DESINFECTIONS_ALERTES WHERE idVehicule = :idVehicule)veh ON t.idVehiculesDesinfectionsType = veh.idVehiculesDesinfectionsType
		                                			LEFT OUTER JOIN (SELECT * FROM VEHICULES_DESINFECTIONS WHERE idVehicule = :idVehicule)cal ON t.idVehiculesDesinfectionsType = cal.idVehiculesDesinfectionsType
		                                		WHERE
		                                			t.affichageSynthese = 1
		                                		GROUP BY
		                                			t.idVehiculesDesinfectionsType
		                                		ORDER BY
		                                			libelleVehiculesDesinfectionsType
		                                	');
		                                	$desinfections->execute(array('idVehicule'=>$data['idVehicule']));
		                                	while($desinfection = $desinfections->fetch())
		                                	{
		                                		if($desinfection['frequenceDesinfection'] > 0)
		                                		{
		                                			echo '<td>';
		                                			if($desinfection['dateDesinfection'] == Null)
		                                			{
		                                				echo '<span class="badge bg-red">Aucune désinfection enregistrée</span>';
		                                			}
		                                			else
		                                			{
		                                				if(date('Y-m-d', strtotime($desinfection['dateDesinfection']. ' + '.$desinfection['frequenceDesinfection'].' days')) <= date('Y-m-d'))
			                                			{
			                                				echo '<span class="badge bg-red">Dernièrement: '.$desinfection['dateDesinfection'].'</span>';
			                                			}
			                                			else
			                                			{
			                                				echo '<span class="badge bg-green">Dernièrement: '.$desinfection['dateDesinfection'].'</span>';
			                                			}
		                                			}
		                                			echo '<br/>(Fréquence: '.$desinfection['frequenceDesinfection'].' jours)</td>';
		                                		}
		                                		else
		                                		{
		                                			if($desinfection['dateDesinfection'] != Null)
		                                			{
		                                				echo '<td>Dernièrement: '.$desinfection['dateDesinfection'].'</td>';
		                                			}
		                                			else
		                                			{
		                                				echo '<td></td>';
		                                			}
		                                		}
		                                		
		                                	}
		                                ?>
		                                <td>
		                                    <?php if($_SESSION['desinfections_ajout']==1){ ?><a href="vehiculesDesinfectionsForm.php?idVehicule=<?=$data['idVehicule']?>" class="btn btn-xs btn-success modal-form" title="Ajouter"><i class="fa fa-plus"></i></a><?php } ?>
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
		                    <h3 class="box-title">Calendrier des désinfections</h3>
		                </div>
		            	<div class="box-body">
		            		<?php
		            			$events = [];
		                        
		                        $query = $db->query('
		                        	SELECT
                                        *
                                    FROM
                                        VEHICULES_DESINFECTIONS d
                                        LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
                                        LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON d.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType
                                    WHERE
                                    	v.affichageSyntheseDesinfections = 1
                                    	AND
                                    	t.affichageSynthese = 1
		                        ;');
		                        while ($data = $query->fetch())
		                        {
		                        	$events[] = [
	                                    'date'     => date_format(date_create($data['dateDesinfection']), 'Y-m-d'),
	                                    'title'    => 'Désinfection faite',
	                                    'subTitle' => $data['libelleVehicule'],
	                                    'color'    => '#00a65a',
	                                    'url'      => 'indexModalCalendrier.php?case=vehiculesDesinfectionFaite&id='.$data['idVehiculesDesinfection'],
	                                ];
		                        }
		                        
		                        $query = $db->query('
		                        	SELECT
                                        a.*,
                                        vv.libelleVehicule,
                                        t.libelleVehiculesDesinfectionsType,
                                        MAX(v.dateDesinfection) as dateDesinfection
                                    FROM
                                        VEHICULES_DESINFECTIONS_ALERTES a
                                        LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON a.idVehiculesDesinfectionsType=t.idVehiculesDesinfectionsType
                                        LEFT OUTER JOIN VEHICULES_DESINFECTIONS v ON a.idVehiculesDesinfectionsType = v.idVehiculesDesinfectionsType AND a.idVehicule=v.idVehicule
                                        LEFT OUTER JOIN VEHICULES vv ON a.idVehicule = vv.idVehicule
                                    GROUP BY
                                        a.idDesinfectionsAlerte
		                        ;');
		                        while ($data = $query->fetch())
		                        {
		                        	$events[] = [
	                                    'date'     => date('Y-m-d', strtotime($data['dateDesinfection']. ' + '.$data['frequenceDesinfection'].' days')),
	                                    'title'    => 'Désinfection à faire',
	                                    'subTitle' => $data['libelleVehicule'],
	                                    'color'    => '#f39c12',
	                                    'url'      => 'indexModalCalendrier.php?case=vehiculesDesinfectionAFaire&id='.$data['idDesinfectionsAlerte'],
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
		                                <th>Désinfection</th>
		                                <th>Date</th>
		                            </tr>
		                        </thead>
		                        <tbody>
		                        <?php
		                        $query = $db->query('
		                        	SELECT
		                        		v.libelleVehicule,
		                        		t.libelleVehiculesDesinfectionsType,
		                        		d.dateDesinfection
		                        	FROM
		                        		VEHICULES_DESINFECTIONS d
		                        		LEFT OUTER JOIN VEHICULES_DESINFECTIONS_TYPES t ON d.idVehiculesDesinfectionsType = t.idVehiculesDesinfectionsType
		                        		LEFT OUTER JOIN VEHICULES v ON d.idVehicule = v.idVehicule
		                        	WHERE
		                        		dateDesinfection > NOW() - INTERVAL 3 MONTH
		                        		AND
		                        		t.affichageSynthese = 1
		                        		AND
		                        		v.affichageSyntheseDesinfections = 1
		                        	ORDER BY
		                        		dateDesinfection DESC;');
		                        while ($data = $query->fetch())
		                        {?>
		                            <tr>
		                                <td><?= $data['libelleVehicule'] ?></td>
										<td><?= $data['libelleVehiculesDesinfectionsType'] ?></td>
		                                <td><?= $data['dateDesinfection'] ?></td>
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
