import React, {useState, useEffect} from 'react';
import { Form, Button, Modal, Alert, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import Select from 'react-select';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { consommationPubliqueModificationMateriel } from 'helpers/yupValidationSchema';

const AfficherConsommable = ({
    socket,
    idConsommation,
    consommation,
}) => {
    const colonnes = [
        {
            accessor: 'libelleLot',
            Header: 'Lot',
        },
        {
            accessor: 'libelleMateriel',
            Header: 'Matériel',
        },
        {
            accessor: 'quantiteConsommation',
            Header: 'Quantité',
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        <IconButton
                            icon='pen'
                            size = 'sm'
                            variant="outline-warning"
                            className="me-1"
                            onClick={()=>{handleShowUpdateModal(row.original.idConsommationMateriel)}}
                        />
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{handleShowDeleteModal(row.original.idConsommationMateriel)}}
                        />
                    </>
                );
			},
        },
    ];

    const [isLoading, setLoading] = useState(false);
    const [catalogue, setCatalogue] = useState([]);
    const [lots, setLots] = useState([]);

    const initPage = async () => {
        try {
            setLoading(true);

            let getForSelect = await Axios.get('/select/getPublicCatalogueMateriel');
            setCatalogue(getForSelect.data);

            getForSelect = await Axios.get('/select/getLotsPublics');
            setLots(getForSelect.data);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    
    useEffect(() => {
        initPage();
    }, [])

    /* UPDATE */
    const [showUpdateModal, setShowUpdateModal] = useState(false);
    const [updateModalIdConsommationMateriel, setUpdateModalIdConsommationMateriel] = useState();

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(consommationPubliqueModificationMateriel),
    });

    const handleCloseUpdateModal = () => {
        setUpdateModalIdConsommationMateriel();
        setShowUpdateModal(false);
        setLoading(false);
    };
    const handleShowUpdateModal = (id) => {
        setUpdateModalIdConsommationMateriel(id);
        setShowUpdateModal(true);

        let oneItem = consommation.elements.filter(elem => elem.idConsommationMateriel == id)[0];
        setValue("idMaterielCatalogue", oneItem.idMaterielCatalogue);
        setValue("idLot", oneItem.idLot);
        setValue("quantiteConsommation", oneItem.quantiteConsommation);
    };

    const updateConso = async (data) => {
        try {
            setLoading(true);

            await socket.emit("consommation_updateElement",{
                idConsommation: idConsommation,
                idConsommationMateriel: updateModalIdConsommationMateriel,
                idMaterielCatalogue: data.idMaterielCatalogue,
                idLot: data.idLot,
                quantiteConsommation: data.quantiteConsommation,
            });

            handleCloseUpdateModal();
            reset();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdConsommationMateriel, setDeleteModalIdConsommationMateriel] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdConsommationMateriel();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdConsommationMateriel(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            await socket.emit("consommation_deleteElement",{
                idConsommationMateriel: deleteModalIdConsommationMateriel,
                idConsommation: idConsommation,
            });
            
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* FIN CONSOMMATION */
    const [showFinishModal, setShowFinishModal] = useState(false);
    const [commentaire, setCommentaire] = useState();
    const handleCloseFinishModal = () => {
        setShowFinishModal(false);
        setLoading(false);
    };
    const handleShowFinishModal = () => {
        setShowFinishModal(true);
    };

    const finirLaConso = async () => {
        try {
            setLoading(true);

            await socket.emit("consommation_terminerSaisie",{
                idConsommation: idConsommation,
                commentairesConsommation: commentaire,
            });
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
                Attention, vous allez supprimer un lot (id: {deleteModalIdConsommationMateriel}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showFinishModal} onHide={handleCloseFinishModal} backdrop="static" size='lg' keyboard={false}>
            <Modal.Header>
                <Modal.Title>Valider la saisie</Modal.Title>
                <FalconCloseButton onClick={handleCloseFinishModal}/>
            </Modal.Header>
            <Modal.Body>
                <Alert>Vous êtes sur le point de valider la saisie qui ne pourra plus être modifiée par la suite. Le prochain écran vous permettera de gérer le reconditionnement avec des réserves.</Alert>
                <Form.Control size="sm" as="textarea" placeholder="Zone de commentaires" rows={5} name={"commentaire"} id={"commentaire"} onChange={(e)=>{setCommentaire(e.target.value)}}/>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseFinishModal}>
                    Annuler
                </Button>
                <Button variant='success' onClick={finirLaConso} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Valider'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showUpdateModal} onHide={handleCloseUpdateModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Mise à jour</Modal.Title>
                <FalconCloseButton onClick={handleCloseUpdateModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmit(updateConso)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Matériel utilisé:</Form.Label>
                        <Select
                            id="idMaterielCatalogue"
                            name="idMaterielCatalogue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={true}
                            placeholder='Aucun élément selectionné'
                            options={catalogue}
                            value={catalogue.find(c => c.value === watch("idMaterielCatalogue"))}
                            onChange={val => val != null ? setValue("idMaterielCatalogue", val.value) : setValue("idMaterielCatalogue", null)}
                        />
                        <small className="text-danger">{errors.idMaterielCatalogue?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Où l'avez vous pris ?</Form.Label>
                        <Select
                            id="idLot"
                            name="idLot"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun emplacement selectionné'
                            options={lots}
                            value={lots.find(c => c.value === watch("idLot"))}
                            onChange={val => val != null ? setValue("idLot", val.value) : setValue("idLot", null)}
                        />
                        <small className="text-danger">{errors.idLot?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Quelle quantité ?</Form.Label>
                        <Form.Control size="sm" type="number" min="1" name='quantiteConsommation' id='quantiteConsommation' {...register('quantiteConsommation')}/>
                        <small className="text-danger">{errors.quantiteConsommation?.message}</small>
                    </Form.Group>

                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Modifier'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseUpdateModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <FalconComponentCard>
            <FalconComponentCard.Header
                title="Ce qui a été utilisé"
            />
            <FalconComponentCard.Body>
                <GPMtable
                    columns={colonnes}
                    data={consommation.elements}
                    topButtonShow={false}
                />
                <div className="d-grid gap-2 mt-3">
                    <Button variant='primary' className='me-2 mb-1' onClick={handleShowFinishModal} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Evènement terminé, passer au reconditionnement'}</Button>
                </div>
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

AfficherConsommable.propTypes = {};

export default AfficherConsommable;
