ALTER TABLE COMMANDES CHANGE remarquesGenerales remarquesGenerales TEXT;

ALTER TABLE CENTRE_COUTS_PERSONNES ADD idGerant INT PRIMARY KEY NOT NULL AUTO_INCREMENT FIRST;
ALTER TABLE CENTRE_COUTS_PERSONNES ADD montantMaxValidation INT;
ALTER TABLE CENTRE_COUTS_PERSONNES ADD depasseBudget BOOLEAN;
ALTER TABLE CENTRE_COUTS_PERSONNES ADD validerClos BOOLEAN;
ALTER TABLE CENTRE_COUTS_PERSONNES ADD UNIQUE(idPersonne, idCentreDeCout);

ALTER TABLE COMMANDES ADD savHistorique BOOLEAN;

DROP TABLE COMMANDES_VALIDEURS_DEFAULT;

DROP VIEW IF EXISTS VIEW_HABILITATIONS;
ALTER TABLE PROFILS DROP commande_valider;
ALTER TABLE PROFILS DROP commande_valider_seuil;
DROP TABLE COMMANDES_VALIDEURS;
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

CREATE TABLE COMMANDES_NOTIFICATIONS(
	idNotif INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	descriptifNotif TEXT,
	booleanConfigLibelle TEXT,
	destinatairesQuery TEXT,
	sujetNotif TEXT,
	corpsNotif TEXT
);
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 1,
	descriptifNotif      = "Etat 2 vers 3 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_validationOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation positive de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient d'être acceptée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 2,
	descriptifNotif      = "Etat 2 vers 3 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_validationOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation positive de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>La commande :idCommande dont vous êtes le valideur vient d'être acceptée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 3,
	descriptifNotif      = "Etat 2 vers 3 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_validationOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation positive de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient d'être acceptée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 4,
	descriptifNotif      = "Etat 2 vers 3 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_validationOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation positive de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient d'être acceptée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 5,
	descriptifNotif      = "Etat 2 vers 1 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_validationNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation négative de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient d'être refusée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 6,
	descriptifNotif      = "Etat 2 vers 1 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_validationNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation négative de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>La commande :idCommande dont vous êtes le valideur vient d'être refusée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 7,
	descriptifNotif      = "Etat 2 vers 1 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_validationNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation négative de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient d'être refusée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 8,
	descriptifNotif      = "Etat 2 vers 1 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_validationNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Validation négative de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient d'être refusée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 9,
	descriptifNotif      = "Etat 1 vers 2 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_validation",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Demande de validation provenant de :sessionActive pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient de passer au stade de demande de validation.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 10,
	descriptifNotif      = "Etat 1 vers 2 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_validation",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Demande de validation provenant de :sessionActive pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le valideur vient de passer au stade de demande de validation.<br/><br/>Merci de vous connecter sur :APPNAME afin de donner une réponse à la demande de validation.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 11,
	descriptifNotif      = "Etat 1 vers 2 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_validation",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Demande de validation provenant de :sessionActive pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient de passer au stade de demande de validation.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 12,
	descriptifNotif      = "Etat 1 vers 2 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_validation",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Demande de validation provenant de :sessionActive pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient de passer au stade de demande de validation.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 13,
	descriptifNotif      = "Etat 3 vers 4 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_passee",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Commande :idCommande (:nomCommande) passée auprès du fournisseur :nomFournisseur",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient d'être passée après du fournisseur.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 14,
	descriptifNotif      = "Etat 3 vers 4 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_passee",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Commande :idCommande (:nomCommande) passée auprès du fournisseur :nomFournisseur",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le valideur vient d'être passée après du fournisseur.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 15,
	descriptifNotif      = "Etat 3 vers 4 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_passee",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Commande :idCommande (:nomCommande) passée auprès du fournisseur :nomFournisseur",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient d'être passée après du fournisseur.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 16,
	descriptifNotif      = "Etat 3 vers 4 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_passee",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Commande :idCommande (:nomCommande) passée auprès du fournisseur :nomFournisseur",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient d'être passée après du fournisseur.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 17,
	descriptifNotif      = "Etat 6 vers 5 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_savOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] SAV clos pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient de terminer son traitement SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 18,
	descriptifNotif      = "Etat 6 vers 5 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_savOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] SAV clos pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le valideur vient de terminer son traitement SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 19,
	descriptifNotif      = "Etat 6 vers 5 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_savOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] SAV clos pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient de terminer son traitement SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 20,
	descriptifNotif      = "Etat 6 vers 5 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_savOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] SAV clos pour la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient de terminer son traitement SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 21,
	descriptifNotif      = "Etat 4 vers 5 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_livraisonOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec succès de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient d'être livrée sans SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 22,
	descriptifNotif      = "Etat 4 vers 5 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_livraisonOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec succès de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le valideur vient d'être livrée sans SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 23,
	descriptifNotif      = "Etat 4 vers 5 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_livraisonOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec succès de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient d'être livrée sans SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 24,
	descriptifNotif      = "Etat 4 vers 5 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_livraisonOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec succès de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient d'être livrée sans SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 25,
	descriptifNotif      = "Etat 4 vers 6 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_livraisonNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec SAV de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient d'être livrée avec ouverture d'un SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 26,
	descriptifNotif      = "Etat 4 vers 6 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_livraisonNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec SAV de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le valideur vient d'être livrée avec ouverture d'un SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 27,
	descriptifNotif      = "Etat 4 vers 6 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_livraisonNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec SAV de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient d'être livrée avec ouverture d'un SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 28,
	descriptifNotif      = "Etat 4 vers 6 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_livraisonNOK",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Livraison avec SAV de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient d'être livrée avec ouverture d'un SAV.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 29,
	descriptifNotif      = "Etat 5 vers 7 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_cloture",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Clôture de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient d'être clôturée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 30,
	descriptifNotif      = "Etat 5 vers 7 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_cloture",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Clôture de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le valideur vient d'être clôturée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 31,
	descriptifNotif      = "Etat 5 vers 7 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_cloture",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Clôture de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient d'être clôturée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 32,
	descriptifNotif      = "Etat 5 vers 7 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_cloture",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Clôture de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient d'être clôturée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 33,
	descriptifNotif      = "Etat 8 - Envoyée aux demandeurs",
	booleanConfigLibelle = "notifications_commandes_demandeur_abandon",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_DEMANDEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idDemandeur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Abandon de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le demandeur vient d'être abandonnée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 34,
	descriptifNotif      = "Etat 8 - Envoyée aux valideurs",
	booleanConfigLibelle = "notifications_commandes_valideur_abandon",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM CENTRE_COUTS_PERSONNES c INNER JOIN PERSONNE_REFERENTE p ON c.idPersonne = p.idPersonne WHERE idCentreDeCout = (SELECT idCentreDeCout FROM COMMANDES WHERE idCommande = :idCommande) AND (montantMaxValidation >= :montantTotalCommande OR montantMaxValidation IS NULL);",
	sujetNotif           = "[:APPNAME][COMMANDE] Abandon de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes le valideur vient d'être abandonnée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 35,
	descriptifNotif      = "Etat 8 - Envoyée aux affectés",
	booleanConfigLibelle = "notifications_commandes_affectee_abandon",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_AFFECTEES ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idAffectee = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Abandon de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande qui vous est affectée vient d'être abandonnée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;
INSERT INTO	COMMANDES_NOTIFICATIONS SET
	idNotif              = 36,
	descriptifNotif      = "Etat 8 - Envoyée aux observateurs",
	booleanConfigLibelle = "notifications_commandes_observateur_abandon",
	destinatairesQuery   = "SELECT mailPersonne, prenomPersonne FROM COMMANDES_OBSERVATEURS ca LEFT OUTER JOIN PERSONNE_REFERENTE pr ON ca.idObservateur = pr.idPersonne WHERE idCommande = :idCommande AND mailPersonne != '' AND mailPersonne IS NOT NULL;",
	sujetNotif           = "[:APPNAME][COMMANDE] Abandon de la commande :idCommande - :nomFournisseur - :nomCommande",
	corpsNotif           = "Bonjour :prenomPersonne,<br/><br/>Pour information, la commande :idCommande dont vous êtes l'observateur vient d'être abandonnée.<br/><br/>Cordialement<br/><br/>L'équipe administrative de :APPNAME"
;

UPDATE CONFIG set version = '10.3';