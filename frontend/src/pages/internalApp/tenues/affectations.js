import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';

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
        {accessor: 'personne',     Header: 'Personne'},
        {accessor: 'affectation',  Header: 'Affectation'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of affectations)
        {
            tempTable.push({
                personne:<>{item.nomPrenom}{item.type=='externe' ? <SoftBadge bg='secondary' className='ms-1'>Externe</SoftBadge>:null}</>,
                affectation:<Table className="fs--1 mt-3" size='sm' responsive>
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
                        {item.affectations.map((affect, i)=>{return(
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
                </Table>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [affectations, affectationsRow])

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
            setValue("dateAffectation", oneItemFromArray.dateAffectation != null ? new Date(oneItemFromArray.dateAffectation) : null);
            setValue("dateRetour", oneItemFromArray.dateRetour != null ? new Date(oneItemFromArray.dateRetour) : null);
        }
        else
        {
            setValue("dateAffectation", new Date());
        }

        let getData = await Axios.get('/tenues/getCatalogue');
        setCatalogue(getData.data);

        getData = await Axios.get('/tenues/getPersonnesSuggested');
        setPersonnesExternes(getData.data);

        getData = await Axios.get('/settingsUtilisateurs/getAllUsers');
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
                    idCatalogueTenue: data.idCatalogueTenue,
                    idPersonne: data.idPersonne > 0 ? data.idPersonne : null,
                    personneNonGPM: data.personneNonGPM,
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
                        <Form.Select size="sm" name="idCatalogueTenue" id="idCatalogueTenue" {...register("idCatalogueTenue")}>
                            <option key="0" value="">--- Choisir un élément ---</option>
                            {catalogue.map((cat, i) => {
                                return (<option key={cat.idCatalogueTenue} value={cat.idCatalogueTenue}>{cat.libelleCatalogueTenue} (Taille: {cat.tailleCatalogueTenue})</option>);
                            })}
                        </Form.Select>
                        <small className="text-danger">{errors.idCatalogueTenue?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Affecté à</Form.Label>
                        <Form.Select size="sm" name="idPersonne" id="idPersonne" {...register("idPersonne")}>
                            <option key="0" value="0">--- Affecté à un externe ---</option>
                            {personnesInternes.map((perso, i) => {
                                return (<option key={perso.idPersonne} value={perso.idPersonne}>{perso.identifiant}</option>);
                            })}
                        </Form.Select>
                        <small className="text-danger">{errors.idPersonne?.message}</small>

                        {watch("idPersonne") == 0 ? <>
                            <Form.Control list='suggestionsExternes' size="sm" type="text" name='personneNonGPM' id='personneNonGPM' {...register('personneNonGPM')}/>
                            <datalist id='suggestionsExternes'>
                                {personnesExternes.map((perso, i) => {
                                    return (<option key={i}>{perso.personneNonGPM}</option>);
                                })}
                            </datalist>
                            <small className="text-danger">{errors.personneNonGPM?.message}</small>
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
            <Modal.Body>
                Attention, vous allez supprimer cette affectation (id: {deleteModalIdTenue}). Etes-vous certain de vouloir continuer ?
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
                        data={lignes}
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
