import React, {useState} from 'react';
import { Modal, Button, Table } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';

import HabilitationService from 'services/habilitationsService';

const VehiculeLotsCharges = ({lots}) => {
    const [showModal, setShowModal] = useState(false);
    const handleCloseModal = () => {
        setShowModal(false);
    }
    const handleShowModal = () => {
        setShowModal(true);
    }

    return (<>
        <IconButton
            icon='briefcase-medical'
            size = 'sm'
            variant="outline-info"
            className="me-1"
            onClick={handleShowModal}
        >Voir les lots ({lots.length || 0})</IconButton>

        <Modal show={showModal} onHide={handleCloseModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Lots chargés</Modal.Title>
                <FalconCloseButton onClick={handleCloseModal}/>
            </Modal.Header>
            <Modal.Body>
                <Table>
                    <thead>
                        <tr>
                            <th>Lot</th>
                            <th>Accès</th>
                        </tr>
                    </thead>
                    <tbody>
                        {lots.map((lot, i)=>{return(
                            <tr>
                                <td>{lot.libelleLot}</td>
                                <td>{HabilitationService.habilitations['lots_lecture'] ? 'TODO > ALLER SUR LE LOT' : null}</td>
                            </tr>
                        )})}
                    </tbody>
                </Table>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseModal}>
                    Fermer
                </Button>
            </Modal.Footer>
        </Modal>
    </>);
};

VehiculeLotsCharges.propTypes = {};

export default VehiculeLotsCharges;
