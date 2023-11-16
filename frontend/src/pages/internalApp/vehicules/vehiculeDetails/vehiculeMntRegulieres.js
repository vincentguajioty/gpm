import React, {useEffect, useState} from 'react';
import { Table, Modal, Button } from 'react-bootstrap';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import VehiculeMaintenancesRegulieresAlertes from './vehiculeMntRegulieresAlertes';
import VehiculeMaintenancesRegulieresForm from './vehiculeMntRegulieresForm';

const VehiculeMaintenancesRegulieres = ({idVehicule, maintenancesRegulieres, maintenancesRegulieresAlertes, setPageNeedsRefresh}) => {
    const nl2br = require('react-nl2br');
    const colonnes = [
        {accessor: 'dateHealth'      , Header: 'Date'},
        {accessor: 'interventions'   , Header: 'Interventions'},
        {accessor: 'identifiant'     , Header: 'Intervenant'},
        {accessor: 'actions'         , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of maintenancesRegulieres)
        {
            tempTable.push({
                dateHealth: moment(item.dateHealth).format('DD/MM/YYYY'),
                interventions: <>
                    <Table responsive size='sm'>
                        <thead>
                            <tr>
                                <th>Actes</th>
                                <th>Remarques</th>
                            </tr>
                        </thead>
                        <tbody>
                            {item.checks.map((verif, i)=>{return(
                                <tr>
                                    <td>{verif.libelleHealthType}</td>
                                    <td>{nl2br(verif.remarquesCheck)}</td>
                                </tr>
                            )})}
                        </tbody>
                    </Table>
                </>,
                identifiant: item.identifiant,
                actions:<>
                    {HabilitationService.habilitations['vehiculeHealth_modification'] ? 
                        <VehiculeMaintenancesRegulieresForm
                            idVehicule = {idVehicule}
                            maintenance = {item}
                            idVehiculeHealth = {item.idVehiculeHealth}
                            maintenancesRegulieresAlertes = {maintenancesRegulieresAlertes}
                            setPageNeedsRefresh = {setPageNeedsRefresh}
                        />
                    : null}

                    {HabilitationService.habilitations['vehiculeHealth_suppression'] ? 
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{handleShowDeleteModal(item.idVehiculeHealth)}}
                        />
                    : null}
                </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [maintenancesRegulieres])

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdVehiculeHealth, setDeleteModalIdVehiculeHealth] = useState();
    const [isLoading, setLoading] = useState(false);
    const handleCloseDeleteModal = () => {
        setDeleteModalIdVehiculeHealth();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVehiculeHealth(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/deleteMaintenanceReguliere',{
                idVehiculeHealth: deleteModalIdVehiculeHealth,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }
    
    return (<>
        <center className='mb-3'>
            {maintenancesRegulieresAlertes.map((alerte, i)=>{return(
                <SoftBadge
                    className="fs--1 me-1 mb-1"
                    bg={alerte.isInAlert ? 'danger' : 'success'}
                >{alerte.libelleHealthType}</SoftBadge>
            )})}
        </center>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>Suppression</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer une maintenance régulière et toutes ses taches (id: {deleteModalIdVehiculeHealth}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <GPMtable
            columns={colonnes}
            data={lignes}
            topButtonShow={true}
            topButton={<>
                {HabilitationService.habilitations['vehiculeHealth_ajout'] ?
                    <VehiculeMaintenancesRegulieresForm
                        idVehicule = {idVehicule}
                        idVehiculeHealth = {"0"}
                        maintenancesRegulieresAlertes = {maintenancesRegulieresAlertes}
                        setPageNeedsRefresh = {setPageNeedsRefresh}
                        buttonSize={'large'}
                    />
                : ''}
                
                {HabilitationService.habilitations['vehicules_modification'] ?
                    <VehiculeMaintenancesRegulieresAlertes
                        idVehicule={idVehicule}
                        maintenancesRegulieresAlertes={maintenancesRegulieresAlertes}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                : ''}
            </>
            }
        />
    </>);
};

VehiculeMaintenancesRegulieres.propTypes = {};

export default VehiculeMaintenancesRegulieres;
