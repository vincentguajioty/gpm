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

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { cautionsForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const Cautions = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [cautions, setCautions] = useState([]);
    const [cautionsRow, setCautionsRow] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/tenues/getCautions');
            setCautions(getData.data);
            const getRowData = await Axios.get('/tenues/getCautionsRow');
            setCautionsRow(getRowData.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {accessor: 'personne', Header: 'Personne'},
        {accessor: 'cautions', Header: 'Cautions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of cautions)
        {
            tempTable.push({
                personne:<>{item.nomPrenom}{item.type=='externe' ? <SoftBadge bg='secondary' className='ms-1'>Externe</SoftBadge>:null}</>,
                cautions:<Table className="fs--1 mt-3" size='sm' responsive>
                    <thead>
                        <tr>
                            <th>Emission</th>
                            <th>Expiration</th>
                            <th>Montant</th>
                            <th>Détails</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {item.cautions.map((caution, i)=>{return(
                            <tr>
                                <td>{moment(caution.dateEmissionCaution).format('DD/MM/YYYY')}</td>
                                <td>{caution.dateExpirationCaution != null ? 
                                        new Date(caution.dateExpirationCaution) < new Date() ?
                                            <SoftBadge bg='danger'>{moment(caution.dateExpirationCaution).format('DD/MM/YYYY')}</SoftBadge>
                                        :
                                            <SoftBadge bg='success'>{moment(caution.dateExpirationCaution).format('DD/MM/YYYY')}</SoftBadge>
                                    : null}
                                </td>
                                <td>{caution.montantCaution} €</td>
                                <td>{caution.detailsMoyenPaiement}</td>
                                <td>
                                    {HabilitationService.habilitations['cautions_modification'] ? 
                                        <IconButton
                                            icon='pen'
                                            size = 'sm'
                                            variant="outline-warning"
                                            className="me-1"
                                            onClick={()=>{handleShowOffCanevas(caution.idCaution)}}
                                        />
                                    : null}
                                    {HabilitationService.habilitations['cautions_suppression'] ? 
                                        <IconButton
                                            icon='trash'
                                            size = 'sm'
                                            variant="outline-danger"
                                            className="me-1"
                                            onClick={()=>{handleShowDeleteModal(caution.idCaution)}}
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
    }, [cautions, cautionsRow])

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdCaution, setOffCanevasIdCaution] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(cautionsForm),
    });
    const [personnesInternes, setPersonnesInternes] = useState([]);
    const [personnesExternes, setPersonnesExternes] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdCaution();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdCaution(id);

        if(id > 0)
        {
            let oneItemFromArray = cautionsRow.filter(ligne => ligne.idCaution == id)[0];
            setValue("idPersonne", oneItemFromArray.idPersonne > 0 ? oneItemFromArray.idPersonne : 0);
            setValue("personneNonGPM", oneItemFromArray.personneNonGPM);
            setValue("montantCaution", oneItemFromArray.montantCaution);
            setValue("dateEmissionCaution", oneItemFromArray.dateEmissionCaution != null ? new Date(oneItemFromArray.dateEmissionCaution) : null);
            setValue("dateExpirationCaution", oneItemFromArray.dateExpirationCaution != null ? new Date(oneItemFromArray.dateExpirationCaution) : null);
            setValue("detailsMoyenPaiement", oneItemFromArray.detailsMoyenPaiement);
        }
        else
        {
            setValue("dateEmissionCaution", new Date());
            setValue("idPersonne", 0);
        }

        let getData = await Axios.get('/tenues/getPersonnesSuggested');
        setPersonnesExternes(getData.data);

        getData = await Axios.get('/settingsUtilisateurs/getAllUsers');
        setPersonnesInternes(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdCaution > 0)    
            {
                const response = await Axios.post('/tenues/updateCautions',{
                    idCaution: offCanevasIdCaution,
                    idPersonne: data.idPersonne > 0 ? data.idPersonne : null,
                    personneNonGPM: data.personneNonGPM,
                    montantCaution: data.montantCaution,
                    dateEmissionCaution: data.dateEmissionCaution,
                    dateExpirationCaution: data.dateExpirationCaution,
                    detailsMoyenPaiement: data.detailsMoyenPaiement,
                });
            }
            else
            {
                const response = await Axios.post('/tenues/addCautions',{
                    idPersonne: data.idPersonne > 0 ? data.idPersonne : null,
                    personneNonGPM: data.personneNonGPM,
                    montantCaution: data.montantCaution,
                    dateEmissionCaution: data.dateEmissionCaution,
                    dateExpirationCaution: data.dateExpirationCaution,
                    detailsMoyenPaiement: data.detailsMoyenPaiement,
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
    const [deleteModalIdCaution, setDeleteModalIdCaution] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdCaution();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdCaution(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteCautions',{
                idCaution: deleteModalIdCaution,
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
            title="Cautions"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdCaution > 0 ? "Modification" : "Ajout"} d'une caution financière</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Emise par</Form.Label>
                        <Form.Select size="sm" name="idPersonne" id="idPersonne" {...register("idPersonne")}>
                            <option key="0" value="0">--- Un externe ---</option>
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
                        <Form.Label>Montant (€)</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step='0.01' name='montantCaution' id='montantCaution' {...register('montantCaution')}/>
                        <small className="text-danger">{errors.montantCaution?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date d'emission</Form.Label>
                        <DatePicker
                            selected={watch("dateEmissionCaution")}
                            onChange={(date)=>setValue("dateEmissionCaution", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateEmissionCaution?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date d'expiration</Form.Label>
                        <DatePicker
                            selected={watch("dateExpirationCaution")}
                            onChange={(date)=>setValue("dateExpirationCaution", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateExpirationCaution?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Détails</Form.Label>
                        <Form.Control size="sm" type="text" name='detailsMoyenPaiement' id='detailsMoyenPaiement' {...register('detailsMoyenPaiement')}/>
                        <small className="text-danger">{errors.detailsMoyenPaiement?.message}</small>
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
                Attention, vous allez supprimer cette affectation (id: {deleteModalIdCaution}). Etes-vous certain de vouloir continuer ?
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
                                >Nouvelle caution</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Cautions.propTypes = {};

export default Cautions;
