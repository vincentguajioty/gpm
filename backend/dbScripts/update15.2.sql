CREATE TABLE CHAMPS_CARTE_GRISE(
	idChamp INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	codeChamp VARCHAR(10),
    libelleChamp VARCHAR(150)
);

INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="#Crit'Air", libelleChamp="Numéro inscrit sur la vignette Crit'Air";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="A", libelleChamp="Numéro d'immatriculation";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="A.1", libelleChamp="Numéro d'immatriculation précédent du véhicule";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="B", libelleChamp="Date de la première immatriculation du véhicule";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="C.1", libelleChamp="Nom et prénom du propriétaire, titulaire de la carte grise";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="C.3", libelleChamp="Adresse de la résidence principale du titulaire, personne physique ou morale pouvant disposer du véhicule";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="C.4 a", libelleChamp="Indication sur le titulaire nommé en C.1 est le propriétaire du véhicule";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="C.4.1", libelleChamp="Nombre et nom de personne co-titulaire du certificat d'immatriculation dans le cas d'une multi-propriété";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="D.1", libelleChamp="Marque du véhicule";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="D.2", libelleChamp="Type, variante ou version (si disponible)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="D.2.1", libelleChamp="Code national d'identification du type (CNIT)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="D.3", libelleChamp="Modèle ou dénomination commerciale";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="E", libelleChamp="Numéro d'identification du véhicule VIN (ancien numéro dans la série)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="F.1", libelleChamp="Masse en charge maximale techniquement admissible en kg, aussi appelée Poids total autorisé en charge (PTAC)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="F.2", libelleChamp="Masse en charge maximale admissible en service dans l'État membre (en kg)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="F.3", libelleChamp="Masse en charge maximale admissible de l'ensemble en service (en kg), ancien Poids total roulant autorisé (PTRA)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="G", libelleChamp="Masse du véhicule en service avec carrosserie et dispositif d'attelage en cas de véhicule tracteur de catégorie autre que M1 (en kg)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="G.1", libelleChamp="Poids à vide national";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="H", libelleChamp="Période de validité si elle n'est pas illimitée";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="I", libelleChamp="Date de l'immatriculation à laquelle se réfère la carte grise";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="I.1", libelleChamp="Date de l'immatriculation à laquelle se réfère la précédente carte grise";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="J", libelleChamp="Catégorie du véhicule (CE)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="J.1", libelleChamp="Genre national";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="J.2", libelleChamp="Carrosserie (CE)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="J.3", libelleChamp="Carrosserie (désignation nationale)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="K", libelleChamp="Numéro de réception par type";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="P.1", libelleChamp="Cylindrée (en cm3)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="P.2", libelleChamp="Puissance nette maximale (en kW)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="P.3", libelleChamp="Type de carburant ou source d'énergie";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="P.6", libelleChamp="Puissance fiscale ou puissance administrative nationale, aussi exprimée en chevaux fiscaux CV";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Q", libelleChamp="Rapport puissance/masse en kW/kg (uniquement pour les motocycles)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="S.1", libelleChamp="Nombre de places assises y compris celle du conducteur";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="S.2", libelleChamp="Nombre de places debout";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="U.1", libelleChamp="Niveau sonore à l'arrêt en dB(A)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="U.2", libelleChamp="Vitesse du moteur en min-1 (ancien régime moteur, tr/min)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="V.7", libelleChamp="Taux d'émission de CO2 (en g/Km)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="V.9", libelleChamp="Classe environnementale de réception CE";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="X.1", libelleChamp="Date de fin de validité du contrôle technique";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Y.1", libelleChamp="Montant de la taxe régionale en €";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Y.2", libelleChamp="Montant de la taxe pour le développement des actions de formation professionnelle dans les transports (en €)";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Y.3", libelleChamp="Montant de la taxe CO2 ou malus écologique (écotaxe) en euros";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Y.4", libelleChamp="Montant de la taxe fixe de gestion du certificat d'immatriculation en euros";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Y.5", libelleChamp="Montant de la redevance pour acheminement du certificat d'immatriculation en euros";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Y.6", libelleChamp="Montant total des taxes additionnelles, prix de la carte grise à s'acquitter en Euro";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Z.1", libelleChamp="Mentions spécifiques";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Z.2";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Z.3";
INSERT INTO CHAMPS_CARTE_GRISE SET codeChamp="Z.4";

CREATE TABLE VEHICULES_CHAMPS_CG(
	idVehiculeChampCG INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idChamp INT,
    idVehicule INT,
    detailsChamps VARCHAR(100),
    CONSTRAINT fk_CG_champs
		FOREIGN KEY (idChamp)
		REFERENCES CHAMPS_CARTE_GRISE(idChamp),
    CONSTRAINT fk_CG_vehicule
		FOREIGN KEY (idVehicule)
		REFERENCES VEHICULES(idVehicule)
);

UPDATE CONFIG SET version = '15.2';