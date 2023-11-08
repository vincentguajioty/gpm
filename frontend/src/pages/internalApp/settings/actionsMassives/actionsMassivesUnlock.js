import React, { useState } from 'react';
import { Button, Form, Modal, Alert } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { actionsMassivesUnlockModalForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

import ActionsMassivesService from 'services/actionsMassivesAuthService';

const ActionsMassivesUnlock = ({
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const [showModal, setShowModal] = useState(false);
    const [showError, setShowError] = useState(false);
    const handleCloseModal = () => {
        setShowModal(false);
        setIsLoading(false);
        reset();
        setShowError(false);
    };
    const handleShowModal = () => {
        setShowModal(true);
    };

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(actionsMassivesUnlockModalForm),
    });

    const unlockAccess = async (data) => {
        try {
            setIsLoading(true);

            const getToken = await Axios.post('/actionsmassives/authenticateForAM',{
                motDePasse: data.motDePasse
            });

            if(getToken.data.auth && getToken.data.auth === true)
            {
                setShowError(false);
                ActionsMassivesService.setAmToken(getToken.data.amToken);
                ActionsMassivesService.setAmTokenValidUntil(getToken.data.amTokenValidUntil);
                handleCloseModal();
                setPageNeedsRefresh(true);
            }
            else
            {
                setShowError(true);
                reset();
            }
            
            setIsLoading(false);
        } catch (e) {
            console.log(e);
        }
    }


    return (<>
        <IconButton
            variant="outline-warning"
            icon="unlock-alt"
            onClick={handleShowModal}
            className='me-1'
        >
            Accéder à la fonction d'actions massives
        </IconButton>

        <Modal show={showModal} onHide={handleCloseModal} backdrop="static" keyboard={false}>
            <Form onSubmit={handleSubmit(unlockAccess)}>
                <Modal.Header>
                    <Modal.Title>Actions massives</Modal.Title>
                    <FalconCloseButton onClick={handleCloseModal}/>
                </Modal.Header>
                <Modal.Body>
                    <i>L'accès aux actions massives est sécurisé. Merci de saisir votre mot de passe pour déverouiller l'accès.</i>
                    {showError ?
                        <Alert variant='danger'>Mauvais mot de passe.</Alert>
                    :null}
                    <Form.Group className="mt-1 mb-3">
                        <Form.Label>Mot de passe:</Form.Label>
                        <Form.Control type="password" id="motDePasse" name="motDePasse" {...register("motDePasse")}/>
                        <small className="text-danger">{errors.motDePasse?.message}</small>
                    </Form.Group>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseModal}>
                        Annuler
                    </Button>
                    <Button variant='primary' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Déverrouiller'}</Button>
                </Modal.Footer>
            </Form>
        </Modal>
        
    </>);
};

ActionsMassivesUnlock.propTypes = {};

export default ActionsMassivesUnlock;
