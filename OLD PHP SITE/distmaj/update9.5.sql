ALTER TABLE COMMANDES ADD nomCommande TEXT AFTER idCommande;

ALTER TABLE MESSAGES MODIFY corpsMessage TEXT;
UPDATE MESSAGES SET corpsMessage = CONCAT(titreMessage," - ",corpsMessage);
ALTER TABLE MESSAGES DROP titreMessage;

CREATE TABLE MESSAGES_TYPES(
	idMessageType INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleMessageType TEXT,
	couleurMessageType TEXT,
	iconMessageType TEXT
);
INSERT INTO MESSAGES_TYPES SET libelleMessageType='Information', couleurMessageType='alert-info', iconMessageType='fa-info';
INSERT INTO MESSAGES_TYPES SET libelleMessageType='Attention', couleurMessageType='alert-warning', iconMessageType='fa-exclamation';
INSERT INTO MESSAGES_TYPES SET libelleMessageType='Danger', couleurMessageType='alert-danger', iconMessageType='fa-warning';

ALTER TABLE MESSAGES ADD idMessageType INT;
ALTER TABLE MESSAGES ADD CONSTRAINT fk_messages_types FOREIGN KEY(idMessageType) REFERENCES MESSAGES_TYPES(idMessageType);

UPDATE MESSAGES SET idMessageType = 1;

CREATE TABLE CARBURANTS(
	idCarburant INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleCarburant TEXT
);
INSERT INTO CARBURANTS SET libelleCarburant='Diesel';
INSERT INTO CARBURANTS SET libelleCarburant='Essence';
INSERT INTO CARBURANTS SET libelleCarburant='Electricité';
INSERT INTO CARBURANTS SET libelleCarburant='Hybride';
INSERT INTO CARBURANTS SET libelleCarburant='GPL';

ALTER TABLE VEHICULES ADD idCarburant INT;
ALTER TABLE VEHICULES ADD CONSTRAINT fk_vehicules_carburant FOREIGN KEY(idCarburant) REFERENCES CARBURANTS(idCarburant);


UPDATE CONFIG set version = '9.5';