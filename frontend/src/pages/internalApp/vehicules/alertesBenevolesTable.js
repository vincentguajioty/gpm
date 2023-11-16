import React, {useState, useEffect} from 'react';
import { Dropdown, DropdownButton, Modal, Button, Form, } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import moment from 'moment-timezone';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { alerteVehiculeAffectation } from 'helpers/yupValidationSchema';

const AlertesBenevolesVehiculesTable = ({idVehicule = 0}) => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [alertes, setAlertes] = useState([]);

    const initTable = async () => {
        try {
            const getData = await Axios.post('/vehicules/getVehiculesAlertes',{
                idVehicule: idVehicule,
            });
            setAlertes(getData.data);
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }
    useEffect(()=>{
        initTable();
    },[])

    const colonnes = [
        {accessor: 'nomDeclarant'               , Header: 'Demandeur'},
        {accessor: 'dateCreationAlerte'         , Header: 'Ouverture'},
        {accessor: 'libelleVehicule'            , Header: 'Véhicule'},
        {accessor: 'messageAlerteVehicule'      , Header: 'Message'},
        {accessor: 'libelleVehiculesAlertesEtat', Header: 'Traitement'},
        {accessor: 'idTraitant'                 , Header: 'Affectation'},
        {accessor: 'actions'                    , Header: 'Clore'},
    ];
    const [lignes, setLignes] = useState([]);
    const nl2br = require('react-nl2br');
    const initTableau = () => {
        let tempTable  = [];
        for(const item of alertes)
        {
            tempTable.push({
                nomDeclarant: item.nomDeclarant,
                dateCreationAlerte: moment(item.dateCreationAlerte).format('DD/MM/YYYY HH:mm'),
                libelleVehicule: item.libelleVehicule,
                messageAlerteVehicule: nl2br(item.messageAlerteVehicule),
                libelleVehiculesAlertesEtat: item.idVehiculesAlertesEtat == 2 || item.idVehiculesAlertesEtat == 3 ?
                    <DropdownButton
                        variant={item.couleurVehiuclesAlertesEtat}
                        title={item.libelleVehiculesAlertesEtat}
                        size='sm'
                        disabled={item.idTraitant != HabilitationService.habilitations.idPersonne}
                    >
                        <Dropdown.Item onClick={()=>{miseEnAttente(item.idAlerte)}} active={item.idVehiculesAlertesEtat == 3}>Mise en attente</Dropdown.Item>
                        <Dropdown.Item onClick={()=>{repriseTravaux(item.idAlerte)}} active={item.idVehiculesAlertesEtat == 2}>Reprise des travaux</Dropdown.Item>
                    </DropdownButton>
                :
                    <SoftBadge bg={item.couleurVehiuclesAlertesEtat}>{item.libelleVehiculesAlertesEtat}</SoftBadge>,
                idTraitant: item.idTraitant != null ? <SoftBadge>{item.prenomPersonne} {item.nomPersonne}</SoftBadge> : item.idVehiculesAlertesEtat == 1 ? <>
                    <IconButton
                        size='sm'
                        className='me-1 mb-1'
                        icon='user'
                        variant='outline-primary'
                        onClick={()=>{autoAffect(item.idAlerte)}}
                        disabled={!HabilitationService.habilitations.alertesBenevolesVehicules_affectation}
                    >Me l'affecter</IconButton>
                    <br/>
                    <IconButton
                        size='sm'
                        className='me-1 mb-1'
                        icon='users'
                        variant='outline-primary'
                        disabled={!HabilitationService.habilitations.alertesBenevolesVehicules_affectationTier}
                        onClick={()=>{handleShowAffectModal(item.idAlerte)}}
                    >Affecter à un tier</IconButton>
                </> : null,
                actions:
                    item.idVehiculesAlertesEtat == 1 ?
                        <IconButton
                            size='sm'
                            className='me-1 mb-1'
                            icon='user-shield'
                            variant='outline-warning'
                            disabled={!HabilitationService.habilitations.alertesBenevolesVehicules_affectationTier && !HabilitationService.habilitations.alertesBenevolesVehicules_affectation}
                            onClick={()=>{doublonAlerte(item.idAlerte)}}
                        >Signaler comme doublon</IconButton>
                    : item.idVehiculesAlertesEtat == 2 || item.idVehiculesAlertesEtat == 3 ?
                        <IconButton
                            size='sm'
                            className='me-1 mb-1'
                            icon='check'
                            variant='outline-success'
                            disabled={item.idTraitant != HabilitationService.habilitations.idPersonne}
                            onClick={()=>{cloreAlerte(item.idAlerte)}}
                        >Clore</IconButton>
                    : null,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [alertes])

    //auto-affecation
    const autoAffect = async (idAlerte) => {
        try {
            const getData = await Axios.post('/vehicules/autoAffect',{
                idAlerte: idAlerte,
            });
            initTable();
        } catch (error) {
            console.log(error)
        }
    }
    const affectTier = async (data) => {
        try {
            const getData = await Axios.post('/vehicules/affectationTier',{
                idTraitant: data.idTraitant,
                idAlerte: affectModalIdAlerte,
            });
            initTable();
            handleCloseAffectModal();
        } catch (error) {
            console.log(error)
        }
    }
    //Changments de statut
    const cloreAlerte = async (idAlerte) => {
        try {
            const getData = await Axios.post('/vehicules/udpateStatut',{
                idAlerte: idAlerte,
                idVehiculesAlertesEtat: 4
            });
            initTable();
        } catch (error) {
            console.log(error)
        }
    }
    const miseEnAttente = async (idAlerte) => {
        try {
            const getData = await Axios.post('/vehicules/udpateStatut',{
                idAlerte: idAlerte,
                idVehiculesAlertesEtat: 3
            });
            initTable();
        } catch (error) {
            console.log(error)
        }
    }
    const repriseTravaux = async (idAlerte) => {
        try {
            const getData = await Axios.post('/vehicules/udpateStatut',{
                idAlerte: idAlerte,
                idVehiculesAlertesEtat: 2
            });
            initTable();
        } catch (error) {
            console.log(error)
        }
    }
    const doublonAlerte = async (idAlerte) => {
        try {
            const getData = await Axios.post('/vehicules/udpateStatut',{
                idAlerte: idAlerte,
                idVehiculesAlertesEtat: 5
            });
            initTable();
        } catch (error) {
            console.log(error)
        }
    }

    /* DELETE */
    const [showAffectModal, setShowAffectModal] = useState(false);
    const [affectModalIdAlerte, setAffectModalIdAlerte] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(alerteVehiculeAffectation),
    });
    const handleCloseAffectModal = () => {
        setAffectModalIdAlerte();
        setShowAffectModal(false);
        setLoading(false);
    };
    const [personnes, setPersonnes] = useState([]);
    const handleShowAffectModal = async (id) => {
        try {
            let getData = await Axios.get('/select/getActivePersonnes');
            setPersonnes(getData.data);

            setAffectModalIdAlerte(id);
            setShowAffectModal(true);
        } catch (error) {
            console.log(error)
        }
    };

    return (<>
        <Modal show={showAffectModal} onHide={handleCloseAffectModal} backdrop="static" keyboard={false}>
            <Form onSubmit={handleSubmit(affectTier)}>
                <Modal.Header closeButton >
                    <Modal.Title>Affecter l'alerte</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form.Group className="mb-3">
                        <Form.Label>Affecter le traitement de l'alerte à:</Form.Label>
                        <Select
                            id="idTraitant"
                            name="idTraitant"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucune personne selectionnée'
                            options={personnes}
                            value={personnes.find(c => c.value === watch("idTraitant"))}
                            onChange={val => val != null ? setValue("idTraitant", val.value) : setValue("idTraitant", null)}
                        />
                        <small className="text-danger">{errors.idTraitant?.message}</small>
                    </Form.Group>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseAffectModal}>
                        Annuler
                    </Button>
                    <Button variant='primary' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Affecter'}</Button>
                </Modal.Footer>
            </Form>
        </Modal>
    
        {readyToDisplay ?
            <GPMtable
                columns={colonnes}
                data={lignes}
            />
        : <LoaderInfiniteLoop/>}
    </>);
};

AlertesBenevolesVehiculesTable.propTypes = {};

export default AlertesBenevolesVehiculesTable;
