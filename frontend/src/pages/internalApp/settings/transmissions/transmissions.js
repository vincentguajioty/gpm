import React from 'react';
import { Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import SettingsMetierCRUD from 'components/settings/settingsMetierCRUD';
import { vhfEquipementsTypesDelete, vhfAccessoiresTypesDelete, vhfEtatsEquipementsDelete, vhfTechnologiesDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

const TransmissionsSettings = () => {
    const listeParametres = [
        {
            cardTitle: "Types d'équipements Radio",
            idAccessorForDb: "idVhfType",
            fields: [
                {dbName:"idVhfType", type:"int", displayName:"idVhfType", showInTable: false, showInForm: false,},
                {dbName:"libelleType", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "vhfTypes",
            boGetRoute: "/settingsMetiers/getVHFTypesEquipements",
            boAddRoute: "/settingsMetiers/addVHFTypesEquipements",
            boUpdateRoute: "/settingsMetiers/updateVHFTypesEquipements",
            boDeleteRoute: "/settingsMetiers/deleteVHFTypesEquipements",
            profilGet: "appli_conf",
            profilAdd: "appli_conf",
            profilUpdate: "appli_conf",
            profilDelete: "appli_conf",
            deleteWarning: vhfEquipementsTypesDelete,
        },
        {
            cardTitle: "Types d'accessoires Radio",
            idAccessorForDb: "idVhfAccessoireType",
            fields: [
                {dbName:"idVhfAccessoireType", type:"int", displayName:"idVhfAccessoireType", showInTable: false, showInForm: false,},
                {dbName:"libelleVhfAccessoireType", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "vhfAccessoiresTypes",
            boGetRoute: "/settingsMetiers/getVHFTypesAccessoires",
            boAddRoute: "/settingsMetiers/addVHFTypesAccessoires",
            boUpdateRoute: "/settingsMetiers/updateVHFTypesAccessoires",
            boDeleteRoute: "/settingsMetiers/deleteVHFTypesAccessoires",
            profilGet: "appli_conf",
            profilAdd: "appli_conf",
            profilUpdate: "appli_conf",
            profilDelete: "appli_conf",
            deleteWarning: vhfAccessoiresTypesDelete,
        },
        {
            cardTitle: "Etats des équipements Radio",
            idAccessorForDb: "idVhfEtat",
            fields: [
                {dbName:"idVhfEtat", type:"int", displayName:"idVhfEtat", showInTable: false, showInForm: false,},
                {dbName:"libelleVhfEtat", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "vhfEtats",
            boGetRoute: "/settingsMetiers/getEtatsVHF",
            boAddRoute: "/settingsMetiers/addEtatsVHF",
            boUpdateRoute: "/settingsMetiers/updateEtatsVHF",
            boDeleteRoute: "/settingsMetiers/deleteEtatsVHF",
            profilGet: "appli_conf",
            profilAdd: "appli_conf",
            profilUpdate: "appli_conf",
            profilDelete: "appli_conf",
            deleteWarning: vhfEtatsEquipementsDelete,
        },
        {
            cardTitle: "Technologies Radio",
            idAccessorForDb: "idVhfTechno",
            fields: [
                {dbName:"idVhfTechno", type:"int", displayName:"idVhfTechno", showInTable: false, showInForm: false,},
                {dbName:"libelleTechno", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "vhfTechnologies",
            boGetRoute: "/settingsMetiers/getTechnologiesVHF",
            boAddRoute: "/settingsMetiers/addTechnologiesVHF",
            boUpdateRoute: "/settingsMetiers/updateTechnologiesVHF",
            boDeleteRoute: "/settingsMetiers/deleteTechnologiesVHF",
            profilGet: "appli_conf",
            profilAdd: "appli_conf",
            profilUpdate: "appli_conf",
            profilDelete: "appli_conf",
            deleteWarning: vhfTechnologiesDelete,
        },
    ];

    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Transmissions"
            className="mb-3"
        />

        <Row>
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

TransmissionsSettings.propTypes = {};

export default TransmissionsSettings;
