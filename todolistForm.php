<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';


if (isset($_GET['id']))
{
    $query = $db->prepare('SELECT * FROM TODOLIST WHERE idTache=:idTache;');
    $query->execute(array('idTache' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
}
?>

<div class="modal fade" id="modalAnnuaireAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tache de la ToDoList</h4>
            </div>
            <form role="form" action="<?= isset($_GET['id']) ? 'todolistUpdate.php?id='.$_GET['id'] : 'todolistAdd.php?idCreateur=' . $_GET['idCreateur'] . '&idExecutant=' . $_GET['idExecutant'] ?>" method="POST">
                <div class="modal-body">
                    <?php
                        if (isset($_GET['id']))
                        {
                    ?>
                        <div class="form-group">
                            <label>Créée par:</label>
                            <select class="form-control select2" style="width: 100%;" name="idCreateur" disabled>
                                <option value=""></option>
                                <?php
                                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY nomPersonne, prenomPersonne;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idCreateur']) AND ($data2['idPersonne'] == $data['idCreateur'])) { echo 'selected'; } ?>><?php echo $data2['nomPersonne'] . ' ' . $data2['prenomPersonne']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="row">
                        	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                        <div class="form-group">
		                            <label>Créée le:</label>
		                            <input disabled type="text" class="input-datetimepicker form-control" name="dateCreation" value="<?php echo $data['dateCreation']; ?>">
		                        </div>
		                    </div>
		                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		                        <div class="form-group">
		                            <label>Clôturée le:</label>
		                            <input disabled type="text" class="input-datetimepicker form-control" name="dateCloture" value="<?php echo $data['dateCloture']; ?>">
		                        </div>
		                    </div>
                        </div>
	                    <div class="form-group">
	                        <label>Affectée à:</label>
	                        <select class="form-control select2" style="width: 100%;" name="idExecutant[]" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 OR (tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']) AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?> multiple>
	                            <?php
					            if (isset($_GET['id']))
					            {
					                $query2 = $db->prepare('
                                        SELECT
                                            ao.*,
                                            (SELECT idExecutant FROM TODOLIST_PERSONNES aop WHERE ao.idPersonne = aop.idExecutant AND aop.idTache = :idTache) as idExecutant
                                        FROM
                                            PERSONNE_REFERENTE ao
                                        ORDER BY
                                            nomPersonne,
                                            prenomPersonne;');
					                $query2->execute(array('idTache' => $_GET['id']));
					            }
					            else
					            {
					                $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY nomPersonne, prenomPersonne;');
					            }

                                while ($data2 = $query2->fetch())
                                {
                                    
                                    echo '<option value=' . $data2['idPersonne'];

					                if (isset($data2['idExecutant']) AND $data2['idExecutant'])
					                {
					                    echo " selected ";
					                }
					                echo '>' . $data2['nomPersonne'] . ' ' . $data2['prenomPersonne'] . '</option>';
                                }
                                $query2->closeCursor();?>
	                        </select>
	                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label>Priorité:</label>
                        <select class="form-control" style="width: 100%;" name="idTDLpriorite" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND (tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']) AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>>
                            <?php
                                $query2 = $db->query('SELECT * FROM TODOLIST_PRIORITES;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value="<?php echo $data2['idTDLpriorite']; ?>" <?php if ($data['idTDLpriorite'] == $data2['idTDLpriorite']) { echo 'selected'; } ?>><?php echo $data2['libellePriorite']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor();
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>A faire avant le:</label>
                        <input type="text" class="input-datetimepicker form-control" name="dateExecution" value="<?php echo $data['dateExecution']; ?>" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND (tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']) AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>>
                    </div>
                    <div class="form-group">
                        <label>Titre:</label>
                        <input type="text" class="form-control" value="<?= isset($data['titre']) ? $data['titre'] : ''?>" name="titre" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND (tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']) AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>>
                    </div>
                    <div class="form-group">
                        <label>Contenu:</label>
                        <textarea class="form-control" rows="3" name="details" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND (tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']) AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>><?php echo $data['details']; ?></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                    <?php if(isset($_GET['id']) AND (($_SESSION['todolist_modification'])OR(tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']) AND $_SESSION['todolist_perso']))){?><a href="modalDeleteConfirm.php?case=tdlDelete&id=<?=$_GET['id']?>" class="btn btn-danger modal-form">Supprimer</a><?php } ?>
                    <?php if(isset($_GET['id']) AND $data['realisee']==0 AND ($_SESSION['todolist_modification'] OR tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']))){?><a href="todolistSetOk.php?id=<?= $_GET['id'] ?>" class="btn btn-success">Marquer comme terminée</a><?php } ?>
                    <?php if(isset($_GET['id']) AND $data['realisee']==1 AND ($_SESSION['todolist_modification'] OR tdlEstExecutant($_SESSION['idPersonne'],$_GET['id']))){?><a href="todolistSetKo.php?id=<?= $_GET['id'] ?>" class="btn btn-warning">Marquer comme non-terminée</a><?php } ?>
                    <?php if(isset($_GET['id']) AND (($_SESSION['todolist_modification']==1)OR($_SESSION['todolist_perso']==1 AND tdlEstExecutant($_SESSION['idPersonne'],$_GET['id'])))){?><button type="submit" class="btn btn-primary pull-right">Modifier</button><?php } ?>
                    <?php if(!isset($_GET['id']) AND ($_SESSION['todolist_modification']==1 OR $_SESSION['todolist_perso']==1)){?><button type="submit" class="btn btn-primary pull-right">Ajouter</button><?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
