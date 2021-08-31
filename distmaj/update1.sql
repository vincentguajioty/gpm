SET @@auto_increment_increment=1;
SET @@auto_increment_offset=1;

CREATE TABLE LOGS_LEVEL(
	idLogLevel INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleLevel VARCHAR(100),
	faIcon VARCHAR(50));

CREATE TABLE LOGS(
	idLog INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	dateEvt DATETIME,
	adresseIP VARCHAR(40),
	utilisateurApollonEvt VARCHAR(100),
	idLogLevel INT,
	detailEvt VARCHAR(500),
	CONSTRAINT fk_logs_level
		FOREIGN KEY (idLogLevel)
		REFERENCES LOGS_LEVEL(idLogLevel));

CREATE TABLE PROFILS(
	idProfil INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleProfil VARCHAR(100),
	descriptifProfil VARCHAR(500),
	notifications BOOLEAN,
	connexion_connexion BOOLEAN,
	logs_lecture BOOLEAN,
	annuaire_lecture BOOLEAN,
	annuaire_ajout BOOLEAN,
	annuaire_modification BOOLEAN,
	annuaire_mdp BOOLEAN,
	annuaire_suppression BOOLEAN,
	profils_lecture BOOLEAN,
	profils_ajout BOOLEAN,
	profils_modification BOOLEAN,
	profils_suppression BOOLEAN,
	categories_lecture BOOLEAN,
	categories_ajout BOOLEAN,
	categories_modification BOOLEAN,
	categories_suppression BOOLEAN,
	fournisseurs_lecture BOOLEAN,
	fournisseurs_ajout BOOLEAN,
	fournisseurs_modification BOOLEAN,
	fournisseurs_suppression BOOLEAN,
	typesLots_lecture BOOLEAN,
	typesLots_ajout BOOLEAN,
	typesLots_modification BOOLEAN,
	typesLots_suppression BOOLEAN,
	etats_lecture BOOLEAN,
	etats_ajout BOOLEAN,
	etats_modification BOOLEAN,
	etats_suppression BOOLEAN,
	lieux_lecture BOOLEAN,
	lieux_ajout BOOLEAN,
	lieux_modification BOOLEAN,
	lieux_suppression BOOLEAN,
	lots_lecture BOOLEAN,
	lots_ajout BOOLEAN,
	lots_modification BOOLEAN,
	lots_suppression BOOLEAN,
	sac_lecture BOOLEAN,
	sac_ajout BOOLEAN,
	sac_modification BOOLEAN,
	sac_suppression BOOLEAN,
	sac2_lecture BOOLEAN,
	sac2_ajout BOOLEAN,
	sac2_modification BOOLEAN,
	sac2_suppression BOOLEAN,
	catalogue_lecture BOOLEAN,
	catalogue_ajout BOOLEAN,
	catalogue_modification BOOLEAN,
	catalogue_suppression BOOLEAN,
	materiel_lecture BOOLEAN,
	materiel_ajout BOOLEAN,
	materiel_modification BOOLEAN,
	materiel_suppression BOOLEAN,
	messages_ajout BOOLEAN,
	messages_suppression BOOLEAN,
	messages_chat INT);

CREATE TABLE PERSONNE_REFERENTE(
	idPersonne INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idProfil INT,
	identifiant VARCHAR(100),
	motDePasse VARCHAR(100),
	nomPersonne VARCHAR(100),
	prenomPersonne VARCHAR(100),
	mailPersonne VARCHAR(100),
	telPersonne VARCHAR(100),
	fonction VARCHAR(100),
	CONSTRAINT fk_personne_profil
		FOREIGN KEY (idProfil)
		REFERENCES PROFILS(idProfil));

CREATE TABLE MESSAGES(
	idMessage INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idPersonne INT,
	titreMessage VARCHAR(50),
	corpsMessage VARCHAR(500),
	CONSTRAINT fk_messages_persones
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne));

CREATE TABLE MATERIEL_CATEGORIES(
	idCategorie INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleCategorie VARCHAR(100));

