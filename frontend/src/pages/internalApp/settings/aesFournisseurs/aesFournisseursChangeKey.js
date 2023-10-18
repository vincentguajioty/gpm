import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Table, Modal, Alert } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import IconButton from 'components/common/IconButton';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { aesFournisseursKeyUpdateModalForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

import AesFournisseursService from 'services/aesFournisseursService';

const AesFournisseursChangeKey = ({
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const [showModal, setShowModal] = useState(false);
    const handleCloseModal = () => {
        setShowModal(false);
        setIsLoading(false);
        reset();
    };
    const handleShowModal = () => {
        setShowModal(true);
    };

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(aesFournisseursKeyUpdateModalForm),
    });

    const keyUpdate = async (data) => {
        try {
            setIsLoading(true);

            const getToken = await Axios.post('/fournisseurs/changeKey',{
                aesKey: data.aesKey,
                aesToken: AesFournisseursService.aesToken,
            });

            if(getToken.data.auth && getToken.data.auth === true)
            {
                AesFournisseursService.setAesToken(getToken.data.aesToken);
                AesFournisseursService.setAesTokenValidUntil(getToken.data.aesTokenValidUntil);
                handleCloseModal();
                setPageNeedsRefresh(true);
            }
            else
            {
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
            Modifier la clef de chiffrement
        </IconButton>

        <Modal show={showModal} onHide={handleCloseModal} backdrop="static" keyboard={false}>
            <Form onSubmit={handleSubmit(keyUpdate)}>
                <Modal.Header>
                    <Modal.Title>Modifier la clef de chiffrement</Modal.Title>
                    <FalconCloseButton onClick={handleCloseModal}/>
                </Modal.Header>
                <Modal.Body>
                    <Form.Group className="mt-1 mb-3">
                        <Form.Label>Nouvelle clef:</Form.Label>
                        <Form.Control type="text" id="aesKey" name="aesKey" {...register("aesKey")}/>
                        <small className="text-danger">{errors.aesKey?.message}</small>
                    </Form.Group>
                    <Form.Group className="mt-1 mb-3">
                        <Form.Label>Confirmation de la nouvelle clef:</Form.Label>
                        <Form.Control type="text" id="aesKeyConfirmed" name="aesKeyConfirmed" {...register("aesKeyConfirmed")}/>
                        <small className="text-danger">{errors.aesKeyConfirmed?.message}</small>
                    </Form.Group>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseModal}>
                        Annuler
                    </Button>
                    <Button variant='warning' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Modifier la clef'}</Button>
                </Modal.Footer>
            </Form>
        </Modal>
        
    </>);
};

AesFournisseursChangeKey.propTypes = {};

export default AesFournisseursChangeKey;
