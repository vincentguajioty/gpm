UPDATE CONFIG set version = '2.5';

ALTER TABLE CONFIG ADD mailcopy BOOLEAN;
UPDATE CONFIG set mailcopy = 0;

ALTER TABLE CONFIG ADD notifications_commandes_demandeur_creation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_validation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_validationOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_validationNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_passee BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_livraisonOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_livraisonNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_savOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_cloture BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_demandeur_abandon BOOLEAN;

ALTER TABLE CONFIG ADD notifications_commandes_valideur_creation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_validation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_validationOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_validationNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_passee BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_livraisonOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_livraisonNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_savOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_cloture BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_valideur_abandon BOOLEAN;

ALTER TABLE CONFIG ADD notifications_commandes_affectee_creation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_validation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_validationOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_validationNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_passee BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_livraisonOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_livraisonNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_savOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_cloture BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_affectee_abandon BOOLEAN;

ALTER TABLE CONFIG ADD notifications_commandes_observateur_creation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_validation BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_validationOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_validationNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_passee BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_livraisonOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_livraisonNOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_savOK BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_cloture BOOLEAN;
ALTER TABLE CONFIG ADD notifications_commandes_observateur_abandon BOOLEAN;

UPDATE CONFIG SET
notifications_commandes_demandeur_creation = 1,
notifications_commandes_demandeur_validation = 1,
notifications_commandes_demandeur_validationOK = 1,
notifications_commandes_demandeur_validationNOK = 1,
notifications_commandes_demandeur_passee = 1,
notifications_commandes_demandeur_livraisonOK = 1,
notifications_commandes_demandeur_livraisonNOK = 1,
notifications_commandes_demandeur_savOK = 1,
notifications_commandes_demandeur_cloture = 1,
notifications_commandes_demandeur_abandon = 1,
notifications_commandes_valideur_creation = 1,
notifications_commandes_valideur_validation = 1,
notifications_commandes_valideur_validationOK = 1,
notifications_commandes_valideur_validationNOK = 1,
notifications_commandes_valideur_passee = 1,
notifications_commandes_valideur_livraisonOK = 1,
notifications_commandes_valideur_livraisonNOK = 1,
notifications_commandes_valideur_savOK = 1,
notifications_commandes_valideur_cloture = 1,
notifications_commandes_valideur_abandon = 1,
notifications_commandes_affectee_creation = 1,
notifications_commandes_affectee_validation = 1,
notifications_commandes_affectee_validationOK = 1,
notifications_commandes_affectee_validationNOK = 1,
notifications_commandes_affectee_passee = 1,
notifications_commandes_affectee_livraisonOK = 1,
notifications_commandes_affectee_livraisonNOK = 1,
notifications_commandes_affectee_savOK = 1,
notifications_commandes_affectee_cloture = 1,
notifications_commandes_affectee_abandon = 1,
notifications_commandes_observateur_creation = 1,
notifications_commandes_observateur_validation = 1,
notifications_commandes_observateur_validationOK = 1,
notifications_commandes_observateur_validationNOK = 1,
notifications_commandes_observateur_passee = 1,
notifications_commandes_observateur_livraisonOK = 1,
notifications_commandes_observateur_livraisonNOK = 1,
notifications_commandes_observateur_savOK = 1,
notifications_commandes_observateur_cloture = 1,
notifications_commandes_observateur_abandon = 1;

OPTIMIZE TABLE LOGS;
OPTIMIZE TABLE COMMANDES_TIMELINE;
OPTIMIZE TABLE INVENTAIRES_CONTENUS;

INSERT INTO LOGS(dateEvt, adresseIP, utilisateurEvt, idLogLevel, detailEvt) VALUES(CURRENT_TIMESTAMP, 'local', 'sysAdmin', 2, 'Mise à jour vers la version 2.5.');