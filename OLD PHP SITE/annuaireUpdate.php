<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['annuaire_modification']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else {


    $query = $db->prepare('
        UPDATE
            PERSONNE_REFERENTE
        SET
            identifiant    = :identifiant,
            nomPersonne    = :nomPersonne,
            prenomPersonne = :prenomPersonne,
            mailPersonne   = :mailPersonne,
            telPersonne    = :telPersonne,
            fonction       = :fonction
        WHERE
            idPersonne     = :idPersonne ;');
    $query->execute(array(
        'idPersonne'     => $_GET['id'],
        'identifiant'    => $_POST['identifiant'],
        'nomPersonne'    => $_POST['nomPersonne'],
        'prenomPersonne' => $_POST['prenomPersonne'],
        'mailPersonne'   => $_POST['mailPersonne'],
        'telPersonne'    => $_POST['telPersonne'],
        'fonction'       => $_POST['fonction']
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de l'utilisateur " . $_POST['identifiant'], '1', NULL);
            $_SESSION['returnMessage'] = 'Utilisateur modifié avec succès.';
            $_SESSION['returnType'] = '1';

            if ($_SESSION['profils_modification']==1)
            {
	            $queryDelete = $db->prepare('DELETE FROM PROFILS_PERSONNES WHERE idPersonne = :idPersonne');
	            $queryDelete->execute([
	                ':idPersonne' => $_GET['id']
	            ]);
	            if (!empty($_POST['idProfil'])) {
	                $insertSQL = 'INSERT INTO PROFILS_PERSONNES (idProfil, idPersonne) VALUES';
	                foreach ($_POST['idProfil'] as $idProfil) {
	                    $insertSQL .= ' ('. (int)$idProfil.', '. (int)$_GET['id'] .'),';
	                }
	
	                $insertSQL = substr($insertSQL, 0, -1);
	
	                $db->query($insertSQL);
	            }
            }

            break;

        case '23000':
            writeInLogs("Doublon détecté lors de la modification de l'utilisateur " . $_POST['identifiant'], '2', NULL);
            $_SESSION['returnMessage'] = 'Un utilisateur existe déjà avec le même identifiant. Merci de changer l\'identifiant.';
            $_SESSION['returnType'] = '2';
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modifciation de l'utilisateur " . $_POST['identifiant'], '3', NULL);
            $_SESSION['returnMessage'] = 'Erreur inconnue lors de la modification de l\'utilisateur.';
            $_SESSION['returnType'] = '2';
    }

    majIndicateursPersonne($_GET['id'],1);
    majNotificationsPersonne($_GET['id'],1);
    majValideursPersonne(1);

    echo "<script>window.location = document.referrer;</script>";
}
?>