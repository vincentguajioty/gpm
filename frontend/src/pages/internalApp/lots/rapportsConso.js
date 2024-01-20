import React, {useState, useEffect} from 'react';
import { Alert, Card, Accordion, } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import moment from 'moment-timezone';

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

            {ConfigurationService.config['consommation_benevoles_auto'] == true ?
                <Alert variant='info'>La configuration actuelle de l'outil valide automatiquement toutes les consommations saisies sans action de votre part.</Alert>
            : null}

            {rapports.length == 0 ?
                <Alert variant='info'>Aucun rapport de consommation à afficher</Alert>
            :
                <Card className="mb-3">
                    <Card.Body>
                        <Accordion>
                            {rapports.map((conso, i)=>{return(
                                <RapportsConsoOneAccordion
                                    idConsommation={conso.idConsommation}
                                    setPageNeedsRefesh={setPageNeedsRefesh}
                                />
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
