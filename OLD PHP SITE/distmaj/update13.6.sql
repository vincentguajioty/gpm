CREATE TABLE NOTIFICATIONS_MAILS(
	idMail INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	contexte VARCHAR(50),
	adresseDest TEXT,
	sujet TEXT,
	niveau INT,
	contenu TEXT,
	dateEntreeQueue DATETIME,
	dateEnvoiMail DATETIME,
	nombreRetry INT
);

UPDATE CONFIG set version = '13.6';