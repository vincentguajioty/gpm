<!DOCTYPE html>
<html>
<?php include('header.php'); require_once('config/config.php'); ?>
<?php
session_start();
$_SESSION['page'] = 506;
require_once('logCheck.php');

?>
<?php
if ($_SESSION['actionsMassives']==0)
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
                Interface de lancement d'actions massives en base de données
            </h1>
            <ol class="breadcrumb">
                <li><a href="index.php"><i class="fa fa-home"></i>Accueil</a></li>
                <li class="active">Actions massives</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <?php include('confirmationBox.php'); ?>

            <?php
            	if (isset($_POST['mdp']) AND $_POST['mdp']!='')
            	{
            		$user = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
            		$user->execute(array('idPersonne'=>$_SESSION['idPersonne']));
            		$user = $user->fetch();
            		if(password_verify($SELPRE.$_POST['mdp'].$SELPOST, $user['motDePasse']))
            		{
            			$_SESSION['actionsMassives_authent_ok'] = 1;
            			writeInLogs("Accès autorisé aux actions massives.", '1', NULL);
            		}
            		else
            		{
            			echo '<div class="alert alert-warning">';
				        echo '<i class="icon fa fa-warning"></i> Erreur dans la vérification de l\'utilisateur.';
				        echo '</div>';
				        writeInLogs("Erreur d'authentification de l'utilisateur pour accéder aux actions massives.", '2', NULL);
            		}
            	}
            ?>

            <?php
            	if ($_SESSION['actionsMassives_authent_ok'] != 1)
            	{ ?>
            		<div class="box box-danger">
            			<div class="box-header">
							<i class="fa fa-lock"></i>
            				<h3 class="box-title">Espace sécurisé - Merci de re-saisir votre mot de passe</h3>
            			</div>
	            		<form role="form" action="actionsMassives.php" method="POST">
	            			<div class="box-body">
	            				<div class="form-group">
		                            <label>Identifiant:</label>
		                            <input type="text" class="form-control" value="<?= $_SESSION['identifiant'] ?>" name="identifiant" disabled>
		                        </div>
		                        <div class="form-group">
		                            <label>Mot de passe:</label>
		                            <input type="password" class="form-control" name="mdp" requested>
		                        </div>
	            			</div>
	            			<div class="box-footer">
	            				<button type="submit" class="btn btn-primary pull-right">Accéder à la fonctionnalité</button>
	            			</div>
	            		</form>
	            	</div>
            	<?php }
            	else
            	{
            		unset($_SESSION['actionsMassives_authent_ok']);

            		?>
            		
            		<?php
            			$M11 = $db->query('SELECT COUNT(*) AS nb FROM MATERIEL_ELEMENT WHERE idEmplacement IS NULL;');
            			$M11 = $M11->fetch();
            			$M11 = $M11['nb'];
            			
            			$M12 = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT WHERE peremption Is Not Null OR peremptionNotification Is Not Null;');
            			$M12 = $M12->fetch();
            			$M12 = $M12['nb'];
            			
            			$M13 = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT WHERE quantiteAlerte != quantite - 1;');
            			$M13 = $M13->fetch();
            			$M13 = $M13['nb'];
            			
            			$M14 = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_ELEMENT WHERE peremptionNotification != DATE_SUB(peremption, INTERVAL 20 DAY);');
            			$M14 = $M14->fetch();
            			$M14 = $M14['nb'];
            			
            			$M21 = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_EMPLACEMENT WHERE idSac Is Null;');
            			$M21 = $M21->fetch();
            			$M21 = $M21['nb'];
            			
            			$M22 = $db->query('
            				SELECT
					    		COUNT(c.idEmplacement) as nb
					    	FROM
					    		(
					    		SELECT
					    			em.idEmplacement,
					    			COUNT(el.idElement) as nbMateriel
					    		FROM
					    			MATERIEL_EMPLACEMENT em
					    			LEFT OUTER JOIN MATERIEL_ELEMENT el ON em.idEmplacement = el.idEmplacement
					    		GROUP BY em.idEmplacement
					    		) c
					    	WHERE
					    		c.nbMateriel = 0
						;');
            			$M22 = $M22->fetch();
            			$M22 = $M22['nb'];
            			
            			$M31 = $db->query('SELECT COUNT(*) as nb FROM MATERIEL_SAC WHERE idLot Is Null;');
            			$M31 = $M31->fetch();
            			$M31 = $M31['nb'];
            			
            			$M32 = $db->query('
            				SELECT
					    		COUNT(c.idSac) as nb
					    	FROM
					    		(
					    		SELECT
					    			em.idSac,
					    			COUNT(el.idEmplacement) as nbEmplacement
					    		FROM
					    			MATERIEL_SAC em
					    			LEFT OUTER JOIN MATERIEL_EMPLACEMENT el ON em.idSac = el.idSac
					    		GROUP BY em.idSac
					    		) c
					    	WHERE
					    		c.nbEmplacement = 0
						;');
            			$M32 = $M32->fetch();
            			$M32 = $M32['nb'];
            			
            			$M33 = $db->query('
                            SELECT
                                COUNT(e.idElement) as nb
                            FROM
                                MATERIEL_SAC ms
                                LEFT OUTER JOIN MATERIEL_EMPLACEMENT me ON ms.idSac=me.idSac
                                LEFT OUTER JOIN MATERIEL_ELEMENT e ON me.idEmplacement = e.idEmplacement
                            WHERE
                                ms.idLot IS NULL
                        ;');
            			$M33 = $M33->fetch();
            			$M33 = $M33['nb'];
            			
            			$M41 = $db->query('
            				SELECT
					    		COUNT(c.idLot) as nb
					    	FROM
					    		(
					    		SELECT
					    			em.idLot,
					    			COUNT(el.idSac) as nbSacs
					    		FROM
					    			LOTS_LOTS em
					    			LEFT OUTER JOIN MATERIEL_SAC el ON em.idLot = el.idLot
					    		GROUP BY em.idLot
					    		) c
					    	WHERE
					    		c.nbSacs = 0
						;');
            			$M41 = $M41->fetch();
            			$M41 = $M41['nb'];
            			
            		?>
            		
            		<div class="box box-danger">
            			<div class="box-body">
            				<table class="table table-hover">
            					<tr>
            						<th>Ref</th>
            						<th>Catégorie</th>
            						<th>Action</th>
            						<th>Estimation d'entrées impactées</th>
            						<th>Lancement</th>
            					</tr>
            					<tr>
            						<td>11</td>
            						<td>Matériels</td>
            						<td>Supprimer tous les matériels de la section opérationnelle qui ne sont pas rattachés à aucun emplacement.</td>
            						<td><?= $M11 ?></td>
            						<td><a href="actionsMassives11.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>12</td>
            						<td>Matériels</td>
            						<td>Modifier tous les matériels de la section opérationnelle pour supprimer toutes les dates de péremption et les dates d'anticipation de péremption.</td>
            						<td><?= $M12 ?></td>
            						<td><a href="actionsMassives12.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>13</td>
            						<td>Matériels</td>
            						<td>Modifier tous les matériels de la section opérationnelle pour changer la quantité d'alerte avec la valeur: quantité actuelle - 1.</td>
            						<td><?= $M13 ?></td>
            						<td><a href="actionsMassives13.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>14</td>
            						<td>Matériels</td>
            						<td>Modifier tous les matériels de la section opérationnelle pour changer la date d'alerte avec la valeur: date de péremption - 20 jours.</td>
            						<td><?= $M14 ?></td>
            						<td><a href="actionsMassives14.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>21</td>
            						<td>Emplacements</td>
            						<td>Supprimer tous les emplacements qui n'ont pas de sac de rattachement.</td>
            						<td><?= $M21 ?></td>
            						<td><a href="actionsMassives21.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>22</td>
            						<td>Emplacements</td>
            						<td>Supprimer tous les emplacements qui ne contiennent pas de matériel.</td>
            						<td><?= $M22 ?></td>
            						<td><a href="actionsMassives22.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>31</td>
            						<td>Sacs</td>
            						<td>Supprimer tous les sacs qui n'ont pas de lot de rattachement.</td>
            						<td><?= $M31 ?></td>
            						<td><a href="actionsMassives31.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>32</td>
            						<td>Sacs</td>
            						<td>Supprimer tous les sacs qui ne contiennent pas d'emplacement.</td>
            						<td><?= $M32 ?></td>
            						<td><a href="actionsMassives32.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>33</td>
            						<td>Sacs</td>
            						<td>Supprimer tous les sacs et leurs emplacements et leur matériel dès lors que les sacs ne sont pas rattachés à un lot.</td>
            						<td><?= $M33 ?></td>
            						<td><a href="actionsMassives33.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            					<tr>
            						<td>41</td>
            						<td>Lots</td>
            						<td>Supprimer tous les lots qui ne contiennent pas de sac.</td>
            						<td><?= $M41 ?></td>
            						<td><a href="actionsMassives41.php" class="btn btn-xs btn-warning spinnerAttenteClick"><i class="fa fa-database"></i> Lancer l'action</a></td>
            					</tr>
            				</table>
            			</div>
            		</div>

            		
            	<?php }
            ?>

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
