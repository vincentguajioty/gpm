import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, FloatingLabel, Table, Modal, Row, Col, Dropdown } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';

import CardDropdown from 'components/common/CardDropdown';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { planCanauxForm } from 'helpers/yupValidationSchema';

const PlansCanaux = ({vhfPlan}) => {
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

            const frequences = await Axios.get('/vhf/getFrequences');
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
        >Voir la programmation</IconButton>

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
                                            <Form.Control type="number" min="1" value={prog.numeroCanal} onChange={(e) => {updateNumeroCanal(i, e.target.value)}} disabled={!HabilitationService.habilitations['vhf_plan_modification']} />
                                        </Form.Group>
                                    </td>
                                    <td>
                                        <Form.Group className="mb-3">
                                            <Form.Select size="sm" value={prog.idVhfCanal} onChange={(e) => {updateFrequence(i, e.target.value)}} disabled={!HabilitationService.habilitations['vhf_plan_modification']}>
                                                <option key="0" value="">--- Aucune fréquence programmée ---</option>
                                                {frequences.map((freq, i) => {
                                                    return (<option key={freq.idVhfCanal} value={freq.idVhfCanal}>{freq.chName}</option>);
                                                })}
                                            </Form.Select>
                                        </Form.Group>
                                    </td>
                                </tr>
                            )})}
                            <tr key={0}>
                                <td>
                                    <Form.Group className="mb-3">
                                        <Form.Control type="number" min="1" value='' placeholder='Nouvelle entrée' onChange={(e) => {addFromNumeroCanal(e.target.value)}} disabled={!HabilitationService.habilitations['vhf_plan_modification']} />
                                    </Form.Group>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </Table>
                    <Button variant='primary' className='me-2 mb-1' onClick={updateProg} disabled={!readyToDisplay}>{!readyToDisplay ? 'Patientez...' : 'Enregistrer les étapes'}</Button>
                </>
            : <LoaderInfiniteLoop/>}
            </Modal.Body>
        </Modal>
    </>);
};

PlansCanaux.propTypes = {};

export default PlansCanaux;