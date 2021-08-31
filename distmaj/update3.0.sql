UPDATE CONFIG set version = '3.0';

CREATE TABLE RESERVES_CONTENEUR(
	idConteneur INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idLieu INT,
	libelleConteneur VARCHAR(100),
	CONSTRAINT fk_reserveMalle_lieux
		FOREIGN KEY (idLieu)
		REFERENCES LIEUX(idLieu));

CREATE TABLE RESERVES_MATERIEL(
	idReserveElement INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idMaterielCatalogue INT,
	idConteneur INT,
	idFournisseur INT,
	quantiteReserve INT,
	quantiteAlerteReserve INT,
	peremptionReserve DATE,
	peremptionNotificationReserve DATE,
	commentairesReserveElement VARCHAR(500),
	CONSTRAINT fk_reserve_element_catalogue
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue),
	CONSTRAINT fk_reserve_element_emplacement
		FOREIGN KEY (idConteneur)
		REFERENCES RESERVES_CONTENEUR(idConteneur),
	CONSTRAINT fk_reserve_element_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur));
		
ALTER TABLE RESERVES_MATERIEL ADD UNIQUE(idMaterielCatalogue, idConteneur);
		
ALTER TABLE PROFILS ADD reserve_lecture BOOLEAN;
ALTER TABLE PROFILS ADD reserve_ajout BOOLEAN;
ALTER TABLE PROFILS ADD reserve_modification BOOLEAN;
ALTER TABLE PROFILS ADD reserve_suppression BOOLEAN;
ALTER TABLE PROFILS ADD reserve_cmdVersReserve BOOLEAN;
ALTER TABLE PROFILS ADD reserve_ReserveVersLot BOOLEAN;
UPDATE PROFILS set reserve_lecture = 0, reserve_ajout = 0, reserve_modification = 0, reserve_suppression = 0, reserve_cmdVersReserve = 0, reserve_ReserveVersLot = 0;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 3.0.');