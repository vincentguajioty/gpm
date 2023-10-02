CREATE TABLE PROFILS_PERSONNES(
	idPersonne INT,
	idProfil INT,
	CONSTRAINT fk_profilsPersonnes_personnes
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_profilsPersonnes_profils
		FOREIGN KEY (idProfil)
		REFERENCES PROFILS(idProfil));
		
INSERT INTO PROFILS_PERSONNES (idPersonne, idProfil) SELECT idPersonne, idProfil FROM PERSONNE_REFERENTE WHERE idProfil IS NOT NULL;

ALTER TABLE PERSONNE_REFERENTE DROP FOREIGN KEY fk_personne_profil;
ALTER TABLE PERSONNE_REFERENTE DROP idProfil;

ALTER TABLE PROFILS ADD todolist_perso BOOLEAN;
ALTER TABLE PROFILS ADD todolist_lecture BOOLEAN;
ALTER TABLE PROFILS ADD todolist_modification BOOLEAN;
ALTER TABLE PROFILS ADD contactMailGroupe BOOLEAN;

UPDATE PROFILS SET todolist_perso = 0, todolist_lecture = 0, todolist_modification = 0, contactMailGroupe = 0;

CREATE TABLE TODOLIST(
	idTache INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idCreateur INT,
	idExecutant INT,
	dateCreation DATETIME,
	dateExecution DATETIME,
	titre TEXT,
	details TEXT,
	priorite VARCHAR(50),
	realisee BOOLEAN,
	CONSTRAINT fk_todolist_createur
		FOREIGN KEY (idCreateur)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_todolist_executant
		FOREIGN KEY (idExecutant)
		REFERENCES PERSONNE_REFERENTE(idPersonne)
);

UPDATE CONFIG set version = '6.0';