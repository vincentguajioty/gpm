UPDATE CONFIG set version = '5.0';

CREATE TABLE VEHICULES_TYPES(
	idVehiculesType INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleType VARCHAR(100)
);

CREATE TABLE VEHICULES(
	idVehicule INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleVehicule VARCHAR(100),
	immatriculation VARCHAR(15),
	marqueModele VARCHAR(100),
	idLieu INT,
	kilometrage INT,
	nbPlaces INT,
	dimensions VARCHAR(10),
	idVehiculesType INT,
	idEtat INT,
	idResponsable INT,
	dateAchat DATE,
	dateNextRevision DATE,
	dateNextCT DATE,
	assuranceNumero VARCHAR(50),
	assuranceExpiration DATE,
	pneusAVhivers BOOLEAN,
	pneusARhivers BOOLEAN,
	climatisation BOOLEAN,
	signaletiqueOrange BOOLEAN,
	signaletiqueBleue BOOLEAN,
	signaletique2tons BOOLEAN,
	signaletique3tons BOOLEAN,
	pmv BOOLEAN,
	fleche BOOLEAN,
	nbCones INT,
	priseAlimentation220 BOOLEAN,
	remarquesVehicule VARCHAR(500),
	CONSTRAINT fk_vehicules_types
		FOREIGN KEY (idVehiculesType)
		REFERENCES VEHICULES_TYPES(idVehiculesType),
	CONSTRAINT fk_vehicules_personne
		FOREIGN KEY (idResponsable)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_vehicules_etat
		FOREIGN KEY (idEtat)
		REFERENCES ETATS(idEtat),
	CONSTRAINT fk_vehicules_lieux
		FOREIGN KEY (idLieu)
		REFERENCES LIEUX(idLieu));
		
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(21,'Carte grise');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(22,'Carte verte');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(23,'Bilan contrôle technique');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(24,'Bilan révision');

CREATE TABLE DOCUMENTS_VEHICULES(
	idDocVehicules INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVehicule	INT,
	nomDocVehicule VARCHAR(100),
	formatDocVehicule VARCHAR(10),
	dateDocVehicule DATETIME,
	urlFichierDocVehicule VARCHAR(50),
	idTypeDocument INT,
	CONSTRAINT fk_docVehicule_type
		FOREIGN KEY (idTypeDocument)
		REFERENCES DOCUMENTS_TYPES(idTypeDocument),
	CONSTRAINT fk_docVehicule_Vehicule
		FOREIGN KEY (idVehicule)
		REFERENCES VEHICULES(idVehicule));
		
CREATE TABLE VEHICULES_MAINTENANCE_TYPES(
	idTypeMaintenance INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleTypeMaintenance VARCHAR(50)
);

INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (1, 'Contrôle technique');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (2, 'Révision');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (3, 'Changements pneus');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (4, 'Vérification des niveaux');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (5, 'Pression des pneus');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (6, 'Autres');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (7, 'Opération pare-brise');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (8, 'Changement essuie-glace');
INSERT INTO VEHICULES_MAINTENANCE_TYPES (idTypeMaintenance, libelleTypeMaintenance) VALUES (9, 'Lavage');

CREATE TABLE VEHICULES_MAINTENANCE(
	idMaintenance INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVehicule INT,
	idExecutant INT,
	dateMaintenance DATETIME,
	idTypeMaintenance INT,
	detailsMaintenance VARCHAR (500),
	CONSTRAINT fk_maintenance_type
		FOREIGN KEY (idTypeMaintenance)
		REFERENCES VEHICULES_MAINTENANCE_TYPES(idTypeMaintenance),
	CONSTRAINT fk_maintenance_personne
		FOREIGN KEY (idExecutant)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_maintenance_Vehicule
		FOREIGN KEY (idVehicule)
		REFERENCES VEHICULES(idVehicule)
);
		
ALTER TABLE LOTS_LOTS ADD idVehicule INT;
ALTER TABLE LOTS_LOTS ADD CONSTRAINT fk_lots_vehicules FOREIGN KEY(idVehicule) REFERENCES VEHICULES(idVehicule);

ALTER TABLE PROFILS ADD vehicules_lecture BOOLEAN;
ALTER TABLE PROFILS ADD vehicules_ajout BOOLEAN;
ALTER TABLE PROFILS ADD vehicules_modification BOOLEAN;
ALTER TABLE PROFILS ADD vehicules_suppression BOOLEAN;

ALTER TABLE PROFILS ADD vehicules_types_lecture BOOLEAN;
ALTER TABLE PROFILS ADD vehicules_types_ajout BOOLEAN;
ALTER TABLE PROFILS ADD vehicules_types_modification BOOLEAN;
ALTER TABLE PROFILS ADD vehicules_types_suppression BOOLEAN;

UPDATE PROFILS set
vehicules_lecture = 0,
vehicules_ajout = 0,
vehicules_modification = 0,
vehicules_suppression = 0,
vehicules_types_lecture = 0,
vehicules_types_ajout = 0,
vehicules_types_modification = 0,
vehicules_types_suppression = 0;

ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur7Accueil INT;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur8Accueil INT;

UPDATE PERSONNE_REFERENTE SET conf_indicateur7Accueil = 1, conf_indicateur8Accueil = 1;