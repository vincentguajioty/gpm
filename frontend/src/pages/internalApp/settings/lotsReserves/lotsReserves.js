import React from 'react';
import { Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import SettingsMetierCRUD from 'components/settings/settingsMetierCRUD';

import Catalogue from './catalogue';
import CodesBarre from './codesBarre';

import HabilitationService from 'services/habilitationsService';

const LotsReservesSettings = () => {
    const listeParametres = [
        {
            cardTitle: "Catégories de matériels",
            idAccessorForDb: "idCategorie",
            fields: [
                {dbName:"idCategorie", type:"int", displayName:"idCategorie", showInTable: false, showInForm: false,},
                {dbName:"libelleCategorie", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "categoriesMateriels",
            boGetRoute: "/settingsMetiers/getCategoriesMateriels",
            boAddRoute: "/settingsMetiers/addCategoriesMateriels",
            boUpdateRoute: "/settingsMetiers/updateCategoriesMateriels",
            boDeleteRoute: "/settingsMetiers/deleteCategoriesMateriels",
            profilGet: "categories_lecture",
            profilAdd: "categories_ajout",
            profilUpdate: "categories_modification",
            profilDelete: "categories_suppression",
        },
        {
            cardTitle: "Etats des lots",
            idAccessorForDb: "idLotsEtat",
            fields: [
                {dbName:"idLotsEtat", type:"int", displayName:"idLotsEtat", showInTable: false, showInForm: false,},
                {dbName:"libelleLotsEtat", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "etatsLots",
            boGetRoute: "/settingsMetiers/getEtatsLots",
            boAddRoute: "/settingsMetiers/addEtatsLots",
            boUpdateRoute: "/settingsMetiers/updateEtatsLots",
            boDeleteRoute: "/settingsMetiers/deleteEtatsLots",
            profilGet: "etats_lecture",
            profilAdd: "etats_ajout",
            profilUpdate: "etats_modification",
            profilDelete: "etats_suppression",
        },
        {
            cardTitle: "Etats des matériels",
            idAccessorForDb: "idMaterielsEtat",
            fields: [
                {dbName:"idMaterielsEtat", type:"int", displayName:"idMaterielsEtat", showInTable: false, showInForm: false,},
                {dbName:"libelleMaterielsEtat", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "etatsMateriels",
            boGetRoute: "/settingsMetiers/getEtatsMateriels",
            boAddRoute: "/settingsMetiers/addEtatsMateriels",
            boUpdateRoute: "/settingsMetiers/updateEtatsMateriels",
            boDeleteRoute: "/settingsMetiers/deleteEtatsMateriels",
            profilGet: "etats_lecture",
            profilAdd: "etats_ajout",
            profilUpdate: "etats_modification",
            profilDelete: "etats_suppression",
        },
    ];

    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Lots et réserves"
            className="mb-3"
        />

        <Row>
            {HabilitationService.habilitations['catalogue_lecture'] ? 
                <Col md={12}>
                    <Catalogue />    
                </Col>
            :null}

            {HabilitationService.habilitations['codeBarre_lecture'] ? 
                <Col md={12}>
                    <CodesBarre />    
                </Col>
            :null}

            {listeParametres.map((param, i) => {
                if(HabilitationService.habilitations[param.profilGet])
                {
                    return(
                    <Col md={4}>
                        <SettingsMetierCRUD parametre={param} />
                    </Col>);
                }
            })}
        </Row>
    </>);
};

LotsReservesSettings.propTypes = {};

export default LotsReservesSettings;
