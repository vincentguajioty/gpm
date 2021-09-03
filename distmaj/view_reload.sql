DROP VIEW IF EXISTS VIEW_SCAN_RESULTS_RESERVES;
DROP VIEW IF EXISTS VIEW_SCAN_RESULTS_LOTS;
DROP VIEW IF EXISTS VIEW_VEHICULES_KM;
DROP VIEW IF EXISTS VIEW_HABILITATIONS;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_COMMANDES;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_CENTRE_COUTS;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_VEHICULES;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_CANAL_VHF;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_PLAN_VHF;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_VHF;
DROP VIEW IF EXISTS VIEW_DOCUMENTS_CENTRE_COUTS_COMMANDES;
DROP VIEW IF EXISTS VIEW_PERSONNE_REFERENTE;

CREATE OR REPLACE VIEW VIEW_PERSONNE_REFERENTE AS
	SELECT
		*
	FROM
		PERSONNE_REFERENTE
	WHERE
		cnil_anonyme = 0
;

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
		p.cnil_anonyme,
		p.disclaimerAccept,
		p.isActiveDirectory,
		(SELECT MAX(connexion_connexion)                       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as connexion_connexion,
		(SELECT MAX(annuaire_lecture)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_lecture,
		(SELECT MAX(annuaire_ajout)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_ajout,
		(SELECT MAX(annuaire_modification)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_modification,
		(SELECT MAX(annuaire_mdp)                              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_mdp,
		(SELECT MAX(annuaire_suppression)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as annuaire_suppression,
		(SELECT MAX(profils_lecture)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_lecture,
		(SELECT MAX(profils_ajout)                             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_ajout,
		(SELECT MAX(profils_modification)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_modification,
		(SELECT MAX(profils_suppression)                       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as profils_suppression,
		(SELECT MAX(categories_lecture)                        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_lecture,
		(SELECT MAX(categories_ajout)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_ajout,
		(SELECT MAX(categories_modification)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_modification,
		(SELECT MAX(categories_suppression)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as categories_suppression,
		(SELECT MAX(fournisseurs_lecture)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_lecture,
		(SELECT MAX(fournisseurs_ajout)                        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_ajout,
		(SELECT MAX(fournisseurs_modification)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_modification,
		(SELECT MAX(fournisseurs_suppression)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as fournisseurs_suppression,
		(SELECT MAX(typesLots_lecture)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_lecture,
		(SELECT MAX(typesLots_ajout)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_ajout,
		(SELECT MAX(typesLots_modification)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_modification,
		(SELECT MAX(typesLots_suppression)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesLots_suppression,
		(SELECT MAX(lieux_lecture)                             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_lecture,
		(SELECT MAX(lieux_ajout)                               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_ajout,
		(SELECT MAX(lieux_modification)                        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_modification,
		(SELECT MAX(lieux_suppression)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lieux_suppression,
		(SELECT MAX(lots_lecture)                              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_lecture,
		(SELECT MAX(lots_ajout)                                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_ajout,
		(SELECT MAX(lots_modification)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_modification,
		(SELECT MAX(lots_suppression)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as lots_suppression,
		(SELECT MAX(sac_lecture)                               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_lecture,
		(SELECT MAX(sac_ajout)                                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_ajout,
		(SELECT MAX(sac_modification)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_modification,
		(SELECT MAX(sac_suppression)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac_suppression,
		(SELECT MAX(sac2_lecture)                              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_lecture,
		(SELECT MAX(sac2_ajout)                                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_ajout,
		(SELECT MAX(sac2_modification)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_modification,
		(SELECT MAX(sac2_suppression)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as sac2_suppression,
		(SELECT MAX(catalogue_lecture)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_lecture,
		(SELECT MAX(catalogue_ajout)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_ajout,
		(SELECT MAX(catalogue_modification)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_modification,
		(SELECT MAX(catalogue_suppression)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as catalogue_suppression,
		(SELECT MAX(materiel_lecture)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_lecture,
		(SELECT MAX(materiel_ajout)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_ajout,
		(SELECT MAX(materiel_modification)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_modification,
		(SELECT MAX(materiel_suppression)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as materiel_suppression,
		(SELECT MAX(messages_ajout)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as messages_ajout,
		(SELECT MAX(messages_suppression)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as messages_suppression,
		(SELECT MAX(verrouIP)                                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as verrouIP,
		(SELECT MAX(commande_lecture)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_lecture,
		(SELECT MAX(commande_ajout)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_ajout,
		(SELECT MAX(commande_etreEnCharge)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_etreEnCharge,
		(SELECT MAX(commande_abandonner)                       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_abandonner,
		(SELECT MAX(commande_valider_delegate)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as commande_valider_delegate,
		(SELECT MAX(cout_lecture)                              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_lecture,
		(SELECT MAX(cout_ajout)                                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_ajout,
		(SELECT MAX(cout_etreEnCharge)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_etreEnCharge,
		(SELECT MAX(cout_supprimer)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cout_supprimer,
		(SELECT MAX(appli_conf)                                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as appli_conf,
		(SELECT MAX(reserve_lecture)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_lecture,
		(SELECT MAX(reserve_ajout)                             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_ajout,
		(SELECT MAX(reserve_modification)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_modification,
		(SELECT MAX(reserve_suppression)                       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_suppression,
		(SELECT MAX(reserve_cmdVersReserve)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_cmdVersReserve,
		(SELECT MAX(reserve_ReserveVersLot)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as reserve_ReserveVersLot,
		(SELECT MAX(vhf_canal_lecture)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_lecture,
		(SELECT MAX(vhf_canal_ajout)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_ajout,
		(SELECT MAX(vhf_canal_modification)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_modification,
		(SELECT MAX(vhf_canal_suppression)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_canal_suppression,
		(SELECT MAX(vhf_plan_lecture)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_lecture,
		(SELECT MAX(vhf_plan_ajout)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_ajout,
		(SELECT MAX(vhf_plan_modification)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_modification,
		(SELECT MAX(vhf_plan_suppression)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_plan_suppression,
		(SELECT MAX(vhf_equipement_lecture)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_lecture,
		(SELECT MAX(vhf_equipement_ajout)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_ajout,
		(SELECT MAX(vhf_equipement_modification)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_modification,
		(SELECT MAX(vhf_equipement_suppression)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vhf_equipement_suppression,
		(SELECT MAX(vehicules_lecture)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_lecture,
		(SELECT MAX(vehicules_ajout)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_ajout,
		(SELECT MAX(vehicules_modification)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_modification,
		(SELECT MAX(vehicules_suppression)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_suppression,
		(SELECT MAX(vehicules_types_lecture)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_lecture,
		(SELECT MAX(vehicules_types_ajout)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_ajout,
		(SELECT MAX(vehicules_types_modification)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_modification,
		(SELECT MAX(vehicules_types_suppression)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehicules_types_suppression,
		(SELECT MAX(tenues_lecture)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_lecture,
		(SELECT MAX(tenues_ajout)                              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_ajout,
		(SELECT MAX(tenues_modification)                       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_modification,
		(SELECT MAX(tenues_suppression)                        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenues_suppression,
		(SELECT MAX(tenuesCatalogue_lecture)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_lecture,
		(SELECT MAX(tenuesCatalogue_ajout)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_ajout,
		(SELECT MAX(tenuesCatalogue_modification)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_modification,
		(SELECT MAX(tenuesCatalogue_suppression)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as tenuesCatalogue_suppression,
		(SELECT MAX(cautions_lecture)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_lecture,
		(SELECT MAX(cautions_ajout)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_ajout,
		(SELECT MAX(cautions_modification)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_modification,
		(SELECT MAX(cautions_suppression)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as cautions_suppression,
		(SELECT MAX(maintenance)                               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as maintenance,
		(SELECT MAX(todolist_perso)                            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_perso,
		(SELECT MAX(todolist_lecture)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_lecture,
		(SELECT MAX(todolist_modification)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as todolist_modification,
		(SELECT MAX(contactMailGroupe)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as contactMailGroupe,
		(SELECT MAX(etats_lecture)                             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_lecture,
		(SELECT MAX(etats_ajout)                               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_ajout,
		(SELECT MAX(etats_modification)                        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_modification,
		(SELECT MAX(etats_suppression)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as etats_suppression,
		(SELECT MAX(notifications)                             FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as notifications,
		(SELECT MAX(actionsMassives)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as actionsMassives,
		(SELECT MAX(delegation)                                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as delegation,
		(SELECT MAX(desinfections_lecture)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_lecture,
		(SELECT MAX(desinfections_ajout)                       FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_ajout,
		(SELECT MAX(desinfections_modification)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_modification,
		(SELECT MAX(desinfections_suppression)                 FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as desinfections_suppression,
		(SELECT MAX(typesDesinfections_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_lecture,
		(SELECT MAX(typesDesinfections_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_ajout,
		(SELECT MAX(typesDesinfections_modification)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_modification,
		(SELECT MAX(typesDesinfections_suppression)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as typesDesinfections_suppression,
		(SELECT MAX(carburants_lecture)                        FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_lecture,
		(SELECT MAX(carburants_ajout)                          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_ajout,
		(SELECT MAX(carburants_modification)                   FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_modification,
		(SELECT MAX(carburants_suppression)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as carburants_suppression,
		(SELECT MAX(vehiculeHealthType_lecture)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_lecture,
		(SELECT MAX(vehiculeHealthType_ajout)                  FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_ajout,
		(SELECT MAX(vehiculeHealthType_modification)           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_modification,
		(SELECT MAX(vehiculeHealthType_suppression)            FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealthType_suppression,
		(SELECT MAX(vehiculeHealth_lecture)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_lecture,
		(SELECT MAX(vehiculeHealth_ajout)                      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_ajout,
		(SELECT MAX(vehiculeHealth_modification)               FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_modification,
		(SELECT MAX(vehiculeHealth_suppression)                FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as vehiculeHealth_suppression,
		(SELECT MAX(alertesBenevolesLots_lecture)              FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as alertesBenevolesLots_lecture,
		(SELECT MAX(alertesBenevolesLots_affectation)          FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as alertesBenevolesLots_affectation,
		(SELECT MAX(alertesBenevolesLots_affectationTier)      FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as alertesBenevolesLots_affectationTier,
		(SELECT MAX(alertesBenevolesVehicules_lecture)         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as alertesBenevolesVehicules_lecture,
		(SELECT MAX(alertesBenevolesVehicules_affectation)     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as alertesBenevolesVehicules_affectation,
		(SELECT MAX(alertesBenevolesVehicules_affectationTier) FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as alertesBenevolesVehicules_affectationTier,
		(SELECT MAX(codeBarre_lecture)                         FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as codeBarre_lecture,
		(SELECT MAX(codeBarre_ajout)                           FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as codeBarre_ajout,
		(SELECT MAX(codeBarre_modification)                    FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as codeBarre_modification,
		(SELECT MAX(codeBarre_suppression)                     FROM PROFILS_PERSONNES pp JOIN PROFILS po ON pp.idProfil = po.idProfil WHERE pp.idPersonne = p.idPersonne) as codeBarre_suppression
	FROM
		PERSONNE_REFERENTE p
;
CREATE OR REPLACE VIEW VIEW_DOCUMENTS_COMMANDES AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_COMMANDES c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocCommande ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_CENTRE_COUTS AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_CENTRE_COUTS c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocCouts ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_CENTRE_COUTS_COMMANDES AS
	(
		SELECT
			c.*,
			t.libelleTypeDocument,
			Null as 'idDocCommande'
		FROM
			DOCUMENTS_CENTRE_COUTS c
			LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
		ORDER BY
			nomDocCouts ASC
	)
	UNION
	(
		SELECT
			Null as 'idDocCouts',
			cc.idCentreDeCout as 'idCentreDeCout',
			c.nomDocCommande as 'nomDocCouts',
			c.formatDocCommande as 'formatDocCouts',
			c.dateDocCommande as 'dateDocCouts',
			c.urlFichierDocCommande as 'urlFichierDocCouts',
			c.idTypeDocument as 'idTypeDocument',
			t.libelleTypeDocument as 'libelleTypeDocument',
			c.idDocCommande as 'idDocCommande'
		FROM
			DOCUMENTS_COMMANDES c
			LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
			LEFT OUTER JOIN COMMANDES cc ON c.idCommande = cc.idCommande
		ORDER BY
			nomDocCommande ASC
	)
;


CREATE OR REPLACE VIEW VIEW_DOCUMENTS_VEHICULES AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_VEHICULES c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocVehicule ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_CANAL_VHF AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_CANAL_VHF c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocCanalVHF ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_PLAN_VHF AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_PLAN_VHF c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocPlanVHF ASC
;

CREATE OR REPLACE VIEW VIEW_DOCUMENTS_VHF AS
	SELECT
		c.*,
		t.libelleTypeDocument
	FROM
		DOCUMENTS_VHF c
		LEFT OUTER JOIN DOCUMENTS_TYPES t ON c.idTypeDocument = t.idTypeDocument
	ORDER BY
		nomDocVHF ASC
;

CREATE OR REPLACE VIEW VIEW_VEHICULES_KM AS
	(SELECT idReleve, idVehicule, dateReleve, releveKilometrique, idPersonne FROM VEHICULES_RELEVES)
	UNION
	(SELECT NULL as idReleve, idVehicule, dateMaintenance as dateReleve, releveKilometrique, idExecutant as idPersonne  FROM VEHICULES_MAINTENANCE WHERE releveKilometrique IS NOT NULL)
	UNION
	(SELECT NULL as idReleve, idVehicule, dateHealth as dateReleve, releveKilometrique, idPersonne  FROM VEHICULES_HEALTH WHERE releveKilometrique IS NOT NULL)
;

CREATE OR REPLACE VIEW VIEW_SCAN_RESULTS_LOTS AS
	SELECT
		t.idLot,
		t.idEmplacement,
	    b.idMaterielCatalogue,
	    c.libelleMateriel,
	    MIN(peremptionConsommable) as peremption,
	    COUNT(t.codeBarre) as quantite
	FROM
		LOTS_INVENTAIRES_TEMP t
	    LEFT OUTER JOIN CODES_BARRE b ON t.codeBarre = b.codeBarre
	    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON b.idMaterielCatalogue = c.idMaterielCatalogue
	GROUP BY
		t.idEmplacement,
	    b.idMaterielCatalogue
;

CREATE OR REPLACE VIEW VIEW_SCAN_RESULTS_RESERVES AS
	SELECT
		t.idConteneur,
	    b.idMaterielCatalogue,
	    c.libelleMateriel,
	    MIN(peremptionConsommable) as peremption,
	    COUNT(t.codeBarre) as quantite
	FROM
		RESERVES_INVENTAIRES_TEMP t
	    LEFT OUTER JOIN CODES_BARRE b ON t.codeBarre = b.codeBarre
	    LEFT OUTER JOIN MATERIEL_CATALOGUE c ON b.idMaterielCatalogue = c.idMaterielCatalogue
	GROUP BY
		t.idConteneur,
	    b.idMaterielCatalogue
;
