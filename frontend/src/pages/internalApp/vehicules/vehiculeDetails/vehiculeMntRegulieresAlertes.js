import React, {useEffect, useState} from 'react';
import { Row, Col, Card, Form, Table, Modal, Button } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
registerLocale('fr', fr);
setDefaultLocale('fr');

const VehiculeMaintenancesRegulieresAlertes = ({idVehicule, maintenancesRegulieresAlertes, setPageNeedsRefresh}) => {
    
    const [showAlerteModal, setShowAlerteModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const handleCloseAlerteModal = () => {
        setShowAlerteModal(false);
        setLoading(false);
    };
    
    const [typesMaintenance, setTypesMaintenance] = useState([]);
    const handleShowAlerteModal = async () => {
        try {
            setShowAlerteModal(true);
            setLoading(true);

            let getData = await Axios.get('/select/getTypesMaintenancesRegulieresVehicules');
            for(const mnt of getData.data)
            {
                let selectedMnt = maintenancesRegulieresAlertes.filter(oneAlerte => oneAlerte.idHealthType == mnt.value)
                if(selectedMnt.length > 0)
                {
                    mnt.frequenceHealth = selectedMnt[0].frequenceHealth
                }
                else
                {
                    mnt.frequenceHealth = null
                }
            }
            
            setTypesMaintenance(getData.data);
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const enregistrerRecurrences = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/updateMaintenanceReguliereAlertes',{
                idVehicule: idVehicule,
                typesMaintenance: typesMaintenance,
            });
            
            setPageNeedsRefresh(true);
            handleCloseAlerteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    const updateFrequenceHealth = (index, frequenceHealth) => {
        const newState = typesMaintenance.map((mnt, i) => {
            if(i === index)
            {
                return {...mnt, frequenceHealth: frequenceHealth}
            }
            else
            {
                return mnt;
            }
        })
        setTypesMaintenance(newState);
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
                <Modal.Title>Gestion des alertes et récurrences pour les maintenances régulières</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                {isLoading ? <LoaderInfiniteLoop/> :
                    <Table>
                        <thead>
                            <tr>
                                <th>Maintenance</th>
                                <th>Alerte active</th>
                                <th>Jours de récurrence</th>
                            </tr>
                        </thead>
                        <tbody>
                            {typesMaintenance.map((type, i)=>{return(
                                <tr>
                                    <td>{type.label}</td>
                                    <td>{type.frequenceHealth > 0 ?
                                        <FontAwesomeIcon icon='check' />
                                    : null}</td>
                                    <td>
                                        <Form.Control size="sm" type="number" min="0" name="frequenceHealth" id="frequenceHealth" value={type.frequenceHealth} onChange={(e) => {updateFrequenceHealth(i, e.target.value)}}/>
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

VehiculeMaintenancesRegulieresAlertes.propTypes = {};

export default VehiculeMaintenancesRegulieresAlertes;
