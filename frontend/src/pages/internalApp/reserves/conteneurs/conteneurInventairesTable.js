import React, {useState, useEffect} from 'react';
import { useNavigate } from 'react-router-dom';
import { Modal, Button, Table, Alert, Form } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import moment from 'moment-timezone';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import GPMtable from 'components/gpmTable/gpmTable';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { reservesStartInventaireModal } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const ConteneurInventairesTable = ({
    idConteneur,
    inventaireEnCours,
    inventaires,
    setPageNeedsRefresh
}) => {
    const nl2br = require('react-nl2br');
    const navigate = useNavigate();

    const [idReserveInventaireEnCours, setIdReserveInventaireEnCours] = useState();
    const colonnes = [
        {
            accessor: 'dateInventaire',
            Header: 'Date',
            Cell: ({ value, row }) => {
				return(moment(value).format('DD/MM/YYYY'));
			},
        },
        {
            accessor: 'idPersonne',
            Header: 'Réalisé par',
            Cell: ({ value, row }) => {
				return(row.original.nomPersonne+' '+row.original.prenomPersonne);
			},
        },
        {
            accessor: 'commentairesInventaire',
            Header: 'Commentaires',
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
                        {row.original.inventaireEnCours && inventaireEnCours ?
                            <IconButton
                                icon='binoculars'
                                size = 'sm'
                                variant="warning"
                                className="me-1"
                                onClick={()=>{navigate('/inventaireReserveEnCours/'+row.original.idReserveInventaire)}}
                            >Rejoindre l'inventaire en cours</IconButton>
                        : <>
                            <IconButton
                                icon='eye'
                                size = 'sm'
                                variant="outline-info"
                                className="me-1"
                                onClick={()=>{handleShowDetailsModal(row.original.idReserveInventaire)}}
                            />

                            {HabilitationService.habilitations['reserve_suppression'] ? 
                                <IconButton
                                    icon='trash'
                                    size = 'sm'
                                    variant="outline-danger"
                                    className="me-1"
                                    onClick={()=>{handleShowDeleteModal(row.original.idReserveInventaire)}}
                                />
                            : null}
                        </>}
                    </>
                );
			},
        },
    ];
    const initInventaireEnCoursID = () => {
        for(const item of inventaires)
        {
            if(item.inventaireEnCours)
            {setIdReserveInventaireEnCours(item.idReserveInventaire)}
        }
    }
    useEffect(() => {
        initInventaireEnCoursID();
    }, [inventaires])

    useEffect(() => {
        initInventaireEnCoursID();
    }, [])

    /* DELETE */
    const [isLoading, setLoading] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdReserveInventaire, setDeleteModalIdReserveInventaire] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdReserveInventaire();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdReserveInventaire(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/reserves/reserveInventaireDelete',{
                idReserveInventaire: deleteModalIdReserveInventaire,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* DETAILS */
    const [showDetailsModal, setShowDetailsModal] = useState(false);
    const [inventaire, setInventaire] = useState([]);
    const inventaireColonnes = [
        {
            accessor: 'libelleMateriel',
            Header: 'Matériel',
        },
        {
            accessor: 'libelleCategorie',
            Header: 'Catégorie',
        },
        {
            accessor: 'quantiteInventaire',
            Header: 'Quantité',
        },
        {
            accessor: 'peremptionInventaire',
            Header: 'Péremption',
            Cell: ({ value, row }) => {
				return(value ? moment(value).format('DD/MM/YYYY') : null);
			},
        },
    ];
    const handleCloseDetailsModal = () => {
        setShowDetailsModal(false);
        setLoading(false);
        setInventaire([]);
    };
    const handleShowDetailsModal = async (id) => {
        try {
            setLoading(true);
            setShowDetailsModal(true);
            
            const getInventaire = await Axios.post('/reserves/getOneInventaireForDisplay',{
                idReserveInventaire: id,
            })
            setInventaire(getInventaire.data);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    /* START NOUVEL INVENTAIRE */
    const [showStartModal, setShowStartModal] = useState(false);
    const [personnes, setPersonnes] = useState([]);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(reservesStartInventaireModal),
    });
    const handleCloseStartModal = () => {
        setShowStartModal(false);
        setLoading(false);
    };
    const handleShowStartModal = async () => {
        try {
            setLoading(true);
            setShowStartModal(true);
            
            setValue("dateInventaire", new Date());
            setValue("idPersonne", HabilitationService.habilitations.idPersonne);
            setValue("isLastInventaire", true);

            let getData = await Axios.get('/select/getPersonnes');
            setPersonnes(getData.data);
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };
    const startInventaire = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/reserves/startInventaire',{
                idConteneur: idConteneur,
                dateInventaire: data.dateInventaire,
                idPersonne: data.idPersonne,
                isLastInventaire: data.isLastInventaire,
            });

            let idTarget = response.data.idReserveInventaire;
            navigate('/inventaireReserveEnCours/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    
    return (<>
        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un inventaire (id: {deleteModalIdReserveInventaire}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showDetailsModal} onHide={handleCloseDetailsModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Détails de l'inventaire</Modal.Title>
                <FalconCloseButton onClick={handleCloseDetailsModal}/>
            </Modal.Header>
            <Modal.Body>
                {showDetailsModal ? isLoading ? <LoaderInfiniteLoop/> : 
                    <>
                        <Table className="fs--1 mt-3" size='sm' responsive>
                            <tbody>
                                <tr>
                                    <th className="bg-100" style={{ width: '30%' }}>Lot</th>
                                    <td>{inventaire.inventaire.libelleConteneur}</td>
                                </tr>
                                <tr>
                                    <th className="bg-100" style={{ width: '30%' }}>Date de l'inventaire</th>
                                    <td>{moment(inventaire.inventaire.dateInventaire).format('DD/MM/YYYY')}</td>
                                </tr>
                                <tr>
                                    <th className="bg-100" style={{ width: '30%' }}>Réalisé par</th>
                                    <td>{inventaire.inventaire.prenomPersonne} {inventaire.inventaire.nomPersonne}</td>
                                </tr>
                                <tr>
                                    <th className="bg-100" style={{ width: '30%' }}>Remarques</th>
                                    <td>{nl2br(inventaire.inventaire.commentairesInventaire)}</td>
                                </tr>
                            </tbody>
                        </Table>
                        <GPMtable
                            columns={inventaireColonnes}
                            data={inventaire.contenu}
                            topButtonShow={false}
                        />
                    </>
                : null}
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDetailsModal}>
                    Fermer
                </Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showStartModal} onHide={handleCloseStartModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Démarrer un nouvel inventaire</Modal.Title>
                <FalconCloseButton onClick={handleCloseStartModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmit(startInventaire)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Date de l'inventaire</Form.Label>
                        <DatePicker
                            selected={watch("dateInventaire")}
                            onChange={(date)=>setValue("dateInventaire", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateInventaire?.message}</small>
                        <Form.Check
                            id='isLastInventaire'
                            name='isLastInventaire'
                            label="Il s'agira de l'inventaire le plus récent pour ce lot"
                            type='switch'
                            checked={watch("isLastInventaire")}
                            onClick={(e)=>{
                                setValue("isLastInventaire", !watch("isLastInventaire"))
                            }}
                        />
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Personne responsable de cet inventaire</Form.Label>
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

                    <div className="d-grid gap-2 mt-3">
                        <Button variant='success' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Démarrer'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseStartModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>
        
        <GPMtable
            columns={colonnes}
            data={inventaires}
            topButtonShow={true}
            topButton={
                inventaireEnCours ?
                    <IconButton
                        icon='binoculars'
                        size = 'sm'
                        variant="warning"
                        className="me-1"
                        onClick={()=>{navigate('/inventaireReserveEnCours/'+idReserveInventaireEnCours)}}
                    >Rejoindre l'inventaire en cours</IconButton>    
                :
                
                    HabilitationService.habilitations['reserve_modification'] ?
                        <IconButton
                            icon='binoculars'
                            size = 'sm'
                            variant="outline-success"
                            onClick={handleShowStartModal}
                        >Lancer un nouvel inventaire</IconButton>
                    : null
            }
        />
    </>);
};

ConteneurInventairesTable.propTypes = {};

export default ConteneurInventairesTable;
