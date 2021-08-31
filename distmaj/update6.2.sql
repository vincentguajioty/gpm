UPDATE CONFIG set version = '6.2';
		
CREATE TABLE TODOLIST_PERSONNES(
	idTache INT,
	idExecutant INT,
	CONSTRAINT fk_todolist_tache
		FOREIGN KEY (idTache)
		REFERENCES TODOLIST(idTache),
	CONSTRAINT fk_todolist_personne
		FOREIGN KEY (idExecutant)
		REFERENCES PERSONNE_REFERENTE(idPersonne));

INSERT INTO TODOLIST_PERSONNES(idTache, idExecutant) SELECT idTache, idExecutant FROM TODOLIST WHERE idExecutant IS NOT NULL;

ALTER TABLE TODOLIST DROP FOREIGN KEY fk_todolist_executant;
ALTER TABLE TODOLIST DROP idExecutant;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 6.2.');