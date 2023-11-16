import React, {useEffect, useState} from 'react';
import { Row, Col, Form, Table, Modal, Button } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { maintenanceReguliere } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const VehiculeMaintenancesRegulieresForm = ({
    idVehicule,
    maintenance,
    idVehiculeHealth=0,
    maintenancesRegulieresAlertes=[],
    setPageNeedsRefresh,
    buttonSize
}) => {
    const [showEditModal, setShowEditModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(maintenanceReguliere),
    });
    const [personnes, setPersonnes] = useState([]);
    const [checks, setChecks] = useState([]);
    const [remainingChecks, setRemainingChecks] = useState([]);
    const [typesMaintenance, setTypesMaintenance] = useState([]);
    const handleCloseEditModal = () => {
        setShowEditModal(false);
        setChecks([]);
        setRemainingChecks([]);
        reset();
        setLoading(false);
    }
    const handleShowEditModal = async () => {

        if(idVehiculeHealth > 0)
        {
            setValue("dateHealth", new Date(maintenance.dateHealth));
            setValue("idPersonne", maintenance.idPersonne);
            setValue("releveKilometrique", maintenance.releveKilometrique);
            setChecks(maintenance.checks);
            setRemainingChecks(maintenance.remainingChecks);

            let getData = await Axios.get('/select/getTypesMaintenancesRegulieresVehicules');
            setTypesMaintenance(getData.data);
        }
        else
        {
            setValue("dateHealth", new Date());
            setValue("idPersonne", HabilitationService.habilitations.idPersonne);
            setChecks([]);

            let getData = await Axios.get('/select/getTypesMaintenancesRegulieresVehicules');
            setTypesMaintenance(getData.data);
            setRemainingChecks(getData.data);
        }

        let getData = await Axios.get('/select/getPersonnes');
        setPersonnes(getData.data);

        setShowEditModal(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(idVehiculeHealth > 0)    
            {
                const response = await Axios.post('/vehicules/updateMaintenanceReguliere',{
                    idVehicule: idVehicule,
                    idVehiculeHealth: idVehiculeHealth,
                    dateHealth: data.dateHealth,
                    idPersonne: data.idPersonne,
                    releveKilometrique: data.releveKilometrique,
                    checks: checks || [],
                });
            }
            else
            {
                const response = await Axios.post('/vehicules/addMaintenanceReguliere',{
                    idVehicule: idVehicule,
                    dateHealth: data.dateHealth,
                    idPersonne: data.idPersonne,
                    releveKilometrique: data.releveKilometrique,
                    checks: checks || [],
                });
            }

            handleCloseEditModal();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }
    const updateIdHealthType = (index, idHealthType) => {
        const newState = checks.map((check, i) => {
            if(i === index)
            {
                return {...check, idHealthType: idHealthType}
            }
            else
            {
                return check;
            }
        })
        setChecks(newState);
    }
    const updateRemarquesCheck = (index, remarquesCheck) => {
        const newState = checks.map((check, i) => {
            if(i === index)
            {
                return {...check, remarquesCheck: remarquesCheck}
            }
            else
            {
                return check;
            }
        })
        setChecks(newState);
    }
    const addFromButton = (idHealthType) => {
        let tempChecks = [];
        for(const check of checks)
        {
            tempChecks.push(check);
        }
        tempChecks.push({idHealthType: idHealthType})
        setChecks(tempChecks);

        let tempRemaings = [];
        for(const remaining of remainingChecks)
        {
            let id = remaining.idHealthType || remaining.value;
            if(id != idHealthType)
            {
                tempRemaings.push(remaining)
            }
        }
        setRemainingChecks(tempRemaings);
    }

      
    return (<>
        {idVehiculeHealth > 0 ?
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
                >Nouvelle maintenance</IconButton>
        }

        <Modal show={showEditModal} onHide={handleCloseEditModal} fullscreen={true} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>{idVehiculeHealth > 0 ? "Modification" : "Ajout"} d'une maintenance ponctuelle</Modal.Title>
            </Modal.Header>
            <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                <Modal.Body>
                    <Row>
                        <Col md={3} className='mb-2 border-end'>
                            <h6>Informations génériques</h6>
                            <Form.Group className="mb-3">
                                <Form.Label>Date</Form.Label>
                                <DatePicker
                                    selected={watch("dateHealth")}
                                    onChange={(date)=>setValue("dateHealth", date)}
                                    formatWeekDay={day => day.slice(0, 3)}
                                    className='form-control'
                                    placeholderText="Choisir une date"
                                    dateFormat="dd/MM/yyyy"
                                    fixedHeight
                                    locale="fr"
                                />
                                <small className="text-danger">{errors.dateHealth?.message}</small>
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
                                    placeholder='Aucune personne selectionnée'
                                    options={personnes}
                                    value={personnes.find(c => c.value === watch("idPersonne"))}
                                    onChange={val => val != null ? setValue("idPersonne", val.value) : setValue("idPersonne", null)}
                                />
                                <small className="text-danger">{errors.idPersonne?.message}</small>
                            </Form.Group>
                        </Col>
                        <Col md={3} className='mb-2 border-end'>
                            <h6>Ajouter des actes techniques</h6>
                            {remainingChecks.map((remaining, i)=>{
                                let idHealthType = remaining.idHealthType || remaining.value;
                                let label = remaining.label || remaining.libelleHealthType;
                                let variant = 'primary';

                                if(idVehiculeHealth == 0)
                                {
                                    let alerte = maintenancesRegulieresAlertes.filter(alerte => alerte.idHealthType == idHealthType);
                                    if(alerte.length > 0)
                                    {
                                        variant = alerte[0].isInAlert ? 'danger' : 'success'
                                    }
                                }
                                return(<>
                                    <IconButton
                                        size='sm'    
                                        className='me-1 mb-1'
                                        variant={variant}
                                        icon="plus"
                                        onClick={()=>{addFromButton(idHealthType)}}
                                    >{label}</IconButton><br/></>
                                )
                            })}
                        </Col>
                        <Col md={6} className='mb-2'>
                            <h6>Actes enregistrés</h6>
                            <Table>
                                <thead>
                                    <tr>
                                        <th>Acte</th>
                                        <th>Remarques</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {checks.map((check, i)=>{return(
                                        <tr>
                                            <td>
                                                <Form.Select size="sm" name="idHealthType" id="idHealthType" value={check.idHealthType} onChange={(e) => {updateIdHealthType(i, e.target.value)}}>
                                                    <option key="0" value="">--- Acte annulé ---</option>
                                                    {typesMaintenance.map((item, i) => {
                                                        return (<option key={item.value} value={item.value}>{item.label}</option>);
                                                    })}
                                                </Form.Select>
                                            </td>
                                            <td>
                                                <Form.Control size="sm" as="textarea" rows={3} name="remarquesCheck" id="remarquesCheck" value={check.remarquesCheck} onChange={(e) => {updateRemarquesCheck(i, e.target.value)}}/>
                                            </td>
                                        </tr>
                                    )})}
                                </tbody>
                            </Table>
                        </Col>
                    </Row>
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

VehiculeMaintenancesRegulieresForm.propTypes = {};

export default VehiculeMaintenancesRegulieresForm;
