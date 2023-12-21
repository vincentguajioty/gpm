import React, {useEffect, useState} from 'react';
import { Row, Col, Card, Alert, Modal, Button, Form, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import PageHeader from 'components/common/PageHeader';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Select from 'react-select';

import ConfigurationService from 'services/configurationService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { alerteBenevoleLots, alerteBenevoleVehicules } from 'helpers/yupValidationSchema';

const DeclarationIncident = () => {
    const [incidentLotOk, setIncidentLotOk] = useState(false);
    const [incidentVehiculeOk, setIncidentVehiculeOk] = useState(false);
    const [isLoading, setLoading] = useState(false);

    const [lots, setLots] = useState([]);
    const [vehicules, setVehicules] = useState([]);

    const initListes = async () => {
        try {
            let getFromDb = await Axios.get('/select/getLotsPublics');
            setLots(getFromDb.data);

            getFromDb = await Axios.get('/select/getVehiculesPublics');
            setVehicules(getFromDb.data);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initListes();
    },[]);

    /* MODAL LOTS */
    const [showLotsModal, setShowLotsModal] = useState(false);
    const { register: registerLots, handleSubmit: handleSubmitLots, formState: { errors: errorsLots }, setValue: setValueLots, reset: resetLots, watch: watchLots } = useForm({
        resolver: yupResolver(alerteBenevoleLots),
    });
    const handleCloseLotsModal = () => {
        setShowLotsModal(false);
        setLoading(false);
        resetLots();
    };
    const handleShowLotsModal = () => {
        setShowLotsModal(true);
        setIncidentLotOk(false);
    };
    const saveAlerteLot = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/lots/createAlerte',{
                nomDeclarant: data.nomDeclarant,
                mailDeclarant: data.mailDeclarant,
                idLot: data.idLot,
                messageAlerteLot: data.messageAlerteLot,
            });

            setLoading(false);
            setIncidentLotOk(true);
            handleCloseLotsModal();
        } catch (error) {
            console.log(error)
        }
    }

    /* MODAL VEHICULES */
    const [showVehiculesModal, setShowVehiculesModal] = useState(false);
    const { register: registerVehicules, handleSubmit: handleSubmitVehicules, formState: { errors: errorsVehicules }, setValue: setValueVehicules, reset: resetVehicules, watch: watchVehicules } = useForm({
        resolver: yupResolver(alerteBenevoleVehicules),
    });
    const handleCloseVehiculesModal = () => {
        setShowVehiculesModal(false);
        setLoading(false);
        resetVehicules();
    };
    const handleShowVehiculesModal = () => {
        setShowVehiculesModal(true);
        setIncidentVehiculeOk(false);
    };
    const saveAlerteVehicule = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/createAlerte',{
                nomDeclarant: data.nomDeclarant,
                mailDeclarant: data.mailDeclarant,
                idVehicule: data.idVehicule,
                messageAlerteVehicule: data.messageAlerteVehicule,
            });

            setLoading(false);
            setIncidentVehiculeOk(true);
            handleCloseVehiculesModal();
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <PageHeader
            preTitle="Espace public"
            title="Remonter un incident"
            className="mb-3"
        />

        <Modal show={showLotsModal} onHide={handleCloseLotsModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Alerte sur un Lot Opérationnel</Modal.Title>
                <FalconCloseButton onClick={handleCloseLotsModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmitLots(saveAlerteLot)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Votre Nom/Prénom</Form.Label>
                        <Form.Control size="sm" type="text" name='nomDeclarant' id='nomDeclarant' {...registerLots('nomDeclarant')}/>
                        <small className="text-danger">{errorsLots.nomDeclarant?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Votre adresse email</Form.Label>
                        <Form.Control size="sm" type="text" name='mailDeclarant' id='mailDeclarant' {...registerLots('mailDeclarant')}/>
                        <small className="text-danger">{errorsLots.mailDeclarant?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Selectionner le lot qui pose problème</Form.Label>
                        <Select
                            id="idLot"
                            name="idLot"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun item selectionné'
                            options={lots}
                            value={lots.find(c => c.value === watchLots("idLot"))}
                            onChange={val => val != null ? setValueLots("idLot", val.value) : setValueLots("idLot", null)}
                        />
                        <small className="text-danger">{errorsLots.idLot?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Commentaires</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={5} name='messageAlerteLot' id='messageAlerteLot' {...registerLots('messageAlerteLot')}/>
                        <small className="text-danger">{errorsLots.messageAlerteLot?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Envoyer l\'alerte'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseLotsModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showVehiculesModal} onHide={handleCloseVehiculesModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Alerte sur un Véhicule</Modal.Title>
                <FalconCloseButton onClick={handleCloseVehiculesModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmitVehicules(saveAlerteVehicule)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Votre Nom/Prénom</Form.Label>
                        <Form.Control size="sm" type="text" name='nomDeclarant' id='nomDeclarant' {...registerVehicules('nomDeclarant')}/>
                        <small className="text-danger">{errorsVehicules.nomDeclarant?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Votre adresse email</Form.Label>
                        <Form.Control size="sm" type="text" name='mailDeclarant' id='mailDeclarant' {...registerVehicules('mailDeclarant')}/>
                        <small className="text-danger">{errorsVehicules.mailDeclarant?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Selectionner le véhicule qui pose problème</Form.Label>
                        <Select
                            id="idVehicule"
                            name="idVehicule"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun item selectionné'
                            options={vehicules}
                            value={vehicules.find(c => c.value === watchVehicules("idVehicule"))}
                            onChange={val => val != null ? setValueVehicules("idVehicule", val.value) : setValueVehicules("idVehicule", null)}
                        />
                        <small className="text-danger">{errorsVehicules.idVehicule?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Commentaires</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={5} name='messageAlerteVehicule' id='messageAlerteVehicule' {...registerVehicules('messageAlerteVehicule')}/>
                        <small className="text-danger">{errorsVehicules.messageAlerteVehicule?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Envoyer l\'alerte'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseVehiculesModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <Row className="justify-content-center text-center">
            {ConfigurationService.config['alertes_benevoles_lots'] ?
                <Col lg={4} className='mt-5 mb-2'>
                    <Card className="card-span" onClick={handleShowLotsModal}>
                        <div className="card-span-img">
                            <FontAwesomeIcon
                                icon='briefcase-medical'
                                className='text-warning fs-3'
                            />
                        </div>
                        <Card.Body className="pt-6 pb-4">
                            <h5>Incident sur un lot</h5>
                            {incidentLotOk ?
                                <Alert variant='success' className='mt-3'>Incident enregistré, merci pour votre remontée</Alert>
                            : null}
                        </Card.Body>
                    </Card>
                </Col>
            : null}

            {ConfigurationService.config['alertes_benevoles_vehicules'] ?
                <Col lg={4} className='mt-5 mb-2'>
                    <Card className="card-span" onClick={handleShowVehiculesModal}>
                        <div className="card-span-img">
                            <FontAwesomeIcon
                                icon='ambulance'
                                className='text-warning fs-3'
                            />
                        </div>
                        <Card.Body className="pt-6 pb-4">
                            <h5>Incident sur un véhicule</h5>
                            {incidentVehiculeOk ?
                                <Alert variant='success' className='mt-3'>Incident enregistré, merci pour votre remontée</Alert>
                            : null}
                        </Card.Body>
                    </Card>
                </Col>
            : null}

        </Row>
    </>);
};

export default DeclarationIncident;
