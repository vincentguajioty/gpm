import React, {useEffect, useState} from 'react';
import { Form, Modal, Button } from 'react-bootstrap';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { desinfectionForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

import VehiculeDesinfectionsAlertes from './vehiculeDesinfectionsAlertes';

const VehiculeDesinfections = ({idVehicule, desinfections, desinfectionsAlertes, setPageNeedsRefresh}) => {
    const nl2br = require('react-nl2br');
    const colonnes = [
        {accessor: 'dateDesinfection', Header: 'Date'},
        {accessor: 'desinfections'   , Header: 'Désinfection'},
        {accessor: 'identifiant'     , Header: 'Intervenant'},
        {accessor: 'actions'         , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of desinfections)
        {
            tempTable.push({
                dateDesinfection: moment(item.dateDesinfection).format('DD/MM/YYYY'),
                desinfections: item.libelleVehiculesDesinfectionsType,
                identifiant: item.identifiant,
                actions:<>
                    {HabilitationService.habilitations['vehicules_modification'] ? 
                        <IconButton
                            icon='pen'
                            size = 'sm'
                            variant="outline-warning"
                            className="me-1"
                            onClick={()=>{handleShowEditModal(item.idVehiculesDesinfection)}}
                        />
                    : null}

                    {HabilitationService.habilitations['vehicules_modification'] ? 
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{handleShowDeleteModal(item.idVehiculesDesinfection)}}
                        />
                    : null}
                </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [desinfections])

    //formulaire d'ajout
    const [showEditModal, setShowEditModal] = useState(false);
    const [editModalIdVehiculesDesinfection, setEditModalIdVehiculesDesinfection] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(desinfectionForm),
    });
    const [personnes, setPersonnes] = useState([]);
    const [typesDesinfections, setTypesDesinfections] = useState([]);
    const handleCloseEditModal = () => {
        setShowEditModal(false);
        setEditModalIdVehiculesDesinfection();
        reset();
        setLoading(false);
    }
    const handleShowEditModal = async (id) => {
        setEditModalIdVehiculesDesinfection(id);

        if(id > 0)
        {
            let oneItemFromArray = desinfections.filter(ligne => ligne.idVehiculesDesinfection == id)[0];
            setValue("dateDesinfection", new Date(oneItemFromArray.dateDesinfection));
            setValue("idExecutant", oneItemFromArray.idExecutant);
            setValue("remarquesDesinfection", oneItemFromArray.remarquesDesinfection);
            setValue("idVehiculesDesinfectionsType", oneItemFromArray.idVehiculesDesinfectionsType);
        }
        else
        {
            setValue("dateDesinfection", new Date());
            setValue("idExecutant", HabilitationService.habilitations.idPersonne);
        }

        let getData = await Axios.get('/select/getPersonnes');
        setPersonnes(getData.data);
        getData = await Axios.get('/select/getTypesDesinfections');
        setTypesDesinfections(getData.data);

        setShowEditModal(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(editModalIdVehiculesDesinfection > 0)    
            {
                const response = await Axios.post('/vehicules/updateDesinfection',{
                    idVehicule: idVehicule,
                    idVehiculesDesinfection: editModalIdVehiculesDesinfection,
                    dateDesinfection: data.dateDesinfection,
                    idExecutant: data.idExecutant,
                    remarquesDesinfection: data.remarquesDesinfection,
                    idVehiculesDesinfectionsType: data.idVehiculesDesinfectionsType,
                });
            }
            else
            {
                const response = await Axios.post('/vehicules/addDesinfection',{
                    idVehicule: idVehicule,
                    dateDesinfection: data.dateDesinfection,
                    idExecutant: data.idExecutant,
                    remarquesDesinfection: data.remarquesDesinfection,
                    idVehiculesDesinfectionsType: data.idVehiculesDesinfectionsType,
                });
            }

            handleCloseEditModal();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdVehiculesDesinfection, setDeleteModalIdVehiculesDesinfection] = useState();

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
        
        <Modal show={showEditModal} onHide={handleCloseEditModal} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>{editModalIdVehiculesDesinfection > 0 ? "Modification" : "Ajout"} d'une désinfection</Modal.Title>
            </Modal.Header>
            <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                <Modal.Body>
                    <Form.Group className="mb-3">
                        <Form.Label>Date</Form.Label>
                        <DatePicker
                            selected={watch("dateDesinfection")}
                            onChange={(date)=>setValue("dateDesinfection", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateDesinfection?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Personne ayant fait le relevé</Form.Label>
                        <Form.Select size="sm" name="idExecutant" id="idExecutant" {...register("idExecutant")}>
                            <option key="0" value="">--- Renseigner une personne ---</option>
                            {personnes.map((item, i) => {
                                return (<option key={item.value} value={item.value}>{item.label}</option>);
                            })}
                        </Form.Select>
                        <small className="text-danger">{errors.idExecutant?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Type de désinfection</Form.Label>
                        <Form.Select size="sm" name="idVehiculesDesinfectionsType" id="idVehiculesDesinfectionsType" {...register("idVehiculesDesinfectionsType")}>
                            <option key="0" value="">--- Renseigner un type ---</option>
                            {typesDesinfections.map((item, i) => {
                                return (<option key={item.value} value={item.value}>{item.label}</option>);
                            })}
                        </Form.Select>
                        <small className="text-danger">{errors.idVehiculesDesinfectionsType?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name='remarquesDesinfection' id='remarquesDesinfection' {...register('remarquesDesinfection')}/>
                        <small className="text-danger">{errors.remarquesDesinfection?.message}</small>
                    </Form.Group>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseEditModal}>
                        Annuler
                    </Button>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Modal.Footer>
            </Form>
        </Modal>

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
            data={lignes}
            topButtonShow={true}
            topButton={
                HabilitationService.habilitations['vehicules_modification'] ?
                    <>
                        <IconButton
                            icon='plus'
                            size = 'sm'
                            variant="outline-success"
                            className="me-1"
                            onClick={()=>{handleShowEditModal(0)}}
                        >Nouvelle désinfection</IconButton>

                        <VehiculeDesinfectionsAlertes
                            idVehicule={idVehicule}
                            desinfectionsAlertes={desinfectionsAlertes}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                    </>
                : null
            }
        />
    </>);
};

VehiculeDesinfections.propTypes = {};

export default VehiculeDesinfections;
