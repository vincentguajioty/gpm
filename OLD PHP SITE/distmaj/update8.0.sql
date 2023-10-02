UPDATE CONFIG set version = '8.0';

ALTER TABLE CENTRE_COUTS ADD commentairesCentreCout TEXT;

CREATE TABLE CENTRE_COUTS_OPERATIONS(
	idOperations INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	dateOperation DATETIME,
	libelleOperation TEXT,
	idCentreDeCout INT,
	montantEntrant FLOAT,
	montantSortant FLOAT,
	detailsMoyenTransaction TEXT,
	idPersonne INT,
	idCommande INT,
	CONSTRAINT fk_operationscouts_centre
		FOREIGN KEY (idCentreDeCout)
		REFERENCES CENTRE_COUTS(idCentreDeCout),
	CONSTRAINT fk_operationscouts_personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_operationscouts_commande
		FOREIGN KEY (idCommande)
		REFERENCES COMMANDES(idCommande)
);

ALTER TABLE COMMANDES ADD integreCentreCouts BOOLEAN;
UPDATE COMMANDES SET integreCentreCouts = FALSE;

CREATE TABLE DOCUMENTS_CENTRE_COUTS(
	idDocCouts INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idCentreDeCout	INT,
	nomDocCouts VARCHAR(100),
	formatDocCouts VARCHAR(10),
	dateDocCouts DATETIME,
	urlFichierDocCouts VARCHAR(50),
	idTypeDocument INT,
	CONSTRAINT fk_docCouts_type
		FOREIGN KEY (idTypeDocument)
		REFERENCES DOCUMENTS_TYPES(idTypeDocument),
	CONSTRAINT fk_docCouts_cdc
		FOREIGN KEY (idCentreDeCout)
		REFERENCES CENTRE_COUTS(idCentreDeCout));

