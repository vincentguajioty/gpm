import React, {useState, useEffect} from 'react';
import { Alert, Card, Accordion, AccordionContext } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';
import { faHome } from '@fortawesome/free-solid-svg-icons';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';
import ConfigurationService from 'services/configurationService';

import { Axios } from 'helpers/axios';

import RapportsConsoOneAccordion from './rapportsConso/oneRapportAccordion';
import SoftBadge from 'components/common/SoftBadge';

const RapportsConso = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [rapports, setRapports] = useState([]);
    const [pageNeedsRefresh, setPageNeedsRefesh] = useState(false);

    const initPage = async () => {
        try {
            if(ConfigurationService.config['consommation_benevoles'] == true)
            {
                const getFromDB = await Axios.get('/consommations/getAllConso');
                setRapports(getFromDB.data);
            }

            setReadyToDisplay(true);
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
            setPageNeedsRefesh(false);
            initPage();
        }
    },[pageNeedsRefresh])

    return (<>
        <PageHeader
            preTitle="Lots opérationnels"
            title="Rapports de consommation de matériel"
            className="mb-3"
        />

        {readyToDisplay ?<>

            {ConfigurationService.config['consommation_benevoles'] != true ?
                <Alert variant='warning'>La fonctionnalité de saisie de rapports de consommation est désactivée.</Alert>
            : null}

            {rapports.length == 0 ?
                <Alert variant='info'>Aucun rapport de consommation à afficher</Alert>
            :
                <Card className="mb-3">
                    <Card.Body>
                        <Accordion>
                            {rapports.map((conso, i)=>{return(
                                <Accordion.Item eventKey={conso.idConsommation} flush="true">
                                    <Accordion.Header>
                                        {conso.declarationEnCours && conso.reapproEnCours ?
                                            <SoftBadge bg="info" className='me-1'>Réappro</SoftBadge>
                                            : conso.declarationEnCours ? <SoftBadge bg="info" className='me-1'>Saisie</SoftBadge>
                                            : conso.qttMaterielsNonTraites > 0 ? <SoftBadge bg="danger" className='me-1'>A TRAITER</SoftBadge>
                                            : <SoftBadge bg="success" className='me-1'>Traité</SoftBadge>
                                        }
                                        {moment(conso.dateConsommation).format('DD/MM/YYYY')} - {conso.evenementConsommation}
                                    </Accordion.Header>
                                    <Accordion.Body>
                                        <RapportsConsoOneAccordion
                                            idConsommation={conso.idConsommation}
                                            setPageNeedsRefesh={setPageNeedsRefesh}
                                        />
                                    </Accordion.Body>
                                </Accordion.Item>
                            )})}
                        </Accordion>
                    </Card.Body>
                </Card>
            }
        
        </>: <LoaderInfiniteLoop/>}

        
    </>);
};

RapportsConso.propTypes = {};

export default RapportsConso;
