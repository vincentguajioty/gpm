import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';
import { catalogueDelete } from 'helpers/deleteModalWarningContent';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
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
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'libelleMateriel',
            Header: 'Libellé et dispo bénévole',
            Cell: ({ value, row }) => {
				return(<>
                    <SoftBadge className='mt-1 me-1' bg={row.original.disponibleBenevolesConso ? 'warning' : 'success'}>
                        <FontAwesomeIcon icon={row.original.disponibleBenevolesConso ? 'check' : 'lock'} />
                    </SoftBadge>
                    {value} {row.original.taille ? ' (taille '+row.original.taille+')' : null}
                </>);
			},
        },
        {
            accessor: 'libelleCategorie',
            Header: 'Catégorie',
        },
        {
            accessor: 'accesModules',
            Header: 'Modules',
            Cell: ({ value, row }) => {
				return(<>
                    <SoftBadge className='me-1 mb-1' bg={row.original.modules_ope ? 'success' : 'secondary'}>Opérationnel (lots et réserves)</SoftBadge><br/>
                    <SoftBadge className='me-1 mb-1' bg={row.original.modules_vehicules ? 'success' : 'secondary'}>Véhicules</SoftBadge><br/>
                    <SoftBadge className='me-1 mb-1' bg={row.original.modules_tenues ? 'success' : 'secondary'}>Tenues</SoftBadge><br/>
                    <SoftBadge className='me-1 mb-1' bg={row.original.modules_vhf ? 'success' : 'secondary'}>Transmissions</SoftBadge>
                </>);
			},
        },
        {
            accessor: 'peremptionAnticipationOpe',
            Header: 'Anticipation de péremption',
            Cell: ({ value, row }) => {
				return(<>
                    {row.original.peremptionAnticipationOpe != null ? <>Lots: {row.original.peremptionAnticipationOpe}j<br/></> : null}
                    {row.original.peremptionAnticipationRes != null ? <>Réserves: {row.original.peremptionAnticipationRes}j<br/></> : null}
                    {row.original.peremptionAnticipationVehicule != null ? <>Véhicules: {row.original.peremptionAnticipationVehicule}j<br/></> : null}
                    {row.original.peremptionAnticipationTenues != null ? <>Tenues: {row.original.peremptionAnticipationTenues}j<br/></> : null}
                    {row.original.peremptionAnticipationVHF != null ? <>Transmissions: {row.original.peremptionAnticipationVHF}j<br/></> : null}
                </>);
			},
        },
        {
            accessor: 'commentairesMateriel',
            Header: 'Commentaires',
            Cell: ({ value, row }) => {
				return(nl2br(value));
			},
        },
        {
            accessor: 'nbCodesBarre',
            Header: 'Codes barre liés',
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {HabilitationService.habilitations['catalogue_modification'] ? 
                            <ActionButton onClick={() => handleShowOffCanevas(row.original.idMaterielCatalogue)} icon="pen" title="Modifier" variant="action" className="p-0 me-2" />
                        : null }
                        {HabilitationService.habilitations['catalogue_suppression'] ? 
                            <ActionButton onClick={() => handleShowDeleteModal(row.original.idMaterielCatalogue)} icon="trash" title="Supprimer" variant="action" className="p-0" />
                        : null }
                    </>
                );
			},
        },
    ];

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
                setValue("peremptionAnticipationVehicule", oneItemFromArray.peremptionAnticipationVehicule);
                setValue("peremptionAnticipationTenues", oneItemFromArray.peremptionAnticipationTenues);
                setValue("peremptionAnticipationVHF", oneItemFromArray.peremptionAnticipationVHF);
                setValue("disponibleBenevolesConso", oneItemFromArray.disponibleBenevolesConso ? true : false)

                if(oneItemFromArray.modules_ope){setValue("module", 'modules_ope')}
                if(oneItemFromArray.modules_vehicules){setValue("module", 'modules_vehicules')}
                if(oneItemFromArray.modules_tenues){setValue("module", 'modules_tenues')}
                if(oneItemFromArray.modules_vhf){setValue("module", 'modules_vhf')}
            }else{
                setValue("module", 'modules_ope')
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
                    peremptionAnticipationOpe: data.module == 'modules_ope' ? data.peremptionAnticipationOpe : null,
                    peremptionAnticipationRes: data.module == 'modules_ope' ? data.peremptionAnticipationRes : null,
                    peremptionAnticipationVehicule: data.module == 'modules_vehicules' ? data.peremptionAnticipationVehicule : null,
                    peremptionAnticipationTenues: data.module == 'modules_tenues' ? data.peremptionAnticipationTenues : null,
                    peremptionAnticipationVHF: data.module == 'modules_vhf' ? data.peremptionAnticipationVHF : null,
                    disponibleBenevolesConso: data.disponibleBenevolesConso  || 0,
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
                    peremptionAnticipationOpe: data.module == 'modules_ope' ? data.peremptionAnticipationOpe : null,
                    peremptionAnticipationRes: data.module == 'modules_ope' ? data.peremptionAnticipationRes : null,
                    peremptionAnticipationVehicule: data.module == 'modules_vehicules' ? data.peremptionAnticipationVehicule : null,
                    peremptionAnticipationTenues: data.module == 'modules_tenues' ? data.peremptionAnticipationTenues : null,
                    peremptionAnticipationVHF: data.module == 'modules_vhf' ? data.peremptionAnticipationVHF : null,
                    disponibleBenevolesConso: data.disponibleBenevolesConso || 0,
                    modules_ope: data.module == 'modules_ope' ? true : false,
                    modules_vehicules: data.module == 'modules_vehicules' ? true : false,
                    modules_tenues: data.module == 'modules_tenues' ? true : false,
                    modules_vhf: data.module == 'modules_vhf' ? true : false,
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
                    <Modal.Body>{catalogueDelete}</Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={handleCloseDeleteModal}>
                            Annuler
                        </Button>
                        <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
                    </Modal.Footer>
                </Modal>

                <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
                    <Offcanvas.Header closeButton >
                        <Offcanvas.Title>{offCanevasIdMaterielCatalogue > 0 ? "Modification" : "Ajout"} d'un item dans le catalogue commun</Offcanvas.Title>
                    </Offcanvas.Header>
                    <Offcanvas.Body>
                        <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                            {offCanevasIdMaterielCatalogue == 0 ?
                                <Form.Group className="mb-3">
                                    <Form.Label>Modules concernés par ce matériel:</Form.Label>
                                    <Form.Check 
                                        key="modules_ope"
                                        type='radio'
                                        id={`radio-modules_ope`}
                                        label='Opérationnel'
                                        name='radio'
                                        checked={watch("module") == 'modules_ope'}
                                        onClick={(e) => setValue('module', 'modules_ope')}
                                    />
                                    <Form.Check 
                                        key="modules_vehicules"
                                        type='radio'
                                        id={`radio-modules_vehicules`}
                                        label='Véhicules'
                                        name='radio'
                                        checked={watch("module") == 'modules_vehicules'}
                                        onClick={(e) => setValue('module', 'modules_vehicules')}
                                    />
                                    <Form.Check 
                                        key="modules_tenues"
                                        type='radio'
                                        id={`radio-modules_tenues`}
                                        label='Tenues'
                                        name='radio'
                                        checked={watch("module") == 'modules_tenues'}
                                        onClick={(e) => setValue('module', 'modules_tenues')}
                                    />
                                    <Form.Check 
                                        key="modules_vhf"
                                        type='radio'
                                        id={`radio-modules_vhf`}
                                        label='Transmissions'
                                        name='radio'
                                        checked={watch("module") == 'modules_vhf'}
                                        onClick={(e) => setValue('module', 'modules_vhf')}
                                    />
                                    <small className="text-danger">{errors.module?.message}</small>
                                </Form.Group>
                            : null}

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
                                    label='Une date de péremption est obligatoire pour ce matériel'
                                    checked={watch("sterilite")}
                                    onClick={(e)=>{setValue("sterilite", !watch("sterilite"))}}
                                />
                                <small className="text-danger">{errors.sterilite?.message}</small>
                                {watch("sterilite") ?
                                    <>
                                        {watch("module") == 'modules_ope' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption dans les lots (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationOpe" name="peremptionAnticipationOpe" {...register("peremptionAnticipationOpe")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationOpe?.message}</small>
                                                <br/>
                                                <Form.Label>Anticipation de la péremption dans les réserves (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationRes" name="peremptionAnticipationRes" {...register("peremptionAnticipationRes")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationRes?.message}</small>
                                            </>
                                        : null}

                                        {watch("module") == 'modules_vehicules' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption pour les véhicules (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationVehicule" name="peremptionAnticipationVehicule" {...register("peremptionAnticipationVehicule")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationVehicule?.message}</small>
                                            </>
                                        : null}

                                        {watch("module") == 'modules_tenues' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption pour les tenues (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationTenues" name="peremptionAnticipationTenues" {...register("peremptionAnticipationTenues")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationTenues?.message}</small>
                                            </>
                                        : null}

                                        {watch("module") == 'modules_vhf' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption pour les transmissions (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationVHF" name="peremptionAnticipationVHF" {...register("peremptionAnticipationVHF")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationVHF?.message}</small>
                                            </>
                                        : null}
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

                            {watch("module") == 'modules_ope' ?
                                <Form.Group className="mb-3">
                                    <Form.Label>Accessible hors connexion</Form.Label>
                                    <Form.Check
                                        type='switch'
                                        id="disponibleBenevolesConso"
                                        name="disponibleBenevolesConso"
                                        label='Matériel disponible pour les rapports de consommation'
                                        checked={watch("disponibleBenevolesConso")}
                                        onClick={(e)=>{setValue("disponibleBenevolesConso", !watch("disponibleBenevolesConso"))}}
                                    />
                                    <small className="text-danger">{errors.disponibleBenevolesConso?.message}</small>
                                </Form.Group>
                            : null}

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
                            data={catalogue}
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