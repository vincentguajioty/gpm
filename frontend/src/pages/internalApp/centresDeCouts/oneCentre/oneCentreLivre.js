import React, {useState, useEffect} from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Tabs, Tab, Modal, Button, Offcanvas, Form, FloatingLabel } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { operationLivreDeCompte } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
import SoftBadge from 'components/common/SoftBadge';
registerLocale('fr', fr);
setDefaultLocale('fr');

const OneCentreLivre = ({
    idCentreDeCout,
    centre,
    setPageNeedsRefresh,
    tabInReadOnly,
    mesDroits,
}) => {
    const navigate = useNavigate();
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'dateOperation',
            Header: 'Date',
            Cell: ({ value, row }) => {
				return(value && value != null ? moment(value).format('DD/MM/YYYY HH:mm') : null);
			},
        },
        {
            accessor: 'libelleOperation',
            Header: 'Titre',
        },
        {
            accessor: 'montantSortant',
            Header: 'Transaction',
            Cell: ({ value, row }) => {
				return(<>
                    {row.original.montantSortant ? <SoftBadge className='me-1 mb-1' bg='warning'>- {row.original.montantSortant} €</SoftBadge> : null}
                    {row.original.montantEntrant ? <SoftBadge className='me-1 mb-1' bg='info'>+ {row.original.montantEntrant} €</SoftBadge> : null}
                </>);
			},
        },
        {
            accessor: 'detailsMoyenTransaction',
            Header: 'Détails',
            Cell: ({ value, row }) => {
				return(nl2br(value));
			},
        },
        {
            accessor: 'identifiant',
            Header: 'Responsable',
        },
        {
            accessor: 'idCommande',
            Header: 'Commande',
            Cell: ({ value, row }) => {
                if(value && value != null)
                {
                    if(HabilitationService.habilitations.commande_lecture)
                    {
                        return(<IconButton
                            icon='eye'
                            size = 'sm'
                            variant="outline-primary"
                            className="me-1"
                            onClick={()=>{navigate('/commandes/'+value)}}
                        />)
                    }else{
                        return('Commande '+value)
                    }
                }else{ return null}
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
                return(<>
                    {HabilitationService.habilitations['cout_etreEnCharge'] && !tabInReadOnly ?
                        <IconButton
                            icon='pen'
                            size = 'sm'
                            variant="outline-warning"
                            className="me-1"
                            onClick={()=>{handleShowOffCanevas(row.original.idOperations)}}
                        />
                    : null}
                    {HabilitationService.habilitations['cout_supprimer'] && !tabInReadOnly ?
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{handleShowDeleteModal(row.original.idOperations)}}
                        />
                    : null}
                </>)
			},
        },
    ];

    /*FORM*/
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdOperations, setOffCanevasIdOperations] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(operationLivreDeCompte),
    });
    const handleCloseOffCanevas = () => {
        setOffCanevasIdOperations();
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const [personnes, setPersonnes] = useState([]);
    const handleShowOffCanevas = async (id) => {
        try {
            if(mesDroits == false)
            { return; }

            setOffCanevasIdOperations(id);
            setShowOffCanevas(true);
            setLoading(true);

            let getForSelect = await Axios.get('/select/getPersonnes');
            setPersonnes(getForSelect.data);

            if(mesDroits.montantMaxValidation >= 0)
            {
                setValue("montantMaxCredit", mesDroits.montantMaxValidation);
            }else{
                setValue("montantMaxCredit", null);
            }

            if(id > 0)
            {
                let oneOperation = centre.operations.filter(ope => ope.idOperations == id)[0];
                setValue("dateOperation", oneOperation.dateOperation ? new Date(oneOperation.dateOperation) : null);
                setValue("libelleOperation", oneOperation.libelleOperation);
                setValue("montantEntrant", oneOperation.montantEntrant);
                setValue("montantSortant", oneOperation.montantSortant);
                setValue("detailsMoyenTransaction", oneOperation.detailsMoyenTransaction);
                setValue("idPersonne", oneOperation.idPersonne);

                if(mesDroits.montantMaxValidation != null)
                {
                    if(centre.centreDetails.soldeActuel < mesDroits.montantMaxValidation)
                    {
                        if(mesDroits.depasseBudget != true)
                        {
                            setValue("montantMaxDepense", centre.centreDetails.soldeActuel + oneOperation.montantSortant - oneOperation.montantEntrant);
                        }else{
                            setValue("montantMaxDepense", mesDroits.montantMaxValidation);
                        }
                    }else{
                        setValue("montantMaxDepense", mesDroits.montantMaxValidation);
                    }
                }else{
                    if(mesDroits.depasseBudget != true)
                    {
                        setValue("montantMaxDepense", centre.centreDetails.soldeActuel + oneOperation.montantSortant - oneOperation.montantEntrant);
                    }else{
                        setValue("montantMaxDepense", null);
                    }
                }
            }else{
                setValue("idPersonne", HabilitationService.habilitations.idPersonne);
                setValue("dateOperation", new Date());
                
                if(mesDroits.montantMaxValidation != null)
                {
                    if(centre.centreDetails.soldeActuel < mesDroits.montantMaxValidation)
                    {
                        if(mesDroits.depasseBudget != true)
                        {
                            setValue("montantMaxDepense", centre.centreDetails.soldeActuel);
                        }else{
                            setValue("montantMaxDepense", mesDroits.montantMaxValidation);
                        }
                    }else{
                        setValue("montantMaxDepense", mesDroits.montantMaxValidation);
                    }
                }else{
                    if(mesDroits.depasseBudget != true)
                    {
                        setValue("montantMaxDepense", centre.centreDetails.soldeActuel);
                    }else{
                        setValue("montantMaxDepense", null);
                    }
                }
            }

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdOperations > 0)    
            {
                const response = await Axios.post('/centresCouts/updateOperation',{
                    idOperations: offCanevasIdOperations,
                    idCentreDeCout : idCentreDeCout,
                    dateOperation: data.dateOperation,
                    libelleOperation: data.libelleOperation,
                    montantEntrant: data.montantEntrant,
                    montantSortant: data.montantSortant,
                    detailsMoyenTransaction: data.detailsMoyenTransaction,
                    idPersonne: data.idPersonne,
                });
            }
            else
            {
                const response = await Axios.post('/centresCouts/addOperation',{
                    idCentreDeCout : idCentreDeCout,
                    dateOperation: data.dateOperation,
                    libelleOperation: data.libelleOperation,
                    montantEntrant: data.montantEntrant,
                    montantSortant: data.montantSortant,
                    detailsMoyenTransaction: data.detailsMoyenTransaction,
                    idPersonne: data.idPersonne,
                });
            }

            handleCloseOffCanevas();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /*Delete*/
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdOperations, setDeleteModalIdOperations] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdOperations();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdOperations(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/centresCouts/centreCoutsOperationsDelete',{
                idOperations: deleteModalIdOperations,
                idCentreDeCout: idCentreDeCout,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return(<>
        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer cette opération (id: {deleteModalIdOperations}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdOperations > 0 ? "Modification" : "Ajout"} d'une ligne dans le livre de comptes</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Date de la transaction</Form.Label>
                        <DatePicker
                            selected={watch("dateOperation")}
                            onChange={(date)=>setValue("dateOperation", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date et heure"
                            timeIntervals={15}
                            dateFormat="dd/MM/yyyy HH:mm"
                            showTimeSelect
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateOperation?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Libellé</Form.Label>
                        <Form.Control size="sm" type="text" name='libelleOperation' id='libelleOperation' {...register('libelleOperation')} />
                        <small className="text-danger">{errors.libelleOperation?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Montants</Form.Label>
                        <FloatingLabel
                            controlId="floatingInput"
                            label={"Montant sortant"+ (watch("montantMaxDepense") != null ? ' (Max: '+watch("montantMaxDepense")+' €)' : "")}
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.01" max={watch("montantMaxDepense")} name='montantSortant' id='montantSortant' {...register('montantSortant')}/>
                            <small className="text-danger">{errors.montantSortant?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label={"Montant entrant"+ (watch("montantMaxCredit") != null ? ' (Max: '+watch("montantMaxCredit")+' €)' : "")}
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.01" max={watch("montantMaxCredit")} name='montantEntrant' id='montantEntrant' {...register('montantEntrant')}/>
                            <small className="text-danger">{errors.montantEntrant?.message}</small>
                        </FloatingLabel>
                    </Form.Group>
                    
                    <Form.Group className="mb-3">
                        <Form.Label>Personnes responsable de la transaction</Form.Label>
                        <Select
                            id="idPersonne"
                            name="idPersonne"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun élément selectionné'
                            options={personnes}
                            value={personnes.find(c => c.value === watch("idPersonne"))}
                            onChange={val => val != null ? setValue("idPersonne", val.value) : setValue("idPersonne", null)}
                        />
                        <small className="text-danger">{errors.idPersonne?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Détails</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"detailsMoyenTransaction"} id={"detailsMoyenTransaction"} {...register("detailsMoyenTransaction")}/>
                        <small className="text-danger">{errors.detailsMoyenTransaction?.message}</small>
                    </Form.Group>

                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <GPMtable
            columns={colonnes}
            data={centre.operations}
            topButtonShow={!tabInReadOnly}
            topButton={
                HabilitationService.habilitations['cout_etreEnCharge'] ?
                    <>
                        <IconButton
                            icon='plus'
                            size = 'sm'
                            variant="outline-success"
                            onClick={()=>{handleShowOffCanevas(0)}}
                            disabled={mesDroits == false ? true : false}
                        >Nouvelle operation</IconButton>
                    </>
                : null
            }
        />
    </>)
};

OneCentreLivre.propTypes = {};

export default OneCentreLivre;