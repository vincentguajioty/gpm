import React, {useState, useEffect} from 'react';
import { Accordion, Alert, Button, Card } from 'react-bootstrap';
import moment from 'moment-timezone';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';
import TenuesPublicService from 'services/tenuesPublicService';

const TenuesPretPublic = ({
    setPageNeedsRefresh,
    externe,
}) => {
    
    return(<>
        <IconButton
            icon='clock'
            size = 'sm'
            variant="success"
            className="me-1 mb-2"
        >
        Demander le prêt d'un élément</IconButton>
    </>)
};

TenuesPretPublic.propTypes = {};

export default TenuesPretPublic;