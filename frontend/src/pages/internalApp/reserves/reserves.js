import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';
import { reserveConteneurDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { conteneursAddForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const Reserves = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [conteneursArray, setConteneursArray] = useState([]);

    const initPage = async () => {
        try {
            const getConfig = await Axios.get('/reserves/getConteneurs');
            setConteneursArray(getConfig.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    const colonnes = [
        {
            accessor: 'libelleConteneur',
            Header: 'Libellé',
            Cell: ({ value, row }) => {
				return(<Link to={'/reservesConteneurs/'+row.original.idConteneur}>{row.original.libelleConteneur}</Link>);
			},
        },
        {
            accessor: 'libelleLieu',
            Header: 'Lieu',
        },
        {
            accessor: 'prochainInventaire',
            Header: 'Prochain Inventaire',
            Cell: ({ value, row }) => {
				return(
                    row.original.inventaireEnCours == true ?
                    <SoftBadge bg="primary">Inventaire en cours</SoftBadge>
                    : <SoftBadge bg={value != null ? (new Date(value) < new Date() ? 'danger' : 'success') : 'secondary'}>{value != null ? moment(value).format('DD/MM/YYYY') : 'N/A'}</SoftBadge>
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
                            onClick={()=>{navigate('/reservesConteneurs/'+row.original.idConteneur)}}
                        />
                        {HabilitationService.habilitations['lots_suppression'] && row.original.inventaireEnCours != true ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idConteneur)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    useEffect(() => {
        initPage();
    }, [])

    const navigate = useNavigate();
    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const [formMode, setFormMode] = useState('new');
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(conteneursAddForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async () => {
        try {
            setShowOffCanevas(true);
            setLoading(true);
            setValue("dateDernierInventaire", new Date());

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    const ajouterEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/reserves/addConteneur',{
                libelleConteneur: data.libelleConteneur,
                dateDernierInventaire: data.dateDernierInventaire,
                frequenceInventaire: data.frequenceInventaire,
            });

            let idTarget = response.data.idConteneur;
            navigate('/reservesConteneurs/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdConteneur, setDeleteModalIdConteneur] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdConteneur();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdConteneur(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/reserves/reserveConteneurDelete',{
                idConteneur: deleteModalIdConteneur,
            });
            
            initPage();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    //Export
    const requestExport = async () => {
        try {
            setLoading(true);

            let fileRequest = await Axios.get('/reserves/exporterReservesEtendues');

            let documentData = await Axios.post('getSecureFile/temp',
            {
                fileName: fileRequest.data.fileName,
            },
            {
                responseType: 'blob'
            });
            
            // create file link in browser's memory
            const href = URL.createObjectURL(documentData.data);
            
            // create "a" HTML element with href to file & click
            const link = document.createElement('a');
            link.href = href;
            link.setAttribute('download', fileRequest.data.fileName); //or any other extension
            document.body.appendChild(link);
            link.click();

            // clean up "a" element & remove ObjectURL
            document.body.removeChild(link);
            URL.revokeObjectURL(href);
            
            setDownloadGenerated(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <PageHeader
            preTitle="Réserves"
            title="Conteneurs de réserve"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouveau Conteneur</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé</Form.Label>
                        <Form.Control size="sm" type="text" name='libelleConteneur' id='libelleConteneur' {...register('libelleConteneur')}/>
                        <small className="text-danger">{errors.libelleConteneur?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date du dernier inventaire</Form.Label>
                        <DatePicker
                            selected={watch("dateDernierInventaire")}
                            onChange={(date)=>setValue("dateDernierInventaire", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateDernierInventaire?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Fréquence d'inventaire (jours)</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='frequenceInventaire' id='frequenceInventaire' {...register('frequenceInventaire')}/>
                        <small className="text-danger">{errors.frequenceInventaire?.message}</small>
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
            <Modal.Body>{reserveConteneurDelete}</Modal.Body>
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
                        data={conteneursArray}
                        topButtonShow={true}
                        topButton={
                            <>
                                {HabilitationService.habilitations['reserve_ajout'] ?
                                    <IconButton
                                        icon='plus'
                                        size = 'sm'
                                        variant="outline-success"
                                        onClick={handleShowOffCanevas}
                                    >Nouveau conteneur</IconButton>
                                : ''}

                                {HabilitationService.habilitations['reserve_lecture'] ?
                                    <IconButton
                                        icon='download'
                                        size = 'sm'
                                        variant="outline-info"
                                        onClick={requestExport}
                                        className='ms-1'
                                        disabled={isLoading}
                                    >{isLoading ? "Génération en cours" : "Télécharger un état des lieux complet"}</IconButton>
                                : ''}
                            </>
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Reserves.propTypes = {};

export default Reserves;
