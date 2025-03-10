import React, { useState } from 'react';
import { Modal, Button } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import { vehiculesStockDelete } from 'helpers/deleteModalWarningContent';

import { Axios } from 'helpers/axios';

const VehiculesStockDeleteModal = ({
    idVehiculesStock,
    setPageNeedsRefresh
}) => {
    
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const handleCloseDeleteModal = () => {
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = () => {
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/deleteVehiculeStock',{
                idVehiculesStock: idVehiculesStock,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return (
    <>
        <IconButton
            icon='trash'
            size = 'sm'
            variant="outline-danger"
            className="me-1"
            onClick={handleShowDeleteModal}
        />
        
        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>Suppression</Modal.Title>
            </Modal.Header>
            <Modal.Body>{vehiculesStockDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
    </>);
};

VehiculesStockDeleteModal.propTypes = {};

export default VehiculesStockDeleteModal;
