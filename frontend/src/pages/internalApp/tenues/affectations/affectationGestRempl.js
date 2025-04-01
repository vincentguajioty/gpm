import React, { useState, } from 'react';
import { Button, Modal, Form, ButtonGroup, ToggleButton, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { affectationsTenuesRemplRep } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

const AffectationGestionRemplacement = ({
    idTenue,
    tenue,
    setPageNeedsRefresh,
}) => {
    const [isLoading, setLoading] = useState(false);
    const [showRemplModal, setShowRemplModal] = useState(false);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(affectationsTenuesRemplRep),
    });

    const handleCloseRemplModal = () => {
        setShowRemplModal(false);
        setLoading(false);
    };
    const handleShowRemplModal = (id) => {
        setShowRemplModal(true);
        setValue("reponseBinaire", true);
    };

    const enregistrerReponse = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/reponseDemandeRemplacement',{
                idTenue: idTenue,
                reponseBinaire: data.reponseBinaire,
                reponseDetails: data.reponseDetails,
            });
            
            setPageNeedsRefresh(true);
            handleCloseRemplModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    const nl2br = require('react-nl2br');
    return(<>
        <IconButton
            icon='recycle'
            size = 'sm'
            variant="warning"
            className="me-1 mb-1"
            onClick={handleShowRemplModal}
        >Gérer le remplacement</IconButton>

        <Modal show={showRemplModal} onHide={handleCloseRemplModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Gérer la demande de remplacement</Modal.Title>
                <FalconCloseButton onClick={handleCloseRemplModal}/>
            </Modal.Header>
            <Modal.Body>
                <u>Motif de la demande:</u>
                <p><i>{nl2br(tenue.demandeBenevoleRemplacementMotif)}</i></p>

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

                    {watch("reponseBinaire") === true ?
                        <i>Une fois validé, pensez à gérer le retour au stock de l'élément, l'affectation de l'élément suivant, ...</i>
                    : null}

                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' className='me-2 mb-1' type='submit' disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                    </div>
                </Form>

            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseRemplModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>
    </>)
}

AffectationGestionRemplacement.propTypes = {};

export default AffectationGestionRemplacement;