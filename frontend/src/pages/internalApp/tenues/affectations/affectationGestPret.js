import React, { useState, } from 'react';
import { Button, Modal, Form, ButtonGroup, ToggleButton, Row, Col } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import moment from 'moment-timezone';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { affectationsTenuesPretRep } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

import { Axios } from 'helpers/axios';

const AffectationGestionPret = ({
    idTenue,
    tenue,
    setPageNeedsRefresh,
}) => {
    const [isLoading, setLoading] = useState(false);
    const [showPretModal, setShowPretModal] = useState(false);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(affectationsTenuesPretRep),
    });

    const handleClosePretModal = () => {
        setShowPretModal(false);
        setLoading(false);
        reset();
    };
    const handleShowPretModal = () => {
        setShowPretModal(true);
        setValue("reponseBinaire", true);
        setValue("dateAffectation", new Date(tenue.dateAffectation));
        setValue("dateRetour", new Date(tenue.dateRetour));
        setValue("decompteStock", true);
    };

    const enregistrerReponse = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/reponseDemandePret',{
                idTenue: idTenue,
                reponseBinaire: data.reponseBinaire,
                reponseDetails: data.reponseDetails,
                dateAffectation: data.dateAffectation,
                dateRetour: data.dateRetour,
                decompteStock: data.decompteStock,
            });
            
            setPageNeedsRefresh(true);
            handleClosePretModal();
            setLoading(false);
            reset();
        } catch (e) {
            console.log(e);
        }
    }

    const nl2br = require('react-nl2br');
    return(<>
        <IconButton
            icon='clock'
            size = 'sm'
            variant="warning"
            className="me-1 mb-1"
            onClick={handleShowPretModal}
        >Gérer le prêt</IconButton>

        <Modal show={showPretModal} onHide={handleClosePretModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Gérer la demande de prêt</Modal.Title>
                <FalconCloseButton onClick={handleClosePretModal}/>
            </Modal.Header>
            <Modal.Body>
                <u>Motif de la demande:</u>
                <p><i>{nl2br(tenue.demandeBenevolePretMotif)}</i></p>
                <p>Du {moment(tenue.dateAffectation).format('DD/MM/YYYY')} au {moment(tenue.dateRetour).format('DD/MM/YYYY')}</p>

                <hr/>

                <Form onSubmit={handleSubmit(enregistrerReponse)}>
                    <Form.Group className="mb-1">
                        <ButtonGroup>
                            <ToggleButton
                                key='ok'
                                id='ok'
                                variant={watch("reponseBinaire") === true ? 'success' : 'outline-success'}
                                name="radio"
                                value={true}
                                checked={watch("reponseBinaire") === true}
                                onClick={(e) => {setValue("reponseBinaire", true)}}
                                size='sm'
                            >
                                ACCEPTE
                            </ToggleButton>
                            <ToggleButton
                                key='ko'
                                id='ko'
                                variant={watch("reponseBinaire") === false ? 'danger' : 'outline-danger'}
                                name="radio"
                                value={false}
                                checked={watch("reponseBinaire") === false}
                                onClick={(e) => {setValue("reponseBinaire", false)}}
                                size='sm'
                            >
                                REFUSE
                            </ToggleButton>
                        </ButtonGroup>
                        <small className="text-danger">{errors.reponseBinaire?.message}</small>
                    </Form.Group>
                    
                    <Form.Group className="mb-3">
                        <Form.Label>Réponse envoyée au demandeur:</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"reponseDetails"} id={"reponseDetails"} {...register("reponseDetails")} placeholder='Expliquez votre déicision et donnez les prochaines étapes et instructions'/>
                        <small className="text-danger">{errors.reponseDetails?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Row>
                            <Col md={6}>
                                <Form.Label>Confirmez la date de début de prêt:</Form.Label>
                                <DatePicker
                                    selected={watch("dateAffectation")}
                                    onChange={(date)=>setValue("dateAffectation", date)}
                                    formatWeekDay={day => day.slice(0, 3)}
                                    className='form-control'
                                    placeholderText="Choisir une date"
                                    dateFormat="dd/MM/yyyy"
                                    fixedHeight
                                    locale="fr"
                                /><br/>
                                <small className="text-danger">{errors.dateAffectation?.message}</small>
                            </Col>
                            <Col md={6}>
                                <Form.Label>Confirmez la date de fin de prêt:</Form.Label>
                                <DatePicker
                                    selected={watch("dateRetour")}
                                    onChange={(date)=>setValue("dateRetour", date)}
                                    formatWeekDay={day => day.slice(0, 3)}
                                    className='form-control'
                                    placeholderText="Choisir une date"
                                    dateFormat="dd/MM/yyyy"
                                    fixedHeight
                                    locale="fr"
                                /><br/>
                                <small className="text-danger">{errors.dateRetour?.message}</small>
                            </Col>
                        </Row>
                    </Form.Group>

                    {watch("reponseBinaire") === true ?
                        <Form.Group className="mb-1">
                            <ButtonGroup>
                                <ToggleButton
                                    key='ok'
                                    id='ok'
                                    variant={watch("decompteStock") === true ? 'success' : 'outline-success'}
                                    name="radio"
                                    value={true}
                                    checked={watch("decompteStock") === true}
                                    onClick={(e) => {setValue("decompteStock", true)}}
                                    size='sm'
                                >
                                    Décompter si possible le stock
                                </ToggleButton>
                                <ToggleButton
                                    key='ko'
                                    id='ko'
                                    variant={watch("decompteStock") === false ? 'warning' : 'outline-warning'}
                                    name="radio"
                                    value={false}
                                    checked={watch("decompteStock") === false}
                                    onClick={(e) => {setValue("decompteStock", false)}}
                                    size='sm'
                                >
                                    Ne pas décompter le stock
                                </ToggleButton>
                            </ButtonGroup>
                            <small className="text-danger">{errors.decompteStock?.message}</small>
                        </Form.Group>
                    : null}

                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' className='me-2 mb-1' type='submit' disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                    </div>
                </Form>

            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleClosePretModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>
    </>)
}

AffectationGestionPret.propTypes = {};

export default AffectationGestionPret;