CREATE TABLE FOURNISSEURS(
	idFournisseur INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	nomFournisseur VARCHAR(100),
	adresseFournisseur VARCHAR(150),
	telephoneFournisseur VARCHAR(150),
	mailFournisseur VARCHAR(100));

CREATE TABLE ETATS(
	idEtat INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleEtat VARCHAR(100));

CREATE TABLE LOTS_TYPES(
	idTypeLot INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleTypeLot VARCHAR(100));

CREATE TABLE LIEUX(
	idLieu INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleLieu VARCHAR(100),
	adresseLieu VARCHAR(150),
	detailsLieu VARCHAR(200),
	accesReserve BOOLEAN);

CREATE TABLE LOTS_LOTS(
	idLot INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleLot VARCHAR(100),
	idTypeLot INT,
	idEtat INT,
	idLieu INT,
	idPersonne INT,
	dateDernierInventaire DATE,
	frequenceInventaire INT,
	commentairesLots VARCHAR(500),
	CONSTRAINT fk_lot_type
		FOREIGN KEY (idTypeLot)
		REFERENCES LOTS_TYPES(idTypeLot),
	CONSTRAINT fk_lot_etat
		FOREIGN KEY (idEtat)
		REFERENCES ETATS(idEtat),
	CONSTRAINT fk_lot_lieu
		FOREIGN KEY (idLieu)
		REFERENCES LIEUX(idLieu),
	CONSTRAINT fk_lot_personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne));

CREATE TABLE MATERIEL_CATALOGUE(
	idMaterielCatalogue INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleMateriel VARCHAR(100),
	idCategorie INT,
	taille VARCHAR(100),
	sterilite BOOLEAN,
	conditionnementMultiple VARCHAR(100),
	commentairesMateriel VARCHAR(500),
	CONSTRAINT fk_referentiel_categorie
		FOREIGN KEY (idCategorie)
		REFERENCES MATERIEL_CATEGORIES(idCategorie));

CREATE TABLE REFERENTIELS(
	idMaterielCatalogue INT,
	idTypeLot INT,
	quantiteReferentiel INT,
	obligatoire BOOLEAN,
	commentairesReferentiel VARCHAR(500),
	CONSTRAINT fk_referentiel_catalogue
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue),
	CONSTRAINT fk_referentiel_lot
		FOREIGN KEY (idTypeLot)
		REFERENCES LOTS_TYPES(idTypeLot));

CREATE TABLE MATERIEL_SAC(
	idSac INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleSac VARCHAR(100),
	idLot INT,
	taille VARCHAR(50),
	couleur VARCHAR(000),
	idFournisseur INT,
	CONSTRAINT fk_sac_lot
		FOREIGN KEY (idLot)
		REFERENCES LOTS_LOTS(idLot),
	CONSTRAINT fk_sac_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur));

CREATE TABLE MATERIEL_EMPLACEMENT(
	idEmplacement INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleEmplacement VARCHAR(100),
	idSac INT,
	CONSTRAINT fk_emplacement_sac
		FOREIGN KEY (idSac)
		REFERENCES MATERIEL_SAC(idSac));

CREATE TABLE MATERIEL_ELEMENT(
	idElement INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idMaterielCatalogue INT,
	idEmplacement INT,
	idFournisseur INT,
	quantite INT,
	quantiteAlerte INT,
	peremption DATE,
	peremptionNotification DATE,
	commentairesElement VARCHAR(500),
	CONSTRAINT fk_element_catalogue
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue),
	CONSTRAINT fk_element_emplacement
		FOREIGN KEY (idEmplacement)
		REFERENCES MATERIEL_EMPLACEMENT(idEmplacement),
	CONSTRAINT fk_element_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur));

CREATE TABLE INVENTAIRES(
	idInventaire INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idLot INT,
	dateInventaire DATE,
	idPersonne INT,
	commentairesInventaire VARCHAR(500),
	CONSTRAINT fk_inventaire_lot
		FOREIGN KEY (idLot)
		REFERENCES LOTS_LOTS(idLot),
	CONSTRAINT fk_inventaire_personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne));

