ALTER TABLE CONFIG ADD consommation_benevoles BOOLEAN AFTER alertes_benevoles_vehicules;
UPDATE CONFIG set consommation_benevoles = false;

UPDATE CONFIG set version = '13.7';