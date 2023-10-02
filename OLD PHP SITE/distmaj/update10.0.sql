CREATE TABLE VEHICULES_HEALTH_TYPES(
	idHealthType INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	affichageSynthese BOOLEAN,
	libelleHealthType TEXT
);

CREATE TABLE VEHICULES_HEALTH_ALERTES(
	idHealthAlerte INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVehicule INT,
	idHealthType INT,
	frequenceHealth INT,
	CONSTRAINT fk_healthAlertes_vehicule
		FOREIGN KEY (idVehicule)
		REFERENCES VEHICULES(idVehicule),
	CONSTRAINT fk_healthAlertes_type
		FOREIGN KEY (idHealthType)
		REFERENCES VEHICULES_HEALTH_TYPES(idHealthType)
);

CREATE TABLE VEHICULES_HEALTH(
	idVehiculeHealth INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idVehicule INT,
	dateHealth DATE,
	idPersonne INT,
	CONSTRAINT fk_health_vehicule
		FOREIGN KEY (idVehicule)
		REFERENCES VEHICULES(idVehicule),
	CONSTRAINT fk_health_executant
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne)
);

CREATE TABLE VEHICULES_HEALTH_CHECKS(
	idVehiculeHealth INT,
	idHealthType INT,
	remarquesCheck TEXT,
	CONSTRAINT fk_healthCheck_health
		FOREIGN KEY (idVehiculeHealth)
		REFERENCES VEHICULES_HEALTH(idVehiculeHealth),
	CONSTRAINT fk_healthCheck_type
		FOREIGN KEY (idHealthType)
		REFERENCES VEHICULES_HEALTH_TYPES(idHealthType)
);

ALTER TABLE VEHICULES ADD alerteMaintenance BOOLEAN AFTER alerteDesinfection;
ALTER TABLE VEHICULES ADD affichageSyntheseHealth BOOLEAN AFTER affichageSyntheseDesinfections;
UPDATE VEHICULES SET affichageSyntheseHealth = 1;

ALTER TABLE PERSONNE_REFERENTE ADD notif_vehicules_health BOOLEAN AFTER notif_vehicules_ct;
ALTER TABLE PERSONNE_REFERENTE ADD conf_indicateur12Accueil BOOLEAN AFTER conf_indicateur11Accueil;
UPDATE PERSONNE_REFERENTE SET notif_vehicules_health = 1, conf_indicateur12Accueil = 1;

ALTER TABLE PROFILS ADD vehiculeHealthType_lecture BOOLEAN;
ALTER TABLE PROFILS ADD vehiculeHealthType_ajout BOOLEAN;
ALTER TABLE PROFILS ADD vehiculeHealthType_modification BOOLEAN;
ALTER TABLE PROFILS ADD vehiculeHealthType_suppression BOOLEAN;
ALTER TABLE PROFILS ADD vehiculeHealth_lecture BOOLEAN;
ALTER TABLE PROFILS ADD vehiculeHealth_ajout BOOLEAN;
ALTER TABLE PROFILS ADD vehiculeHealth_modification BOOLEAN;
ALTER TABLE PROFILS ADD vehiculeHealth_suppression BOOLEAN;
ALTER TABLE PROFILS ADD commande_valider_seuil INT AFTER commande_valider;

UPDATE PROFILS SET
	vehiculeHealthType_lecture = 0,
	vehiculeHealthType_ajout = 0,
	vehiculeHealthType_modification = 0,
	vehiculeHealthType_suppression = 0,
	vehiculeHealth_lecture = 0,
	vehiculeHealth_ajout = 0,
	vehiculeHealth_modification = 0,
	vehiculeHealth_suppression = 0;

