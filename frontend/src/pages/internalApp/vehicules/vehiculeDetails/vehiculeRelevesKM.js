import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Form } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import PageHeader from 'components/common/PageHeader';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

const VehiculeRelevesKM = ({idVehicule, relevesKM, setPageNeedsRefresh}) => {
    return (<>
        Graphique des KM avec un tableau de données et le form de saisie des releves
    </>);
};

VehiculeRelevesKM.propTypes = {};

export default VehiculeRelevesKM;
