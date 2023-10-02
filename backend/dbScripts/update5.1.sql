UPDATE VEHICULES SET assuranceExpiration = Null WHERE assuranceExpiration = '0000-00-00';
ALTER TABLE LOTS_LOTS ADD alerteConfRef BOOLEAN;

UPDATE CONFIG set version = '5.1';