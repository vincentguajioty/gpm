UPDATE
	COMMANDES_NOTIFICATIONS
SET
	destinatairesQuery = 
		"
			WITH validDef AS (
				SELECT
					mailPersonne,
					prenomPersonne
				FROM
					CENTRE_COUTS_PERSONNES c
					INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne
				WHERE
					idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande)
					AND
					(montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL))

			SELECT * FROM validDef UNION
				SELECT
					mailPersonne,
					prenomPersonne
				FROM
					VIEW_HABILITATIONS
				WHERE
					commande_valider_delegate = 1
					AND
					NOT EXISTS (SELECT * FROM validDef)
		"
WHERE
	idNotif = 10
;

ALTER TABLE CONFIG ADD notifications_commandes_valideur_centreCout BOOLEAN AFTER notifications_commandes_valideur_passee;

UPDATE CONFIG SET notifications_commandes_valideur_centreCout = 0;

INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 37,
	descriptifNotif      = "Etat 3 vers 4 - Envoyée aux responsable centre de couts",
	booleanConfigLibelle = "notifications_commandes_valideur_centreCout",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande);",
	sujetNotif           = "[:APPNAME][COMMANDE] Intégration au centre de couts de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui est rattachée à votre centre de couts vient d'être passée auprès du fournisseur. Vous pouvez donc dès à présent l'intégrer à votre centre couts.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

ALTER TABLE PERSONNE_REFERENTE ADD doubleAuthSecret VARCHAR(25);

UPDATE PERSONNE_REFERENTE SET doubleAuthSecret = Null;

UPDATE CONFIG set version = '12.3';