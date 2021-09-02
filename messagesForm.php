<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['messages_ajout']==1)
{ ?>
    <div class="modal fade" id="modalMessageGeneralAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ajout d'un message général</h4>
                </div>
                <form role="form" action="messagesAdd.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Type de message: </label>
                            <select class="form-control select2" style="width: 100%;" name="idMessageType">
                                <?php
                                $query2 = $db->query('SELECT * FROM MESSAGES_TYPES;');
                                while ($data2 = $query2->fetch())
                                {
                                    ?>
                                    <option value ="<?php echo $data2['idMessageType']; ?>" <?php if (isset($data['idMessageType']) AND ($data2['idMessageType'] == $data['idMessageType'])) { echo 'selected'; } ?> ><?php echo $data2['libelleMessageType']; ?></option>
                                    <?php
                                }
                                $query2->closeCursor(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Message:</label>
                            <textarea class="form-control" rows="3" placeholder="Corps du message" name="corpsMessage" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary pull-right">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>