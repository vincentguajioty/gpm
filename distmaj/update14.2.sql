UPDATE
	COMMANDES_NOTIFICATIONS
SET
	destinatairesQuery = "WITH validDef AS (SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND ( montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL) AND ( c.depasseBudget = 1 OR (SELECT SUM(montantEntrant)-SUM(montantSortant) FROM CENTRE_COUTS_OPERATIONS WHERE idCentreDeCout = c.idCentreDeCout) >= :montantTotalCommande)) SELECT * FROM validDef UNION SELECT mailPersonne, prenomPersonne FROM VIEW_HABILITATIONS WHERE commande_valider_delegate = 1 AND NOT EXISTS (SELECT * FROM validDef);"
WHERE
	idNotif = 10
;

ALTER TABLE RESERVES_CONTENEUR ADD dispoBenevoles BOOLEAN;
UPDATE RESERVES_CONTENEUR SET dispoBenevoles = true;

UPDATE CONFIG set version = '14.2';