CREATE TABLE INVENTAIRES_CONTENUS(
	idInventaire INT,
	idMaterielCatalogue INT,
	quantiteInventaire INT,
	peremptionInventaire DATE,
	CONSTRAINT fk_inventaireContenu_inventaire
		FOREIGN KEY (idInventaire)
		REFERENCES INVENTAIRES(idInventaire),
	CONSTRAINT fk_inventaireContenu_materiel
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue));


ALTER TABLE MATERIEL_EMPLACEMENT ADD UNIQUE(libelleEmplacement, idSac);
ALTER TABLE MATERIEL_SAC ADD UNIQUE(libelleSac, idLot);
ALTER TABLE REFERENTIELS ADD UNIQUE(idMaterielCatalogue, idTypeLot);
ALTER TABLE MATERIEL_CATALOGUE ADD UNIQUE(libelleMateriel);
ALTER TABLE LOTS_LOTS ADD UNIQUE(libelleLot, idTypeLot, idLieu);
ALTER TABLE LIEUX ADD UNIQUE(libelleLieu);
ALTER TABLE LOTS_TYPES ADD UNIQUE(libelleTypeLot);
ALTER TABLE ETATS ADD UNIQUE(libelleEtat);
ALTER TABLE FOURNISSEURS ADD UNIQUE(nomFournisseur);
ALTER TABLE MATERIEL_CATEGORIES ADD UNIQUE(libelleCategorie);
ALTER TABLE PERSONNE_REFERENTE ADD UNIQUE(identifiant);
ALTER TABLE PROFILS ADD UNIQUE(libelleProfil);
ALTER TABLE MATERIEL_ELEMENT ADD UNIQUE(idMaterielCatalogue, idEmplacement);


