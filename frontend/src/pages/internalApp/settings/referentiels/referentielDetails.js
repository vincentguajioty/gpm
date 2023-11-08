import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Card, Form } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import PageHeader from 'components/common/PageHeader';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import ReferentielContenuTableau from './referentielContenuTableau';
import ReferentielContenuForm from './referentielContenuForm';

import { Axios } from 'helpers/axios';

const ReferentielDetails = () => {
    let {idTypeLot} = useParams();

    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [referentiel, setReferentiel] = useState([]);
    
    const initPage = async () => {
        try {
            const getData = await Axios.post('/referentiels/getOneReferentiel',{
                idTypeLot: idTypeLot
            });
            setReferentiel(getData.data[0]);  
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
            setModeEdition(false);
            initPage();
        }
    }, [pageNeedsRefresh])

    const [modeEdition, setModeEdition] = useState(false);
    const handleEdition = () => {
        setModeEdition(!modeEdition);
    }

    if(readyToDisplay)
    {
        return (<>
            <PageHeader
                preTitle="Attention - Zone de paramétrage"
                title={referentiel.libelleTypeLot}
                className="mb-3"
            />

            <Card className="mb-3">
                <Card.Header className="p-2 border-bottom">
                    <Flex>
                        <div className="p-2 flex-grow-1">
                            <FontAwesomeIcon icon="landmark" className="me-1"/> Contenu du référentiel
                        </div>
                        <div className="p-2">
                            <Form.Check 
                                type='switch'
                                id='defaultSwitch'
                                label='Modifier'
                                onClick={handleEdition}
                                checked={modeEdition}
                                disabled={!HabilitationService.habilitations['typesLots_modification']}
                            />
                        </div>
                    </Flex>
                </Card.Header>
                <Card.Body>
                    {modeEdition ? <ReferentielContenuForm referentiel={referentiel} setPageNeedsRefresh={setPageNeedsRefresh} /> : <ReferentielContenuTableau contenu={referentiel.contenu} />}
                </Card.Body>
            </Card>
        </>);
    }
    else
    {
        return(<LoaderInfiniteLoop/>)
    }
};

ReferentielDetails.propTypes = {};

export default ReferentielDetails;
