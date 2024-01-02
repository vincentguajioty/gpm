import React, {useEffect, useState} from 'react';
import { Modal, Button } from 'react-bootstrap';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import VehiculeDesinfectionsAlertes from './vehiculeDesinfectionsAlertes';
import VehiculeDesinfectionsForm from './vehiculeDesinfectionsForm';

const VehiculeDesinfections = ({idVehicule, desinfections, desinfectionsAlertes, setPageNeedsRefresh}) => {
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'dateDesinfection',
            Header: 'Date',
            Cell: ({ value, row }) => {
				return(moment(value).format('DD/MM/YYYY'));
			},
        },
        {
            accessor: 'desinfections',
            Header: 'Désinfection',
        },
        {
            accessor: 'identifiant',
            Header: 'Intervenant',
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {HabilitationService.habilitations['desinfections_modification'] ? 
                            <VehiculeDesinfectionsForm
                                idVehicule = {idVehicule}
                                desinfection = {row.original}
                                idVehiculesDesinfection = {row.original.idVehiculesDesinfection}
                                setPageNeedsRefresh = {setPageNeedsRefresh}
                            />
                        : null}

                        {HabilitationService.habilitations['desinfections_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idVehiculesDesinfection)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdVehiculesDesinfection, setDeleteModalIdVehiculesDesinfection] = useState();
    const [isLoading, setLoading] = useState(false);

    const handleCloseDeleteModal = () => {
        setDeleteModalIdVehiculesDesinfection();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVehiculesDesinfection(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/deleteDesinfection',{
                idVehiculesDesinfection: deleteModalIdVehiculesDesinfection,
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
            {desinfectionsAlertes.map((alerte, i)=>{return(
                <SoftBadge
                    className="fs--1 me-1 mb-1"
                    bg={alerte.isInAlert ? 'danger' : 'success'}
                >{alerte.libelleVehiculesDesinfectionsType}</SoftBadge>
            )})}
        </center>
        
        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>Suppression</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer une désinfection (id: {deleteModalIdVehiculesDesinfection}). Etes-vous certain de vouloir continuer ?
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
            data={desinfections}
            topButtonShow={true}
            topButton={<>
                {HabilitationService.habilitations['desinfections_ajout'] ? 
                    <VehiculeDesinfectionsForm
                        idVehicule = {idVehicule}
                        idVehiculesDesinfection = {0}
                        setPageNeedsRefresh = {setPageNeedsRefresh}
                        buttonSize = 'large'
                    />
                : ''}

                {HabilitationService.habilitations['vehicules_modification'] ? 
                    <VehiculeDesinfectionsAlertes
                        idVehicule={idVehicule}
                        desinfectionsAlertes={desinfectionsAlertes}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                : ''}
            </>}
        />
    </>);
};

VehiculeDesinfections.propTypes = {};

export default VehiculeDesinfections;
