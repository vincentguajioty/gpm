import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Table, Modal, Alert } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { aesFournisseursUnlockModalForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

import AesFournisseursService from 'services/aesFournisseursService';

const AesFournisseursUnlock = ({
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
        resolver: yupResolver(aesFournisseursUnlockModalForm),
    });

    const unlockAccess = async (data) => {
        try {
            setIsLoading(true);

            const getToken = await Axios.post('/fournisseurs/authenticateForAES',{
                aesKey: data.aesKey
            });

            if(getToken.data.auth && getToken.data.auth === true)
            {
                setShowError(false);
                AesFournisseursService.setAesToken(getToken.data.aesToken);
                AesFournisseursService.setAesTokenValidUntil(getToken.data.aesTokenValidUntil);
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
            Déverouiller les informations chiffrées
        </IconButton>

        <Modal show={showModal} onHide={handleCloseModal} backdrop="static" keyboard={false}>
            <Form onSubmit={handleSubmit(unlockAccess)}>
                <Modal.Header>
                    <Modal.Title>Accès aux données chiffrées</Modal.Title>
                    <FalconCloseButton onClick={handleCloseModal}/>
                </Modal.Header>
                <Modal.Body>
                    <i>L'accès aux données chiffrées concernant les fournisseurs nécessite une clef de chiffrement. Merci de la saisir ci-dessous.</i>
                    {showError ?
                        <Alert variant='danger'>Mauvaise clef de chiffrement saisie.</Alert>
                    :null}
                    <Form.Group className="mt-1 mb-3">
                        <Form.Label>Clef:</Form.Label>
                        <Form.Control type="password" id="aesKey" name="aesKey" {...register("aesKey")}/>
                        <small className="text-danger">{errors.aesKey?.message}</small>
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

AesFournisseursUnlock.propTypes = {};

export default AesFournisseursUnlock;