INSERT INTO PROFILS(libelleProfil, descriptifProfil, connexion_connexion, logs_lecture, annuaire_lecture, annuaire_ajout, annuaire_modification, annuaire_mdp, annuaire_suppression, profils_lecture, profils_ajout, profils_modification, profils_suppression, categories_lecture, categories_ajout, categories_modification, categories_suppression, fournisseurs_lecture, fournisseurs_ajout, fournisseurs_modification, fournisseurs_suppression, typesLots_lecture, typesLots_ajout, typesLots_modification, typesLots_suppression, etats_lecture, etats_ajout, etats_modification, etats_suppression, lieux_lecture, lieux_ajout, lieux_modification, lieux_suppression, lots_lecture, lots_ajout, lots_modification, lots_suppression, sac_lecture, sac_ajout, sac_modification, sac_suppression, sac2_lecture, sac2_ajout, sac2_modification, sac2_suppression, catalogue_lecture, catalogue_ajout, catalogue_modification, catalogue_suppression, materiel_lecture, materiel_ajout, materiel_modification, materiel_suppression, messages_ajout, messages_suppression, messages_chat, notifications) VALUES('Administrateur notifications activées', 'Administrateur de toute la solution GPM', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO PROFILS(libelleProfil, descriptifProfil, connexion_connexion, logs_lecture, annuaire_lecture, annuaire_ajout, annuaire_modification, annuaire_mdp, annuaire_suppression, profils_lecture, profils_ajout, profils_modification, profils_suppression, categories_lecture, categories_ajout, categories_modification, categories_suppression, fournisseurs_lecture, fournisseurs_ajout, fournisseurs_modification, fournisseurs_suppression, typesLots_lecture, typesLots_ajout, typesLots_modification, typesLots_suppression, etats_lecture, etats_ajout, etats_modification, etats_suppression, lieux_lecture, lieux_ajout, lieux_modification, lieux_suppression, lots_lecture, lots_ajout, lots_modification, lots_suppression, sac_lecture, sac_ajout, sac_modification, sac_suppression, sac2_lecture, sac2_ajout, sac2_modification, sac2_suppression, catalogue_lecture, catalogue_ajout, catalogue_modification, catalogue_suppression, materiel_lecture, materiel_ajout, materiel_modification, materiel_suppression, messages_ajout, messages_suppression, messages_chat, notifications) VALUES('Administrateur notifications désactivées', 'Administrateur de toute la solution GPM', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0);
INSERT INTO PROFILS(libelleProfil, descriptifProfil, connexion_connexion, logs_lecture, annuaire_lecture, annuaire_ajout, annuaire_modification, annuaire_mdp, annuaire_suppression, profils_lecture, profils_ajout, profils_modification, profils_suppression, categories_lecture, categories_ajout, categories_modification, categories_suppression, fournisseurs_lecture, fournisseurs_ajout, fournisseurs_modification, fournisseurs_suppression, typesLots_lecture, typesLots_ajout, typesLots_modification, typesLots_suppression, etats_lecture, etats_ajout, etats_modification, etats_suppression, lieux_lecture, lieux_ajout, lieux_modification, lieux_suppression, lots_lecture, lots_ajout, lots_modification, lots_suppression, sac_lecture, sac_ajout, sac_modification, sac_suppression, sac2_lecture, sac2_ajout, sac2_modification, sac2_suppression, catalogue_lecture, catalogue_ajout, catalogue_modification, catalogue_suppression, materiel_lecture, materiel_ajout, materiel_modification, materiel_suppression, messages_ajout, messages_suppression, messages_chat, notifications) VALUES('Désactivé', 'Profil permettant de ne pas supprimer les utilisateurs mais de leur couper tous leurs accès afin de les garder en historique.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);


INSERT INTO PERSONNE_REFERENTE(identifiant, motDePasse, idProfil, nomPersonne, prenomPersonne, mailPersonne, telPersonne, fonction) VALUES('admin', '$2y$10$84K4OnVGoX7b2iABahZPq.tD/2d9IzvuI/f9SQKm7u2Z1/uEMTP2e', 1, 'Administrateur', 'Administrateur', 'admin@admin.fr', '0023456789', 'Membre du comité de direction');

INSERT INTO ETATS(libelleEtat) VALUES('Opérationnel');
INSERT INTO ETATS(libelleEtat) VALUES('Au rebus');

INSERT INTO LOGS_LEVEL(idLogLevel, libelleLevel, faIcon) VALUES(0, 'Connexion', 'fa fa-expeditedssl bg-blue');
INSERT INTO LOGS_LEVEL(idLogLevel, libelleLevel, faIcon) VALUES(2, 'Ajout', 'fa fa-plus-circle bg-green');
INSERT INTO LOGS_LEVEL(idLogLevel, libelleLevel, faIcon) VALUES(3, 'Modification', 'fa fa-pencil-square-o bg-orange');
INSERT INTO LOGS_LEVEL(idLogLevel, libelleLevel, faIcon) VALUES(4, 'Suppression', 'fa fa-trash bg-red');
INSERT INTO LOGS_LEVEL(idLogLevel, libelleLevel, faIcon) VALUES(5, 'Erreur', 'fa fa-warning bg-black');

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurApollonEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'Serveur Principal', 'Vincent Guajioty', 2, 'Déploiement de la base de données GPM.');
INSERT INTO LOGS(dateEvt, adresseIP, utilisateurApollonEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'Serveur Principal', 'Vincent Guajioty', 2, 'Installation de l\'interface web.');
INSERT INTO LOGS(dateEvt, adresseIP, utilisateurApollonEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'Serveur Principal', 'Vincent Guajioty', 2, 'Création de l\'utilisateur admin et de son profil administrateur.');

ALTER TABLE PROFILS ADD verrouIP BOOLEAN;
UPDATE PROFILS SET verrouIP=0;

CREATE TABLE VERROUILLAGE_IP(
	idIP INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	adresseIPverr VARCHAR(40),
	dateVerr DATETIME
);

ALTER TABLE PROFILS DROP messages_chat;

ALTER TABLE PROFILS ADD commande_lecture BOOLEAN;
ALTER TABLE PROFILS ADD commande_ajout BOOLEAN;
ALTER TABLE PROFILS ADD commande_valider BOOLEAN;
ALTER TABLE PROFILS ADD commande_etreEnCharge BOOLEAN;
ALTER TABLE PROFILS ADD commande_abandonner BOOLEAN;
UPDATE PROFILS SET commande_lecture=0, commande_ajout=0, commande_valider=0, commande_etreEnCharge=0, commande_abandonner=0;

ALTER TABLE PROFILS ADD cout_lecture BOOLEAN;
ALTER TABLE PROFILS ADD cout_ajout BOOLEAN;
ALTER TABLE PROFILS ADD cout_etreEnCharge BOOLEAN;
ALTER TABLE PROFILS ADD cout_supprimer BOOLEAN;
UPDATE PROFILS SET cout_lecture=0, cout_ajout=0, cout_etreEnCharge=0, cout_supprimer=0;

CREATE TABLE CENTRE_COUTS(
	idCentreDeCout INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleCentreDecout VARCHAR(100),
	idResponsable INT,
	CONSTRAINT fk_couts_responsable
		FOREIGN KEY (idResponsable)
		REFERENCES PERSONNE_REFERENTE(idPersonne)
);

CREATE TABLE COMMANDES_ETATS(
	idEtat INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleEtat VARCHAR(100)
);

INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(1, 'Nouveau');
INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(2, 'Attente de validation');
INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(3, 'Validation OK');
INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(4, 'Attente de livraison');
INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(5, 'Livraison OK');
INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(6, 'SAV');
INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(7, 'Clos');
INSERT INTO COMMANDES_ETATS(idEtat, libelleEtat) VALUES(8, 'Abandonnée');

CREATE TABLE COMMANDES(
	idCommande INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idEtat INT,
	idDemandeur INT,
	idObservateur INT,
	idValideur INT,
	idAffectee INT,
	idCentreDeCout INT,
	idFournisseur INT,
	idLieuLivraison INT,
	numCommandeFournisseur VARCHAR(50),
	dateCreation DATETIME,
	dateDemandeValidation DATETIME,
	dateValidation DATETIME,
	datePassage DATETIME,
	dateLivraisonPrevue DATETIME,
	dateLivraisoneffective DATETIME,
	dateCloture DATETIME,
	remarquesGenerales VARCHAR(1000),
	remarquesValidation VARCHAR(1000),
	remarquesLivraison VARCHAR(1000),
	CONSTRAINT fk_commande_etat
		FOREIGN KEY (idEtat)
		REFERENCES COMMANDES_ETATS(idEtat),
	CONSTRAINT fk_commande_demandeur
		FOREIGN KEY (idDemandeur)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_commande_observateur
		FOREIGN KEY (idObservateur)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_commande_valideur
		FOREIGN KEY (idValideur)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_commande_affectee
		FOREIGN KEY (idAffectee)
		REFERENCES PERSONNE_REFERENTE(idPersonne),
	CONSTRAINT fk_commande_cout
		FOREIGN KEY (idCentreDeCout)
		REFERENCES CENTRE_COUTS(idCentreDeCout),
	CONSTRAINT fk_commande_fournisseur
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur),
	CONSTRAINT fk_commande_lieu
		FOREIGN KEY (idLieuLivraison)
		REFERENCES LIEUX(idLieu)
);

