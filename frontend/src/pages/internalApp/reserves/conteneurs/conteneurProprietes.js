import React, {useState} from 'react';
import { Card, Form, } from 'react-bootstrap';
import Flex from 'components/common/Flex';

import HabilitationService from 'services/habilitationsService';

import ConteneurProprietesTable from './conteneurProprietesTable';
import ConteneurProprietesForm from './conteneurProprietesForm';

const ConteneurProprietes = ({conteneur, setPageNeedsRefresh}) => {
    const [modeEdition, setModeEdition] = useState(false);
    const handleEdition = () => {
        if(!modeEdition)
        {
            initForm();
        }
        setModeEdition(!modeEdition);
    }

    const initForm = async () => {
        try {
            
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <Card className="mb-3">
            <Card.Header className="p-2 border-bottom">
                <Flex>
                    <div className="p-2 flex-grow-1">
                        Détails du conteneur
                    </div>
                    <div className="p-2">
                        <Form.Check 
                            type='switch'
                            id='defaultSwitch'
                            label='Modifier'
                            onClick={handleEdition}
                            checked={modeEdition}
                            disabled={!HabilitationService.habilitations['reserve_modification'] || conteneur.conteneur.inventaireEnCours}
                        />
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                {modeEdition ?
                  <ConteneurProprietesForm
                        conteneur={conteneur.conteneur}
                        setModeEdition={setModeEdition}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                :
                  <ConteneurProprietesTable
                        conteneur={conteneur}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                }
            </Card.Body>
        </Card>
    </>);
};

ConteneurProprietes.propTypes = {};

export default ConteneurProprietes;
