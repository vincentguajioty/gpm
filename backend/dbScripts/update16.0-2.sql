ALTER TABLE TENUES_AFFECTATION DROP FOREIGN KEY fk_tenuesAff_Catalogue;
ALTER TABLE TENUES_AFFECTATION DROP idCatalogueTenue;

ALTER TABLE TENUES_CATALOGUE DROP libelleCatalogueTenue;
ALTER TABLE TENUES_CATALOGUE DROP tailleCatalogueTenue;

UPDATE MATERIEL_CATALOGUE SET taille = null WHERE taille = "";
UPDATE MATERIEL_CATALOGUE SET conditionnementMultiple = null WHERE conditionnementMultiple = "";
UPDATE MATERIEL_CATALOGUE SET commentairesMateriel = null WHERE commentairesMateriel = "";

CREATE TABLE VEHICULES_STOCK(
	idVehiculesStock INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idMaterielCatalogue INT,
  idFournisseur INT,
  quantiteVehiculesStock INT,
  quantiteAlerteVehiculesStock INT,
  peremptionVehiculesStock DATE,
  peremptionAnticipationVehiculesStock INT,
  peremptionNotificationVehiculesStock DATE AS (DATE_SUB(peremptionVehiculesStock, INTERVAL peremptionAnticipationVehiculesStock DAY)),
  commentairesVehiculesStock TEXT,
	CONSTRAINT fk_vehStock_catalogue
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue),
    CONSTRAINT fk_vehStock_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur)
);

CREATE TABLE VHF_STOCK(
	idVhfStock INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idMaterielCatalogue INT,
  idFournisseur INT,
  quantiteVhfStock INT,
  quantiteAlerteVhfStock INT,
  peremptionVhfStock DATE,
  peremptionAnticipationVhfStock INT,
  peremptionNotificationVhfStock DATE AS (DATE_SUB(peremptionVhfStock, INTERVAL peremptionAnticipationVhfStock DAY)),
  commentairesVhfStock TEXT,
	CONSTRAINT fk_vhfStock_catalogue
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue),
    CONSTRAINT fk_vhfStock_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur)
);




UPDATE
  TENUES_AFFECTATION t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.mailPersonneNonGPM = p.mailPersonne
WHERE
  t.idPersonne IS NOT NULL
;

UPDATE
  TENUES_AFFECTATION t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = CONCAT(p.nomPersonne, " ", p.prenomPersonne)
WHERE
  t.idPersonne IS NOT NULL
  AND p.nomPersonne IS NOT NULL
  AND p.prenomPersonne IS NOT NULL
;

UPDATE
  TENUES_AFFECTATION t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = p.identifiant
WHERE
  t.idPersonne IS NOT NULL
  AND t.personneNonGPM IS NULL
;

ALTER TABLE TENUES_AFFECTATION DROP CONSTRAINT fk_tenuesAff_Personne;
ALTER TABLE TENUES_AFFECTATION DROP idPersonne;


UPDATE
  CAUTIONS t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.mailPersonneNonGPM = p.mailPersonne
WHERE
  t.idPersonne IS NOT NULL
;

UPDATE
  CAUTIONS t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = CONCAT(p.nomPersonne, " ", p.prenomPersonne)
WHERE
  t.idPersonne IS NOT NULL
  AND p.nomPersonne IS NOT NULL
  AND p.prenomPersonne IS NOT NULL
;

UPDATE
  CAUTIONS t
  LEFT OUTER JOIN PERSONNE_REFERENTE p ON t.idPersonne = p.idPersonne
SET
  t.personneNonGPM = p.identifiant
WHERE
  t.idPersonne IS NOT NULL
  AND t.personneNonGPM IS NULL
;

ALTER TABLE CAUTIONS DROP CONSTRAINT fk_cautions_personnes;
ALTER TABLE CAUTIONS DROP idPersonne;


UPDATE CONFIG SET version = '16.0';