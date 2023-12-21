import React, { useState, } from 'react';
import { Button, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import Barcode from 'react-barcode';
import IconButton from 'components/common/IconButton';

const CodesBarreModal = ({
    valeurCodeBarre,
    title = null,
}) => {
    /* AFFICHAGE CB */
    const [showAffichageModal, setShowAffichageModal] = useState(false);
    const handleCloseAffichageModal = () => {
        setShowAffichageModal(false);
    };
    const handleShowAffichageModal = () => {
        setShowAffichageModal(true);
    };

    /* RENDER */
    return (
        <>
            <IconButton
                className='mt-1 me-2'
                size='sm'
                icon='barcode'
                onClick={handleShowAffichageModal}
                variant='outline-success'
            >{title}</IconButton>
            
            <Modal show={showAffichageModal} onHide={handleCloseAffichageModal} backdrop="static" keyboard={false}>
                <Modal.Header>
                    <Modal.Title>Affichage du code barre</Modal.Title>
                    <FalconCloseButton onClick={handleCloseAffichageModal}/>
                </Modal.Header>
                <Modal.Body>
                    <div id="printThis">
                        <center>
                            <Barcode
                                value={valeurCodeBarre}
                                height='30'
                                displayValue={true}
                            />
                        </center>
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseAffichageModal}>
                        Fermer
                    </Button>
                </Modal.Footer>
            </Modal>
        </>
    );

};

CodesBarreModal.propTypes = {};

export default CodesBarreModal;