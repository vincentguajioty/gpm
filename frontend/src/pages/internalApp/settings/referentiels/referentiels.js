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
import { referentielsDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { referentielAddForm } from 'helpers/yupValidationSchema';

const ReferentielsSettings = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [referentiels, setReferentiels] = useState([]);

    const navigate = useNavigate();

    const initPage = async () => {
        try {
            const getData = await Axios.get('/referentiels/getReferentiels');
            setReferentiels(getData.data);  
            
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
            accessor: 'libelleTypeLot',
            Header: 'Libellé',
            Cell: ({ value, row }) => {
				return(<Link to={'/settingsReferentiels/'+row.original.idTypeLot}>{row.original.libelleTypeLot}</Link>);
			},
        },
        {
            accessor: 'lotsRattaches',
            Header: 'Lots rattachés',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {row.original.lotsRattaches.map((lot, j) => {return(
                            <SoftBadge bg={lot.alerteConfRef ? 'danger' : 'success'} className='me-1'>{lot.libelleLot}</SoftBadge>
                        )})}
                    </>
                );
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
                            onClick={()=>{navigate('/settingsReferentiels/'+row.original.idTypeLot)}}
                        />
                        {HabilitationService.habilitations['typesLots_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idTypeLot)}}
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
        resolver: yupResolver(referentielAddForm),
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

            const response = await Axios.post('/referentiels/addReferentiel',{
                libelleTypeLot: data.libelleTypeLot,
            });

            let idTarget = response.data.idTypeLot;
            navigate('/settingsReferentiels/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdTypeLot, setDeleteModalIdTypeLot] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdTypeLot();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdTypeLot(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/referentiels/deleteReferentiel',{
                idTypeLot: deleteModalIdTypeLot,
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
            preTitle="Attention - Zone de paramétrage"
            title="Référentiels des lots opérationnels"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouveau référentiel</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterEntree)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Libellé</Form.Label>
                            <Form.Control size="sm" type="text" name='libelleTypeLot' id='libelleTypeLot' {...register('libelleTypeLot')}/>
                            <small className="text-danger">{errors.libelleTypeLot?.message}</small>
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
            <Modal.Body>{referentielsDelete}</Modal.Body>
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
                        data={referentiels}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['typesLots_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={handleShowOffCanevas}
                                >Nouveau référentiel</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

ReferentielsSettings.propTypes = {};

export default ReferentielsSettings;
