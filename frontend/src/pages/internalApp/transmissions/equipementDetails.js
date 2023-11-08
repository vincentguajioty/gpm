import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

import EquipementVhfProprietes from './equipementProprietes';
import EquipementVhfAccessoires from './equipementAccessoires';
import EquipementVhfPJ from './equipementPJ';

const EquipementVhfDetails = () => {
    let {idVhfEquipement} = useParams();

    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [equipement, setEquipement] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.post('/vhf/getOneEquipement',{
                idVhfEquipement: idVhfEquipement
            });
            setEquipement(getData.data[0]);  
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage()
    }, [])
    useEffect(() => {
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    }, [pageNeedsRefresh])

    if(readyToDisplay)
    {
        return(<>
            <PageHeader
                preTitle="Transmissions"
                title={equipement.vhfIndicatif}
                className="mb-3"
            />

            <Row>
                <Col md={4}>
                    <EquipementVhfProprietes
                        equipement={equipement}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
                <Col md={8}>
                    <EquipementVhfAccessoires
                        equipement={equipement}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                    <EquipementVhfPJ
                        equipement={equipement}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
            </Row>
        </>);
    }
    else
    {
        return(<LoaderInfiniteLoop/>)
    }
};

EquipementVhfDetails.propTypes = {};

export default EquipementVhfDetails;