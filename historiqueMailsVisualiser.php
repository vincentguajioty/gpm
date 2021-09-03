<?php

require_once('config/config.php');
session_start();
require_once('logCheck.php');
require_once 'config/bdd.php';

if($_SESSION['appli_conf']==1)
{

    $query = $db->prepare('SELECT * FROM NOTIFICATIONS_MAILS WHERE idMail = :idMail;');
    $query->execute(array('idMail' => $_GET['id']));
    $data = $query->fetch();
    $query->closeCursor();
    ?>
    
    <div class="modal fade" id="modalMails">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Visualiser un mail</h4>
                </div>
                <form role="form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Titre:</label><br/>
                            <?=$data['sujet']?>
                        </div>
                        <div class="form-group">
                            <label>Importance:</label><br/>
                            <?=$data['niveau']?>
                        </div>
                        <!-- textarea -->
                        <div class="form-group">
                            <label>Contenu:</label><br/>
                            <?=$data['contenu']?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php } ?>


