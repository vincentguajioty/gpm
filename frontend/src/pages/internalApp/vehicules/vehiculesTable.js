import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vehiculeAddForm } from 'helpers/yupValidationSchema';

const VehiculesTable = ({
    displayLibelleVehicule = true,
    displayLibelleType = true,
    displayLibelleVehiculesEtat = true,
    displayIdentifiant = true,
    displayImmatriculation = true,
    displayMarqueModele = true,
    displayControles = true,
    displayNbAlertesEnCours = true,
    displayLibelleNotificationEnabled = true,
    displayActions = true,
    filterUserAffected = false,
    alternativeTitle = null,
}) => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [vehiculesTable, setVehiculesTable] = useState([]);

    const navigate = useNavigate();

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vehicules/getAllVehicules');

            if(filterUserAffected == true)
            {
                setVehiculesTable(getData.data.filter(vehicule => vehicule.idResponsable == HabilitationService.habilitations.idPersonne));
            }else{
                setVehiculesTable(getData.data);
            }
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {
            accessor: 'libelleVehicule',
            isHidden: !displayLibelleVehicule,
            Header: 'Libellé',
            Cell: ({ value, row }) => {
				return(<Link to={'/vehicules/'+row.original.idVehicule}>{row.original.libelleVehicule}</Link>);
			},
        },
        {
            accessor: 'libelleType',
            isHidden: !displayLibelleType,
            Header: 'Type',
        },
        {
            accessor: 'libelleVehiculesEtat',
            isHidden: !displayLibelleVehiculesEtat,
            Header: 'Etat',
        },
        {
            accessor: 'identifiant',
            isHidden: !displayIdentifiant,
            Header: 'Responsable',
        },
        {
            accessor: 'immatriculation',
            isHidden: !displayImmatriculation,
            Header: 'Immatriculation',
        },
        {
            accessor: 'marqueModele',
            isHidden: !displayMarqueModele,
            Header: 'Marque/Modèle',
        },
        {
            accessor: 'controles',
            isHidden: !displayControles,
            Header: 'Contrôles',
            Cell: ({ value, row }) => {
				return(
                    <>
                        <SoftBadge className='me-1' bg={row.original.alerteDesinfection == null ? 'secondary' : row.original.alerteDesinfection == 0 ? 'success' : 'danger'}>Désinfections</SoftBadge>
                        <SoftBadge className='me-1' bg={row.original.alerteMaintenance == null ? 'secondary' : row.original.alerteMaintenance == 0 ? 'success' : 'danger'}>Maintenance</SoftBadge>
                    </>
                );
			},
        },
        {
            accessor: 'nbAlertesEnCours',
            isHidden: !displayNbAlertesEnCours,
            Header: 'Alertes bénévoles',
            Cell: ({ value, row }) => {
				return(row.original.nbAlertesEnCours > 0 ? <SoftBadge>{row.original.nbAlertesEnCours}</SoftBadge> : null);
			},
        },
        {
            accessor: 'libelleNotificationEnabled',
            isHidden: !displayLibelleNotificationEnabled,
            Header: 'Notifications',
            Cell: ({ value, row }) => {
				return(row.original.notifiationEnabled == true ? <FontAwesomeIcon icon='bell' /> : <FontAwesomeIcon icon='bell-slash'/>);
			},
        },
        {
            accessor: 'actions',
            isHidden: !displayActions,
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        <IconButton
                            icon='eye'
                            size = 'sm'
                            variant="outline-primary"
                            className="me-1"
                            onClick={()=>{navigate('/vehicules/'+row.original.idVehicule)}}
                        />
                        {HabilitationService.habilitations['vehicules_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idVehicule)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

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
                        data={vehiculesTable}
                        topButtonShow={true}
                        topButton={
                            alternativeTitle != null ?
                                <h5>{alternativeTitle}</h5>
                            :
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

VehiculesTable.propTypes = {};

export default VehiculesTable;
