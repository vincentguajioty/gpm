<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';

if ($_SESSION['codeBarre_ajout']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $_POST['peremptionConsommable'] = ($_POST['peremptionConsommable'] == Null) ? Null : $_POST['peremptionConsommable'];

    $query = $db->prepare('
        INSERT INTO
            CODES_BARRE
        SET
            codeBarre             = Null,
            internalReference     = 1,
            peremptionConsommable = :peremptionConsommable,
            commentairesCode      = :commentairesCode,
            idMaterielCatalogue   = :idMaterielCatalogue
        ;');
    $query->execute(array(
        'peremptionConsommable' => $_POST['peremptionConsommable'],
        'commentairesCode'      => $_POST['commentairesCode'],
        'idMaterielCatalogue'   => $_POST['idMaterielCatalogue'],
    ));

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Ajout d'un code barre interne pour le matériel id " . $_POST['idMaterielCatalogue'], '1', NULL);


            $code = $db->query('SELECT MAX(idCode) as idCode FROM CODES_BARRE;');
            $code = $code->fetch();

            $query = $db->prepare('
                UPDATE
                    CODES_BARRE
                SET
                    codeBarre = :codeBarre
                WHERE
                    idCode    = :idCode
                ;');
            $query->execute(array(
                'codeBarre' => 'GPM'.$code['idCode'],
                'idCode'    => $code['idCode'],
            ));


            $_SESSION['returnMessage'] = 'Code barre ajouté avec succès';
            $_SESSION['returnType'] = '1';
            break;

        default:
            writeInLogs("Erreur inconnue lors de l'ajout d'un code barre interne pour le matériel id " . $_POST['idMaterielCatalogue'], '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors l'ajout du code barre.";
            $_SESSION['returnType'] = '2';
    }

    echo "<script>window.location = document.referrer;</script>";
}
?>