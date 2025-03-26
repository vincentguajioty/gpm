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
import { vhfEquipementsDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vhfEquipementsAddForm } from 'helpers/yupValidationSchema';

const Equipements = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [equipementsVhf, setEquipementsVhf] = useState([]);

    const navigate = useNavigate();

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vhf/getEquipementsVhf');
            setEquipementsVhf(getData.data);  
            
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
            accessor: 'vhfIndicatif',
            Header: 'Indicatif',
            Cell: ({ value, row }) => {
				return(<Link to={'/vhfEquipements/'+row.original.idVhfEquipement}>{row.original.vhfIndicatif}</Link>);
			},
        },
        {
            accessor: 'libelleType',
            Header: 'Type',
        },
        {
            accessor: 'libelleVhfEtat',
            Header: 'Etat',
        },
        {
            accessor: 'libelleTechno',
            Header: 'Technologie',
        },
        {
            accessor: 'nbAccessoires',
            Header: 'Accessoires',
        },
        {
            accessor: 'nbAlertesEnCours',
            Header: 'Alertes bénévoles',
            Cell: ({ value, row }) => {
                return(row.original.nbAlertesEnCours > 0 ? <SoftBadge>{row.original.nbAlertesEnCours}</SoftBadge> : null);
            },
        },
        {
            accessor: 'libellePlan',
            Header: 'Plan de fréquence',
            Cell: ({ value, row }) => {
				return(row.original.idVhfPlan > 0 ? <SoftBadge bg='info'>{row.original.libellePlan}</SoftBadge> : <SoftBadge bg='danger'>Non-Programmée</SoftBadge>);
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        <IconButton
                            icon='eye'
                            size = 'sm'
                            variant="outline-primary"
                            className="me-1"
                            onClick={()=>{navigate('/vhfEquipements/'+row.original.idVhfEquipement)}}
                        />
                        {HabilitationService.habilitations['vhf_equipement_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idVhfEquipement)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vhfEquipementsAddForm),
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

            const response = await Axios.post('/vhf/addEquipement',{
                vhfIndicatif: data.vhfIndicatif,
            });

            let idTarget = response.data.idVhfEquipement;
            navigate('/vhfEquipements/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdVhfEquipement, setDeleteModalIdVhfEquipement] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdVhfEquipement();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVhfEquipement(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vhf/deleteEquipement',{
                idVhfEquipement: deleteModalIdVhfEquipement,
            });
            
            initPage();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return (<>
        <PageHeader
            preTitle="Transmissions"
            title="Equipements radios et accessoires"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouvel équipement radio</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Indicatif</Form.Label>
                        <Form.Control size="sm" type="text" name='vhfIndicatif' id='vhfIndicatif' {...register('vhfIndicatif')}/>
                        <small className="text-danger">{errors.vhfIndicatif?.message}</small>
                    </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{vhfEquipementsDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ?
                    <GPMtable
                        columns={colonnes}
                        data={equipementsVhf}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['vhf_equipement_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={handleShowOffCanevas}
                                >Nouvel équipement</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Equipements.propTypes = {};

export default Equipements;
