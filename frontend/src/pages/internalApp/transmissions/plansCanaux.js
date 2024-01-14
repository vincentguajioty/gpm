import React, { useState } from 'react';
import { Button, Form, Table, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconButton from 'components/common/IconButton';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

const PlansCanaux = ({vhfPlan, lockEdit = false, displayNameInButton = false}) => {
    const[readyToDisplay, setReadyToDisplay] = useState(false);
    const[canauxOnePlan, setCanauxOnePlan] = useState([]);
    const[frequences, setFrequences] = useState([]);

    const[showCanauxModal, setShowCanauxModal] = useState(false);
    const handleCloseCanauxModal = () => {
        setShowCanauxModal(false);
        setCanauxOnePlan([]);
        setReadyToDisplay(false);
    }
    const handleShowCanauxModal = async () => {
        try {
            setShowCanauxModal(true);

            const response = await Axios.post('/vhf/getCanauxOnePlan',{
                idVhfPlan: vhfPlan.idVhfPlan
            });
            setCanauxOnePlan(response.data);

            const frequences = await Axios.get('/select/getVhfFrequences');
            setFrequences(frequences.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    const updateNumeroCanal = (index, numeroCanal) => {
        const newState = canauxOnePlan.map((canal, i) => {
            if(i === index)
            {
                return {...canal, numeroCanal: numeroCanal}
            }
            else
            {
                return canal;
            }
        })
        setCanauxOnePlan(newState);
    }

    const updateFrequence = (index, idVhfCanal) => {
        const newState = canauxOnePlan.map((canal, i) => {
            if(i === index)
            {
                return {...canal, idVhfCanal: idVhfCanal}
            }
            else
            {
                return canal;
            }
        })
        setCanauxOnePlan(newState);
    }

    const addFromNumeroCanal = (numeroCanal) => {
        setCanauxOnePlan(canauxOnePlan => [...canauxOnePlan, {numeroCanal: numeroCanal}]);
    }

    const updateProg = async (e) => {
        try {
            setReadyToDisplay(false);

            const response = await Axios.post('/vhf/updateCanauxOnePlan',{
                idVhfPlan: vhfPlan.idVhfPlan,
                canaux: canauxOnePlan,
            });

            handleShowCanauxModal();

        } catch (error) {
            console.log(error)
        }
    }

    
    return(<>
        <IconButton
            icon='wifi'
            size = 'sm'
            variant="outline-success"
            className="me-1"
            onClick={handleShowCanauxModal}
        >{displayNameInButton ? vhfPlan.libellePlan : 'Voir la programmation'}</IconButton>

        <Modal show={showCanauxModal} onHide={handleCloseCanauxModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Détails de la programmation de {vhfPlan.libellePlan}</Modal.Title>
                <FalconCloseButton onClick={handleCloseCanauxModal}/>
            </Modal.Header>
            <Modal.Body>
            {readyToDisplay ?
                <>
                    <Table responsive>
                        <thead>
                            <tr>
                                <th scope="col">Canal</th>
                                <th scope="col">Fréquence</th>
                            </tr>
                        </thead>
                        <tbody>
                            {canauxOnePlan.map((prog, i)=>{return(
                                <tr key={i}>
                                    <td>
                                        <Form.Group className="mb-3">
                                            <Form.Control type="number" min="1" value={prog.numeroCanal} onChange={(e) => {updateNumeroCanal(i, e.target.value)}} disabled={!HabilitationService.habilitations['vhf_plan_modification'] || lockEdit} />
                                        </Form.Group>
                                    </td>
                                    <td>
                                        <Form.Group className="mb-3">
                                            <Form.Select size="sm" value={prog.idVhfCanal} onChange={(e) => {updateFrequence(i, e.target.value)}} disabled={!HabilitationService.habilitations['vhf_plan_modification'] || lockEdit}>
                                                <option key="0" value="">--- Aucune fréquence programmée ---</option>
                                                {frequences.map((item, i) => {
                                                    return (<option key={item.value} value={item.value}>{item.label}</option>);
                                                })}
                                            </Form.Select>
                                        </Form.Group>
                                    </td>
                                </tr>
                            )})}
                            {HabilitationService.habilitations['vhf_plan_modification'] && !lockEdit ?
                                <tr key={0}>
                                    <td>
                                        <Form.Group className="mb-3">
                                            <Form.Control type="number" min="1" value='' placeholder='Nouvelle entrée' onChange={(e) => {addFromNumeroCanal(e.target.value)}} disabled={!HabilitationService.habilitations['vhf_plan_modification'] || lockEdit} />
                                        </Form.Group>
                                    </td>
                                    <td></td>
                                </tr>
                            : null}
                        </tbody>
                    </Table>
                    {HabilitationService.habilitations['vhf_plan_modification'] && !lockEdit ? <Button variant='primary' className='me-2 mb-1' onClick={updateProg} disabled={!readyToDisplay}>{!readyToDisplay ? 'Patientez...' : 'Enregistrer les fréquences'}</Button> : null}
                </>
            : <LoaderInfiniteLoop/>}
            </Modal.Body>
        </Modal>
    </>);
};

PlansCanaux.propTypes = {};

export default PlansCanaux;