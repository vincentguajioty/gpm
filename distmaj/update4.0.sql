UPDATE CONFIG set version = '4.0';

CREATE TABLE VHF_TYPES_EQUIPEMENTS(
	idVhfType INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleType VARCHAR(100)
);

INSERT INTO VHF_TYPES_EQUIPEMENTS(idVhfType, libelleType) VALUES (1, 'Portative');
INSERT INTO VHF_TYPES_EQUIPEMENTS(idVhfType, libelleType) VALUES (2, 'Mobile');
INSERT INTO VHF_TYPES_EQUIPEMENTS(idVhfType, libelleType) VALUES (3, 'Base');
INSERT INTO VHF_TYPES_EQUIPEMENTS(idVhfType, libelleType) VALUES (4, 'Relais');

CREATE TABLE VHF_TECHNOLOGIES(
	idVhfTechno INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleTechno VARCHAR(100)
);

INSERT INTO VHF_TECHNOLOGIES(idVhfTechno, libelleTechno) VALUES (1, 'Analogique');
INSERT INTO VHF_TECHNOLOGIES(idVhfTechno, libelleTechno) VALUES (2, 'Numérique');
INSERT INTO VHF_TECHNOLOGIES(idVhfTechno, libelleTechno) VALUES (3, 'Analogique + Numérique');

CREATE TABLE VHF_ETATS(
	idVhfEtat INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleVhfEtat VARCHAR(100)
);

INSERT INTO VHF_ETATS(idVhfEtat, libelleVhfEtat) VALUES (1, 'Opérationnelle');
INSERT INTO VHF_ETATS(idVhfEtat, libelleVhfEtat) VALUES (2, 'Au rebus');
INSERT INTO VHF_ETATS(idVhfEtat, libelleVhfEtat) VALUES (3, 'Non Programmée');

CREATE TABLE VHF_CANAL(
	idVhfCanal INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	rxFreq FLOAT,
	txFreq FLOAT,
	rxCtcss FLOAT,
	txCtcss FLOAT,
	niveauCtcss FLOAT,
	txPower FLOAT,
	chName VARCHAR(50),
	appelSelectifCode VARCHAR(10),
	appelSelectifPorteuse FLOAT,
	let INT,
	notone INT,
	idVhfTechno INT,
	remarquesCanal VARCHAR(500),
	CONSTRAINT fk_techno_Canal
		FOREIGN KEY (idVhfTechno)
		REFERENCES VHF_TECHNOLOGIES(idVhfTechno));

CREATE TABLE VHF_PLAN(
	idVhfPlan INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libellePlan VARCHAR(100),
	remarquesPlan VARCHAR(500));

CREATE TABLE VHF_PLAN_CANAL(
	idVhfPlan INT,
	idVhfCanal INT,
	numeroCanal INT,
	CONSTRAINT fk_plan_plan
		FOREIGN KEY (idVhfPlan)
		REFERENCES VHF_PLAN(idVhfPlan),
	CONSTRAINT fk_plan_canal
		FOREIGN KEY (idVhfCanal)
		REFERENCES VHF_CANAL(idVhfCanal));

ALTER TABLE VHF_PLAN_CANAL ADD UNIQUE(idVhfPlan, numeroCanal);

CREATE TABLE VHF_EQUIPEMENTS(
	idVhfEquipement INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	vhfMarqueModele VARCHAR(100),
	vhfSN VARCHAR(100),
	vhfIndicatif VARCHAR(100),
	idVhfEtat INT,
	idVhfType INT,
	idVhfTechno INT,
	idVhfPlan INT,
	dateDerniereProg DATE,
	idResponsable INT,
	remarquesVhfEquipement VARCHAR(500),
	CONSTRAINT fk_vhf_etat
		FOREIGN KEY (idVhfEtat)
		REFERENCES VHF_ETATS(idVhfEtat),
	CONSTRAINT fk_vhf_type
		FOREIGN KEY (idVhfType)
		REFERENCES VHF_TYPES_EQUIPEMENTS(idVhfType),
	CONSTRAINT fk_vhf_techno
		FOREIGN KEY (idVhfTechno)
		REFERENCES VHF_TECHNOLOGIES(idVhfTechno),
	CONSTRAINT fk_vhf_plan
		FOREIGN KEY (idVhfPlan)
		REFERENCES VHF_PLAN(idVhfPlan),
	CONSTRAINT fk_vhf_responsable
		FOREIGN KEY (idResponsable)
		REFERENCES PERSONNE_REFERENTE(idPersonne)
);

INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(16,'Instructions de programmation VHF');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(17,'Plan de programmation VHF');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(18,'Export de programmation VHF');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(19,'Licence VHF');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(20,'Autorisation exploitation VHF');

CREATE TABLE DOCUMENTS_VHF(
	idDocVHF INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVhfEquipement INT,
	nomDocVHF VARCHAR(100),
	formatDocVHF VARCHAR(10),
	dateDocVHF DATETIME,
	urlFichierDocVHF VARCHAR(50),
	idTypeDocument INT,
	CONSTRAINT fk_docVHF_type
		FOREIGN KEY (idTypeDocument)
		REFERENCES DOCUMENTS_TYPES(idTypeDocument),
	CONSTRAINT fk_docVHF_VHF
		FOREIGN KEY (idVhfEquipement)
		REFERENCES VHF_EQUIPEMENTS(idVhfEquipement));

CREATE TABLE DOCUMENTS_PLAN_VHF(
	idDocPlanVHF INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVhfPlan INT,
	nomDocPlanVHF VARCHAR(100),
	formatDocPlanVHF VARCHAR(10),
	dateDocPlanVHF DATETIME,
	urlFichierDocPlanVHF VARCHAR(50),
	idTypeDocument INT,
	CONSTRAINT fk_docPlan_type
		FOREIGN KEY (idTypeDocument)
		REFERENCES DOCUMENTS_TYPES(idTypeDocument),
	CONSTRAINT fk_docPlan_Plan
		FOREIGN KEY (idVhfPlan)
		REFERENCES VHF_PLAN(idVhfPlan));

CREATE TABLE DOCUMENTS_CANAL_VHF(
	idDocCanalVHF INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVhfCanal INT,
	nomDocCanalVHF VARCHAR(100),
	formatDocCanalVHF VARCHAR(10),
	dateDocCanalVHF DATETIME,
	urlFichierDocCanalVHF VARCHAR(50),
	idTypeDocument INT,
	CONSTRAINT fk_docCanal_type
		FOREIGN KEY (idTypeDocument)
		REFERENCES DOCUMENTS_TYPES(idTypeDocument),
	CONSTRAINT fk_docCanal_Canal
		FOREIGN KEY (idVhfCanal)
		REFERENCES VHF_CANAL(idVhfCanal));


ALTER TABLE PROFILS ADD vhf_canal_lecture BOOLEAN;
ALTER TABLE PROFILS ADD vhf_canal_ajout BOOLEAN;
ALTER TABLE PROFILS ADD vhf_canal_modification BOOLEAN;
ALTER TABLE PROFILS ADD vhf_canal_suppression BOOLEAN;

ALTER TABLE PROFILS ADD vhf_plan_lecture BOOLEAN;
ALTER TABLE PROFILS ADD vhf_plan_ajout BOOLEAN;
ALTER TABLE PROFILS ADD vhf_plan_modification BOOLEAN;
ALTER TABLE PROFILS ADD vhf_plan_suppression BOOLEAN;

ALTER TABLE PROFILS ADD vhf_equipement_lecture BOOLEAN;
ALTER TABLE PROFILS ADD vhf_equipement_ajout BOOLEAN;
ALTER TABLE PROFILS ADD vhf_equipement_modification BOOLEAN;
ALTER TABLE PROFILS ADD vhf_equipement_suppression BOOLEAN;


UPDATE PROFILS set
vhf_canal_lecture = 0,
vhf_canal_ajout = 0,
vhf_canal_modification = 0,
vhf_canal_suppression = 0,
vhf_plan_lecture = 0,
vhf_plan_ajout = 0,
vhf_plan_modification = 0,
vhf_plan_suppression = 0,
vhf_equipement_lecture = 0,
vhf_equipement_ajout = 0,
vhf_equipement_modification = 0,
vhf_equipement_suppression = 0;