CREATE OR REPLACE VIEW VIEW_HABILITATIONS AS
	SELECT
		p.idPersonne,
		p.identifiant,
		p.nomPersonne,
		p.prenomPersonne,
		p.mailPersonne,
		p.telPersonne,
		p.fonction,
		p.derniereConnexion,
		(SELECT MAX(connexion_connexion)             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as connexion_connexion,
		(SELECT MAX(annuaire_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_lecture,
		(SELECT MAX(annuaire_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_ajout,
		(SELECT MAX(annuaire_modification)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_modification,
		(SELECT MAX(annuaire_mdp)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_mdp,
		(SELECT MAX(annuaire_suppression)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_suppression,
		(SELECT MAX(profils_lecture)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_lecture,
		(SELECT MAX(profils_ajout)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_ajout,
		(SELECT MAX(profils_modification)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_modification,
		(SELECT MAX(profils_suppression)             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_suppression,
		(SELECT MAX(categories_lecture)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_lecture,
		(SELECT MAX(categories_ajout)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_ajout,
		(SELECT MAX(categories_modification)         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_modification,
		(SELECT MAX(categories_suppression)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_suppression,
		(SELECT MAX(fournisseurs_lecture)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_lecture,
		(SELECT MAX(fournisseurs_ajout)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_ajout,
		(SELECT MAX(fournisseurs_modification)       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_modification,
		(SELECT MAX(fournisseurs_suppression)        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_suppression,
		(SELECT MAX(typesLots_lecture)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_lecture,
		(SELECT MAX(typesLots_ajout)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_ajout,
		(SELECT MAX(typesLots_modification)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_modification,
		(SELECT MAX(typesLots_suppression)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_suppression,
		(SELECT MAX(lieux_lecture)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_lecture,
		(SELECT MAX(lieux_ajout)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_ajout,
		(SELECT MAX(lieux_modification)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_modification,
		(SELECT MAX(lieux_suppression)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_suppression,
		(SELECT MAX(lots_lecture)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_lecture,
		(SELECT MAX(lots_ajout)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_ajout,
		(SELECT MAX(lots_modification)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_modification,
		(SELECT MAX(lots_suppression)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_suppression,
		(SELECT MAX(sac_lecture)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_lecture,
		(SELECT MAX(sac_ajout)                       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_ajout,
		(SELECT MAX(sac_modification)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_modification,
		(SELECT MAX(sac_suppression)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_suppression,
		(SELECT MAX(sac2_lecture)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_lecture,
		(SELECT MAX(sac2_ajout)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_ajout,
		(SELECT MAX(sac2_modification)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_modification,
		(SELECT MAX(sac2_suppression)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_suppression,
		(SELECT MAX(catalogue_lecture)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_lecture,
		(SELECT MAX(catalogue_ajout)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_ajout,
		(SELECT MAX(catalogue_modification)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_modification,
		(SELECT MAX(catalogue_suppression)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_suppression,
		(SELECT MAX(materiel_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_lecture,
		(SELECT MAX(materiel_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_ajout,
		(SELECT MAX(materiel_modification)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_modification,
		(SELECT MAX(materiel_suppression)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_suppression,
		(SELECT MAX(messages_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as messages_ajout,
		(SELECT MAX(messages_suppression)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as messages_suppression,
		(SELECT MAX(verrouIP)                        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as verrouIP,
		(SELECT MAX(commande_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_lecture,
		(SELECT MAX(commande_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_ajout,
		(SELECT MAX(commande_valider)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_valider,
		(SELECT MAX(commande_valider_seuil)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_valider_seuil,
		(SELECT MAX(commande_etreEnCharge)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_etreEnCharge,
		(SELECT MAX(commande_abandonner)             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_abandonner,
		(SELECT MAX(commande_valider_delegate)       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_valider_delegate,
		(SELECT MAX(cout_lecture)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_lecture,
		(SELECT MAX(cout_ajout)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_ajout,
		(SELECT MAX(cout_etreEnCharge)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_etreEnCharge,
		(SELECT MAX(cout_supprimer)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_supprimer,
		(SELECT MAX(appli_conf)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as appli_conf,
		(SELECT MAX(reserve_lecture)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_lecture,
		(SELECT MAX(reserve_ajout)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_ajout,
		(SELECT MAX(reserve_modification)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_modification,
		(SELECT MAX(reserve_suppression)             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_suppression,
		(SELECT MAX(reserve_cmdVersReserve)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_cmdVersReserve,
		(SELECT MAX(reserve_ReserveVersLot)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_ReserveVersLot,
		(SELECT MAX(vhf_canal_lecture)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_lecture,
		(SELECT MAX(vhf_canal_ajout)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_ajout,
		(SELECT MAX(vhf_canal_modification)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_modification,
		(SELECT MAX(vhf_canal_suppression)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_suppression,
		(SELECT MAX(vhf_plan_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_lecture,
		(SELECT MAX(vhf_plan_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_ajout,
		(SELECT MAX(vhf_plan_modification)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_modification,
		(SELECT MAX(vhf_plan_suppression)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_suppression,
		(SELECT MAX(vhf_equipement_lecture)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_lecture,
		(SELECT MAX(vhf_equipement_ajout)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_ajout,
		(SELECT MAX(vhf_equipement_modification)     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_modification,
		(SELECT MAX(vhf_equipement_suppression)      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_suppression,
		(SELECT MAX(vehicules_lecture)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_lecture,
		(SELECT MAX(vehicules_ajout)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_ajout,
		(SELECT MAX(vehicules_modification)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_modification,
		(SELECT MAX(vehicules_suppression)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_suppression,
		(SELECT MAX(vehicules_types_lecture)         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_lecture,
		(SELECT MAX(vehicules_types_ajout)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_ajout,
		(SELECT MAX(vehicules_types_modification)    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_modification,
		(SELECT MAX(vehicules_types_suppression)     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_suppression,
		(SELECT MAX(tenues_lecture)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_lecture,
		(SELECT MAX(tenues_ajout)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_ajout,
		(SELECT MAX(tenues_modification)             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_modification,
		(SELECT MAX(tenues_suppression)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_suppression,
		(SELECT MAX(tenuesCatalogue_lecture)         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_lecture,
		(SELECT MAX(tenuesCatalogue_ajout)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_ajout,
		(SELECT MAX(tenuesCatalogue_modification)    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_modification,
		(SELECT MAX(tenuesCatalogue_suppression)     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_suppression,
		(SELECT MAX(cautions_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_lecture,
		(SELECT MAX(cautions_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_ajout,
		(SELECT MAX(cautions_modification)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_modification,
		(SELECT MAX(cautions_suppression)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_suppression,
		(SELECT MAX(maintenance)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as maintenance,
		(SELECT MAX(todolist_perso)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_perso,
		(SELECT MAX(todolist_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_lecture,
		(SELECT MAX(todolist_modification)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_modification,
		(SELECT MAX(contactMailGroupe)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as contactMailGroupe,
		(SELECT MAX(etats_lecture)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_lecture,
		(SELECT MAX(etats_ajout)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_ajout,
		(SELECT MAX(etats_modification)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_modification,
		(SELECT MAX(etats_suppression)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_suppression,
		(SELECT MAX(notifications)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as notifications,
		(SELECT MAX(actionsMassives)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as actionsMassives,
		(SELECT MAX(delegation)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as delegation,
		(SELECT MAX(desinfections_lecture)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_lecture,
		(SELECT MAX(desinfections_ajout)             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_ajout,
		(SELECT MAX(desinfections_modification)      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_modification,
		(SELECT MAX(desinfections_suppression)       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_suppression,
		(SELECT MAX(typesDesinfections_lecture)      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_lecture,
		(SELECT MAX(typesDesinfections_ajout)        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_ajout,
		(SELECT MAX(typesDesinfections_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_modification,
		(SELECT MAX(typesDesinfections_suppression)  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_suppression,
		(SELECT MAX(carburants_lecture)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_lecture,
		(SELECT MAX(carburants_ajout)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_ajout,
		(SELECT MAX(carburants_modification)         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_modification,
		(SELECT MAX(carburants_suppression)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_suppression,
		(SELECT MAX(vehiculeHealthType_lecture)      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_lecture,
		(SELECT MAX(vehiculeHealthType_ajout)        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_ajout,
		(SELECT MAX(vehiculeHealthType_modification) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_modification,
		(SELECT MAX(vehiculeHealthType_suppression)  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_suppression,
		(SELECT MAX(vehiculeHealth_lecture)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_lecture,
		(SELECT MAX(vehiculeHealth_ajout)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_ajout,
		(SELECT MAX(vehiculeHealth_modification)     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_modification,
		(SELECT MAX(vehiculeHealth_suppression)      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_suppression
	FROM
		PERSONNE_REFERENTE p
;

ALTER TABLE PERSONNE_REFERENTE ADD agenda_healthF VARCHAR(10);
ALTER TABLE PERSONNE_REFERENTE ADD agenda_healthAF VARCHAR(10);
UPDATE PERSONNE_REFERENTE SET agenda_healthF = '#f39c12';
UPDATE PERSONNE_REFERENTE SET agenda_healthAF = '#f39c12';

ALTER TABLE VERROUILLAGE_IP ADD commentaire TEXT;
ALTER TABLE VERROUILLAGE_IP_TEMP ADD commentaire TEXT;

ALTER TABLE MATERIEL_ELEMENT ADD peremptionAnticipation INT AFTER peremption;
UPDATE MATERIEL_ELEMENT SET peremptionAnticipation = ABS(DATEDIFF(peremption,peremptionNotification));
ALTER TABLE MATERIEL_ELEMENT DROP peremptionNotification;
ALTER TABLE
	MATERIEL_ELEMENT
ADD
	peremptionNotification DATE AS (DATE_SUB(peremption, INTERVAL peremptionAnticipation DAY))
AFTER
	peremptionAnticipation
;

ALTER TABLE RESERVES_MATERIEL ADD peremptionReserveAnticipation INT AFTER peremptionReserve;
UPDATE RESERVES_MATERIEL SET peremptionReserveAnticipation = ABS(DATEDIFF(peremptionReserve,peremptionNotificationReserve));
ALTER TABLE RESERVES_MATERIEL DROP peremptionNotificationReserve;
ALTER TABLE
	RESERVES_MATERIEL
ADD
	peremptionNotificationReserve DATE AS (DATE_SUB(peremptionReserve, INTERVAL peremptionReserveAnticipation DAY))
AFTER
	peremptionReserveAnticipation
;

ALTER TABLE CONFIG ADD verrouillage_ip_occurances INT;
ALTER TABLE CONFIG ADD verrouillage_ip_temps INT;
UPDATE CONFIG SET verrouillage_ip_occurances = 3;
UPDATE CONFIG SET verrouillage_ip_temps = 1;





CREATE TABLE NOTIFICATIONS_CONDITIONS(
	idCondition INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	libelleCondition TEXT,
	codeCondition VARCHAR(200),
	actifCeJour BOOLEAN
);
ALTER TABLE NOTIFICATIONS_CONDITIONS ADD UNIQUE(codeCondition);
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les jours",
	codeCondition = "allways",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les lundis",
	codeCondition = "monday",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les mardis",
	codeCondition = "tuesday",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les mercredis",
	codeCondition = "wednesday",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les jeudis",
	codeCondition = "thursday",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les vendredi",
	codeCondition = "friday",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les samedis",
	codeCondition = "saturday",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les dimanches",
	codeCondition = "sunday",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 1 de chaque mois",
	codeCondition = "1",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 2 de chaque mois",
	codeCondition = "2",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 3 de chaque mois",
	codeCondition = "3",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 4 de chaque mois",
	codeCondition = "4",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 5 de chaque mois",
	codeCondition = "5",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 6 de chaque mois",
	codeCondition = "6",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 7 de chaque mois",
	codeCondition = "7",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 8 de chaque mois",
	codeCondition = "8",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 9 de chaque mois",
	codeCondition = "9",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 10 de chaque mois",
	codeCondition = "10",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 11 de chaque mois",
	codeCondition = "11",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 12 de chaque mois",
	codeCondition = "12",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 13 de chaque mois",
	codeCondition = "13",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 14 de chaque mois",
	codeCondition = "14",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 15 de chaque mois",
	codeCondition = "15",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 16 de chaque mois",
	codeCondition = "16",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 17 de chaque mois",
	codeCondition = "17",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 18 de chaque mois",
	codeCondition = "18",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 19 de chaque mois",
	codeCondition = "19",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 20 de chaque mois",
	codeCondition = "20",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 21 de chaque mois",
	codeCondition = "21",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 22 de chaque mois",
	codeCondition = "22",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 23 de chaque mois",
	codeCondition = "23",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 24 de chaque mois",
	codeCondition = "24",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 25 de chaque mois",
	codeCondition = "25",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 26 de chaque mois",
	codeCondition = "26",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 27 de chaque mois",
	codeCondition = "27",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 28 de chaque mois",
	codeCondition = "28",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 29 de chaque mois",
	codeCondition = "29",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 30 de chaque mois",
	codeCondition = "30",
	actifCeJour = false
;
INSERT INTO
	NOTIFICATIONS_CONDITIONS
SET
	libelleCondition = "Tous les 31 de chaque mois",
	codeCondition = "31",
	actifCeJour = false
;

CREATE TABLE NOTIFICATIONS_ABONNEMENTS(
	idCondition INT,
	idPersonne INT,
	CONSTRAINT fk_abo_condition
		FOREIGN KEY (idCondition)
		REFERENCES NOTIFICATIONS_CONDITIONS(idCondition),
	CONSTRAINT fk_abo_personne
		FOREIGN KEY (idPersonne)
		REFERENCES PERSONNE_REFERENTE(idPersonne)
);
INSERT INTO NOTIFICATIONS_ABONNEMENTS (idPersonne) SELECT idPersonne FROM PERSONNE_REFERENTE;
UPDATE NOTIFICATIONS_ABONNEMENTS SET idCondition = 1;

ALTER TABLE PERSONNE_REFERENTE ADD notifications_abo_cejour BOOLEAN;
UPDATE PERSONNE_REFERENTE SET notifications_abo_cejour = 0;

ALTER TABLE FOURNISSEURS ADD aesFournisseur BLOB;
ALTER TABLE CONFIG ADD aesFournisseurTemoin BLOB;

ALTER TABLE MATERIEL_CATALOGUE ADD peremptionAnticipationOpe INT;
ALTER TABLE MATERIEL_CATALOGUE ADD peremptionAnticipationRes INT;

UPDATE CONFIG set version = '10.0';