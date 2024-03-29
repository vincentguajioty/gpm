import React, { useState } from 'react';
import { Button, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import { appliConfAESDrop } from 'helpers/deleteModalWarningContent';

import AesFournisseursService from 'services/aesFournisseursService';

import { Axios } from 'helpers/axios';

const AesFournisseursDisable = ({
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const [showDeleteModal, setShowDeleteModal] = useState(false);

    const handleCloseDeleteModal = () => {
        setShowDeleteModal(false);
        setIsLoading(false);
    };
    const handleShowDeleteModal = () => {
        setShowDeleteModal(true);
    };

    const disabledAES = async () => {
        try {
            setIsLoading(true);

            const response = await Axios.post('/fournisseurs/disableAesAndDelete',{
                aesToken: AesFournisseursService.aesToken,
            });

            sessionStorage.removeItem('aesToken');
            sessionStorage.removeItem('aesTokenValidUntil');
            
            handleCloseDeleteModal();
            setIsLoading(false);
            setPageNeedsRefresh(true);
        } catch (e) {
            console.log(e);
        }
    }


    return (<>
        <IconButton
            variant="outline-danger"
            icon="trash"
            onClick={handleShowDeleteModal}
            className='me-1'
        >
            Désactiver la fonctionnalité et supprimer les données
        </IconButton>

        
        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{appliConfAESDrop}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={disabledAES} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
    </>);
};

AesFournisseursDisable.propTypes = {};

export default AesFournisseursDisable;
