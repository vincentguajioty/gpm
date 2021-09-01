<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';



if($_SESSION['annuaire_lecture']==1 OR $_SESSION['annuaire_ajout']==1 OR $_SESSION['annuaire_modification']==1 OR $_SESSION['annuaire_suppression']==1 OR $_SESSION['annuaire_mdp']==1)
{ ?>
    
    <?php
    if (isset($_GET['id']))
	{
	    $query = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne=:idPersonne;');
	    $query->execute(array('idPersonne' => $_GET['id']));
	    $data = $query->fetch();
	    $query->closeCursor();
	}
    ?>
    
    <div class="modal fade" id="modalAnnuaireAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= isset($_GET['id']) ? 'Modification' : 'Création'?> d'un compte utilisateur</h4>
                </div>
                <form role="form" action="<?= isset($_GET['id']) ? 'annuaireUpdate.php?id='.$_GET['id'] : 'annuaireAdd.php'?>" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Identifiant de connexion: <small style="color:grey;"> Requis</small></label>
                            <input type="text" class="form-control" value="<?= isset($data['identifiant']) ? $data['identifiant'] : ''?>" name="identifiant" required>
                        </div>
                        <div class="form-group">
                            <label>Nom:</label>
                            <input type="text" class="form-control" value="<?= isset($data['nomPersonne']) ? $data['nomPersonne'] : ''?>" name="nomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Prénom:</label>
                            <input type="text" class="form-control" value="<?= isset($data['prenomPersonne']) ? $data['prenomPersonne'] : ''?>" name="prenomPersonne">
                        </div>
                        <div class="form-group">
                            <label>Adresse mail:</label>
                            <input type="text" class="form-control" value="<?= isset($data['mailPersonne']) ? $data['mailPersonne'] : ''?>" name="mailPersonne">
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" class="form-control" value="<?= isset($data['telPersonne']) ? $data['telPersonne'] : ''?>" name="telPersonne">
                        </div>
                        <div class="form-group">
                            <label>Fonction:</label>
                            <input type="text" class="form-control" value="<?= isset($data['fonction']) ? $data['fonction'] : ''?>" name="fonction">
                        </div>
                        <div class="form-group">
                            <label>Profil d'habilitation: </label>
                            <select class="form-control select2" style="width: 100%;" name="libelleProfil[]" multiple <?php if($_SESSION['profils_modification']==0){ echo 'disabled'; } ?>>
                                <?php
					            if (isset($_GET['id']))
					            {
					                $query2 = $db->prepare('SELECT ao.*, aop.idPersonne FROM PROFILS ao LEFT JOIN PROFILS_PERSONNES aop ON (ao.idProfil = aop.idProfil AND aop.idPersonne = :idPersonne) ORDER BY libelleProfil;');
					                $query2->execute(array('idPersonne' => $_GET['id']));
					            }
					            else
					            {
					                $query2 = $db->query('SELECT * FROM PROFILS ORDER BY libelleProfil;');
					            }

                                while ($data2 = $query2->fetch())
                                {
                                    
                                    echo '<option value=' . $data2['idProfil'];

					                if (isset($data2['idPersonne']) AND $data2['idPersonne'])
					                {
					                    echo " selected ";
					                }
					                echo '>' . $data2['libelleProfil'] . '</option>';
                                }
                                $query2->closeCursor();?>
                            </select>
                        </div>
                        <?php
                            if(!isset($_GET['id']))
                            { ?>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" value="1" name="notificationMailCreation" checked> Envoyer un email à l'utilisateur avec son identifiant et son mot de passe
                                        </label>
                                    </div>
                                </div>
                            <?php }
                        ?>
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