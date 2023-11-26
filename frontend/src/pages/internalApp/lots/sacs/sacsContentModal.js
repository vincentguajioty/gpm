import React, { useState } from 'react';
import { Modal, Button, ButtonGroup, ToggleButton } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';
import MaterielsTable from '../materiels/materielsTable';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

const SacsContentModal = ({
    idSac,
    libelleSac,
}) => {
    const [showContentModal, setShowContentModal] = useState(false);
    const handleCloseContentModal = () => {
        setShowContentModal(false);
        setIdEmplacement();
    };
    const handleShowContentModal = () => {
        setShowContentModal(true);
        getEmplacements();
    };

    const [idEmplacement, setIdEmplacement] = useState();
    const [emplacements, setEmplacements] = useState([]);

    const [isLoading, setLoading] = useState(true);
    const getEmplacements = async () => {
        try {
            const getData = await Axios.post('/sacs/getEmplacementsOneSac',{
                idSac: idSac
            });
            setEmplacements(getData.data);  
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return (
    <>
        <IconButton
            icon='stethoscope'
            size = 'sm'
            variant="outline-info"
            className="me-1"
            onClick={handleShowContentModal}
        >Voir la composition</IconButton>
        
        <Modal show={showContentModal} onHide={handleCloseContentModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header closeButton >
                <Modal.Title>Contenu du sac {libelleSac}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                {isLoading ? <LoaderInfiniteLoop/> : 
                    <ButtonGroup>
                        {emplacements.map((empl, idx) => (
                            <ToggleButton
                                key={empl.idEmplacement}
                                id={`radio-${idx}`}
                                type="radio"
                                variant='outline-info'
                                name="radio"
                                value={idEmplacement}
                                checked={idEmplacement === empl.idEmplacement}
                                onChange={(e) => {setIdEmplacement(empl.idEmplacement)}}
                                size='sm'
                            >
                                {empl.libelleEmplacement}
                            </ToggleButton>
                        ))}
                    </ButtonGroup>
                }
                
                {idEmplacement && idEmplacement != null && idEmplacement > 0 ? 
                    <MaterielsTable
                        filterIdEmplacement={idEmplacement}
                        displayLibelleLot={false}
                        displayLibelleSac={false}
                        displayLibelleEmplacement={false}
                        displayNotif={false}
                    />
                : null }
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
