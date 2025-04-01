import React, { useState, useEffect } from 'react';
import { Button, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';

const AffectationDeleteMassif = ({
    displayIdExterne,
    setPageNeedsRefresh,
}) => {
    /* Actions massives - DELETE avec reIntégration */
    const [showDeleteMassifModal, setShowDeleteMassifModal] = useState(false);
    const [isLoading, setLoading] = useState(false);

    const handleCloseDeleteMassifModal = () => {
        setShowDeleteMassifModal(false);
        setLoading(false);
    };
    const handleShowDeleteMassifModal = () => {
        setShowDeleteMassifModal(true);
    };

    const supprimerMassivementEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteMassifAffectations',{
                idExterne: displayIdExterne || null,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteMassifModal();
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
            onClick={handleShowDeleteMassifModal}
        >Le bénévole a tout rendu</IconButton>
        
        <Modal show={showDeleteMassifModal} onHide={handleCloseDeleteMassifModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression massive</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteMassifModal}/>
            </Modal.Header>
            <Modal.Body>
                La personne vous a retourné l'intégralité des éléments de tenue qui lui étaient affectés. Tous les éléments vont être réintégrés au stock.
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteMassifModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerMassivementEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
    </>)
}

AffectationDeleteMassif.propTypes = {};

export default AffectationDeleteMassif;