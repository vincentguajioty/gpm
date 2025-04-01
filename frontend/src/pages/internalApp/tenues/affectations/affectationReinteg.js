import React, { useState, } from 'react';
import { Button, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import { tenuesAffectationsDelete } from 'helpers/deleteModalWarningContent';

import { Axios } from 'helpers/axios';

const AffectationReintegrer = ({
    idTenue,
    setPageNeedsRefresh,
}) => {
    const [isLoading, setLoading] = useState(false);

    /* DELETE avec reIntégration */
    const [showDeleteModal, setShowDeleteModal] = useState(false);

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

            const response = await Axios.post('/tenues/deleteAffectations',{
                idTenue: idTenue,
                reintegration: true,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }
    return(<>

        <IconButton
            icon='recycle'
            size = 'sm'
            variant="outline-primary"
            className="me-1"
            onClick={handleShowDeleteModal}
        />

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{tenuesAffectationsDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
    </>)
}

AffectationReintegrer.propTypes = {};

export default AffectationReintegrer;