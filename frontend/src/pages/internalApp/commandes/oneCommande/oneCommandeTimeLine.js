import React, {useState, useEffect} from 'react';
import { Link, useNavigate, } from 'react-router-dom';
import { Card, Offcanvas, Button, Form, Tab, Nav, Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const OneCommandeTimeLine = ({
    idCommande,
    commande,
}) => {
    
    return (<>
        TimeLine Historique de la commande
    </>);
};

OneCommandeTimeLine.propTypes = {};

export default OneCommandeTimeLine;
