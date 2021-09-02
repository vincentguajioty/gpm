UPDATE CONFIG set version = '8.3';

CREATE TABLE TODOLIST_PRIORITES(
	idTDLpriorite INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libellePriorite VARCHAR(100),
	couleurPriorite VARCHAR(100)
);

INSERT INTO TODOLIST_PRIORITES SET idTDLpriorite = 1, libellePriorite='Urgent', couleurPriorite='danger';
INSERT INTO TODOLIST_PRIORITES SET idTDLpriorite = 2, libellePriorite='Important', couleurPriorite='warning';
INSERT INTO TODOLIST_PRIORITES SET idTDLpriorite = 3, libellePriorite='Normal', couleurPriorite='success';
INSERT INTO TODOLIST_PRIORITES SET idTDLpriorite = 4, libellePriorite='Faible', couleurPriorite='primary';
INSERT INTO TODOLIST_PRIORITES SET idTDLpriorite = 5, libellePriorite='Optionel', couleurPriorite='info';

ALTER TABLE TODOLIST ADD idTDLpriorite INT;
ALTER TABLE TODOLIST ADD CONSTRAINT fk_TDL_pririte FOREIGN KEY (idTDLpriorite) REFERENCES TODOLIST_PRIORITES(idTDLpriorite);

UPDATE TODOLIST SET idTDLpriorite = 1 WHERE priorite='1 - Urgent';
UPDATE TODOLIST SET idTDLpriorite = 2 WHERE priorite='2 - Important';
UPDATE TODOLIST SET idTDLpriorite = 3 WHERE priorite='3 - Normal';
UPDATE TODOLIST SET idTDLpriorite = 4 WHERE priorite='4 - Faible';
UPDATE TODOLIST SET idTDLpriorite = 5 WHERE priorite='5 - Optionel';

ALTER TABLE TODOLIST DROP priorite;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 8.3.');