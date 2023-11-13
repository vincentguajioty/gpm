import React, {useState} from 'react';
import { Card, Form, } from 'react-bootstrap';
import Flex from 'components/common/Flex';

import HabilitationService from 'services/habilitationsService';

import VehiculeProprietesTable from './vehiculeProprietesTable';
import VehiculeProprietesForm from './vehiculeProprietesForm';

const VehiculeProprietes = ({vehicule, setPageNeedsRefresh}) => {
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
                        Détails du véhicule
                    </div>
                    <div className="p-2">
                        <Form.Check 
                            type='switch'
                            id='defaultSwitch'
                            label='Modifier'
                            onClick={handleEdition}
                            checked={modeEdition}
                            disabled={!HabilitationService.habilitations['vhf_equipement_modification']}
                        />
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                {modeEdition ?
                    <VehiculeProprietesForm vehicule={vehicule} setPageNeedsRefresh={setPageNeedsRefresh} setModeEdition={setModeEdition}/>
                :
                    <VehiculeProprietesTable vehicule={vehicule} setPageNeedsRefresh={setPageNeedsRefresh} />
                }
            </Card.Body>
        </Card>
    </>);
};

VehiculeProprietes.propTypes = {};

export default VehiculeProprietes;
