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
                        <div class="form-group">
                            <label>Créée le:</label>
                            <input disabled type="text" class="input-datetimepicker form-control" name="dateCreation" value="<?php echo $data['dateCreation']; ?>">
                        </div>
	                    <div class="form-group">
	                        <label>Affectée à:</label>
	                        <select class="form-control select2" style="width: 100%;" name="idExecutant" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 OR ($data['idExecutant']==$_SESSION['idPersonne'] AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>>
	                            <option value="">--- Non Affectée ---</option>
	                            <?php
	                            $query2 = $db->query('SELECT * FROM PERSONNE_REFERENTE ORDER BY nomPersonne, prenomPersonne;');
	                            while ($data2 = $query2->fetch())
	                            {
	                                ?>
	                                <option value="<?php echo $data2['idPersonne']; ?>" <?php if (isset($data['idExecutant']) AND ($data2['idPersonne'] == $data['idExecutant'])) { echo 'selected'; } ?>><?php echo $data2['nomPersonne'] . ' ' . $data2['prenomPersonne']; ?></option>
	                                <?php
	                            }
	                            $query2->closeCursor(); ?>
	                        </select>
	                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label>Priorité:</label>
                        <select class="form-control" style="width: 100%;" name="priorite" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND ($data['idExecutant']==$_SESSION['idPersonne'] AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>>
                            <option value="1 - Urgent" <?php if (isset($data['priorite']) AND ($data['priorite']=='1 - Urgent')) { echo 'selected'; } ?>>1 - Urgent</option>
                            <option value="2 - Important" <?php if (isset($data['priorite']) AND ($data['priorite']=='2 - Important')) { echo 'selected'; } ?>>2 - Important</option>
                            <option value="3 - Normal" <?php if (isset($data['priorite']) AND ($data['priorite']=='3 - Normal')) { echo 'selected'; } ?> <?php if (!isset($data['priorite'])) { echo 'selected'; } ?>>3 - Normal</option>
                            <option value="4 - Faible" <?php if (isset($data['priorite']) AND ($data['priorite']=='4 - Faible')) { echo 'selected'; } ?>>4 - Faible</option>
                            <option value="5 - Optionel" <?php if (isset($data['priorite']) AND ($data['priorite']=='5 - Optionel')) { echo 'selected'; } ?>>5 - Optionel</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>A faire avant le:</label>
                        <input type="text" class="input-datetimepicker form-control" name="dateExecution" value="<?php echo $data['dateExecution']; ?>" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND ($data['idExecutant']==$_SESSION['idPersonne'] AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>>
                    </div>
                    <div class="form-group">
                        <label>Titre:</label>
                        <input type="text" class="form-control" value="<?= isset($data['titre']) ? $data['titre'] : ''?>" name="titre" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND ($data['idExecutant']==$_SESSION['idPersonne'] AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>>
                    </div>
                    <div class="form-group">
                        <label>Contenu:</label>
                        <textarea class="form-control" rows="3" name="details" <?php if(isset($_GET['id']) AND ($_SESSION['todolist_modification']==0 AND ($data['idExecutant']==$_SESSION['idPersonne'] AND $_SESSION['todolist_perso']==0))){ echo 'disabled'; }?>><?php echo $data['details']; ?></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                    <?php if(isset($_GET['id']) AND (($_SESSION['todolist_modification'])OR($data['idExecutant']==$_SESSION['idPersonne'] AND $_SESSION['todolist_perso']))){?><a href="modalDeleteConfirm.php?case=tdlDelete&id=<?=$_GET['id']?>" class="btn btn-danger modal-form">Supprimer</a><?php } ?>
                    <?php if(isset($_GET['id']) AND $data['realisee']==0 AND ($_SESSION['todolist_modification'] OR $data['idExecutant']==$_SESSION['idPersonne'])){?><a href="todolistSetOk.php?id=<?= $_GET['id'] ?>" class="btn btn-success">Marquer comme terminée</a><?php } ?>
                    <?php if(isset($_GET['id']) AND $data['realisee']==1 AND ($_SESSION['todolist_modification'] OR $data['idExecutant']==$_SESSION['idPersonne'])){?><a href="todolistSetKo.php?id=<?= $_GET['id'] ?>" class="btn btn-warning">Marquer comme non-terminée</a><?php } ?>
                    <?php if(isset($_GET['id']) AND (($_SESSION['todolist_modification']==1)OR($_SESSION['todolist_perso']==1 AND $data['idExecutant']==$_SESSION['idPersonne']))){?><button type="submit" class="btn btn-primary pull-right">Modifier</button><?php } ?>
                    <?php if(!isset($_GET['id']) AND ($_SESSION['todolist_modification']==1 OR $_SESSION['todolist_perso']==1)){?><button type="submit" class="btn btn-primary pull-right">Ajouter</button><?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
