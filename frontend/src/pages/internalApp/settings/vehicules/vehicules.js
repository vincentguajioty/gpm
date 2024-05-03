import React from 'react';
import { Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import SettingsMetierCRUD from 'components/settings/settingsMetierCRUD';
import { 
    etatsVehiculesDelete,
    vehiculesTypesDelete,
    vehiculesTypesDesinfectionsDelete,
    vehiculestypesHealthDelete,
    vehiculesTypesMaintenanceDelete,
    vehiculesCarburantsDelete,
    vehiculesPneumatiquesDelete,
} from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

const VehiculesSettings = () => {
    const listeParametres = [
        {
            cardTitle: "Etats des véhicules",
            idAccessorForDb: "idVehiculesEtat",
            fields: [
                {dbName:"idVehiculesEtat", type:"int", displayName:"idVehiculesEtat", showInTable: false, showInForm: false,},
                {dbName:"libelleVehiculesEtat", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "etatsVehicules",
            boGetRoute: "/settingsMetiers/getEtatsVehicules",
            boAddRoute: "/settingsMetiers/addEtatsVehicules",
            boUpdateRoute: "/settingsMetiers/updateEtatsVehicules",
            boDeleteRoute: "/settingsMetiers/deleteEtatsVehicules",
            profilGet: "etats_lecture",
            profilAdd: "etats_ajout",
            profilUpdate: "etats_modification",
            profilDelete: "etats_suppression",
            deleteWarning: etatsVehiculesDelete,
        },
        {
            cardTitle: "Types de véhicules",
            idAccessorForDb: "idVehiculesType",
            fields: [
                {dbName:"idVehiculesType", type:"int", displayName:"idVehiculesType", showInTable: false, showInForm: false,},
                {dbName:"libelleType", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "typesVehicules",
            boGetRoute: "/settingsMetiers/getTypesVehicules",
            boAddRoute: "/settingsMetiers/addTypesVehicules",
            boUpdateRoute: "/settingsMetiers/updateTypesVehicules",
            boDeleteRoute: "/settingsMetiers/deleteTypesVehicules",
            profilGet: "vehicules_types_lecture",
            profilAdd: "vehicules_types_ajout",
            profilUpdate: "vehicules_types_modification",
            profilDelete: "vehicules_types_suppression",
            deleteWarning: vehiculesTypesDelete,
        },
        {
            cardTitle: "Types de désinfection",
            idAccessorForDb: "idVehiculesDesinfectionsType",
            fields: [
                {dbName:"idVehiculesDesinfectionsType", type:"int", displayName:"idVehiculesDesinfectionsType", showInTable: false, showInForm: false,},
                {dbName:"libelleVehiculesDesinfectionsType", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
                {dbName:"affichageSynthese", type:"boolean", displayName:"Affiché sur la page de synthèse", showInTable: true, showInForm: true,},
            ],
            yupSchema: "typesDesinfections",
            boGetRoute: "/settingsMetiers/getTypesDesinfections",
            boAddRoute: "/settingsMetiers/addTypesDesinfections",
            boUpdateRoute: "/settingsMetiers/updateTypesDesinfections",
            boDeleteRoute: "/settingsMetiers/deleteTypesDesinfections",
            profilGet: "typesDesinfections_lecture",
            profilAdd: "typesDesinfections_ajout",
            profilUpdate: "typesDesinfections_modification",
            profilDelete: "typesDesinfections_suppression",
            deleteWarning: vehiculesTypesDesinfectionsDelete,
        },
        {
            cardTitle: "Types de maintenances régulières",
            idAccessorForDb: "idHealthType",
            fields: [
                {dbName:"idHealthType", type:"int", displayName:"idHealthType", showInTable: false, showInForm: false,},
                {dbName:"libelleHealthType", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
                {dbName:"affichageSynthese", type:"boolean", displayName:"Affiché sur la page de synthèse", showInTable: true, showInForm: true,},
            ],
            yupSchema: "typesHealthVehicules",
            boGetRoute: "/settingsMetiers/getTypesMaintenancesRegulieresVehicules",
            boAddRoute: "/settingsMetiers/addTypesMaintenancesRegulieresVehicules",
            boUpdateRoute: "/settingsMetiers/updateTypesMaintenancesRegulieresVehicules",
            boDeleteRoute: "/settingsMetiers/deleteTypesMaintenancesRegulieresVehicules",
            profilGet: "vehiculeHealthType_lecture",
            profilAdd: "vehiculeHealthType_ajout",
            profilUpdate: "vehiculeHealthType_modification",
            profilDelete: "vehiculeHealthType_suppression",
            deleteWarning: vehiculestypesHealthDelete,
        },
        {
            cardTitle: "Types de maintenance ponctuelle",
            idAccessorForDb: "idTypeMaintenance",
            fields: [
                {dbName:"idTypeMaintenance", type:"int", displayName:"idTypeMaintenance", showInTable: false, showInForm: false,},
                {dbName:"libelleTypeMaintenance", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "typesMaintenanceVehicules",
            boGetRoute: "/settingsMetiers/getTypesMaintenancesPonctuellesVehicules",
            boAddRoute: "/settingsMetiers/addTypesMaintenancesPonctuellesVehicules",
            boUpdateRoute: "/settingsMetiers/updateTypesMaintenancesPonctuellesVehicules",
            boDeleteRoute: "/settingsMetiers/deleteTypesMaintenancesPonctuellesVehicules",
            profilGet: "vehiculeHealthType_lecture",
            profilAdd: "vehiculeHealthType_ajout",
            profilUpdate: "vehiculeHealthType_modification",
            profilDelete: "vehiculeHealthType_suppression",
            deleteWarning: vehiculesTypesMaintenanceDelete,
        },
        {
            cardTitle: "Carburants",
            idAccessorForDb: "idCarburant",
            fields: [
                {dbName:"idCarburant", type:"int", displayName:"idCarburant", showInTable: false, showInForm: false,},
                {dbName:"libelleCarburant", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "carburants",
            boGetRoute: "/settingsMetiers/getCarburants",
            boAddRoute: "/settingsMetiers/addCarburants",
            boUpdateRoute: "/settingsMetiers/updateCarburants",
            boDeleteRoute: "/settingsMetiers/deleteCarburants",
            profilGet: "carburants_lecture",
            profilAdd: "carburants_ajout",
            profilUpdate: "carburants_modification",
            profilDelete: "carburants_suppression",
            deleteWarning: vehiculesCarburantsDelete,
        },
        {
            cardTitle: "Pneumatiques",
            idAccessorForDb: "idPneumatique",
            fields: [
                {dbName:"idPneumatique", type:"int", displayName:"idPneumatique", showInTable: false, showInForm: false,},
                {dbName:"libellePneumatique", type:"text", displayName:"Libellé", showInTable: true, showInForm: true,},
            ],
            yupSchema: "pneumatiques",
            boGetRoute: "/settingsMetiers/getPneumatiques",
            boAddRoute: "/settingsMetiers/addPneumatiques",
            boUpdateRoute: "/settingsMetiers/updatePneumatiques",
            boDeleteRoute: "/settingsMetiers/deletePneumatiques",
            profilGet: "carburants_lecture",
            profilAdd: "carburants_ajout",
            profilUpdate: "carburants_modification",
            profilDelete: "carburants_suppression",
            deleteWarning: vehiculesPneumatiquesDelete,
        },
    ];

    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Véhicules"
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

VehiculesSettings.propTypes = {};

export default VehiculesSettings;
