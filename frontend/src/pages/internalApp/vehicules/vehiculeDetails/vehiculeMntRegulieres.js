import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Form } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import PageHeader from 'components/common/PageHeader';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

const VehiculeMaintenancesRegulieres = ({idVehicule, maintenancesRegulieres, setPageNeedsRefresh}) => {
    return (<>
        En entête: softbadges des maintenances avec coloration pour avoir d'un coup d'oeil l'état des maintenances
        Tableau des maintenances régulières et gestion des alertes/paramétrage
    </>);
};

VehiculeMaintenancesRegulieres.propTypes = {};

export default VehiculeMaintenancesRegulieres;
