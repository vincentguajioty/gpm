<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['vehicules_lecture']==1 OR $_SESSION['vehicules_ajout']==1 OR $_SESSION['vehicules_modification']==1 OR $_SESSION['vehicules_suppression']==1)
{ ?>
    
    <?php
    	if (isset($_GET['id']))
		{
		    $query = $db->prepare('SELECT * FROM VEHICULES WHERE idVehicule=:idVehicule;');
		    $query->execute(array('idVehicule' => $_GET['id']));
		    $data = $query->fetch();
		    $query->closeCursor();
		}
    ?>
    
    
    <div class="modal fade" id="modalVehiculesAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un véhicule</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'vehiculesUpdate.php?id='.$_GET['id'] : 'vehiculesAdd.php'?>" method="POST">
                    <div class="modal-body">
                    	<div class="row">
                    		<div class="col-lg-4 col-xs-12">
		                        <div class="form-group">
		                            <label>Libellé/Indicatif: <small style="color:grey;"> Requis</small></label>
		                            <input type="text" class="form-control" value="<?= isset($data['libelleVehicule']) ? $data['libelleVehicule'] : ''?>" name="libelleVehicule" required>
		                        </div>
		                    </div>
		                    <div class="col-lg-4 col-xs-12">
								<div class="form-group">
		                            <label>Immatriculation: <small style="color:grey;"> Requis</small></label>
		                            <input type="text" class="form-control" value="<?= isset($data['immatriculation']) ? $data['immatriculation'] : ''?>" name="immatriculation" required>
		                        </div>
		                    </div>
		                    <div class="col-lg-4 col-xs-12">
			                    <div class="form-group">
		                            <label>Type:</label>
		                            <select class="form-control select2" style="width: 100%;" name="idVehiculesType">
		                                <option value="">--- Pas de Type ---</option>
										<?php
		                                $query2 = $db->query('SELECT * FROM VEHICULES_TYPES ORDER BY libelleType;');
		                                while ($data2 = $query2->fetch())
		                                {
		                                    ?>
		                                    <option value ="<?php echo $data2['idVehiculesType']; ?>" <?php if (isset($data['idVehiculesType']) AND ($data2['idVehiculesType'] == $data['idVehiculesType'])) { echo 'selected'; } ?> ><?php echo $data2['libelleType']; ?></option>
		                                    <?php
		                                }
		                                $query2->closeCursor(); ?>
		                            </select>
		                        </div>
		                    </div>
                        </div>
                        <div class="row">
                        	<div class="col-lg-6 col-xs-12">
								<div class="form-group">
		                            <label>Marque/Modèle:</label>
		                            <input type="text" class="form-control" value="<?= isset($data['marqueModele']) ? $data['marqueModele'] : ''?>" name="marqueModele">
		                        </div>
		                    </div>
		                    <div class="col-lg-6 col-xs-12">
								<div class="form-group">
		                            <label>Nombre de places:</label>
		                            <input type="number" class="form-control" value="<?= isset($data['nbPlaces']) ? $data['nbPlaces'] : ''?>" name="nbPlaces">
		                        </div>
		                    </div>
		                </div>
		                <div class="row">
		                    <div class="col-lg-6 col-xs-12">
		                        <div class="form-group">
		                            <label>Dimensions:</label>
		                            <input type="text" class="form-control" value="<?= isset($data['dimensions']) ? $data['dimensions'] : ''?>" name="dimensions">
		                        </div>
		                    </div>
		                    <div class="col-lg-6 col-xs-12">
		                        <div class="form-group">
		                            <label>Poids (tonnes):</label>
		                            <input type="number" step="0.01" min="0" class="form-control" value="<?= isset($data['poidsVehicule']) ? $data['poidsVehicule'] : ''?>" name="poidsVehicule">
		                        </div>
		                    </div>
		                </div>
                        <div class="row">
                        	<div class="col-lg-6 col-xs-12">
		                        <div class="form-group">
		                            <label>Etat: <small style="color:grey;">Requis</small></label>
		                            <select class="form-control select2" style="width: 100%;" name="idVehiculesEtat">
		                                <?php
		                                $query2 = $db->query('SELECT * FROM VEHICULES_ETATS ORDER BY libelleVehiculesEtat;');
		                                while ($data2 = $query2->fetch())
		                                {
		                                    ?>
		                                    <option value ="<?php echo $data2['idVehiculesEtat']; ?>" <?php if (isset($data['idVehiculesEtat']) AND ($data2['idVehiculesEtat'] == $data['idVehiculesEtat'])) { echo 'selected'; } ?> ><?php echo $data2['libelleVehiculesEtat']; ?></option>
		                                    <?php
		                                }
		                                $query2->closeCursor(); ?>
		                            </select>
		                        </div>
		                    </div>
		                    <div class="col-lg-6 col-xs-12">
								<div class="form-group">
		                            <label>Notifications: <small style="color:grey;">Requis</small></label>
		                            <select class="form-control select2" style="width: 100%;" name="idEtat">
		                                <?php
		                                $query2 = $db->query('SELECT * FROM ETATS ORDER BY libelleEtat;');
		                                while ($data2 = $query2->fetch())
		                                {
		                                    ?>
		                                    <option value ="<?php echo $data2['idEtat']; ?>" <?php if (isset($data['idEtat']) AND ($data2['idEtat'] == $data['idEtat'])) { echo 'selected'; } ?> ><?php echo $data2['libelleEtat']; ?></option>
		                                    <?php
		                                }
		                                $query2->closeCursor(); ?>
		                            </select>
		                        </div>
		                    </div>
		                </div>
		                <div class="row">
                        	<div class="col-lg-6 col-xs-12">
		                        <div class="form-group">
		                            <label>Lieu de stationnement:</label>
		                            <select class="form-control select2" style="width: 100%;" name="idLieu">
		                                <option value="">--- Pas de Lieu ---</option>
		                                <?php
		                                $query2 = $db->query('SELECT * FROM LIEUX ORDER BY libelleLieu;');
		                                while ($data2 = $query2->fetch())
		                                {
		                                    ?>
		                                    <option value ="<?php echo $data2['idLieu']; ?>" <?php if (isset($data['idLieu']) AND ($data2['idLieu'] == $data['idLieu'])) { echo 'selected'; } ?> ><?php echo $data2['libelleLieu']; ?></option>
		                                    <?php
		                                }
		                                $query2->closeCursor(); ?>
		                            </select>
		                        </div>
		                    </div>
		                    <div class="col-lg-6 col-xs-12">
								<div class="form-group">
		                            <label>Personne référente</label>
		                            <select class="form-control select2" style="width: 100%;" name="idResponsable">
		                                <option value="">--- Pas de Référent ---</option>
		                                <?php
		                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY identifiant;');
		                                while ($data2 = $query2->fetch())
		                                {
		                                    ?>
		                                    <option value ="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idResponsable']) AND ($data2['idPersonne'] == $data['idResponsable'])) { echo 'selected'; } ?> ><?php echo $data2['identifiant']; ?></option>
		                                    <?php
		                                }
		                                $query2->closeCursor(); ?>
		                            </select>
		                        </div>
		                    </div>
		                </div>
		                <div class="row">
		                	<div class="col-lg-6 col-xs-12">
		                		<div class="form-group">
		                            <label>Carburant</label>
		                            <select class="form-control select2" style="width: 100%;" name="idCarburant">
		                                <option value="">--- Non-Spécifié ---</option>
		                                <?php
		                                $query2 = $db->query('SELECT * FROM CARBURANTS ORDER BY libelleCarburant;');
		                                while ($data2 = $query2->fetch())
		                                {
		                                    ?>
		                                    <option value ="<?php echo $data2['idCarburant']; ?>" <?php if (isset($data['idCarburant']) AND ($data2['idCarburant'] == $data['idCarburant'])) { echo 'selected'; } ?> ><?php echo $data2['libelleCarburant']; ?></option>
		                                    <?php
		                                }
		                                $query2->closeCursor(); ?>
		                            </select>
		                        </div>
		                	</div>
                    		<div class="col-lg-6 col-xs-12">
								<div class="form-group">
									<label>Date d'achat:</label>
		                            <div class="input-group">
		                                <div class="input-group-addon">
		                                    <i class="fa fa-calendar"></i>
		                                </div>
		                                <input class="input-datepicker form-control" name="dateAchat" value="<?= isset($data['dateAchat']) ? $data['dateAchat'] : '' ?>">
		                            </div>
								</div>
							</div>
						</div>
						<div class="form-group">
                            <label>Référence Assurance:</label>
                            <input type="text" class="form-control" value="<?= isset($data['assuranceNumero']) ? $data['assuranceNumero'] : ''?>" name="assuranceNumero">
                        </div>
                        <div class="row">
                        	<div class="col-lg-4 col-xs-12">
								<div class="form-group">
									<label>Prochaine révision:</label>
		                            <div class="input-group">
		                                <div class="input-group-addon">
		                                    <i class="fa fa-calendar"></i>
		                                </div>
		                                <input class="input-datepicker form-control" name="dateNextRevision" value="<?= isset($data['dateNextRevision']) ? $data['dateNextRevision'] : '' ?>">
		                            </div>
								</div>
							</div>
							<div class="col-lg-4 col-xs-12">
								<div class="form-group">
									<label>Prochain CT:</label>
		                            <div class="input-group">
		                                <div class="input-group-addon">
		                                    <i class="fa fa-calendar"></i>
		                                </div>
		                                <input class="input-datepicker form-control" name="dateNextCT" value="<?= isset($data['dateNextCT']) ? $data['dateNextCT'] : '' ?>">
		                            </div>
								</div>
							</div>
							<div class="col-lg-4 col-xs-12">
								<div class="form-group">
									<label>Exp. d'assurance:</label>
		                            <div class="input-group">
		                                <div class="input-group-addon">
		                                    <i class="fa fa-calendar"></i>
		                                </div>
		                                <input class="input-datepicker form-control" name="assuranceExpiration" value="<?= isset($data['assuranceExpiration']) ? $data['assuranceExpiration'] : '' ?>">
		                            </div>
								</div>
							</div>
						</div>
						<div class="row">
                        	<div class="col-lg-6 col-xs-12">
								<div class="form-group">
		                            <label>Pneus Hivers:</label>
		                            </br>
		                            <div class="checkbox">
		                                <label>
		                                    <input type="checkbox" value="1" name="pneusAVhivers" <?php if (isset($_GET['id']) AND ($data['pneusAVhivers']==1)) {echo 'checked';} ?>> Train avant
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" value="1" name="pneusARhivers" <?php if (isset($_GET['id']) AND ($data['pneusARhivers']==1)) {echo 'checked';} ?>> Train arrière
		                                </label>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="col-lg-6 col-xs-12">
		                		<div class="form-group">
		                            <label>Autres équipements:</label>
		                            </br>
		                            <div class="checkbox">
		                                <label>
		                                    <input type="checkbox" value="1" name="climatisation" <?php if (isset($_GET['id']) AND ($data['climatisation']==1)) {echo 'checked';} ?>> Climatisation
										</label>
									</div>
									<div class="checkbox">
		                                <label>
		                                    <input type="checkbox" value="1" name="priseAlimentation220" <?php if (isset($_GET['id']) AND ($data['priseAlimentation220']==1)) {echo 'checked';} ?>> Alimentation 220V externe
										</label>
									</div>
		                        </div>
		                    </div>
		                </div>
						<div class="form-group">
                            <label>Signalisation lumineuse et sonore:</label>
                            <div class="row">
                        		<div class="col-lg-6 col-xs-12">
                        			<div class="checkbox">
										<label>
											<input type="checkbox" value="1" name="signaletiqueBleue" <?php if (isset($_GET['id']) AND ($data['signaletiqueBleue']==1)) {echo 'checked';} ?>> Feux bleus
		                                </label>
		                            </div>
									<div class="checkbox">
		                                <label>
		                                    <input type="checkbox" value="1" name="signaletique2tons" <?php if (isset($_GET['id']) AND ($data['signaletique2tons']==1)) {echo 'checked';} ?>> Sirène deux tons
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" value="1" name="signaletique3tons" <?php if (isset($_GET['id']) AND ($data['signaletique3tons']==1)) {echo 'checked';} ?>> Sirène trois tons
		                                </label>
		                            </div>
                        		</div>
                        		<div class="col-lg-6 col-xs-12">
                        			<div class="checkbox">
		                                <label>
		                                    <input type="checkbox" value="1" name="signaletiqueOrange" <?php if (isset($_GET['id']) AND ($data['signaletiqueOrange']==1)) {echo 'checked';} ?>> Feux oranges
										</label>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" value="1" name="pmv" <?php if (isset($_GET['id']) AND ($data['pmv']==1)) {echo 'checked';} ?>> Panneau à message variable
		                                </label>
		                            </div>
									<div class="checkbox">
										<label>
											<input type="checkbox" value="1" name="fleche" <?php if (isset($_GET['id']) AND ($data['fleche']==1)) {echo 'checked';} ?>> Flèche
		                                </label>
		                            </div>
                        		</div>
                        	</div>
                        </div>
                        <div class="form-group">
                        	<label>Synthèses:</label>
                        	<div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="affichageSyntheseDesinfections" <?php if (isset($_GET['id']) AND ($data['affichageSyntheseDesinfections']==1)) {echo 'checked';} ?>> Afficher dans les synthèse des désinfections
								</label>
							</div>
							<div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="affichageSyntheseHealth" <?php if (isset($_GET['id']) AND ($data['affichageSyntheseHealth']==1)) {echo 'checked';} ?>> Afficher dans les synthèse des maintenance
								</label>
							</div>
                        </div>
						<div class="form-group">
                            <label>Nombre de cônes:</label>
                            <input type="number" class="form-control" value="<?= isset($data['nbCones']) ? $data['nbCones'] : ''?>" name="nbCones">
                        </div>
						<div class="form-group">
                            <label>Commentaires</label>
                            <textarea class="form-control" rows="3" name="remarquesVehicule"><?= isset($data['remarquesVehicule']) ? $data['remarquesVehicule'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right"><?= isset($_GET['id']) ? 'Modifier' : 'Ajouter'?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>