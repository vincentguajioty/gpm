const db = require('../db');
const logger = require('../winstonLogger');
const fonctionsDelete = require('../helpers/fonctionsDelete');
const fonctionsMetiers = require('../helpers/fonctionsMetiers');
const excelJS = require("exceljs");

//Réserves - Gestion générale
exports.getConteneurs = async (req, res)=>{
    try {
        let results = await db.query(`
            SELECT
                l.*,
                DATE_ADD(l.dateDernierInventaire, INTERVAL frequenceInventaire DAY) as prochainInventaire,
                li.libelleLieu
            FROM
                RESERVES_CONTENEUR l
                LEFT OUTER JOIN LIEUX li ON li.idLieu = l.idLieu
            ORDER BY
                l.libelleConteneur ASC
        ;`);

        for(const conteneur of results)
        {
            let materielsOK = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    RESERVES_MATERIEL e
                WHERE
                    idConteneur = :idConteneur
                    AND
                    (
                        quantiteReserve > quantiteAlerteReserve AND
                        (
                            peremptionNotificationReserve > CURRENT_DATE
                            OR
                            peremptionNotificationReserve IS NULL
                        )
                    )
            ;`,{
                idConteneur: conteneur.idConteneur,
            });
            let materielsLimites = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    RESERVES_MATERIEL e
                WHERE
                    idConteneur = :idConteneur
                    AND
                    (
                        (
                            quantiteReserve = quantiteAlerteReserve
                            AND
                            peremptionNotificationReserve = CURRENT_DATE
                        )
                        OR
                        (
                            quantiteReserve = quantiteAlerteReserve
                            AND
                            (
                                peremptionNotificationReserve > CURRENT_DATE
                                OR
                                peremptionNotificationReserve IS NULL
                            )
                        )
                        OR
                        (
                            quantiteReserve > quantiteAlerteReserve
                            AND
                            peremptionNotificationReserve = CURRENT_DATE
                        )
                    )
            ;`,{
                idConteneur: conteneur.idConteneur,
            });
            let materielsAlerte = await db.query(`
                SELECT
                    COUNT(*) as nb
                FROM
                    RESERVES_MATERIEL e
                WHERE
                    idConteneur = :idConteneur
                    AND
                    (
                        quantiteReserve < quantiteAlerteReserve OR
                        peremptionNotificationReserve < CURRENT_DATE
                    )
            ;`,{
                idConteneur: conteneur.idConteneur,
            });

            conteneur.materielsOK = materielsOK[0].nb;
            conteneur.materielsLimites = materielsLimites[0].nb;
            conteneur.materielsAlerte = materielsAlerte[0].nb;
        }

        res.send(results);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getOneConteneur = async (req, res)=>{
    try {
        let conteneur = await db.query(`
            SELECT
                l.*,
                DATE_ADD(l.dateDernierInventaire, INTERVAL frequenceInventaire DAY) as prochainInventaire,
                li.libelleLieu
            FROM
                RESERVES_CONTENEUR l
                LEFT OUTER JOIN LIEUX li ON li.idLieu = l.idLieu
            WHERE
                idConteneur = :idConteneur
        ;`,{
            idConteneur: req.body.idConteneur || null,
        });
        conteneur = conteneur[0];

        let materielsOK = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                RESERVES_MATERIEL e
            WHERE
                idConteneur = :idConteneur
                AND
                (
                    quantiteReserve > quantiteAlerteReserve AND
                    (
                        peremptionNotificationReserve > CURRENT_DATE
                        OR
                        peremptionNotificationReserve IS NULL
                    )
                )
        ;`,{
            idConteneur: conteneur.idConteneur,
        });
        let materielsLimites = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                RESERVES_MATERIEL e
            WHERE
                idConteneur = :idConteneur
                AND
                (
                    (
                        quantiteReserve = quantiteAlerteReserve
                        AND
                        peremptionNotificationReserve = CURRENT_DATE
                    )
                    OR
                    (
                        quantiteReserve = quantiteAlerteReserve
                        AND
                        (
                            peremptionNotificationReserve > CURRENT_DATE
                            OR
                            peremptionNotificationReserve IS NULL
                        )
                    )
                    OR
                    (
                        quantiteReserve > quantiteAlerteReserve
                        AND
                        peremptionNotificationReserve = CURRENT_DATE
                    )
                )
        ;`,{
            idConteneur: conteneur.idConteneur,
        });
        let materielsAlerte = await db.query(`
            SELECT
                COUNT(*) as nb
            FROM
                RESERVES_MATERIEL e
            WHERE
                idConteneur = :idConteneur
                AND
                (
                    quantiteReserve < quantiteAlerteReserve OR
                    peremptionNotificationReserve < CURRENT_DATE
                )
        ;`,{
            idConteneur: conteneur.idConteneur,
        });

        conteneur.materielsOK = materielsOK[0].nb;
        conteneur.materielsLimites = materielsLimites[0].nb;
        conteneur.materielsAlerte = materielsAlerte[0].nb;

        let inventaires = await db.query(`
            SELECT
                i.*,
                p.identifiant,
                p.nomPersonne,
                p.prenomPersonne
            FROM
                RESERVES_INVENTAIRES i
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne
            WHERE
                idConteneur = :idConteneur
            ORDER BY
                dateInventaire DESC
        ;`,{
            idConteneur: req.body.idConteneur || null,
        });

        res.send({
            conteneur: conteneur,
            inventaires: inventaires,
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.addConteneur = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                RESERVES_CONTENEUR
            SET
                libelleConteneur = :libelleConteneur,
                dateDernierInventaire = :dateDernierInventaire,
                frequenceInventaire = :frequenceInventaire,
                dispoBenevoles = false
        `,{
            libelleConteneur: req.body.libelleConteneur || null,
            dateDernierInventaire: req.body.dateDernierInventaire || null,
            frequenceInventaire: req.body.frequenceInventaire || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idConteneur) as idConteneur FROM RESERVES_CONTENEUR;'
        );

        res.status(201);
        res.json({idConteneur: selectLast[0].idConteneur});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.updateConteneur = async (req, res)=>{
    try {
        const result = await db.query(`
            UPDATE
                RESERVES_CONTENEUR
            SET
                idLieu = :idLieu,
                libelleConteneur = :libelleConteneur,
                dateDernierInventaire = :dateDernierInventaire,
                frequenceInventaire = :frequenceInventaire,
                dispoBenevoles = :dispoBenevoles
            WHERE
                idConteneur = :idConteneur
        `,{
            idLieu: req.body.idLieu || null,
            libelleConteneur: req.body.libelleConteneur || null,
            dateDernierInventaire: req.body.dateDernierInventaire || null,
            frequenceInventaire: req.body.frequenceInventaire || null,
            dispoBenevoles: req.body.dispoBenevoles || false,
            idConteneur: req.body.idConteneur || null,
        });

        res.sendStatus(201);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.reserveConteneurDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.reserveConteneurDelete(req.verifyJWTandProfile.idPersonne , req.body.idConteneur);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Réserves - Inventaires
exports.getOneInventaireForDisplay = async (req, res)=>{
    try {
        let inventaire = await db.query(`
            SELECT
                i.*,
                l.libelleConteneur,
                p.prenomPersonne,
                p.nomPersonne,
                p.identifiant
            FROM
                RESERVES_INVENTAIRES i
                LEFT OUTER JOIN RESERVES_CONTENEUR l ON i.idConteneur = l.idConteneur
                LEFT OUTER JOIN PERSONNE_REFERENTE p ON i.idPersonne = p.idPersonne
            WHERE
                idReserveInventaire = :idReserveInventaire
        ;`,{
            idReserveInventaire: req.body.idReserveInventaire,
        });

        let contenu = await db.query(`
            SELECT
                i.*,
                c.libelleMateriel,
                cat.libelleCategorie
            FROM
                RESERVES_INVENTAIRES_CONTENUS i
                LEFT OUTER JOIN VIEW_MATERIEL_CATALOGUE_OPE c ON i.idMaterielCatalogue = c.idMaterielCatalogue
                LEFT OUTER JOIN MATERIEL_CATEGORIES cat ON c.idCategorie = cat.idCategorie
            WHERE
                idReserveInventaire = :idReserveInventaire
            ORDER BY
                cat.libelleCategorie,
                c.libelleMateriel
        ;`,{
            idReserveInventaire: req.body.idReserveInventaire,
        });

        res.send({
            inventaire: inventaire[0],
            contenu: contenu,
        });
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.startInventaire = async (req, res)=>{
    try {
        const result = await db.query(`
            INSERT INTO
                RESERVES_INVENTAIRES
            SET
                idConteneur = :idConteneur,
                dateInventaire = :dateInventaire,
                idPersonne = :idPersonne,
                inventaireEnCours = true
        `,{
            idConteneur: req.body.idConteneur || null,
            dateInventaire: req.body.dateInventaire || null,
            idPersonne: req.body.idPersonne || null,
        });
        
        let selectLast = await db.query(
            'SELECT MAX(idReserveInventaire) as idReserveInventaire FROM RESERVES_INVENTAIRES;'
        );

        const updateLotInv = await db.query(`
            UPDATE
                RESERVES_CONTENEUR
            SET
                inventaireEnCours = true
            WHERE
                idConteneur = :idConteneur
        `,{
            idConteneur: req.body.idConteneur || null,
        });

        if(req.body.isLastInventaire && req.body.isLastInventaire == true)
        {
            const updateLotDate = await db.query(`
                UPDATE
                    RESERVES_CONTENEUR
                SET
                    dateDernierInventaire = :dateInventaire
                WHERE
                    idConteneur = :idConteneur
            `,{
                idConteneur: req.body.idConteneur || null,
                dateInventaire: req.body.dateInventaire || null,
            });
        }

        const intialisationInventaire = await db.query(`
            INSERT INTO
                RESERVES_INVENTAIRES_TEMP
            (
                idReserveInventaire,
                idConteneur,
                idReserveElement,
                idMaterielCatalogue,
                libelleMateriel,
                quantiteAvantInventaire,
                quantiteInventoriee,
                quantiteAlerte,
                peremptionAvantInventaire,
                peremptionInventoriee
            )
            SELECT
                :idReserveInventaire as idReserveInventaire,
                e.idConteneur,
                e.idReserveElement,
                e.idMaterielCatalogue,
                cat.libelleMateriel,
                e.quantiteReserve as quantiteAvantInventaire,
                0 as quantiteInventoriee,
                e.quantiteAlerteReserve as quantiteAlerte,
                e.peremptionReserve as peremptionAvantInventaire,
                null as peremptionInventoriee
            FROM
                RESERVES_MATERIEL e
                LEFT OUTER JOIN RESERVES_CONTENEUR emp ON e.idConteneur = emp.idConteneur
                LEFT OUTER JOIN VIEW_MATERIEL_CATALOGUE_OPE cat ON e.idMaterielCatalogue = cat.idMaterielCatalogue
            WHERE
                e.idConteneur = :idConteneur
        `,{
            idReserveInventaire: selectLast[0].idReserveInventaire,
            idConteneur: req.body.idConteneur,
        });

        res.status(201);
        res.json({idReserveInventaire: selectLast[0].idReserveInventaire});
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.getAllElementsInventaireEnCours = async (req, res)=>{
    try {
        let elements = await db.query(`
            SELECT
                *
            FROM
                RESERVES_INVENTAIRES_TEMP
            WHERE
                idReserveInventaire = :idReserveInventaire
        ;`,{
            idReserveInventaire: req.body.idReserveInventaire || null,
        });

        res.send(elements);
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.reserveInventaireCancel = async (req, res)=>{
    try {
        let idConteneur = await db.query(
            'SELECT idConteneur FROM RESERVES_INVENTAIRES WHERE idReserveInventaire = :idReserveInventaire;'
        ,{
            idReserveInventaire: req.body.idReserveInventaire,
        });
        idConteneur = idConteneur[0].idConteneur;

        const deleteResult = await fonctionsDelete.reserveInventaireDelete(req.verifyJWTandProfile.idPersonne , req.body.idReserveInventaire);
        
        if(deleteResult){
            const updateLotInv = await db.query(`
                UPDATE
                    RESERVES_CONTENEUR
                SET
                    inventaireEnCours = false
                WHERE
                    idConteneur = :idConteneur
            `,{
                idConteneur: idConteneur || null,
            });
            
            res.status(201);
            res.json({idConteneur: idConteneur});

        }else{
            res.sendStatus(500);
        }

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

exports.reserveInventaireDelete = async (req, res)=>{
    try {
        const deleteResult = await fonctionsDelete.reserveInventaireDelete(req.verifyJWTandProfile.idPersonne , req.body.idReserveInventaire);
        if(deleteResult){res.sendStatus(201);}else{res.sendStatus(500);}
    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}

//Réserves - Exports données conteneurs et matériels
exports.exporterReservesEtendues = async (req, res)=>{
    try {
        const conteneurs = await db.query(`
            SELECT
                l.*,
                DATE_ADD(l.dateDernierInventaire, INTERVAL frequenceInventaire DAY) as prochainInventaire,
                li.libelleLieu
            FROM
                RESERVES_CONTENEUR l
                LEFT OUTER JOIN LIEUX li ON li.idLieu = l.idLieu
            ORDER BY
                l.libelleConteneur ASC
        ;`);

        const workbook = new excelJS.Workbook();
        
        //FEUILLE LOTS
        const worksheetConteneurs = workbook.addWorksheet('Conteneurs');
        worksheetConteneurs.columns = [
            { header: "Numéro interne",           key: "idConteneur",           width: 15 },
            { header: "Libellé",                  key: "libelleConteneur",      width: 30 },
            { header: "Lieu de stockage",         key: "libelleLieu",           width: 30 },
            { header: "Dernier inventaire",       key: "dateDernierInventaire", width: 20 },
            { header: "Fréquence inventaire (j)", key: "frequenceInventaire",   width: 20 },
            { header: "Prochain inventaire",      key: "prochainInventaire",    width: 20 },
        ];

        for(const conteneur of conteneurs)
        {
            worksheetConteneurs.addRow(conteneur);
        }

        worksheetConteneurs.getRow(1).eachCell((cell) => {
            cell.font = { bold: true };
        });


        //FEUILLES INDIVIDUELLES PAR LOTS
        for(const conteneur of conteneurs)
        {
            const worksheetConteneurIndiv = workbook.addWorksheet(conteneur.libelleConteneur);
            const materiels = await db.query(`
                SELECT
                    *
                FROM
                    RESERVES_CONTENEUR c
                    LEFT OUTER JOIN RESERVES_MATERIEL elem ON c.idConteneur = elem.idConteneur
                    LEFT OUTER JOIN VIEW_MATERIEL_CATALOGUE_OPE cat ON elem.idMaterielCatalogue = cat.idMaterielCatalogue
                WHERE
                    c.idConteneur = :idConteneur
                ORDER BY
                    c.libelleConteneur ASC,
                    cat.libelleMateriel ASC
            ;`,{
                idConteneur: conteneur.idConteneur,
            });
            worksheetConteneurIndiv.columns = [
                { header: "Réserve",                                   key: "libelleConteneur",              width: 30 },
                { header: "Matériel",                                  key: "libelleMateriel",               width: 30 },
                { header: "Quantité",                                  key: "quantiteReserve",               width: 10 },
                { header: "Quantité d'alerte",                         key: "quantiteAlerteReserve",         width: 20 },
                { header: "Péremption",                                key: "peremptionReserve",             width: 15 },
                { header: "Anticpation de la péremption (j)",          key: "peremptionNotificationReserve", width: 20 },
                { header: "Notification prévisionnelle de péremption", key: "peremptionNotificationReserve", width: 20 },
            ];

            for(const materiel of materiels)
            {
                worksheetConteneurIndiv.addRow(materiel);
            }

            worksheetConteneurIndiv.getRow(1).eachCell((cell) => {
                cell.font = { bold: true };
            });
        }

        let fileName = Date.now() + '-ExportReserves.xlsx';

        const saveFile = await workbook.xlsx.writeFile('temp/'+fileName);

        res.send({fileName: fileName});

    } catch (error) {
        logger.error(error);
        res.sendStatus(500);
    }
}