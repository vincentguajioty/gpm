UPDATE CONFIG set version = '4.1';

ALTER TABLE VHF_CANAL CHANGE rxFreq rxFreq DOUBLE;
ALTER TABLE VHF_CANAL CHANGE txFreq txFreq DOUBLE;
ALTER TABLE VHF_CANAL CHANGE rxCtcss rxCtcss DOUBLE;
ALTER TABLE VHF_CANAL CHANGE txCtcss txCtcss DOUBLE;
ALTER TABLE VHF_CANAL CHANGE niveauCtcss niveauCtcss DOUBLE;
ALTER TABLE VHF_CANAL CHANGE txPower txPower DOUBLE;
ALTER TABLE VHF_CANAL CHANGE appelSelectifPorteuse appelSelectifPorteuse DOUBLE;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 4.1.');