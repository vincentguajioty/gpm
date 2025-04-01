import React, { useState, } from 'react';
import { Button, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import { tenuesAffectationsDefinitiveDelete } from 'helpers/deleteModalWarningContent';


import { Axios } from 'helpers/axios';

const AffectationDelete = ({
    idTenue,
    setPageNeedsRefresh,
}) => {
    const [isLoading, setLoading] = useState(false);

    /* DELETE définitive */
    const [showDefinitiveDeleteModal, setShowDefinitiveDeleteModal] = useState(false);

    const handleCloseDefinitiveDeleteModal = () => {
        setShowDefinitiveDeleteModal(false);
        setLoading(false);
    };
    const handleShowDefinitiveDeleteModal = () => {
        setShowDefinitiveDeleteModal(true);
    };

    const supprimerDefinitivementEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteAffectations',{
                idTenue: idTenue,
                reintegration: false,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDefinitiveDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return(<>
        <IconButton
            icon='trash'
            size = 'sm'
            variant="outline-danger"
            className="me-1"
            onClick={handleShowDefinitiveDeleteModal}
        />

        <Modal show={showDefinitiveDeleteModal} onHide={handleCloseDefinitiveDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDefinitiveDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{tenuesAffectationsDefinitiveDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDefinitiveDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerDefinitivementEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
    </>)
}

AffectationDelete.propTypes = {};

export default AffectationDelete;