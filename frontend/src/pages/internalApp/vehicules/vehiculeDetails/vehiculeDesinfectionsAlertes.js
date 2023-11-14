import React, {useState} from 'react';
import { Form, Table, Modal, Button } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

const VehiculeDesinfectionsAlertes = ({idVehicule, desinfectionsAlertes, setPageNeedsRefresh}) => {
    
    const [showAlerteModal, setShowAlerteModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const handleCloseAlerteModal = () => {
        setShowAlerteModal(false);
        setLoading(false);
    };
    
    const [typesDesinfection, setTypesDesinfection] = useState([]);
    const handleShowAlerteModal = async () => {
        try {
            setShowAlerteModal(true);
            setLoading(true);

            let getData = await Axios.get('/select/getTypesDesinfections');
            for(const desinf of getData.data)
            {
                let selectedDesinf = desinfectionsAlertes.filter(oneAlerte => oneAlerte.idVehiculesDesinfectionsType == desinf.value)
                if(selectedDesinf.length > 0)
                {
                    desinf.frequenceDesinfection = selectedDesinf[0].frequenceDesinfection
                }
                else
                {
                    desinf.frequenceDesinfection = null
                }
            }
            
            setTypesDesinfection(getData.data);
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const enregistrerRecurrences = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/updateDesinfectionAlertes',{
                idVehicule: idVehicule,
                typesDesinfection: typesDesinfection,
            });
            
            setPageNeedsRefresh(true);
            handleCloseAlerteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    const updateFrequenceDesinfection = (index, frequenceDesinfection) => {
        const newState = typesDesinfection.map((desinf, i) => {
            if(i === index)
            {
                return {...desinf, frequenceDesinfection: frequenceDesinfection}
            }
            else
            {
                return desinf;
            }
        })
        setTypesDesinfection(newState);
    }
    
    return (<>
        <IconButton
            icon='bell'
            size = 'sm'
            variant="outline-info"
            onClick={handleShowAlerteModal}
        >Gérer les récurrences</IconButton>

        <Modal show={showAlerteModal} onHide={handleCloseAlerteModal} size="lg" backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>Gestion des alertes et récurrences pour les désinfections</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                {isLoading ? <LoaderInfiniteLoop/> :
                    <Table>
                        <thead>
                            <tr>
                                <th>Désinfection</th>
                                <th>Alerte active</th>
                                <th>Jours de récurrence</th>
                            </tr>
                        </thead>
                        <tbody>
                            {typesDesinfection.map((type, i)=>{return(
                                <tr>
                                    <td>{type.label}</td>
                                    <td>{type.frequenceDesinfection > 0 ?
                                        <FontAwesomeIcon icon='check' />
                                    : null}</td>
                                    <td>
                                        <Form.Control size="sm" type="number" min="0" name="frequenceDesinfection" id="frequenceDesinfection" value={type.frequenceDesinfection} onChange={(e) => {updateFrequenceDesinfection(i, e.target.value)}}/>
                                    </td>
                                </tr>
                            )})}
                        </tbody>
                    </Table>
                }
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseAlerteModal}>
                    Annuler
                </Button>
                <Button variant='primary' onClick={enregistrerRecurrences} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
            </Modal.Footer>
        </Modal>
    </>);
};

VehiculeDesinfectionsAlertes.propTypes = {};

export default VehiculeDesinfectionsAlertes;
