ALTER TABLE TENUES_AFFECTATION DROP FOREIGN KEY fk_tenuesAff_Catalogue;
ALTER TABLE TENUES_AFFECTATION DROP idCatalogueTenue;

ALTER TABLE TENUES_CATALOGUE DROP libelleCatalogueTenue;
ALTER TABLE TENUES_CATALOGUE DROP tailleCatalogueTenue;

UPDATE MATERIEL_CATALOGUE SET taille = null WHERE taille = "";
UPDATE MATERIEL_CATALOGUE SET conditionnementMultiple = null WHERE conditionnementMultiple = "";
UPDATE MATERIEL_CATALOGUE SET commentairesMateriel = null WHERE commentairesMateriel = "";

UPDATE CONFIG SET version = '16.0';