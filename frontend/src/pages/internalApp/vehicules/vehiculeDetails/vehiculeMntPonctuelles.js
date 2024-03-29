import React, {useState} from 'react';
import { Form, Offcanvas, Modal, Button } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';
import { vehiculesMaintenanceDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { maintenancePonctuelle } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const VehiculeMaintenancesPonctuelles = ({idVehicule, maintenancesPonctuelles, setPageNeedsRefresh}) => {
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'dateMaintenance',
            Header: 'Date',
            Cell: ({ value, row }) => {
				return(moment(value).format('DD/MM/YYYY'));
			},
        },
        {
            accessor: 'libelleTypeMaintenance',
            Header: 'Intervention',
        },
        {
            accessor: 'identifiant',
            Header: 'Intervenant',
        },
        {
            accessor: 'detailsMaintenance',
            Header: 'Détails',
            Cell: ({ value, row }) => {
				return(nl2br(value));
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {HabilitationService.habilitations['vehicules_modification'] ? 
                            <IconButton
                                icon='pen'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowOffCanevas(row.original.idMaintenance)}}
                            />
                        : null}

                        {HabilitationService.habilitations['vehicules_modification'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idMaintenance)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdMaintenance, setOffCanevasIdMaintenance] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(maintenancePonctuelle),
    });
    const [personnes, setPersonnes] = useState([]);
    const [typesMaintenance, setTypesMaintenance] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdMaintenance();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdMaintenance(id);

        if(id > 0)
        {
            let oneItemFromArray = maintenancesPonctuelles.filter(ligne => ligne.idMaintenance == id)[0];
            setValue("dateMaintenance", oneItemFromArray.dateMaintenance ? new Date(oneItemFromArray.dateMaintenance) : new Date());
            setValue("idTypeMaintenance", oneItemFromArray.idTypeMaintenance);
            setValue("idExecutant", oneItemFromArray.idExecutant);
            setValue("detailsMaintenance", oneItemFromArray.detailsMaintenance);
            setValue("releveKilometrique", oneItemFromArray.releveKilometrique);
        }
        else
        {
            setValue("dateMaintenance", new Date());
            setValue("idExecutant", HabilitationService.habilitations.idPersonne);
        }

        let getData = await Axios.get('/select/getActivePersonnes');
        setPersonnes(getData.data);
        getData = await Axios.get('/select/getTypesMaintenancesPonctuellesVehicules');
        setTypesMaintenance(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdMaintenance > 0)    
            {
                const response = await Axios.post('/vehicules/updateMaintenancePonctuelle',{
                    idMaintenance: offCanevasIdMaintenance,
                    dateMaintenance: data.dateMaintenance,
                    idTypeMaintenance: data.idTypeMaintenance,
                    idExecutant: data.idExecutant,
                    detailsMaintenance: data.detailsMaintenance,
                    releveKilometrique: data.releveKilometrique,
                });
            }
            else
            {
                const response = await Axios.post('/vehicules/addMaintenancePonctuelle',{
                    dateMaintenance: data.dateMaintenance,
                    idTypeMaintenance: data.idTypeMaintenance,
                    idExecutant: data.idExecutant,
                    detailsMaintenance: data.detailsMaintenance,
                    releveKilometrique: data.releveKilometrique,
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
    const [deleteModalIdMaintenance, setDeleteModalIdMaintenance] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdMaintenance();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdMaintenance(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/deleteMaintenancePonctuelle',{
                idMaintenance: deleteModalIdMaintenance,
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
                <Offcanvas.Title>{offCanevasIdMaintenance > 0 ? "Modification" : "Ajout"} d'une maintenance ponctuelle</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Date</Form.Label>
                        <DatePicker
                            selected={watch("dateMaintenance")}
                            onChange={(date)=>setValue("dateMaintenance", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateMaintenance?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Relevé kilométrique</Form.Label>
                        <Form.Control size="sm" type="number" min="0" name='releveKilometrique' id='releveKilometrique' {...register('releveKilometrique')}/>
                        <small className="text-danger">{errors.releveKilometrique?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Personne ayant fait le relevé</Form.Label>
                        <Select
                            id="idExecutant"
                            name="idExecutant"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucune prsonne selectionnée'
                            options={personnes}
                            value={personnes.find(c => c.value === watch("idExecutant"))}
                            onChange={val => val != null ? setValue("idExecutant", val.value) : setValue("idExecutant", null)}
                        />
                        <small className="text-danger">{errors.idExecutant?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Maintenance effectuée</Form.Label>
                        <Select
                            id="idTypeMaintenance"
                            name="idTypeMaintenance"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun type selectionné'
                            options={typesMaintenance}
                            value={typesMaintenance.find(c => c.value === watch("idTypeMaintenance"))}
                            onChange={val => val != null ? setValue("idTypeMaintenance", val.value) : setValue("idTypeMaintenance", null)}
                        />
                        <small className="text-danger">{errors.idTypeMaintenance?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Détails</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name="detailsMaintenance" id="detailsMaintenance" {...register("detailsMaintenance")}/>
                        <small className="text-danger">{errors.detailsMaintenance?.message}</small>
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
            <Modal.Body>{vehiculesMaintenanceDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <GPMtable
            columns={colonnes}
            data={maintenancesPonctuelles}
            topButtonShow={true}
            topButton={
                HabilitationService.habilitations['vehicules_modification'] ?
                    <IconButton
                        icon='plus'
                        size = 'sm'
                        variant="outline-success"
                        onClick={()=>{handleShowOffCanevas(0)}}
                    >Nouvelle maintenance ponctuelle</IconButton>
                : null
            }
        />
    </>);
};

VehiculeMaintenancesPonctuelles.propTypes = {};

export default VehiculeMaintenancesPonctuelles;
