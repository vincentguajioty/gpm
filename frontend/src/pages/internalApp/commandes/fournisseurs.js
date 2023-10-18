import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { fournisseurAddForm } from 'helpers/yupValidationSchema';

const Fournisseurs = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [fournisseurs, setFournisseurs] = useState([]);

    const initPage = async () => {
        try {
            const getConfig = await Axios.get('/fournisseurs/getFournisseurs');
            setFournisseurs(getConfig.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const navigate = useNavigate();
    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(fournisseurAddForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = () => {
        setShowOffCanevas(true);
    }
    const ajouterEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/fournisseurs/addFournisseur',{
                nomFournisseur: data.nomFournisseur,
            });

            let idTarget = response.data.idFournisseur;
            navigate('/fournisseurs/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdFournisseur, setDeleteModalIdFournisseur] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdFournisseur();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdFournisseur(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/fournisseurs/deleteFournisseur',{
                idFournisseur: deleteModalIdFournisseur,
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
            title="Fournisseurs"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouveau fournisseur</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterEntree)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Libellé</Form.Label>
                            <Form.Control size="sm" type="text" name='nomFournisseur' id='nomFournisseur' {...register('nomFournisseur')}/>
                            <small className="text-danger">{errors.nomFournisseur?.message}</small>
                        </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un fournisseur (id: {deleteModalIdFournisseur}). Etes-vous certain de vouloir continuer ?
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
                className="p-0"
            >
                {readyToDisplay ?
                    <Table responsive>
                        <thead>
                            <tr>
                                <th scope="col">Nom</th>
                                <th scope="col">Site</th>
                                <th scope="col">Téléphone</th>
                                <th scope="col">
                                    {HabilitationService.habilitations['fournisseurs_ajout'] ?
                                        <IconButton
                                            icon='plus'
                                            size = 'sm'
                                            variant="outline-success"
                                            onClick={handleShowOffCanevas}
                                        />
                                    : null}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {fournisseurs.map((four, i) => {return(
                                <tr>
                                    <td><Link to={'/fournisseurs/'+four.idFournisseur}>{four.nomFournisseur}</Link></td>
                                    <td><Link to={four.siteWebFournisseur}>{four.siteWebFournisseur}</Link></td>
                                    <td>{four.telephoneFournisseur}</td>
                                    <td>
                                        <IconButton
                                            icon='eye'
                                            size = 'sm'
                                            variant="outline-primary"
                                            className="me-1"
                                            onClick={()=>{navigate('/fournisseurs/'+four.idFournisseur)}}
                                        />
                                        {HabilitationService.habilitations['fournisseurs_suppression'] ? 
                                            <IconButton
                                                icon='trash'
                                                size = 'sm'
                                                variant="outline-danger"
                                                className="me-1"
                                                onClick={()=>{handleShowDeleteModal(four.idFournisseur)}}
                                            />
                                        : null}
                                    </td>
                                </tr>
                            )})}
                        </tbody>
                    </Table>
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Fournisseurs.propTypes = {};

export default Fournisseurs;
