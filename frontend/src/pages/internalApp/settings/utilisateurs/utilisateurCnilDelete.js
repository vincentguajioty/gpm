import React, {useState} from 'react';
import { useNavigate } from 'react-router-dom';
import FalconComponentCard from 'components/common/FalconComponentCard';
import FalconCloseButton from 'components/common/FalconCloseButton';
import { Button, Alert, Modal } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import moment from 'moment-timezone';
import { annuaireCnilAnonyme, annuaireDelete } from 'helpers/deleteModalWarningContent';

import {Axios} from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const UtilisateurCnilDelete = ({personne, setPageNeedsRefresh}) => {

    /* DELETE */
    const [isLoading, setLoading] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);

    const handleCloseDeleteModal = () => {
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = () => {
        setShowDeleteModal(true);
    };

    const navigate = useNavigate();
    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/settingsUtilisateurs/deleteUser',{
                idPersonne: personne.idPersonne,
            });
            
            handleCloseDeleteModal();
            setLoading(false);

            navigate('/teamUtilisateurs');
        } catch (e) {
            console.log(e);
        }
    }

    /* DELETE */
    const [showCnilModal, setShowCnilModal] = useState(false);

    const handleCloseCnilModal = () => {
        setShowCnilModal(false);
        setLoading(false);
    };
    const handleShowCnilModal = () => {
        setShowCnilModal(true);
    };

    const anonymiserEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/settingsUtilisateurs/anonymiserUser',{
                idPersonne: personne.idPersonne,
            });
            
            handleCloseCnilModal();
            setLoading(false);

            setPageNeedsRefresh(true);
        } catch (e) {
            console.log(e);
        }
    }

	return (
		<>
            <Modal show={showCnilModal} onHide={handleCloseCnilModal} backdrop="static" keyboard={false}>
                <Modal.Header>
                    <Modal.Title>Anonymisation</Modal.Title>
                    <FalconCloseButton onClick={handleCloseCnilModal}/>
                </Modal.Header>
                <Modal.Body>{annuaireCnilAnonyme}</Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseCnilModal}>
                        Annuler
                    </Button>
                    <Button variant='danger' onClick={anonymiserEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Anonymiser'}</Button>
                </Modal.Footer>
            </Modal>

            <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
                <Modal.Header>
                    <Modal.Title>Suppression</Modal.Title>
                    <FalconCloseButton onClick={handleCloseDeleteModal}/>
                </Modal.Header>
                <Modal.Body>{annuaireDelete}</Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseDeleteModal}>
                        Annuler
                    </Button>
                    <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
                </Modal.Footer>
            </Modal>
            
            <FalconComponentCard>
                <FalconComponentCard.Header
                    title="Anonymisation et Suppression"
                >
                </FalconComponentCard.Header>
                <FalconComponentCard.Body>
                    {personne.cnil_anonyme ?
                        <Alert variant='warning'>Ce compte utilisateur a été irréversiblement anonymisé (droit à l'oubli).</Alert>
                    :
                        <Alert variant='info'>Compte actif. {personne.derniereConnexion ? 'Dernière connexion le: '+moment(personne.derniereConnexion).format('DD/MM/YYYY HH:mm') : 'Aucune connexion dans le passé.'}</Alert>
                    }
                    
                    {HabilitationService.habilitations['annuaire_suppression'] && !personne.cnil_anonyme ?
                        <IconButton
                            icon='user-secret'    
                            variant='outline-danger'
                            className='me-1 mb-1'
                            onClick={handleShowCnilModal}
                        >
                            Anonymiser le compte
                        </IconButton>
                    : null}
                    {HabilitationService.habilitations['annuaire_suppression'] ?
                        <IconButton
                            icon='trash'
                            variant='outline-danger'
                            className='me-1 mb-1'
                            onClick={handleShowDeleteModal}
                        >
                            Supprimer le compte utilisateur
                        </IconButton>
                    : null}
                </FalconComponentCard.Body>
            </FalconComponentCard>
		</>
	);
};

UtilisateurCnilDelete.propTypes = {};

export default UtilisateurCnilDelete;