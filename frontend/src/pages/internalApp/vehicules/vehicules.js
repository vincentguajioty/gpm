import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vehiculeAddForm } from 'helpers/yupValidationSchema';

const Vehicules = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [vehiculesTable, setVehiculesTable] = useState([]);

    const navigate = useNavigate();

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vehicules/getAllVehicules');
            setVehiculesTable(getData.data);  
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {accessor: 'libelleVehicule'              , Header: 'Libellé'},
        {accessor: 'libelleType'                  , Header: 'Type'},
        {accessor: 'libelleVehiculesEtat'         , Header: 'Etat'},
        {accessor: 'identifiant'                  , Header: 'Responsable'},
        {accessor: 'immatriculation'              , Header: 'Immatriculation'},
        {accessor: 'marqueModele'                 , Header: 'Marque/Modèle'},
        {accessor: 'controles'                    , Header: 'Contrôles'},
        {accessor: 'nbAlertesEnCours'             , Header: 'Alertes bénévoles'},
        {accessor: 'libelleNotificationEnabled'   , Header: 'Notifications'},
        {accessor: 'actions'                      , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of vehiculesTable)
        {
            tempTable.push({
                libelleVehicule: <Link to={'/vehicules/'+item.idVehicule}>{item.libelleVehicule}</Link>,
                libelleType: item.libelleType,
                libelleVehiculesEtat: item.libelleVehiculesEtat,
                identifiant: item.identifiant,
                immatriculation: item.immatriculation,
                marqueModele: item.marqueModele,
                controles:<>
                    <SoftBadge className='me-1' bg={item.alerteDesinfection == null ? 'secondary' : item.alerteDesinfection == 0 ? 'success' : 'danger'}>Désinfections</SoftBadge>
                    <SoftBadge className='me-1' bg={item.alerteMaintenance == null ? 'secondary' : item.alerteMaintenance == 0 ? 'success' : 'danger'}>Maintenance</SoftBadge>
                </>,
                nbAlertesEnCours: item.nbAlertesEnCours > 0 ? <SoftBadge>{item.nbAlertesEnCours}</SoftBadge> : null,
                libelleNotificationEnabled: item.notifiationEnabled == true ? <FontAwesomeIcon icon='bell' /> : <FontAwesomeIcon icon='bell-slash'/>,
                actions:
                    <>
                        <IconButton
                            icon='eye'
                            size = 'sm'
                            variant="outline-primary"
                            className="me-1"
                            onClick={()=>{navigate('/vehicules/'+item.idVehicule)}}
                        />
                        {HabilitationService.habilitations['vehicules_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(item.idVehicule)}}
                            />
                        : null}
                    </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [vehiculesTable])

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdVehicule, setDeleteModalIdVehicule] = useState();
    const [isLoading, setLoading] = useState(false);
    const handleCloseDeleteModal = () => {
        setDeleteModalIdVehicule();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVehicule(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/deleteVehicule',{
                idVehicule: deleteModalIdVehicule,
            });
            
            initPage();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vehiculeAddForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = () => {
        setShowOffCanevas(true);
    }
    const ajouterEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/addVehicule',{
                libelleVehicule: data.libelleVehicule,
            });

            let idTarget = response.data.idVehicule;
            navigate('/vehicules/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    return (<>
        <PageHeader
            preTitle="Véhicules"
            title="Gestion du parc de véhicules"
            className="mb-3"
        />

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un véhicule et tout ce qui lui est lié (id: {deleteModalIdVehicule}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouveau véhicule</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé</Form.Label>
                        <Form.Control size="sm" type="text" name='libelleVehicule' id='libelleVehicule' {...register('libelleVehicule')}/>
                        <small className="text-danger">{errors.libelleVehicule?.message}</small>
                    </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ?
                    <GPMtable
                        columns={colonnes}
                        data={lignes}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['vehicules_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={handleShowOffCanevas}
                                >Nouveau véhicule</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Vehicules.propTypes = {};

export default Vehicules;
