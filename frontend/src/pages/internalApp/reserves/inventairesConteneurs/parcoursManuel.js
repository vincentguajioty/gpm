import React, {useState} from 'react';
import { useNavigate } from 'react-router-dom';
import { Card, Form, Button, Modal, Alert, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import { reserveInventaireDelete } from 'helpers/deleteModalWarningContent';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import InventaireParcoursManuelOneEmplacement from './parcoursManuelOneEmp';

const InventaireParcoursManuel = ({
    idConteneur,
    socket,
    idReserveInventaire,
    inventaireElements,
    catalogueCodesBarres,
    manageDemandePopullationPrecedente,
    validerInventaire,
}) => {
    const navigate = useNavigate();

    /* DELETE */
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

            const response = await Axios.post('/reserves/reserveInventaireCancel',{
                idReserveInventaire: idReserveInventaire,
            });
            
            handleCloseDeleteModal();
            setLoading(false);

            let idTarget = response.data.idConteneur;
            navigate('/reservesConteneurs/'+idTarget);
        } catch (e) {
            console.log(e);
        }
    }

    /* VALIDER */
    const [showValidateModal, setShowValidateModal] = useState(false);
    const [commentaire, setCommentaire] = useState();
    const handleCloseValidateModal = () => {
        setShowValidateModal(false);
        setLoading(false);
    };
    const handleShowValidateModal = () => {
        setShowValidateModal(true);
    };

    return (<>
        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{reserveInventaireDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showValidateModal} onHide={handleCloseValidateModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Validation finale</Modal.Title>
                <FalconCloseButton onClick={handleCloseValidateModal}/>
            </Modal.Header>
            <Modal.Body>
                <Alert>Vous êtes sur le point de valider l'inventaire qui ne pourra plus être modifié par la suite.</Alert>
                <Form.Control size="sm" as="textarea" placeholder="Un dernier commentaire ?" rows={5} name={"commentaire"} id={"commentaire"} onChange={(e)=>{setCommentaire(e.target.value)}}/>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseValidateModal}>
                    Revenir à l'inventaire
                </Button>
                <Button variant='success' onClick={()=>{validerInventaire(commentaire);handleCloseValidateModal();}} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Valider'}</Button>
            </Modal.Footer>
        </Modal>
        
        <Card className="mb-3">
            <Card.Body>
                <InventaireParcoursManuelOneEmplacement
                    idReserveInventaire={idReserveInventaire}
                    socket={socket}
                    idConteneur={idConteneur}
                    inventaireElements={inventaireElements}
                    catalogueCodesBarres={catalogueCodesBarres}
                />

                {HabilitationService.habilitations['reserve_modification'] ? <>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' disabled={isLoading} onClick={handleShowValidateModal}>{isLoading ? 'Patientez...' : 'Terminer'}</Button>
                    </div>
                    
                    <Button variant='outline-danger' className='me-2 mt-3' disabled={isLoading} onClick={handleShowDeleteModal}>{isLoading ? 'Patientez...' : 'Annuler tout'}</Button>
                    <Button variant='outline-warning' className='me-2 mt-3' disabled={isLoading} onClick={manageDemandePopullationPrecedente}>{isLoading ? 'Patientez...' : 'Populler avec l\'inventaire précédent'}</Button>
                </> : null}
            </Card.Body>
        </Card>
    </>);
};

InventaireParcoursManuel.propTypes = {};

export default InventaireParcoursManuel;
