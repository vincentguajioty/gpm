import React, {useState} from 'react';
import { Card, Modal, Button, Offcanvas, Form, } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import FalconCloseButton from 'components/common/FalconCloseButton';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vhfAccessoiresForm } from 'helpers/yupValidationSchema';

const EquipementVhfAccessoires = ({equipement, setPageNeedsRefresh}) => {
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'libelleVhfAccessoireType',
            Header: 'Type',
        },
        {
            accessor: 'libelleVhfAccessoire',
            Header: 'Libellé',
        },
        {
            accessor: 'marqueModeleVhfAccessoire',
            Header: 'Marque/Modèle',
        },
        {
            accessor: 'SnVhfAccessoire',
            Header: 'SN',
        },
        {
            accessor: 'remarquesVhfAccessoire',
            Header: 'Remarques',
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
                        {HabilitationService.habilitations['vhf_equipement_modification'] ? 
                            <IconButton
                                icon='pen'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowOffCanevas(row.original.idVhfAccessoire)}}
                            />
                        : null}
                        {HabilitationService.habilitations['vhf_equipement_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idVhfAccessoire)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    /* FORM */
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdVhfAccessoire, setOffCanevasIdVhfAccessoire] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vhfAccessoiresForm),
    });
    const [types, setTypes] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdVhfAccessoire();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdVhfAccessoire(id);

        if(id > 0)
        {
            let oneItemFromArray = equipement.accessoires.filter(ligne => ligne.idVhfAccessoire == id)[0];
            setValue("libelleVhfAccessoire", oneItemFromArray.libelleVhfAccessoire);
            setValue("marqueModeleVhfAccessoire", oneItemFromArray.marqueModeleVhfAccessoire);
            setValue("idVhfAccessoireType", oneItemFromArray.idVhfAccessoireType);
            setValue("SnVhfAccessoire", oneItemFromArray.SnVhfAccessoire);
            setValue("remarquesVhfAccessoire", oneItemFromArray.remarquesVhfAccessoire);
        }

        const getData = await Axios.get('/select/getVHFTypesAccessoires');
        setTypes(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdVhfAccessoire > 0)    
            {
                const response = await Axios.post('/vhf/updateAccessoire',{
                    idVhfAccessoire: offCanevasIdVhfAccessoire,
                    libelleVhfAccessoire: data.libelleVhfAccessoire,
                    marqueModeleVhfAccessoire: data.marqueModeleVhfAccessoire,
                    idVhfAccessoireType: data.idVhfAccessoireType,
                    SnVhfAccessoire: data.SnVhfAccessoire,
                    remarquesVhfAccessoire: data.remarquesVhfAccessoire,
                });
            }
            else
            {
                const response = await Axios.post('/vhf/addAccessoire',{
                    libelleVhfAccessoire: data.libelleVhfAccessoire,
                    marqueModeleVhfAccessoire: data.marqueModeleVhfAccessoire,
                    idVhfAccessoireType: data.idVhfAccessoireType,
                    SnVhfAccessoire: data.SnVhfAccessoire,
                    remarquesVhfAccessoire: data.remarquesVhfAccessoire,
                    idVhfEquipement: equipement.idVhfEquipement,
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
    const [deleteModalIdVhfAccessoire, setDeleteModalIdVhfAccessoire] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdVhfAccessoire();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVhfAccessoire(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vhf/vhfEquipementsAccessoiresDelete',{
                idVhfAccessoire: deleteModalIdVhfAccessoire,
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
                Attention, vous allez supprimer un accessoire VHF (id: {deleteModalIdVhfAccessoire}). Etes-vous certain de vouloir continuer ?
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
                <Offcanvas.Title>{offCanevasIdVhfAccessoire > 0 ? "Modification" : "Ajout"} d'un accessoire VHF</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé</Form.Label>
                        <Form.Control size="sm" type="text" name='libelleVhfAccessoire' id='libelleVhfAccessoire' {...register('libelleVhfAccessoire')}/>
                        <small className="text-danger">{errors.libelleVhfAccessoire?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Marque/Modèle</Form.Label>
                        <Form.Control size="sm" type="text" name='marqueModeleVhfAccessoire' id='marqueModeleVhfAccessoire' {...register('marqueModeleVhfAccessoire')}/>
                        <small className="text-danger">{errors.marqueModeleVhfAccessoire?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Numéro de série</Form.Label>
                        <Form.Control size="sm" type="text" name='SnVhfAccessoire' id='SnVhfAccessoire' {...register('SnVhfAccessoire')}/>
                        <small className="text-danger">{errors.SnVhfAccessoire?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Type</Form.Label>
                        <Select
                            id="idVhfAccessoireType"
                            name="idVhfAccessoireType"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun type selectionné'
                            options={types}
                            value={types.find(c => c.value === watch("idVhfAccessoireType"))}
                            onChange={val => val != null ? setValue("idVhfAccessoireType", val.value) : setValue("idVhfAccessoireType", null)}
                        />
                        <small className="text-danger">{errors.idVhfAccessoireType?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name="remarquesVhfAccessoire" id="remarquesVhfAccessoire" {...register("remarquesVhfAccessoire")}/>
                        <small className="text-danger">{errors.remarquesVhfAccessoire?.message}</small>
                    </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Card className="mb-3">
            <Card.Header className="p-2 border-bottom">
                <Flex>
                    <div className="p-2 flex-grow-1">
                        Accessoires
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                <GPMtable
                    columns={colonnes}
                    data={equipement.accessoires}
                    topButtonShow={true}
                    topButton={
                        HabilitationService.habilitations['vhf_equipement_ajout'] ?
                            <IconButton
                                icon='plus'
                                size = 'sm'
                                variant="outline-success"
                                onClick={()=>{handleShowOffCanevas(0)}}
                            >Nouvel accessoire</IconButton>
                        : null
                    }
                />
            </Card.Body>
        </Card>
    </>);
};

EquipementVhfAccessoires.propTypes = {};

export default EquipementVhfAccessoires;