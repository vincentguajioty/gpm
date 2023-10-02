CREATE TABLE CENTRE_COUTS_PERSONNES(
	idCentreDeCout INT,
	idPersonne INT,
	CONSTRAINT fk_centreCouts_couts
		FOREIGN KEY (idCentreDeCout)
		REFERENCES CENTRE_COUTS(idCentreDeCout),
	CONSTRAINT fk_centreCouts_profils
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne));