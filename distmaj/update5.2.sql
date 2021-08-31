UPDATE CONFIG set version = '5.2';

CREATE TABLE RESERVES_INVENTAIRES(
	idReserveInventaire INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idConteneur INT,
	dateInventaire DATE,
	idPersonne INT,
	commentairesInventaire VARCHAR(500),
	CONSTRAINT fk_inventaireReserve_personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_inventaireReserve_conteneur
		FOREIGN KEY (idConteneur)
		REFERENCES RESERVES_CONTENEUR(idConteneur));

CREATE TABLE RESERVES_INVENTAIRES_CONTENUS(
	idReserveInventaire INT,
	idMaterielCatalogue INT,
	quantiteInventaire INT,
	peremptionInventaire DATE,
	CONSTRAINT fk_inventaireContenuReserve_inventaire
		FOREIGN KEY (idReserveInventaire)
		REFERENCES RESERVES_INVENTAIRES(idReserveInventaire),
	CONSTRAINT fk_inventaireContenuReserve_materiel
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue));

ALTER TABLE RESERVES_CONTENEUR ADD dateDernierInventaire DATE;
ALTER TABLE RESERVES_CONTENEUR ADD frequenceInventaire INT;

UPDATE RESERVES_CONTENEUR SET dateDernierInventaire = '2017-01-01';
UPDATE RESERVES_CONTENEUR SET frequenceInventaire = 1;

ALTER TABLE PERSONNE_REFERENTE ADD conf_accueilRefresh INT;
UPDATE PERSONNE_REFERENTE SET conf_accueilRefresh = 300;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 5.2.');