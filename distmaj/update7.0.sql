UPDATE CONFIG set version = '7.0';

ALTER TABLE PROFILS ADD tenues_lecture BOOLEAN;
ALTER TABLE PROFILS ADD tenues_ajout BOOLEAN;
ALTER TABLE PROFILS ADD tenues_modification BOOLEAN;
ALTER TABLE PROFILS ADD tenues_suppression BOOLEAN;
ALTER TABLE PROFILS ADD tenuesCatalogue_lecture BOOLEAN;
ALTER TABLE PROFILS ADD tenuesCatalogue_ajout BOOLEAN;
ALTER TABLE PROFILS ADD tenuesCatalogue_modification BOOLEAN;
ALTER TABLE PROFILS ADD tenuesCatalogue_suppression BOOLEAN;

UPDATE PROFILS SET
	tenues_lecture = 0,
	tenues_ajout = 0,
	tenues_modification = 0,
	tenues_suppression = 0,
	tenuesCatalogue_lecture = 0,
	tenuesCatalogue_ajout = 0,
	tenuesCatalogue_modification = 0,
	tenuesCatalogue_suppression = 0;

DROP VIEW IF EXISTS VIEW_HABILITATIONS;
CREATE OR REPLACE VIEW VIEW_HABILITATIONS AS
	SELECT
		p.idPersonne,
		(SELECT MAX(connexion_connexion) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as connexion_connexion,
		(SELECT MAX(logs_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as logs_lecture,
		(SELECT MAX(annuaire_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_lecture,
		(SELECT MAX(annuaire_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_ajout,
		(SELECT MAX(annuaire_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_modification,
		(SELECT MAX(annuaire_mdp) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_mdp,
		(SELECT MAX(annuaire_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_suppression,
		(SELECT MAX(profils_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_lecture,
		(SELECT MAX(profils_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_ajout,
		(SELECT MAX(profils_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_modification,
		(SELECT MAX(profils_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_suppression,
		(SELECT MAX(categories_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_lecture,
		(SELECT MAX(categories_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_ajout,
		(SELECT MAX(categories_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_modification,
		(SELECT MAX(categories_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_suppression,
		(SELECT MAX(fournisseurs_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_lecture,
		(SELECT MAX(fournisseurs_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_ajout,
		(SELECT MAX(fournisseurs_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_modification,
		(SELECT MAX(fournisseurs_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_suppression,
		(SELECT MAX(typesLots_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_lecture,
		(SELECT MAX(typesLots_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_ajout,
		(SELECT MAX(typesLots_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_modification,
		(SELECT MAX(typesLots_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_suppression,
		(SELECT MAX(lieux_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_lecture,
		(SELECT MAX(lieux_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_ajout,
		(SELECT MAX(lieux_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_modification,
		(SELECT MAX(lieux_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_suppression,
		(SELECT MAX(lots_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_lecture,
		(SELECT MAX(lots_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_ajout,
		(SELECT MAX(lots_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_modification,
		(SELECT MAX(lots_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_suppression,
		(SELECT MAX(sac_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_lecture,
		(SELECT MAX(sac_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_ajout,
		(SELECT MAX(sac_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_modification,
		(SELECT MAX(sac_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_suppression,
		(SELECT MAX(sac2_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_lecture,
		(SELECT MAX(sac2_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_ajout,
		(SELECT MAX(sac2_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_modification,
		(SELECT MAX(sac2_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_suppression,
		(SELECT MAX(catalogue_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_lecture,
		(SELECT MAX(catalogue_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_ajout,
		(SELECT MAX(catalogue_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_modification,
		(SELECT MAX(catalogue_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_suppression,
		(SELECT MAX(materiel_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_lecture,
		(SELECT MAX(materiel_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_ajout,
		(SELECT MAX(materiel_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_modification,
		(SELECT MAX(materiel_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_suppression,
		(SELECT MAX(messages_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as messages_ajout,
		(SELECT MAX(messages_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as messages_suppression,
		(SELECT MAX(verrouIP) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as verrouIP,
		(SELECT MAX(commande_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_lecture,
		(SELECT MAX(commande_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_ajout,
		(SELECT MAX(commande_valider) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_valider,
		(SELECT MAX(commande_etreEnCharge) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_etreEnCharge,
		(SELECT MAX(commande_abandonner) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_abandonner,
		(SELECT MAX(cout_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_lecture,
		(SELECT MAX(cout_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_ajout,
		(SELECT MAX(cout_etreEnCharge) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_etreEnCharge,
		(SELECT MAX(cout_supprimer) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_supprimer,
		(SELECT MAX(appli_conf) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as appli_conf,
		(SELECT MAX(reserve_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_lecture,
		(SELECT MAX(reserve_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_ajout,
		(SELECT MAX(reserve_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_modification,
		(SELECT MAX(reserve_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_suppression,
		(SELECT MAX(reserve_cmdVersReserve) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_cmdVersReserve,
		(SELECT MAX(reserve_ReserveVersLot) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_ReserveVersLot,
		(SELECT MAX(vhf_canal_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_lecture,
		(SELECT MAX(vhf_canal_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_ajout,
		(SELECT MAX(vhf_canal_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_modification,
		(SELECT MAX(vhf_canal_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_suppression,
		(SELECT MAX(vhf_plan_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_lecture,
		(SELECT MAX(vhf_plan_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_ajout,
		(SELECT MAX(vhf_plan_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_modification,
		(SELECT MAX(vhf_plan_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_suppression,
		(SELECT MAX(vhf_equipement_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_lecture,
		(SELECT MAX(vhf_equipement_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_ajout,
		(SELECT MAX(vhf_equipement_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_modification,
		(SELECT MAX(vhf_equipement_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_suppression,
		(SELECT MAX(vehicules_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_lecture,
		(SELECT MAX(vehicules_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_ajout,
		(SELECT MAX(vehicules_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_modification,
		(SELECT MAX(vehicules_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_suppression,
		(SELECT MAX(vehicules_types_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_lecture,
		(SELECT MAX(vehicules_types_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_ajout,
		(SELECT MAX(vehicules_types_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_modification,
		(SELECT MAX(vehicules_types_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_suppression,
		(SELECT MAX(tenues_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_lecture,
		(SELECT MAX(tenues_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_ajout,
		(SELECT MAX(tenues_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_modification,
		(SELECT MAX(tenues_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_suppression,
		(SELECT MAX(tenuesCatalogue_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_lecture,
		(SELECT MAX(tenuesCatalogue_ajout) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_ajout,
		(SELECT MAX(tenuesCatalogue_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_modification,
		(SELECT MAX(tenuesCatalogue_suppression) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_suppression,
		(SELECT MAX(maintenance) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as maintenance,
		(SELECT MAX(todolist_perso) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_perso,
		(SELECT MAX(todolist_lecture) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_lecture,
		(SELECT MAX(todolist_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_modification,
		(SELECT MAX(contactMailGroupe) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as contactMailGroupe
	FROM
		PERSONNE_REFERENTE p
;

CREATE TABLE TENUES_CATALOGUE(
	idCatalogueTenue INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleCatalogueTenue VARCHAR(100),
	tailleCatalogueTenue VARCHAR(20),
	serigraphieCatalogueTenue VARCHAR(20),
	idFournisseur INT,
	stockCatalogueTenue INT,
	stockAlerteCatalogueTenue INT,
	CONSTRAINT fk_fournisseurs_tenues
		FOREIGN KEY (idFournisseur)
		REFERENCES FOURNISSEURS(idFournisseur));
		
CREATE TABLE TENUES_AFFECTATION(
	idTenue INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idCatalogueTenue INT,
	idPersonne INT,
	personneNonGPM VARCHAR(200),
	dateAffectation DATE,
	dateRetour DATE,
	CONSTRAINT fk_tenuesAff_Catalogue
		FOREIGN KEY (idCatalogueTenue)
		REFERENCES TENUES_CATALOGUE(idCatalogueTenue),
	CONSTRAINT fk_tenuesAff_Personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne));
		
ALTER TABLE PERSONNE_REFERENTE ADD notif_tenues_stock BOOLEAN;
ALTER TABLE PERSONNE_REFERENTE ADD notif_tenues_retours BOOLEAN;

ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur9Accueil INT AFTER conf_indicateur8Accueil;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur10Accueil INT AFTER conf_indicateur9Accueil;

UPDATE PERSONNE_REFERENTE SET notif_tenues_stock = 0, notif_tenues_retours = 0, conf_indicateur9Accueil = 0, conf_indicateur10Accueil = 0;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 7.0.');