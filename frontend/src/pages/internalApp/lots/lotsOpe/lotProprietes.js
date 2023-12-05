import React, {useState} from 'react';
import { Card, Form, } from 'react-bootstrap';
import Flex from 'components/common/Flex';

import HabilitationService from 'services/habilitationsService';

import LotProprietesTable from './lotProprietesTable';
import LotProprietesForm from './lotProprietesForm';

const LotProprietes = ({lot, setPageNeedsRefresh}) => {
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
                        Détails du lot
                    </div>
                    <div className="p-2">
                        <Form.Check 
                            type='switch'
                            id='defaultSwitch'
                            label='Modifier'
                            onClick={handleEdition}
                            checked={modeEdition}
                            disabled={!HabilitationService.habilitations['lots_modification'] || lot.lot.inventaireEnCours}
                        />
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                {modeEdition ?
                    <LotProprietesForm
                        lot={lot.lot}
                        setModeEdition={setModeEdition}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                :
                    <LotProprietesTable
                        lot={lot}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                }
            </Card.Body>
        </Card>
    </>);
};

LotProprietes.propTypes = {};

export default LotProprietes;
