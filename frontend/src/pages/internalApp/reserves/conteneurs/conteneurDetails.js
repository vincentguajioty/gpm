import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Tabs, Tab, Alert, } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

import ConteneurProprietes from './conteneurProprietes';
import ReservesMaterielsTable from '../materiels/materielsTable';

const ConteneurDetails = () => {
    let {idConteneur} = useParams();
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [conteneur, setConteneur] = useState([]);
    const initPage = async () => {
        try {
            const getData = await Axios.post('/reserves/getOneConteneur',{
                idConteneur: idConteneur
            });
            setConteneur(getData.data);  
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
        return (<>
            <PageHeader
                preTitle="Réserves"
                title={conteneur.conteneur.libelleConteneur}
                className="mb-3"
            />

            {conteneur.conteneur.inventaireEnCours == true ?
                <Alert variant='warning'>Inventaire en cours sur ce conteneur. Toute modification du conteneur est donc impossible.</Alert>
            :null}

            <Row>
                <Col md={4}>
                    <ConteneurProprietes
                        conteneur={conteneur}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
                <Col md={8}>
                    <Card>
                        <Tabs defaultActiveKey="contenu" transition={true}>
                            <Tab eventKey="contenu" title="Matériels" className='border-bottom border-x p-3'>
                                <ReservesMaterielsTable
                                    filterIdConteneur={idConteneur}
                                />
                            </Tab>
                            
                            <Tab eventKey="inventaire" title="Inventaires" className='border-bottom border-x p-3'>
                                Tableau des inventaires
                            </Tab>
                        </Tabs>
                    </Card>
                </Col>
            </Row>
        </>);
    }
    else
    {
        return(<LoaderInfiniteLoop/>)
    }
};

ConteneurDetails.propTypes = {};

export default ConteneurDetails;
