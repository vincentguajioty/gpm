import React, { useState } from 'react';
import { Modal, Button, ButtonGroup, ToggleButton } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';
import MaterielsTable from '../materiels/materielsTable';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SacsContent from './sacsContent';

const SacsContentModal = ({
    idSac,
    libelleSac,
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
