import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Tabs, Tab, Alert, } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import LotProprietes from './lotProprietes';
import LotSacs from './lotSacs';
import LotConformiteReferentiel from './lotConfRef';
import AlertesBenevolesLotsTable from '../alertesBenevolesTable';

const LotDetails = () => {
    let {idLot} = useParams();
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [lot, setLot] = useState([]);
    const initPage = async () => {
        try {
            const getData = await Axios.post('/lots/getOneLot',{
                idLot: idLot
            });
            setLot(getData.data);  
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
                preTitle="Lots opérationnels"
                title={lot.lot.libelleLot}
                className="mb-3"
            />

            {lot.lot.inventaireEnCours == true ?
                <Alert variant='warning'>Inventaire en cours sur ce lot. Toute modification du lot est donc impossible.</Alert>
            :null}

            <Row>
                <Col md={4}>
                    <LotProprietes
                        lot={lot}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
                <Col md={8}>
                    <Card>
                        <Tabs defaultActiveKey="contenu" transition={true}>
                            <Tab eventKey="contenu" title="Sacs" className='border-bottom border-x p-3'>
                                <LotSacs
                                    sacs={lot.sacs}
                                    inventaireEnCours={lot.lot.inventaireEnCours}
                                    setPageNeedsRefresh={setPageNeedsRefresh}
                                />
                            </Tab>
                            {lot.lot.idTypeLot > 0 ?
                                <Tab eventKey="ref" title="Référentiel" className='border-bottom border-x p-3'>
                                    <LotConformiteReferentiel
                                        analyseRef={lot.analyseRef}
                                    />
                                </Tab>
                            : null}
                            <Tab eventKey="inventaire" title="Inventaires" className='border-bottom border-x p-3'>
                                Tableau des inventaires
                            </Tab>
                            {HabilitationService.habilitations.alertesBenevolesLots_lecture ?
                                <Tab eventKey="alertes" title="Alertes de bénévoles" className='border-bottom border-x p-3'>
                                    <AlertesBenevolesLotsTable idLot={idLot} setPageNeedsRefresh={setPageNeedsRefresh} />
                                </Tab>
                            : null}
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

LotDetails.propTypes = {};

export default LotDetails;
