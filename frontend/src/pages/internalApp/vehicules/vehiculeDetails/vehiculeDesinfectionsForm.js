import React, {useState} from 'react';
import { Form, Modal, Button } from 'react-bootstrap';
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

const VehiculeDesinfectionsForm = ({idVehicule, desinfection, idVehiculesDesinfection = 0, setPageNeedsRefresh, buttonSize}) => {
    
    //formulaire d'ajout
    const [showEditModal, setShowEditModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(desinfectionForm),
    });
    const [personnes, setPersonnes] = useState([]);
    const [typesDesinfections, setTypesDesinfections] = useState([]);
    const handleCloseEditModal = () => {
        setShowEditModal(false);
        reset();
        setLoading(false);
    }
    const handleShowEditModal = async () => {

        if(idVehiculesDesinfection > 0)
        {
            setValue("dateDesinfection", new Date(desinfection.dateDesinfection));
            setValue("idExecutant", desinfection.idExecutant);
            setValue("remarquesDesinfection", desinfection.remarquesDesinfection);
            setValue("idVehiculesDesinfectionsType", desinfection.idVehiculesDesinfectionsType);
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

            if(idVehiculesDesinfection > 0)    
            {
                const response = await Axios.post('/vehicules/updateDesinfection',{
                    idVehicule: idVehicule,
                    idVehiculesDesinfection: idVehiculesDesinfection,
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

    return (<>
        {idVehiculesDesinfection > 0 ?
            <IconButton
                icon='pen'
                size = 'sm'
                variant="outline-warning"
                className="me-1"
                onClick={handleShowEditModal}
            />
        :
            buttonSize == 'small' ?
                <IconButton
                    icon='plus'
                    size = 'sm'
                    variant="outline-success"
                    className="me-1"
                    onClick={handleShowEditModal}
                />
            :
                <IconButton
                    icon='plus'
                    size = 'sm'
                    variant="outline-success"
                    className="me-1"
                    onClick={handleShowEditModal}
                >Nouvelle désinfection</IconButton>
        }
        
        
        <Modal show={showEditModal} onHide={handleCloseEditModal} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>{idVehiculesDesinfection > 0 ? "Modification" : "Ajout"} d'une désinfection</Modal.Title>
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
    </>);
};

VehiculeDesinfectionsForm.propTypes = {};

export default VehiculeDesinfectionsForm;
