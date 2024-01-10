ALTER TABLE PROFILS DROP etats_lecture;
ALTER TABLE PROFILS DROP etats_ajout;
ALTER TABLE PROFILS DROP etats_modification;
ALTER TABLE PROFILS DROP etats_suppression;

UPDATE LOTS_LOTS SET idEtat = Null;
DELETE FROM ETATS WHERE idEtat > 0;
INSERT INTO ETATS(idEtat, libelleEtat) VALUES(1, 'Opérationnel');
INSERT INTO ETATS(idEtat, libelleEtat) VALUES(2, 'Au rebus');

UPDATE CONFIG set version = '2.4';