CREATE TABLE COMMANDES_MATERIEL(
	idCommande INT,
	idMaterielCatalogue INT,
	quantiteCommande INT,
	referenceProduitFournisseur VARCHAR(500),
	remiseProduit FLOAT,
	prixProduitHT FLOAT,
	taxeProduit FLOAT,
	prixProduitTTC FLOAT,
	remarqueArticle VARCHAR(500),
	CONSTRAINT fk_commande_commande
		FOREIGN KEY (idCommande)
		REFERENCES COMMANDES(idCommande),
	CONSTRAINT fk_commande_materiel
		FOREIGN KEY (idMaterielCatalogue)
		REFERENCES MATERIEL_CATALOGUE(idMaterielCatalogue)
);

CREATE TABLE COMMANDES_TIMELINE_ICON(
	idComIcon INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	iconFontAsw VARCHAR(20),
	iconColor VARCHAR(20)
);

INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(1, 'fa-plus', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(2, 'fa-plus', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(3, 'fa-plus', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(4, 'fa-plus', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(5, 'fa-minus', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(6, 'fa-minus', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(7, 'fa-minus', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(8, 'fa-minus', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(9, 'fa-file-o', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(10, 'fa-file-o', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(11, 'fa-file-o', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(12, 'fa-file-o', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(13, 'fa-check', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(14, 'fa-check', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(15, 'fa-check', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(16, 'fa-check', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(17, 'fa-close', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(18, 'fa-close', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(19, 'fa-close', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(20, 'fa-close', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(21, 'fa-euro', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(22, 'fa-euro', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(23, 'fa-euro', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(24, 'fa-euro', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(25, 'fa-truck', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(26, 'fa-truck', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(27, 'fa-truck', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(28, 'fa-truck', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(29, 'fa-support', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(30, 'fa-support', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(31, 'fa-support', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(32, 'fa-support', 'blue');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(33, 'fa-commenting', 'green');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(34, 'fa-commenting', 'orange');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(35, 'fa-commenting', 'red');
INSERT INTO COMMANDES_TIMELINE_ICON(idComIcon, iconFontAsw, iconColor) VALUES(36, 'fa-commenting', 'blue');

CREATE TABLE COMMANDES_TIMELINE(
	idEvtCommande INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idCommande INT,
	dateEvtCommande DATETIME,
	detailsEvtCommande VARCHAR(1000),
	idComIcon INT,
	CONSTRAINT fk_commande_log
		FOREIGN KEY (idCommande)
		REFERENCES COMMANDES(idCommande),
	CONSTRAINT fk_commande_icon
		FOREIGN KEY (idComIcon)
		REFERENCES COMMANDES_TIMELINE_ICON(idComIcon)
);

CREATE TABLE VERROUILLAGE_IP_TEMP(
	idTemp INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	dateEchec DATETIME,
	adresseIP VARCHAR(40)
);

ALTER TABLE LOGS CHANGE utilisateurApollonEvt utilisateurEvt VARCHAR(100);

ALTER TABLE PERSONNE_REFERENTE ADD derniereConnexion DATETIME;

CREATE TABLE CONFIG(
	appname VARCHAR(50),
	sitecolor VARCHAR(50),
	urlsite VARCHAR(50),
	version VARCHAR(10),
	mailserver VARCHAR(50),
	logouttemp INT
);
INSERT INTO CONFIG(appname, sitecolor, urlsite, version, mailserver, logouttemp)VALUES('GPM', 'blue', 'https://gpm.test.fr', '2.2', 'contact@test.fr', 60);

ALTER TABLE PROFILS ADD appli_conf BOOLEAN;
UPDATE PROFILS set appli_conf = 0;


CREATE TABLE DOCUMENTS_TYPES(
	idTypeDocument INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleTypeDocument VARCHAR(100)
);
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(1,'Devis');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(2,'Contrat');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(3,'Facture');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(4,'Bon de commande');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(5,'Bon de réception');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(6,'Avis de passage');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(7,'Notice d\'utilisation');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(8,'Bon de retour');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(9,'Compte rendu de SAV');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(10,'Scan de cheque');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(11,'Scan de document officiel');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(12,'Photo');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(13,'Archive');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(14,'Video');
INSERT INTO DOCUMENTS_TYPES (idTypeDocument, libelleTypeDocument)VALUES(15,'Bande son');

CREATE TABLE DOCUMENTS_COMMANDES(
	idDocCommande INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idCommande INT,
	nomDocCommande VARCHAR(100),
	formatDocCommande VARCHAR(10),
	dateDocCommande DATETIME,
	urlFichierDocCommande VARCHAR(50),
	idTypeDocument INT,
	CONSTRAINT fk_docCommande_type
		FOREIGN KEY (idTypeDocument)
		REFERENCES DOCUMENTS_TYPES(idTypeDocument),
	CONSTRAINT fk_docCommande_commande
		FOREIGN KEY (idCommande)
		REFERENCES COMMANDES(idCommande));