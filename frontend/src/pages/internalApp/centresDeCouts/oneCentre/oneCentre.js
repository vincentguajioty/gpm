import React, {useState, useEffect} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

import OneCentreTabs from './oneCentreTabs';
import OneCentreCards from './oneCentreCards';

const OneCentre = () => {
    let {idCentreDeCout} = useParams();
    const [centre, setCentre] = useState([]);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    const [pageReady, setPageReady] = useState(false);

    const initPage = async () => {
        try {
            let getCentre = await Axios.post('/centresCouts/getOneCentre',{
                idCentreDeCout: idCentreDeCout,
            })
            setCentre(getCentre.data);
            setPageReady(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    useEffect(()=>{
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    },[pageNeedsRefresh])
    
    return (
        pageReady ?
            <>
                <PageHeader
                    preTitle='Centres de couts'
                    title={centre.centreDetails.libelleCentreDecout}
                    description={centre.centreDetails.statutOuverture + ' | Solde: ' + centre.centreDetails.soldeActuel+' €'}
                    className="mb-3"
                />

                <Row>
                    <Col md={3}>
                        <OneCentreCards
                            idCentreDeCout={idCentreDeCout}
                            centre={centre}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                    </Col>
                    <Col md={9}>
                        <Card>
                            <OneCentreTabs
                                idCentreDeCout={idCentreDeCout}
                                centre={centre}
                                setPageNeedsRefresh={setPageNeedsRefresh}
                            />
                        </Card>
                    </Col>
                </Row>
            </>
        : <LoaderInfiniteLoop/>
    );
};

OneCentre.propTypes = {};

export default OneCentre;
