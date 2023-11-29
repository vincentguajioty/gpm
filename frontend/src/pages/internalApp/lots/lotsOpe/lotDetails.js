import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Tabs, Tab, } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';


const LotDetails = () => {
    let {idLot} = useParams();
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    
    if(readyToDisplay)
    {
        return (<>
            <PageHeader
                preTitle="Véhicules"
                title={vehicule.libelleVehicule}
                className="mb-3"
            />

            
        </>);
    }
    else
    {
        return(<LoaderInfiniteLoop/>)
    }
};

LotDetails.propTypes = {};

export default LotDetails;
