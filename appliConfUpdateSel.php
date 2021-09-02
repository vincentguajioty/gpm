<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['appli_conf']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $user = $db->prepare('SELECT * FROM PERSONNE_REFERENTE WHERE idPersonne = :idPersonne;');
    $user->execute(array('idPersonne'=>$_SESSION['idPersonne']));
    $user = $user->fetch();
    if (!(password_verify($SELPRE.$_POST['pwd'].$SELPOST, $user['motDePasse'])))
    {
        $_SESSION['returnMessage'] = 'La vérification de l\'ancien mot de passe a échoué.';
        $_SESSION['returnType'] = '2';
    }
    else
    {
        $query = $db->prepare('UPDATE CONFIG SET selPre = :selPre, selPost = :selPost;');
        $query->execute(array(
            'selPre' => $_POST['selPre'],
            'selPost' => $_POST['selPost']
        ));

        switch($query->errorCode())
        {
            case '00000':
                writeInLogs("Modification de la configuration des sels de mots de passe du site" , '1', NULL);
                $_SESSION['returnMessage'] = 'Configuration modifiée avec succès.';
                $_SESSION['returnType'] = '1';

                $query = $db->prepare('UPDATE PERSONNE_REFERENTE SET motDePasse = :motDePasse WHERE idPersonne = :idPersonne ;');
                $query->execute(array(
                    'motDePasse' => password_hash($_POST['selPre'].$_POST['pwd'].$_POST['selPost'], PASSWORD_DEFAULT),
                    'idPersonne' => $_SESSION['idPersonne']
                ));

                break;
                
            default:
                writeInLogs("Echec de la modification de la configuration des sels de mots de passe du site" , '3', NULL);
                $_SESSION['returnMessage'] = 'Erreur inconnue lors la modification de la configuration des sels de mots de passe.';
                $_SESSION['returnType'] = '2';
        }
    }

    echo "<script type='text/javascript'>document.location.replace('appliConf.php');</script>";
}
?>