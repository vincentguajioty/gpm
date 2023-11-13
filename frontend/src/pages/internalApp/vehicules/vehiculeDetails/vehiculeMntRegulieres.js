import React, {useEffect, useState} from 'react';
import { Row, Col, Card, Form, Table, Modal, Button } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

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

const VehiculeMaintenancesRegulieres = ({idVehicule, maintenancesRegulieres, maintenancesRegulieresAlertes, setPageNeedsRefresh}) => {
    const nl2br = require('react-nl2br');
    const colonnes = [
        {accessor: 'dateHealth'      , Header: 'Date'},
        {accessor: 'interventions'   , Header: 'Interventions'},
        {accessor: 'identifiant'     , Header: 'Intervenant'},
        {accessor: 'actions'         , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of maintenancesRegulieres)
        {
            tempTable.push({
                dateHealth: moment(item.dateHealth).format('DD/MM/YYYY'),
                interventions: <>
                    <Table responsive size='sm'>
                        <thead>
                            <tr>
                                <th>Actes</th>
                                <th>Remarques</th>
                            </tr>
                        </thead>
                        <tbody>
                            {item.checks.map((verif, i)=>{return(
                                <tr>
                                    <td>{verif.libelleHealthType}</td>
                                    <td>{nl2br(verif.remarquesCheck)}</td>
                                </tr>
                            )})}
                        </tbody>
                    </Table>
                </>,
                identifiant: item.identifiant,
                actions:<>
                    {HabilitationService.habilitations['vehicules_modification'] ? 
                        <IconButton
                            icon='pen'
                            size = 'sm'
                            variant="outline-warning"
                            className="me-1"
                            onClick={()=>{handleShowEditModal(item.idVehiculeHealth)}}
                        />
                    : null}

                    {HabilitationService.habilitations['vehicules_modification'] ? 
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{handleShowDeleteModal(item.idVehiculeHealth)}}
                        />
                    : null}
                </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [maintenancesRegulieres])

    //formulaire d'ajout
    const [showEditModal, setShowEditModal] = useState(false);
    const [editModalIdVehiculeHealth, setEditModalIdVehiculeHealth] = useState();
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
        setEditModalIdVehiculeHealth();
        setChecks([]);
        setRemainingChecks([]);
        reset();
        setLoading(false);
    }
    const handleShowEditModal = async (id) => {
        setEditModalIdVehiculeHealth(id);

        if(id > 0)
        {
            let oneItemFromArray = maintenancesRegulieres.filter(ligne => ligne.idVehiculeHealth == id)[0];
            setValue("dateHealth", new Date(oneItemFromArray.dateHealth));
            setValue("idPersonne", oneItemFromArray.idPersonne);
            setValue("releveKilometrique", oneItemFromArray.releveKilometrique);
            setChecks(oneItemFromArray.checks);
            setRemainingChecks(oneItemFromArray.remainingChecks);

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

            if(editModalIdVehiculeHealth > 0)    
            {
                const response = await Axios.post('/vehicules/updateMaintenanceReguliere',{
                    idVehicule: idVehicule,
                    idVehiculeHealth: editModalIdVehiculeHealth,
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

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdVehiculeHealth, setDeleteModalIdVehiculeHealth] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdVehiculeHealth();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVehiculeHealth(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/deleteMaintenanceReguliere',{
                idVehiculeHealth: deleteModalIdVehiculeHealth,
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
            {maintenancesRegulieresAlertes.map((alerte, i)=>{return(
                <SoftBadge
                    className="fs--1 me-1 mb-1"
                    bg={alerte.isInAlert ? 'danger' : 'success'}
                >{alerte.libelleHealthType}</SoftBadge>
            )})}
        </center>

        <Modal show={showEditModal} onHide={handleCloseEditModal} fullscreen={true} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>{editModalIdVehiculeHealth > 0 ? "Modification" : "Ajout"} d'une maintenance ponctuelle</Modal.Title>
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
                                <Form.Select size="sm" name="idPersonne" id="idPersonne" {...register("idPersonne")}>
                                    <option key="0" value="">--- Renseigner une personne ---</option>
                                    {personnes.map((item, i) => {
                                        return (<option key={item.value} value={item.value}>{item.label}</option>);
                                    })}
                                </Form.Select>
                                <small className="text-danger">{errors.idPersonne?.message}</small>
                            </Form.Group>
                        </Col>
                        <Col md={3} className='mb-2 border-end'>
                            <h6>Ajouter des actes techniques</h6>
                            {remainingChecks.map((remaining, i)=>{
                                let idHealthType = remaining.idHealthType || remaining.value;
                                let label = remaining.label || remaining.libelleHealthType;
                                let variant = 'primary';

                                if(editModalIdVehiculeHealth == 0)
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

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>Suppression</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer une maintenance régulière et toutes ses taches (id: {deleteModalIdVehiculeHealth}). Etes-vous certain de vouloir continuer ?
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
                            onClick={()=>{handleShowEditModal(0)}}
                        >Nouvelle maintenance régulière</IconButton>
                    </>
                : null
            }
        />
    </>);
};

VehiculeMaintenancesRegulieres.propTypes = {};

export default VehiculeMaintenancesRegulieres;
