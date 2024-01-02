import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, Modal, ButtonGroup, ToggleButton, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import Select from 'react-select';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { lotsAddForm, lotsDuplicateForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const Lots = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [lotsArray, setLotsArray] = useState([]);

    const initPage = async () => {
        try {
            const getConfig = await Axios.get('/lots/getLots');
            setLotsArray(getConfig.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    const colonnes = [
        {
            accessor: 'libelleLot',
            Header: 'Libellé',
            Cell: ({ value, row }) => {
				return(<Link to={'/lots/'+row.original.idLot}>{row.original.libelleLot}</Link>);
			},
        },
        {
            accessor: 'libelleLotsEtat',
            Header: 'Etat',
        },
        {
            accessor: 'libelleTypeLot',
            Header: 'Référentiel',
            Cell: ({ value, row }) => {
				return(<SoftBadge bg={row.original.alerteConfRef == 1 ? 'danger' : row.original.alerteConfRef == 0 ? 'success' : 'secondary'}>{row.original.libelleTypeLot != null ? row.original.libelleTypeLot : 'N/A'}</SoftBadge>);
			},
        },
        {
            accessor: 'identifiant',
            Header: 'Référent',
        },
        {
            accessor: 'materiels',
            Header: 'Quantité de matériel',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {row.original.materielsOK > 0 ? <SoftBadge className='me-1' bg='success'>{row.original.materielsOK}</SoftBadge> : null }
                        {row.original.materielsLimites > 0 ? <SoftBadge className='me-1' bg='warning'>{row.original.materielsLimites}</SoftBadge> : null }
                        {row.original.materielsAlerte > 0 ? <SoftBadge className='me-1' bg='danger'>{row.original.materielsAlerte}</SoftBadge> : null }
                    </>
                );
			},
        },
        {
            accessor: 'prochainInventaire',
            Header: 'Prochain Inventaire',
            Cell: ({ value, row }) => {
				return(
                    row.original.inventaireEnCours == true ?
                    <SoftBadge bg="primary">Inventaire en cours</SoftBadge>
                    : <SoftBadge bg={row.original.prochainInventaire != null ? (new Date(row.original.prochainInventaire) < new Date() ? 'danger' : 'success') : 'secondary'}>{row.original.prochainInventaire != null ? moment(row.original.prochainInventaire).format('DD/MM/YYYY') : 'N/A'}</SoftBadge>
                );
			},
        },
        {
            accessor: 'nbAlertesEnCours',
            Header: 'Alertes Bénévoles',
            Cell: ({ value, row }) => {
				return(value > 0 ? <SoftBadge>{value}</SoftBadge> : null);
			},
        },
        {
            accessor: 'notifiationEnabled',
            Header: 'Notifications',
            Cell: ({ value, row }) => {
				return(value == true ? <FontAwesomeIcon icon='bell' /> : <FontAwesomeIcon icon='bell-slash'/>);
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
                            onClick={()=>{navigate('/lots/'+row.original.idLot)}}
                        />
                        {HabilitationService.habilitations['lots_suppression'] && row.original.inventaireEnCours != true ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idLot)}}
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
    const [etats, setEtats] = useState([]);
    const [notifications, setNotifications] = useState([]);
    const [lotsExistants, setLotsExistants] = useState([]);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(lotsAddForm),
    });
    const { register: registerDupl, handleSubmit: handleSubmitDupl, formState: { errors: errorsDupl }, setValue: setValueDupl, reset: resetDupl, watch: watchDupl } = useForm({
        resolver: yupResolver(lotsDuplicateForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        resetDupl();
        setLoading(false);
    }
    const handleShowOffCanevas = async () => {
        try {
            setShowOffCanevas(true);
            setLoading(true);
            setValue("dateDernierInventaire", new Date());

            let getDataForSelect = await Axios.get('/select/getNotificationsEnabled');
            setNotifications(getDataForSelect.data);
            getDataForSelect = await Axios.get('/select/getEtatsLots');
            setEtats(getDataForSelect.data);
            getDataForSelect = await Axios.get('/select/getLots');
            setLotsExistants(getDataForSelect.data);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    const ajouterEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/lots/addLot',{
                libelleLot: data.libelleLot,
                idNotificationEnabled: data.idNotificationEnabled,
                idLotsEtat: data.idLotsEtat,
                dateDernierInventaire: data.dateDernierInventaire,
                frequenceInventaire: data.frequenceInventaire,
            });

            let idTarget = response.data.idLot;
            navigate('/lots/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    const dupliquerEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/lots/duplicateLot',{
                libelleLot: data.libelleLot,
                idLot: data.idLot,
            });

            let idTarget = response.data.idLot;
            navigate('/lots/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdLot, setDeleteModalIdLot] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdLot();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdLot(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/lots/lotsDelete',{
                idLot: deleteModalIdLot,
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
            preTitle="Lots opérationnels"
            title="Lots"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouveau Lot</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <ButtonGroup>
                    <ToggleButton
                        key="new"
                        id={`radio-new`}
                        variant={formMode == 'new' ? 'info' : 'outline-info'}
                        name="radio"
                        value="0"
                        checked={formMode == 'new'}
                        onClick={(e) => setFormMode('new')}
                        size='sm'
                    >
                        A partir de zéro
                    </ToggleButton>
                    <ToggleButton
                        key="duplicate"
                        id={`radio-duplicate`}
                        variant={formMode == 'duplicate' ? 'info' : 'outline-info'}
                        name="radio"
                        value="0"
                        checked={formMode == 'duplicate'}
                        onClick={(e) => setFormMode('duplicate')}
                        size='sm'
                    >
                        Dupliquer un lot
                    </ToggleButton>
                </ButtonGroup>

                {formMode == 'new' ?
                    <Form onSubmit={handleSubmit(ajouterEntree)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Libellé</Form.Label>
                            <Form.Control size="sm" type="text" name='libelleLot' id='libelleLot' {...register('libelleLot')}/>
                            <small className="text-danger">{errors.libelleLot?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Etat</Form.Label>
                            <Select
                                id="idLotsEtat"
                                name="idLotsEtat"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun état'
                                options={etats}
                                value={etats.find(c => c.value === watch("idLotsEtat"))}
                                onChange={val => val != null ? setValue("idLotsEtat", val.value) : setValue("idLotsEtat", null)}
                            />
                            <small className="text-danger">{errors.idLotsEtat?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Notifications</Form.Label>
                            <Select
                                id="idNotificationEnabled"
                                name="idNotificationEnabled"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun élément selectionné'
                                options={notifications}
                                value={notifications.find(c => c.value === watch("idNotificationEnabled"))}
                                onChange={val => val != null ? setValue("idNotificationEnabled", val.value) : setValue("idNotificationEnabled", null)}
                            />
                            <small className="text-danger">{errors.idNotificationEnabled?.message}</small>
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
                :null}

                {formMode == 'duplicate' ?
                    <Form onSubmit={handleSubmitDupl(dupliquerEntree)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Lot à dupliquer</Form.Label>
                            <Select
                                id="idLot"
                                name="idLot"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun lot selectionné'
                                options={lotsExistants}
                                value={lotsExistants.find(c => c.value === watchDupl("idLot"))}
                                onChange={val => val != null ? setValueDupl("idLot", val.value) : setValueDupl("idLot", null)}
                            />
                            <small className="text-danger">{errorsDupl.idLot?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Libellé du nouveau lot</Form.Label>
                            <Form.Control size="sm" type="text" name='libelleLot' id='libelleLot' {...registerDupl('libelleLot')}/>
                            <small className="text-danger">{errorsDupl.libelleLot?.message}</small>
                        </Form.Group>
                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Dupliquer'}</Button>
                    </Form>
                :null}
            </Offcanvas.Body>
        </Offcanvas>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un lot (id: {deleteModalIdLot}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
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
                        data={lotsArray}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['lots_ajout'] ?
                                <>
                                    <IconButton
                                        icon='plus'
                                        size = 'sm'
                                        variant="outline-success"
                                        onClick={handleShowOffCanevas}
                                    >Nouveau lot</IconButton>
                                </>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Lots.propTypes = {};

export default Lots;
