import React, { useState } from 'react';
import { Offcanvas, Button, Form, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { todolistEditForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const ToDoListForm = ({
    idTache = 0,
    isOwnTDL = false,
    isCompleted = false,
    autoAffectWhenCreate = false,
    showResolvedButton = false,
    showAddButton = false,
    showEditButton = false,
    showAffectationButton = false,
    showDeleteButton = false,
    setComponentsHaveToReload,
}) => {
    //OffCanevas d'édition
    const [showEditOffCanevas, setShowEditOffCanevas] = useState(false);
    const [editOffCanevasIdTache, setEditOffCanevasIdTache] = useState();
    const [isLoading, setLoading] = useState(false);
    const [priorites, setPriorites] = useState([]);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(todolistEditForm),
    });
    const handleCloseEditOffCanevas = () => {
        setShowEditOffCanevas(false);
        setEditOffCanevasIdTache();
        reset();
        setLoading(false);
    }
    const handleShowEditOffCanevas = async (idTache) => {
        try {
            setEditOffCanevasIdTache(idTache);

            let prio = await Axios.get('/select/getPioritesForTDL');
            setPriorites(prio.data);

            if(idTache > 0)
            {
                let tdl = await Axios.post('todolist/getOneTDL',{
                    idTache: idTache,
                });
                setValue("dateExecution", tdl.data[0].dateExecution != null ? new Date(tdl.data[0].dateExecution) : null);
                setValue("dateCloture", tdl.data[0].dateCloture != null ? new Date(tdl.data[0].dateCloture) : null);
                setValue("titre", tdl.data[0].titre);
                setValue("details", tdl.data[0].details);
                setValue("idTDLpriorite", tdl.data[0].idTDLpriorite);
            }

            setShowEditOffCanevas(true);
        } catch (error) {
            console.error(error)
        }
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(editOffCanevasIdTache > 0)    
            {
                const response = await Axios.post('/todolist/updateTDL',{
                    idTache: editOffCanevasIdTache,
                    dateExecution: data.dateExecution,
                    dateCloture: data.dateCloture,
                    titre: data.titre,
                    details: data.details,
                    idTDLpriorite: data.idTDLpriorite,
                });
            }
            else
            {
                const response = await Axios.post('/todolist/addTDL',{
                    dateExecution: data.dateExecution,
                    titre: data.titre,
                    details: data.details,
                    idTDLpriorite: data.idTDLpriorite,
                    idExecutant: autoAffectWhenCreate ? HabilitationService.habilitations.idPersonne : null,
                });
            }

            setComponentsHaveToReload(true);
            handleCloseEditOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    //Offcanevas d'affectation
    const [showAffectationOffCanevas, setShowAffectationOffCanevas] = useState(false);
    const [personnes, setPersonnes] = useState([]);
    const [tdlAffectations, setTdlAffectations] = useState([]);
    const handleCloseAffectationOffCanevas = () => {
        setShowAffectationOffCanevas(false);
        setTdlAffectations([]);
        setLoading(false);
    }
    const handleShowAffectationOffCanevas = async () => {
        try {
            let prio = await Axios.get('todolist/getPersonsForTDL');
            setPersonnes(prio.data);

            let tdl = await Axios.post('todolist/getOneTDL',{
                idTache: idTache,
            });
            setTdlAffectations(tdl.data[0].idExecutant);

            setShowAffectationOffCanevas(true);
        } catch (error) {
            console.error(error)
        }
    }
    const updateAffectations = async (e) => {
        try {
            e.preventDefault();
            setLoading(true);

            const response = await Axios.post('/todolist/updateTDLAffectation',{
                idTache: idTache,
                idExecutant: tdlAffectations,
            });

            setComponentsHaveToReload(true);
            handleCloseAffectationOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    //Modal de suppression
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const handleCloseDeleteModal = () => {
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = () => {
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/todolist/deleteTDL',{
                idTache: idTache,
            });
            
            setComponentsHaveToReload(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    //Noter résolu
    const noterResolu = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/todolist/completedTDL',{
                idTache: idTache,
            });
            
            setComponentsHaveToReload(true);
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }
    const noterEnCours = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/todolist/unCompletedTDL',{
                idTache: idTache,
            });
            
            setComponentsHaveToReload(true);
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return (<>
        {
            showResolvedButton
            && (
                HabilitationService.habilitations.todolist_modification
                || (
                    idTache == 0
                    && HabilitationService.habilitations.todolist_perso
                )
                || (
                    isOwnTDL
                    && HabilitationService.habilitations.todolist_perso
                )    
            ) ?
            <IconButton
                icon={isCompleted ? 'recycle' : 'check'}
                size = 'sm'
                variant={isCompleted ? 'warning' : 'success'}
                className='me-1'
                onClick={()=>{isCompleted ? noterEnCours(idTache) : noterResolu(idTache)}}
            />
            : null
        }
        
        {
            showAddButton
            && (
                HabilitationService.habilitations.todolist_modification
                || (
                    idTache == 0
                    && HabilitationService.habilitations.todolist_perso
                )
                || (
                    isOwnTDL
                    && HabilitationService.habilitations.todolist_perso
                )    
            ) ?
            <IconButton
                icon='plus'
                size = 'sm'
                variant="outline-success"
                className='me-1'
                onClick={()=>{handleShowEditOffCanevas(0)}}
            >
                Nouvelle Tache
            </IconButton>
            : null
        }

        {
            showEditButton
            && (
                HabilitationService.habilitations.todolist_modification
                || (
                    idTache == 0
                    && HabilitationService.habilitations.todolist_perso
                )
                || (
                    isOwnTDL
                    && HabilitationService.habilitations.todolist_perso
                )    
            ) ?
            <IconButton
                icon='pen'
                size = 'sm'
                variant="outline-warning"
                className='me-1'
                onClick={()=>{handleShowEditOffCanevas(idTache)}}
            />
            : null
        }

        {showAffectationButton && HabilitationService.habilitations.todolist_modification ? 
            <IconButton
                icon='user'
                size = 'sm'
                variant="outline-info"
                className='me-1'
                onClick={handleShowAffectationOffCanevas}
            />
        :null }

        {showDeleteButton && HabilitationService.habilitations.todolist_modification ? 
            <IconButton
                icon='trash'
                size = 'sm'
                variant="outline-danger"
                className='me-1'
                onClick={handleShowDeleteModal}
            />
        :null }

        <Offcanvas show={showEditOffCanevas} onHide={handleCloseEditOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{editOffCanevasIdTache > 0 ? "Modification" : "Ajout"} d'une tache</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Titre de la tache</Form.Label>
                        <Form.Control type="text" id="titre" name="titre" {...register("titre")}/>
                        <small className="text-danger">{errors.titre?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Descriptif</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={5} name={"details"} id={"details"} {...register("details")}/>
                        <small className="text-danger">{errors.details?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Priorité</Form.Label>
                        <Select
                            id="idTDLpriorite"
                            name="idTDLpriorite"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucune priorité selectionnée'
                            options={priorites}
                            value={priorites.find(c => c.value === watch("idTDLpriorite"))}
                            onChange={val => val != null ? setValue("idTDLpriorite", val.value) : setValue("idTDLpriorite", null)}
                        />
                        <small className="text-danger">{errors.idTDLpriorite?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>A faire avant le</Form.Label>
                        <DatePicker
                            selected={watch("dateExecution")}
                            onChange={(date)=>setValue("dateExecution", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date et heure"
                            timeIntervals={15}
                            dateFormat="dd/MM/yyyy HH:mm"
                            showTimeSelect
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateExecution?.message}</small>
                    </Form.Group>
                    
                    {editOffCanevasIdTache > 0 ?
                        <Form.Group className="mb-3">
                            <Form.Label>Close le</Form.Label>
                            <DatePicker
                                selected={watch("dateCloture")}
                                onChange={(date)=>setValue("dateCloture", date)}
                                formatWeekDay={day => day.slice(0, 3)}
                                className='form-control'
                                placeholderText="Choisir une date et heure"
                                timeIntervals={15}
                                dateFormat="dd/MM/yyyy HH:mm"
                                showTimeSelect
                                fixedHeight
                                locale="fr"
                            />
                            <small className="text-danger">{errors.dateCloture?.message}</small>
                        </Form.Group>
                    :null}

                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Offcanvas show={showAffectationOffCanevas} onHide={handleCloseAffectationOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Mise à jour d'affectations</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={updateAffectations}>
                    <Form.Group className="mb-3">
                        <Form.Label>Affectations</Form.Label>
                        <Select
                            id="idExecutant"
                            name="idExecutant"
                            size="sm"
                            className='mb-2'
                            closeMenuOnSelect={false}
                            placeholder='Affectez cette tache à une personne'
                            options={personnes}
                            isMulti
                            classNamePrefix="react-select"
                            value={tdlAffectations}
                            onChange={selected => setTdlAffectations(selected)}
                            isDisabled={isLoading}
                        />
                        <small className="text-danger">{errors.idExecutant?.message}</small>
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
                Attention, vous allez supprimer une tache (id: {idTache}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

    </>);
};

ToDoListForm.propTypes = {};

export default ToDoListForm;
