<?php
session_start();
require_once('logCheck.php');
?>
<?php
require_once 'config/bdd.php';
require_once 'commandesCommentAdd.php';
require_once 'config/config.php';
require_once 'config/mailFunction.php';

if ($_SESSION['commande_etreEnCharge']==0)
{
    echo "<script type='text/javascript'>document.location.replace('loginHabilitation.php');</script>";
}
else
{
    $query = $db->query('SELECT * FROM CONFIG;');
    $config = $query -> fetch();

    $_POST['dateLivraisoneffective'] = ($_POST['dateLivraisoneffective'] == Null) ? Null : $_POST['dateLivraisoneffective'];
    
    if ($_POST['button'] == 'ok')
    {
        $query = $db->prepare('UPDATE COMMANDES_MATERIEL SET quantiteAtransferer = quantiteCommande WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id']
        ));
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 5, remarquesLivraison = :remarquesLivraison, dateLivraisoneffective = :dateLivraisoneffective WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesLivraison' => $_POST['remarquesLivraison'],
            'dateLivraisoneffective' => $_POST['dateLivraisoneffective']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " réceptionne la commande sans déclencher de SAV. La commande n'a plus qu'à être clôturée.", "25");

        $sujet = "[" . $APPNAME . "] Livraison de la commande " .$_GET['id']." effectuée avec succès.";

        if($config['notifications_commandes_demandeur_livraisonOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {
                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être livrée sans SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison de commande envoyé au demandeur pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail d'information de livraison de commande au demandeur pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
        if($config['notifications_commandes_valideur_livraisonOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_VALIDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idValideur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {
                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être livrée sans SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison de commande envoyé au valideur pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail d'information de livraison de commande au valideur pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
        if($config['notifications_commandes_affectee_livraisonOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {
                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " qui vous est affectée vient d'être livrée sans SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison de commande envoyé au gérant pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail de passage de livraison de commande au gérant pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
        if($config['notifications_commandes_observateur_livraisonOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {
                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes l'observateur vient d'être livrée sans SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison de commande envoyé à l'observateur pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail de passage de livraison de commande à l'observateur pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
    }
    else
    {
        $query = $db->prepare('UPDATE COMMANDES SET idEtat = 6, remarquesLivraison = :remarquesLivraison, dateLivraisoneffective = :dateLivraisoneffective WHERE idCommande = :idCommande;');
        $query->execute(array(
            'idCommande' => $_GET['id'],
            'remarquesLivraison' => $_POST['remarquesLivraison'],
            'dateLivraisoneffective' => $_POST['dateLivraisoneffective']
        ));
        addCommandeComment($_GET['id'], $_SESSION['identifiant'] . " réceptionne la commande et engage un SAV.", "30");

        $sujet = "[" . $APPNAME . "] Livraison de la commande " .$_GET['id']." effectuée avec engagement d'un SAV.";

        if($config['notifications_commandes_demandeur_livraisonNOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {
                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes le demandeur vient d'être livrée avec ouverture d'un SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison avec ouverture de SAV de commande envoyé au demandeur pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail d'information de livraison avec ouverture de SAV de commande au demandeur pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
        if($config['notifications_commandes_valideur_livraisonNOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_VALIDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idValideur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {
                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> La commande " . $_GET['id'] . " dont vous êtes le valideur vient d'être livrée avec ouverture d'un SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison avec ouverture de SAV de commande envoyé au valideur pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail d'information de livraison avec ouverture de SAV de commande au valideur pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
        if($config['notifications_commandes_affectee_livraisonNOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {

                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " qui vous est affectée vient d'être livrée avec ouverture d'un SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison avec ouverture de SAV de commande envoyé au gérant pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail de passage de livraison avec ouverture de SAV de commande au gérant pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
        if($config['notifications_commandes_observateur_livraisonNOK']==1)
        {
            $query = $db->prepare("SELECT mailPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;");
            $query->execute(array('idCommande'=>$_GET['id']));
            while($data = $query->fetch())
            {
                $message = "Bonjour " . $data['prenomPersonne'] . ", <br/><br/> Pour information, la commande " . $_GET['id'] . " dont vous êtes l'observateur vient d'être livrée avec ouverture d'un SAV.";
                $message = $message . "<br/><br/>Cordialement<br/><br/>L'équipe administrative de " . $APPNAME;
                $message = $RETOURLIGNE.$message.$RETOURLIGNE;
                if(sendmail($data['mailPersonne'], $sujet, 2, $message))
                {
                    writeInLogs("Mail d'information de livraison avec ouverture de SAV de commande envoyé à l'observateur pour la commande " . $_GET['id'], '1', NULL);
                }
                else
                {
                    writeInLogs("Erreur lors de l'envoi du mail de passage de livraison avec ouverture de SAV de commande à l'observateur pour la commande " . $_GET['id'], '3', NULL);
                }
            }
        }
    }

    switch($query->errorCode())
    {
        case '00000':
            writeInLogs("Modification de la commande " . $_GET['id'], '1', NULL);
            break;

        default:
            writeInLogs("Erreur inconnue lors de la modification de la commande.", '3', NULL);
            $_SESSION['returnMessage'] = "Erreur inconnue lors la réception de la commande.";
            $_SESSION['returnType'] = '2';

    }

    echo "<script type='text/javascript'>document.location.replace('commandesToutes.php');</script>";

}
?>