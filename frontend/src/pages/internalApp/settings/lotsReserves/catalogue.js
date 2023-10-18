import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { catalogueForm } from 'helpers/yupValidationSchema';

const Catalogue = () => {
    /*PAGE BASICS*/
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [catalogue, setCatalogue] = useState([]);
    const [categories, setCategories] = useState([]);
    const [fournisseurs, setFournisseurs] = useState([]);

    const initTable = async () => {
        try {
            let getFromDb = await Axios.get('/settingsMetiers/getCatalogueMateriel');
            setCatalogue(getFromDb.data);

            getFromDb = await Axios.get('/settingsMetiers/getCategoriesMateriels');
            setCategories(getFromDb.data);

            getFromDb = await Axios.get('/fournisseurs/getFournisseurs');
            setFournisseurs(getFromDb.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
		initTable();
	}, [])

    /* FORM */
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdMaterielCatalogue, setOffCanevasIdMaterielCatalogue] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(catalogueForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdMaterielCatalogue();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (idMaterielCatalogue) => {
        try {
            setOffCanevasIdMaterielCatalogue(idMaterielCatalogue);

            if(idMaterielCatalogue > 0)
            {
                let oneItemFromArray = catalogue.filter(ligne => ligne.idMaterielCatalogue == idMaterielCatalogue)[0];
                setValue("libelleMateriel", oneItemFromArray.libelleMateriel);
                setValue("idCategorie", oneItemFromArray.idCategorie);
                setValue("taille", oneItemFromArray.taille);
                setValue("sterilite", oneItemFromArray.sterilite);
                setValue("conditionnementMultiple", oneItemFromArray.conditionnementMultiple);
                setValue("commentairesMateriel", oneItemFromArray.commentairesMateriel);
                setValue("idFournisseur", oneItemFromArray.idFournisseur);
                setValue("peremptionAnticipationOpe", oneItemFromArray.peremptionAnticipationOpe);
                setValue("peremptionAnticipationRes", oneItemFromArray.peremptionAnticipationRes);
            }

            setShowOffCanevas(true);
        } catch (error) {
            console.error(error)
        }
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdMaterielCatalogue > 0)    
            {
                const response = await Axios.post('/settingsMetiers/updateCatalogueMateriel',{
                    idMaterielCatalogue: offCanevasIdMaterielCatalogue,
                    libelleMateriel: data.libelleMateriel,
                    idCategorie: data.idCategorie,
                    taille: data.taille,
                    sterilite: data.sterilite,
                    conditionnementMultiple: data.conditionnementMultiple,
                    commentairesMateriel: data.commentairesMateriel,
                    idFournisseur: data.idFournisseur,
                    peremptionAnticipationOpe: data.peremptionAnticipationOpe,
                    peremptionAnticipationRes: data.peremptionAnticipationRes,
                });
            }
            else
            {
                const response = await Axios.post('/settingsMetiers/addCatalogueMateriel',{
                    libelleMateriel: data.libelleMateriel,
                    idCategorie: data.idCategorie,
                    taille: data.taille,
                    sterilite: data.sterilite,
                    conditionnementMultiple: data.conditionnementMultiple,
                    commentairesMateriel: data.commentairesMateriel,
                    idFournisseur: data.idFournisseur,
                    peremptionAnticipationOpe: data.peremptionAnticipationOpe,
                    peremptionAnticipationRes: data.peremptionAnticipationRes,
                });
            }

            handleCloseOffCanevas();
            initTable();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdMaterielCatalogue, setDeleteModalIdMaterielCatalogue] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdMaterielCatalogue();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdMaterielCatalogue(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/settingsMetiers/deleteCatalogueMateriel',{
                idMaterielCatalogue: deleteModalIdMaterielCatalogue,
            });
            
            initTable();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }


    /* RENDER */
    const nl2br = require('react-nl2br');
    return (
        readyToDisplay ?
            <>
                <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
                    <Modal.Header>
                        <Modal.Title>Suppression</Modal.Title>
                        <FalconCloseButton onClick={handleCloseDeleteModal}/>
                    </Modal.Header>
                    <Modal.Body>
                        Attention, vous allez supprimer une entrée (id: {deleteModalIdMaterielCatalogue}). Etes-vous certain de vouloir continuer ?
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
                        <Offcanvas.Title>{offCanevasIdMaterielCatalogue > 0 ? "Modification" : "Ajout"} d'un paramètre</Offcanvas.Title>
                    </Offcanvas.Header>
                    <Offcanvas.Body>
                        <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                            <Form.Group className="mb-3">
                                <Form.Label>Libellé</Form.Label>
                                <Form.Control type="text" id="libelleMateriel" name="libelleMateriel" {...register("libelleMateriel")}/>
                                <small className="text-danger">{errors.libelleMateriel?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Catégorie</Form.Label>
                                <Form.Select size="sm" name="idCategorie" id="idCategorie" {...register("idCategorie")}>
                                    <option key="0" value="">--- Aucune Catégorie ---</option>
                                    {categories.map((cat, i) => {
                                        return (<option key={cat.idCategorie} value={cat.idCategorie}>{cat.libelleCategorie}</option>);
                                    })}
                                </Form.Select>
                                <small className="text-danger">{errors.idCategorie?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Taille</Form.Label>
                                <Form.Control type="text" id="taille" name="taille" {...register("taille")}/>
                                <small className="text-danger">{errors.taille?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Conditionnement</Form.Label>
                                <Form.Control type="text" id="conditionnementMultiple" name="conditionnementMultiple" {...register("conditionnementMultiple")}/>
                                <small className="text-danger">{errors.conditionnementMultiple?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Stérilité</Form.Label>
                                <Form.Check
                                    type='switch'
                                    id="sterilite"
                                    name="sterilite"
                                    label='Matériel stérile'
                                    checked={watch("sterilite")}
                                    onClick={(e)=>{setValue("sterilite", !watch("sterilite"))}}
                                />
                                <small className="text-danger">{errors.sterilite?.message}</small>
                                {watch("sterilite") ?
                                    <>
                                        <Form.Label>Anticipation de la péremption dans les lots (jours)</Form.Label>
                                        <Form.Control type="number" min="0" step="1" id="peremptionAnticipationOpe" name="peremptionAnticipationOpe" {...register("peremptionAnticipationOpe")}/>
                                        <small className="text-danger">{errors.peremptionAnticipationOpe?.message}</small>

                                        <Form.Label>Anticipation de la péremption dans les réserves (jours)</Form.Label>
                                        <Form.Control type="number" min="0" step="1" id="peremptionAnticipationRes" name="peremptionAnticipationRes" {...register("peremptionAnticipationRes")}/>
                                        <small className="text-danger">{errors.peremptionAnticipationRes?.message}</small>
                                    </>
                                :null}
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Fournisseur de prédilection</Form.Label>
                                <Form.Select size="sm" name="idFournisseur" id="idFournisseur" {...register("idFournisseur")}>
                                    <option key="0" value="">--- Aucun fournisseur ---</option>
                                    {fournisseurs.map((four, i) => {
                                        return (<option key={four.idFournisseur} value={four.idFournisseur}>{four.nomFournisseur}</option>);
                                    })}
                                </Form.Select>
                                <small className="text-danger">{errors.idFournisseur?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Commentaires</Form.Label>
                                <Form.Control size="sm" as="textarea" rows={3} name={"commentairesMateriel"} id={"commentairesMateriel"} {...register("commentairesMateriel")}/>
                                <small className="text-danger">{errors.commentairesMateriel?.message}</small>
                            </Form.Group>

                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                        </Form>
                    </Offcanvas.Body>
                </Offcanvas>

                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title="Catalogue du matériel"
                    >
                    </FalconComponentCard.Header>
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                        className="p-0"
                    >
                        <SimpleBarReact style={{ height: '26rem' }}>
                            <Table size='sm' responsive striped>
                                <thead>
                                    <tr>
                                        <th scope="col">Libellé</th>
                                        <th scope="col">Catégorie</th>
                                        <th scope="col">Fournisseur de prédilection</th>
                                        <th scope="col">Anticipation de péremption lots/réserves</th>
                                        <th scope="col">Commentaires</th>
                                        <th scope="col">Codes barre liés</th>
                                        <th className="text-end" scope="col">
                                            {HabilitationService.habilitations['catalogue_ajout'] ? 
                                                <ActionButton onClick={() => handleShowOffCanevas(0)} icon="plus" title="Ajouter" variant="action" className="p-0 me-2" />
                                            : null }
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {catalogue.map((data, i) => {
                                        return(
                                            <tr>
                                                <td>{data.libelleMateriel}</td>
                                                <td>{data.libelleCategorie}</td>
                                                <td>{data.nomFournisseur}</td>
                                                <td>{data.peremptionAnticipationOpe} / {data.peremptionAnticipationRes}</td>
                                                <td>{nl2br(data.commentairesMateriel)}</td>
                                                <td>{data.nbCodesBarre}</td>
                                                <td className="text-end">
                                                    {HabilitationService.habilitations['catalogue_modification'] ? 
                                                        <ActionButton onClick={() => handleShowOffCanevas(data.idMaterielCatalogue)} icon="pen" title="Modifier" variant="action" className="p-0 me-2" />
                                                    : null }
                                                    {HabilitationService.habilitations['catalogue_suppression'] ? 
                                                        <ActionButton onClick={() => handleShowDeleteModal(data.idMaterielCatalogue)} icon="trash" title="Supprimer" variant="action" className="p-0" />
                                                    : null }
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </Table>
                        </SimpleBarReact>

                    </FalconComponentCard.Body>
                </FalconComponentCard>
            </>
        :
            <FalconComponentCard noGuttersBottom className="mb-3">
                <FalconComponentCard.Header
                    title="Catalogue du matériel"
                >
                </FalconComponentCard.Header>
                <FalconComponentCard.Body
                    scope={{ ActionButton }}
                    noLight
                    className="p-0"
                >
                    <LoaderInfiniteLoop />
                </FalconComponentCard.Body>
            </FalconComponentCard>
    );

};

Catalogue.propTypes = {};

export default Catalogue;