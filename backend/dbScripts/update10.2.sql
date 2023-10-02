ALTER TABLE VEHICULES_MAINTENANCE CHANGE dateMaintenance dateMaintenance DATE;

ALTER TABLE VEHICULES_MAINTENANCE ADD releveKilometrique INT;
ALTER TABLE VEHICULES_HEALTH ADD releveKilometrique INT;

CREATE OR REPLACE VIEW VIEW_VEHICULES_KM AS
	(SELECT idReleve, idVehicule, dateReleve, releveKilometrique, idPersonne FROM VEHICULES_RELEVES)
	UNION
	(SELECT NULL as idReleve, idVehicule, dateMaintenance as dateReleve, releveKilometrique, idExecutant as idPersonne  FROM VEHICULES_MAINTENANCE WHERE releveKilometrique IS NOT NULL)
	UNION
	(SELECT NULL as idReleve, idVehicule, dateHealth as dateReleve, releveKilometrique, idPersonne  FROM VEHICULES_HEALTH WHERE releveKilometrique IS NOT NULL)
;

ALTER TABLE VEHICULES ADD couleurGraph VARCHAR(10);

ALTER TABLE CENTRE_COUTS ADD dateOuverture DATE;
ALTER TABLE CENTRE_COUTS ADD dateFermeture DATE;

UPDATE CONFIG set version = '10.2';