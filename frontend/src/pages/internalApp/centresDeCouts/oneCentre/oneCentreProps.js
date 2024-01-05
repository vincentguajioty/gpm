import React, {useState, useEffect} from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Tabs, Tab, Modal, Button, Offcanvas, Form, FloatingLabel, Row, Col, Accordion } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';
import SoftBadge from 'components/common/SoftBadge';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { centresCoutsAddForm, modifGerantCentreCouts } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const ProprietesDuCentre = ({
    idCentreDeCout,
    centre,
    setPageNeedsRefresh,
    mesDroits,
}) => {
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(centresCoutsAddForm),
    });
    const initForm = () => {
        try {
            setValue("libelleCentreDecout", centre.centreDetails.libelleCentreDecout);
            setValue("commentairesCentreCout", centre.centreDetails.commentairesCentreCout);
            setValue("dateOuverture", centre.centreDetails.dateOuverture ? new Date(centre.centreDetails.dateOuverture) : null);
            setValue("dateFermeture", centre.centreDetails.dateFermeture ? new Date(centre.centreDetails.dateFermeture) : null);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initForm();
    },[])
    useEffect(()=>{
        initForm();
    },[centre.centreDetails])

    const saveProps = async (data) => {
        try {
            setLoading(true);

            await Axios.post('centresCouts/updateCentre',{
                libelleCentreDecout: data.libelleCentreDecout,
                commentairesCentreCout: data.commentairesCentreCout,
                dateOuverture: data.dateOuverture,
                dateFermeture: data.dateFermeture,
                idCentreDeCout: idCentreDeCout,
            })

            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /*DELETE CENTRE*/
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const handleCloseDeleteModal = () => {
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = () => {
        setShowDeleteModal(true);
    };
    const navigate = useNavigate();
    const supprimerCentre = async () => {
        try {
            setLoading(true);
            
            await Axios.post('/centresCouts/centreCoutsDelete',{
                idCentreDeCout: idCentreDeCout,
            })

            navigate('/couts');
            handleCloseDeleteModal();
        } catch (error) {
            console.log(error)
        }
    }

    return(<>
        <Form onSubmit={handleSubmit(saveProps)}>
            <Form.Group className="mb-3">
                <Form.Label>Titre du centre</Form.Label>
                <Form.Control size="sm" type="text" name='libelleCentreDecout' id='libelleCentreDecout' {...register('libelleCentreDecout')} disabled={!HabilitationService.habilitations.cout_ajout}/>
                <small className="text-danger">{errors.libelleCentreDecout?.message}</small>
            </Form.Group>
            <Row>
                <Col md={6}>
                    <Form.Group className="mb-3">
                        <Form.Label>Ouverture du centre</Form.Label><br/>
                        <DatePicker
                            selected={watch("dateOuverture")}
                            onChange={(date)=>setValue("dateOuverture", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                            disabled={!HabilitationService.habilitations.cout_ajout}
                        />
                        <small className="text-danger">{errors.dateOuverture?.message}</small>
                    </Form.Group>
                </Col>
                <Col md={6}>
                    <Form.Group className="mb-3">
                        <Form.Label>Fermeture du centre</Form.Label><br/>
                        <DatePicker
                            selected={watch("dateFermeture")}
                            onChange={(date)=>setValue("dateFermeture", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                            disabled={!HabilitationService.habilitations.cout_ajout}
                        />
                        <small className="text-danger">{errors.dateFermeture?.message}</small>
                    </Form.Group>
                </Col>
            </Row>

            <Form.Group className="mb-3">
                <Form.Label>Commentaires</Form.Label>
                <Form.Control size="sm" as="textarea" rows={5} name='commentairesCentreCout' id='commentairesCentreCout' {...register('commentairesCentreCout')} disabled={!HabilitationService.habilitations.cout_ajout}/>
                <small className="text-danger">{errors.message?.commentairesCentreCout}</small>
            </Form.Group>

            <div className="d-grid gap-2 mt-3">
                <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading || !HabilitationService.habilitations.cout_ajout}>{isLoading ? 'Patientez...' : 'Modifier'}</Button>
                <Button variant='outline-danger' className='mb-1'onClick={handleShowDeleteModal} disabled={isLoading || !HabilitationService.habilitations.cout_supprimer}>{isLoading ? 'Patientez...' : 'Supprimer définitivement'}</Button>
            </div>
        </Form>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer le centre de couts. Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerCentre} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
    </>);
}

const GestionnairesTable = ({
    idCentreDeCout,
    gestionnaires,
    setPageNeedsRefresh,
}) => {
    const colonnes = [
        {
            accessor: 'identifiant',
            Header: 'Personne',
        },
        {
            accessor: 'montantMaxValidation',
            Header: 'Seuil de validation des commandes',
            Cell: ({ value, row }) => {
				if(value != null)
                {
                    return <SoftBadge>{value} €</SoftBadge>
                }else{
                    return <SoftBadge bg='warning'>Illimité</SoftBadge>
                }
			},
        },
        {
            accessor: 'droits',
            Header: 'Droits étendus',
            Cell: ({ value, row }) => {
				return(<>
                    {row.original.depasseBudget ?
                        <><SoftBadge bg='warning' className='me-1 mb-1'>Dépassement de budget autorisé</SoftBadge><br/></>
                    : null}
                    {row.original.validerClos ?
                        <><SoftBadge bg='warning' className='me-1 mb-1'>Opérer sur le centre clos</SoftBadge><br/></>
                    : null}
                </>)
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(<>
                    {HabilitationService.habilitations['cout_ajout'] ? <>
                        <IconButton
                            icon='pen'
                            size = 'sm'
                            variant="outline-warning"
                            className="me-1"
                            onClick={()=>{handleShowOffCanevas(row.original.idGerant)}}
                        />
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{handleShowDeleteModal(row.original.idGerant)}}
                        />
                    </>: null}
                </>)
			},
        },
    ];

    /*Form edit*/
    const [isLoading, setLoading] = useState(false);
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdGerant, setOffCanevasIdGerant] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(modifGerantCentreCouts),
    });
    const handleCloseOffCanevas = () => {
        setOffCanevasIdGerant();
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const [personnes, setPersonnes] = useState([]);
    const handleShowOffCanevas = async (id) => {
        try {
            setOffCanevasIdGerant(id);
            setShowOffCanevas(true);
            setLoading(true);

            let getForSelect = await Axios.get('/select/getPersonnes');
            setPersonnes(getForSelect.data);

            if(id > 0)
            {
                let oneGerant = gestionnaires.filter(pers => pers.idGerant == id)[0];
                setValue("idPersonne", oneGerant.idPersonne);
                setValue("montantMaxValidation", oneGerant.montantMaxValidation);
                setValue("depasseBudget", oneGerant.depasseBudget);
                setValue("validerClos", oneGerant.validerClos);
            }

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdGerant > 0)    
            {
                const response = await Axios.post('/centresCouts/updateGerant',{
                    idGerant: offCanevasIdGerant,
                    idCentreDeCout: idCentreDeCout,
                    idPersonne: data.idPersonne,
                    montantMaxValidation: data.montantMaxValidation,
                    depasseBudget: data.depasseBudget,
                    validerClos: data.validerClos,
                });
            }
            else
            {
                const response = await Axios.post('/centresCouts/addGerant',{
                    idCentreDeCout: idCentreDeCout,
                    idPersonne: data.idPersonne,
                    montantMaxValidation: data.montantMaxValidation,
                    depasseBudget: data.depasseBudget,
                    validerClos: data.validerClos,
                });
            }

            handleCloseOffCanevas();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }


    /*delete*/
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdGerant, setDeleteModalIdGerant] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdGerant();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdGerant(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/centresCouts/centreCoutsGerantDelete',{
                idGerant: deleteModalIdGerant,
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
                Attention, vous allez supprimer un gérant (id: {deleteModalIdGerant}). Etes-vous certain de vouloir continuer ?
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
                <Offcanvas.Title>{offCanevasIdGerant > 0 ? "Modification" : "Ajout"} d'un gérant</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Gerant</Form.Label>
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
                        <Form.Label>Montant maximal d'opération sur le centre et de validation des commandes <small>(0 bloque toute action, laissé vide enlève toute contrainte)</small></Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='montantMaxValidation' id='montantMaxValidation' {...register('montantMaxValidation')}/>
                        <small className="text-danger">{errors.montantMaxValidation?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Gestion étendue</Form.Label>
                        <Form.Check 
                            id="depasseBudget"
                            name="depasseBudget"
                            checked={watch("depasseBudget")}
                            onClick={()=>{setValue("depasseBudget", !watch("depasseBudget"))}}
                            type='switch'
                            label="Dépassement de budget"
                        />
                        <Form.Check 
                            id="validerClos"
                            name="validerClos"
                            checked={watch("validerClos")}
                            onClick={()=>{setValue("validerClos", !watch("validerClos"))}}
                            type='switch'
                            label="Opérer sur le centre clos"
                        />
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <GPMtable
            columns={colonnes}
            data={gestionnaires}
            topButtonShow={true}
            topButton={
                HabilitationService.habilitations['cout_ajout'] ?
                    <>
                        <IconButton
                            icon='plus'
                            size = 'sm'
                            variant="outline-success"
                            onClick={()=>{handleShowOffCanevas(0)}}
                        >Nouveau gérant</IconButton>
                    </>
                : null
            }
        />
    </>)
}

const OneCentreProprietes = ({
    idCentreDeCout,
    centre,
    setPageNeedsRefresh,
}) => {
    return(<>
        <Accordion className="mb-3">
            <Accordion.Item eventKey="props" flush="true">
                <Accordion.Header>
                    Propriétés Générales
                </Accordion.Header>
                <Accordion.Body>
                    <ProprietesDuCentre
                        idCentreDeCout={idCentreDeCout}
                        centre={centre}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Accordion.Body>
            </Accordion.Item>

            <Accordion.Item eventKey="gerants" flush="true">
                <Accordion.Header>
                    Gestionnaires
                </Accordion.Header>
                <Accordion.Body>
                    <GestionnairesTable
                        idCentreDeCout={idCentreDeCout}
                        gestionnaires={centre.gestionnaires}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Accordion.Body>
            </Accordion.Item>
        </Accordion>
    </>)
};

OneCentreProprietes.propTypes = {};

export default OneCentreProprietes;
