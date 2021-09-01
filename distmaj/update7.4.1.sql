ALTER TABLE CONFIG ADD confirmationSuppression VARCHAR(50);
UPDATE CONFIG SET confirmationSuppression = 'confirmation';

UPDATE ETATS SET libelleEtat='Activées' WHERE idEtat = 1;
UPDATE ETATS SET libelleEtat='Désactivées' WHERE idEtat = 2;

CREATE TABLE VEHICULES_RELEVES(
	idReleve INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVehicule INT,
	dateReleve DATE,
	releveKilometrique INT,
	idPersonne INT,
	CONSTRAINT fk_relevesVehicules_vehicule
		FOREIGN KEY (idVehicule)
		REFERENCES VEHICULES(idVehicule),
	CONSTRAINT fk_relevesVehicules_personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne)
);