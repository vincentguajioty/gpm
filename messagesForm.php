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
                                    <label>Titre:</label>
                                    <input type="text" class="form-control" placeholder="Titre du message"
                                           name="titreMessage" required>
                                </div>
                                <!-- textarea -->
                                <div class="form-group">
                                    <label>Message:</label>
                                    <textarea class="form-control" rows="3" placeholder="Corps du message"
                                              name="corpsMessage"></textarea>
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