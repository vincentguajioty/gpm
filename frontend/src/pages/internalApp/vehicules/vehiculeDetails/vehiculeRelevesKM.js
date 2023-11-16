import React, {useEffect, useState} from 'react';
import { Form, Offcanvas, Button, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { releveKM } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const VehiculeRelevesKM = ({idVehicule, relevesKM, setPageNeedsRefresh}) => {
    const colonnes = [
        {accessor: 'dateReleve'        , Header: 'Date'},
        {accessor: 'releveKilometrique', Header: 'Relevé'},
        {accessor: 'identifiant'       , Header: 'Intervenant'},
        {accessor: 'actions'           , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of relevesKM)
        {
            tempTable.push({
                dateReleve: moment(item.dateReleve).format('DD/MM/YYYY'),
                releveKilometrique: item.releveKilometrique + ' km',
                identifiant: item.identifiant,
                actions:
                    item.idReleve > 0 ? <>
                        {HabilitationService.habilitations['vehicules_modification'] ? 
                            <IconButton
                                icon='pen'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowOffCanevas(item.idReleve)}}
                            />
                        : null}

                        {HabilitationService.habilitations['vehicules_modification'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(item.idReleve)}}
                            />
                        : null}
                    </> : <i>Relevé fait via une maintenance</i>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [relevesKM])

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdReleve, setOffCanevasIdReleve] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(releveKM),
    });
    const [personnes, setPersonnes] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdReleve();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdReleve(id);

        if(id > 0)
        {
            let oneItemFromArray = relevesKM.filter(ligne => ligne.idReleve == id)[0];
            setValue("dateReleve", oneItemFromArray.dateReleve ? new Date(oneItemFromArray.dateReleve) : new Date());
            setValue("releveKilometrique", oneItemFromArray.releveKilometrique);
            setValue("idPersonne", oneItemFromArray.idPersonne);
        }
        else
        {
            setValue("dateReleve", new Date());
            setValue("idPersonne", HabilitationService.habilitations.idPersonne);
        }

        const getData = await Axios.get('/select/getPersonnes');
        setPersonnes(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdReleve > 0)    
            {
                const response = await Axios.post('/vehicules/updateReleveKM',{
                    idReleve: offCanevasIdReleve,
                    dateReleve: data.dateReleve,
                    releveKilometrique: data.releveKilometrique,
                    idPersonne: data.idPersonne,
                });
            }
            else
            {
                const response = await Axios.post('/vehicules/addReleveKM',{
                    dateReleve: data.dateReleve,
                    releveKilometrique: data.releveKilometrique,
                    idPersonne: data.idPersonne,
                    idVehicule: idVehicule,
                });
            }

            handleCloseOffCanevas();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdReleve, setDeleteModalIdReleve] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdReleve();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdReleve(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/deleteReleveKM',{
                idReleve: deleteModalIdReleve,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }
    
    return (<>
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdReleve > 0 ? "Modification" : "Ajout"} d'un relevé kilométrique</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Date</Form.Label>
                        <DatePicker
                            selected={watch("dateReleve")}
                            onChange={(date)=>setValue("dateReleve", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateReleve?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Relevé kilométrique</Form.Label>
                        <Form.Control size="sm" type="number" min="0" name='releveKilometrique' id='releveKilometrique' {...register('releveKilometrique')}/>
                        <small className="text-danger">{errors.releveKilometrique?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Personne ayant fait le relevé</Form.Label>
                        <Select
                            id="idPersonne"
                            name="idPersonne"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun personne selectionnée'
                            options={personnes}
                            value={personnes.find(c => c.value === watch("idPersonne"))}
                            onChange={val => val != null ? setValue("idPersonne", val.value) : setValue("idPersonne", null)}
                        />
                        <small className="text-danger">{errors.idPersonne?.message}</small>
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
                Attention, vous allez supprimer un relevé kilométrique (id: {deleteModalIdReleve}). Etes-vous certain de vouloir continuer ?
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
                    <IconButton
                        icon='plus'
                        size = 'sm'
                        variant="outline-success"
                        onClick={()=>{handleShowOffCanevas(0)}}
                    >Nouveau relevé</IconButton>
                : null
            }
        />
    </>);
};

VehiculeRelevesKM.propTypes = {};

export default VehiculeRelevesKM;
