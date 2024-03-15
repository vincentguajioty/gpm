import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import Select from 'react-select';
import { tenuesAffectationsDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { affectationsTenuesForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const AffectationsTenues = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [affectations, setAffectations] = useState([]);
    const [affectationsRow, setAffectationsRow] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/tenues/getAffectations');
            setAffectations(getData.data);
            const getRowData = await Axios.get('/tenues/getAffectationsRow');
            setAffectationsRow(getRowData.data);
            
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
            accessor: 'nomPrenom',
            Header: 'Personne',
            Cell: ({ value, row }) => {
				return(<>
                    {value}
                    {row.original.mailPersonne != null ? <><br/><SoftBadge bg='info'>{row.original.mailPersonne}</SoftBadge></>:null}
                    <br/>{row.original.type=='externe' ? <SoftBadge bg='secondary'>Externe</SoftBadge>:null}
                </>);
			},
        },
        {
            accessor: 'affectations',
            Header: 'Affectation',
            Cell: ({ value, row }) => {
				return(
                    <Table className="fs--1 mt-3" size='sm' responsive>
                        <thead>
                            <tr>
                                <th>Element</th>
                                <th>Taille</th>
                                <th>Affecté le</th>
                                <th>Retour prévu le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {value.map((affect, i)=>{return(
                                <tr>
                                    <td>{affect.libelleCatalogueTenue}</td>
                                    <td>{affect.tailleCatalogueTenue}</td>
                                    <td>{affect.dateAffectation != null ? moment(affect.dateAffectation).format('DD/MM/YYYY') : null}</td>
                                    <td>{affect.dateRetour != null ? 
                                            new Date(affect.dateRetour) < new Date() ?
                                                <SoftBadge bg='danger'>{moment(affect.dateRetour).format('DD/MM/YYYY')}</SoftBadge>
                                            :
                                                <SoftBadge bg='success'>{moment(affect.dateRetour).format('DD/MM/YYYY')}</SoftBadge>
                                        : null}
                                    </td>
                                    <td>
                                        {HabilitationService.habilitations['tenues_modification'] ? 
                                            <IconButton
                                                icon='pen'
                                                size = 'sm'
                                                variant="outline-warning"
                                                className="me-1"
                                                onClick={()=>{handleShowOffCanevas(affect.idTenue)}}
                                            />
                                        : null}
                                        {HabilitationService.habilitations['tenues_suppression'] ? 
                                            <IconButton
                                                icon='trash'
                                                size = 'sm'
                                                variant="outline-danger"
                                                className="me-1"
                                                onClick={()=>{handleShowDeleteModal(affect.idTenue)}}
                                            />
                                        : null}
                                    </td>
                                </tr>
                            )})}
                        </tbody>
                    </Table>
                );
			},
        },
    ];
    
    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdTenue, setOffCanevasIdTenue] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(affectationsTenuesForm),
    });
    const [catalogue, setCatalogue] = useState([]);
    const [personnesInternes, setPersonnesInternes] = useState([]);
    const [personnesExternes, setPersonnesExternes] = useState([]);
    const [suggestionsMailsExternes, setSuggestionsMailsExternes] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdTenue();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdTenue(id);

        if(id > 0)
        {
            let oneItemFromArray = affectationsRow.filter(ligne => ligne.idTenue == id)[0];
            setValue("idCatalogueTenueInitial", oneItemFromArray.idCatalogueTenue);
            setValue("idCatalogueTenue", oneItemFromArray.idCatalogueTenue);
            setValue("idPersonne", oneItemFromArray.idPersonne > 0 ? oneItemFromArray.idPersonne : 0);
            setValue("personneNonGPM", oneItemFromArray.personneNonGPM);
            setValue("mailPersonneNonGPM", oneItemFromArray.mailPersonneNonGPM);
            setValue("dateAffectation", oneItemFromArray.dateAffectation != null ? new Date(oneItemFromArray.dateAffectation) : null);
            setValue("dateRetour", oneItemFromArray.dateRetour != null ? new Date(oneItemFromArray.dateRetour) : null);
        }
        else
        {
            setValue("dateAffectation", new Date());
            setValue("idPersonne", 0);
        }

        let getData = await Axios.get('/select/getTenuesCatalogue');
        setCatalogue(getData.data);

        getData = await Axios.get('/tenues/getPersonnesSuggested');
        setPersonnesExternes(getData.data);

        getData = await Axios.get('/select/getNonAnonymesPersonnes');
        getData.data.unshift({value: 0, label: '--- Affecté à un externe ---'})
        setPersonnesInternes(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdTenue > 0)    
            {
                const response = await Axios.post('/tenues/updateAffectations',{
                    idTenue: offCanevasIdTenue,
                    idCatalogueTenueInitial: data.idCatalogueTenueInitial,
                    idCatalogueTenue: data.idCatalogueTenue,
                    idPersonne: data.idPersonne > 0 ? data.idPersonne : null,
                    personneNonGPM: data.personneNonGPM,
                    mailPersonneNonGPM: data.mailPersonneNonGPM,
                    dateAffectation: data.dateAffectation,
                    dateRetour: data.dateRetour,
                });
            }
            else
            {
                const response = await Axios.post('/tenues/addAffectations',{
                    idCatalogueTenue: data.idCatalogueTenue,
                    idPersonne: data.idPersonne > 0 ? data.idPersonne : null,
                    personneNonGPM: data.personneNonGPM,
                    mailPersonneNonGPM: data.mailPersonneNonGPM,
                    dateAffectation: data.dateAffectation,
                    dateRetour: data.dateRetour,
                });
            }

            handleCloseOffCanevas();
            initPage();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    useEffect(()=>{
        setSuggestionsMailsExternes([]);

        let usersTrouves = personnesExternes.filter(perso => perso.personneNonGPM == watch('personneNonGPM') && perso.mailPersonneNonGPM != null);
        let tempMailArray = [];
        for(const perso of usersTrouves)
        {
            tempMailArray.push(perso.mailPersonneNonGPM)
        }
        setSuggestionsMailsExternes(tempMailArray);
        
        if(usersTrouves.length == 1)
        {
            setValue('mailPersonneNonGPM', usersTrouves[0].mailPersonneNonGPM)
        }
    },[watch('personneNonGPM')])

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdTenue, setDeleteModalIdTenue] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdTenue();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdTenue(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteAffectations',{
                idTenue: deleteModalIdTenue,
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
            preTitle="Gestion des tenues"
            title="Affectation des tenues"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdTenue > 0 ? "Modification" : "Ajout"} d'une affectation de tenue</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Element de tenue</Form.Label>
                        <Select
                            id="idCatalogueTenue"
                            name="idCatalogueTenue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun élément selectionné'
                            options={catalogue}
                            value={catalogue.find(c => c.value === watch("idCatalogueTenue"))}
                            onChange={val => val != null ? setValue("idCatalogueTenue", val.value) : setValue("idCatalogueTenue", null)}
                        />
                        <small className="text-danger">{errors.idCatalogueTenue?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Affecté à</Form.Label>
                        <Select
                            id="idPersonne"
                            name="idPersonne"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucune personne selectionnée'
                            options={personnesInternes}
                            value={personnesInternes.find(c => c.value === watch("idPersonne"))}
                            onChange={val => val != null ? setValue("idPersonne", val.value) : setValue("idPersonne", null)}
                        />
                        <small className="text-danger">{errors.idPersonne?.message}</small>

                        {watch("idPersonne") == 0 ? <>
                            <Form.Control className='mt-2' placeholder="Nom et prénom" list='suggestionsExternes' size="sm" type="text" name='personneNonGPM' id='personneNonGPM' {...register('personneNonGPM')}/>
                            <datalist id='suggestionsExternes'>
                                {personnesExternes.map((perso, i) => {
                                    return (<option key={i} value={perso.personneNonGPM}>{perso.mailPersonneNonGPM}</option>);
                                })}
                            </datalist>
                            <small className="text-danger">{errors.personneNonGPM?.message}</small>

                            <Form.Control className='mt-2' placeholder="Adresse email" list='suggestionsMailsConditionnels' size="sm" type="email" name='mailPersonneNonGPM' id='mailPersonneNonGPM' {...register('mailPersonneNonGPM')}/>
                            <datalist id='suggestionsMailsConditionnels'>
                                {suggestionsMailsExternes.map((mail, i) => {
                                    return (<option key={i}>{mail}</option>);
                                })}
                            </datalist>
                            <small className="text-danger">{errors.mailPersonneNonGPM?.message}</small>
                        </>: null}
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date d'affectation</Form.Label>
                        <DatePicker
                            selected={watch("dateAffectation")}
                            onChange={(date)=>setValue("dateAffectation", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateAffectation?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date prévisionnelle de retour</Form.Label>
                        <DatePicker
                            selected={watch("dateRetour")}
                            onChange={(date)=>setValue("dateRetour", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateRetour?.message}</small>
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{tenuesAffectationsDelete}</Modal.Body>
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
                        data={affectations}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['tenues_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={()=>{handleShowOffCanevas(0)}}
                                >Nouvelle affectation</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

AffectationsTenues.propTypes = {};

export default AffectationsTenues;
