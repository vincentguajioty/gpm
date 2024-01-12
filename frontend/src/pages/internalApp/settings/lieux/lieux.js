import React from 'react';
import { Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import SettingsMetierCRUD from 'components/settings/settingsMetierCRUD';
import { lieuxDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

const LieuxSettings = () => {
    const listeParametres = [
        {
            cardTitle: "Lieux",
            idAccessorForDb: "idLieu",
            fields: [
                {dbName:"idLieu", type:"int", displayName:"idLieu", showInTable: false, showInForm: false,},
                {dbName:"libelleLieu", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
                {dbName:"adresseLieu", type:"textLong", displayName:"Adresse", showInTable: true, showInForm: true,},
                {dbName:"detailsLieu", type:"textLong", displayName:"Détails", showInTable: true, showInForm: true,},
                {dbName:"accesReserve", type:"boolean", displayName:"Accès Réservé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "lieuxSettings",
            boGetRoute: "/settingsMetiers/getLieux",
            boAddRoute: "/settingsMetiers/addLieux",
            boUpdateRoute: "/settingsMetiers/updateLieux",
            boDeleteRoute: "/settingsMetiers/deleteLieux",
            profilGet: "lieux_lecture",
            profilAdd: "lieux_ajout",
            profilUpdate: "lieux_modification",
            profilDelete: "lieux_suppression",
            deleteWarning: lieuxDelete,
        },
    ];

    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Lieux de stockage"
            className="mb-3"
        />

        <Row>
            {listeParametres.map((param, i) => {
                if(HabilitationService.habilitations[param.profilGet])
                {
                    return(
                    <Col md={12}>
                        <SettingsMetierCRUD parametre={param} />
                    </Col>);
                }
            })}
        </Row>
    </>);
};

LieuxSettings.propTypes = {};

export default LieuxSettings;
