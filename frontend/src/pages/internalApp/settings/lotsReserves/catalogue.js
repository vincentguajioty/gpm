import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { catalogueForm } from 'helpers/yupValidationSchema';
import GPMtable from 'components/gpmTable/gpmTable';

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

            getFromDb = await Axios.get('/select/getCategoriesMateriels');
            setCategories(getFromDb.data);

            getFromDb = await Axios.get('/select/getFournisseurs');
            setFournisseurs(getFromDb.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
		initTable();
	}, [])

    /* TABLE FOR DISPLAY */
    const colonnes = [
        {accessor: 'libelleMateriel'          , Header: 'Libellé'},
        {accessor: 'libelleCategorie'         , Header: 'Catégorie'},
        {accessor: 'nomFournisseur'           , Header: 'Fournisseur de prédilection'},
        {accessor: 'peremptionAnticipationOpe', Header: 'Anticipation de péremption lots/réserves'},
        {accessor: 'commentairesMateriel'     , Header: 'Commentaires'},
        {accessor: 'nbCodesBarre'             , Header: 'Codes barre liés'},
        {accessor: 'actions'                  , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const nl2br = require('react-nl2br');
    const initTableau = () => {
        let tempTable  = [];
        for(const item of catalogue)
        {
            tempTable.push({
                libelleMateriel          : item.libelleMateriel,
                libelleCategorie         : item.libelleCategorie,
                nomFournisseur           : item.nomFournisseur,
                peremptionAnticipationOpe: <>{item.peremptionAnticipationOpe} / {item.peremptionAnticipationRes}</>,
                commentairesMateriel     : nl2br(item.commentairesMateriel),
                nbCodesBarre             : item.nbCodesBarre,
                actions                  :
                    <>
                        {HabilitationService.habilitations['catalogue_modification'] ? 
                            <ActionButton onClick={() => handleShowOffCanevas(item.idMaterielCatalogue)} icon="pen" title="Modifier" variant="action" className="p-0 me-2" />
                        : null }
                        {HabilitationService.habilitations['catalogue_suppression'] ? 
                            <ActionButton onClick={() => handleShowDeleteModal(item.idMaterielCatalogue)} icon="trash" title="Supprimer" variant="action" className="p-0" />
                        : null }
                    </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
		initTableau();
	}, [catalogue])

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
                                <Select
                                    id="idCategorie"
                                    name="idCategorie"
                                    size="sm"
                                    classNamePrefix="react-select"
                                    closeMenuOnSelect={true}
                                    isClearable={true}
                                    isSearchable={true}
                                    isDisabled={isLoading}
                                    placeholder='Aucune catégorie selectionnée'
                                    options={categories}
                                    value={categories.find(c => c.value === watch("idCategorie"))}
                                    onChange={val => val != null ? setValue("idCategorie", val.value) : setValue("idCategorie", null)}
                                />
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
                                <Select
                                    id="idFournisseur"
                                    name="idFournisseur"
                                    size="sm"
                                    classNamePrefix="react-select"
                                    closeMenuOnSelect={true}
                                    isClearable={true}
                                    isSearchable={false}
                                    isDisabled={isLoading}
                                    placeholder='Aucun fournisseur selectionné'
                                    options={fournisseurs}
                                    value={fournisseurs.find(c => c.value === watch("idFournisseur"))}
                                    onChange={val => val != null ? setValue("idFournisseur", val.value) : setValue("idFournisseur", null)}
                                />
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
                    >
                        <GPMtable
                            columns={colonnes}
                            data={lignes}
                            topButtonShow={true}
                            topButton={
                                HabilitationService.habilitations['catalogue_ajout'] ? 
                                    <IconButton
                                        icon='plus'
                                        size = 'sm'
                                        variant="outline-success"
                                        onClick={() => handleShowOffCanevas(0)}
                                    >Nouvel élément</IconButton>
                                : null
                            }
                        />
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