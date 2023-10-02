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

UPDATE CONFIG set version = '13.4';