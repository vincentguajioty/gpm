import React, { useState } from 'react';
import { Modal, Button, } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';

import SacsContent from './sacsContent';

const SacsContentModal = ({
    idSac,
    libelleSac,
    inventaireEnCours = false,
}) => {
    const [showContentModal, setShowContentModal] = useState(false);
    const handleCloseContentModal = () => {
        setShowContentModal(false);
    };
    const handleShowContentModal = () => {
        setShowContentModal(true);
    };

    return (
    <>
        <IconButton
            icon='stethoscope'
            size = 'sm'
            variant="outline-info"
            className="me-1"
            onClick={handleShowContentModal}
        >Voir la composition</IconButton>
        
        <Modal show={showContentModal} onHide={handleCloseContentModal} backdrop="static" keyboard={false} fullscreen>
            <Modal.Header closeButton >
                <Modal.Title>Contenu du sac {libelleSac}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <SacsContent
                    idSac={idSac}
                    inventaireEnCours={inventaireEnCours}
                    displayActionsForMateriels={false}
                />
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseContentModal}>
                    Fermer
                </Button>
            </Modal.Footer>
        </Modal>
    </>);
};

SacsContentModal.propTypes = {};

export default SacsContentModal;
