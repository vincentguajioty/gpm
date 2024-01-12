import React, { useState } from 'react';
import { Form, Button, Modal, Offcanvas, FloatingLabel, Alert, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';
import { commandeRegleMetierDelete } from 'helpers/deleteModalWarningContent';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { settingsAddCommandeContraint } from 'helpers/yupValidationSchema';

const ConfigGeneraleTabReglesCmd = ({
    reglesCmd,
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const colonnes = [
        {
            accessor: 'idEtat',
            Header: 'Lorsque la commande passe',
            Cell: ({ value, row }) => {
				return(<>
                    De <SoftBadge>{row.original.etatInitial}</SoftBadge> à <SoftBadge>{row.original.etatFinal}</SoftBadge>
                </>);
			},
        },
        {
            accessor: 'libelleContrainte',
            Header: 'Contrainte',
        },
        {
            accessor: 'action',
            Header: 'Action',
            Cell: ({ value, row }) => {
				return(<>
                    <IconButton
                        icon='trash'
                        size = 'sm'
                        variant="outline-danger"
                        className="me-1"
                        onClick={()=>{handleShowDeleteModal(row.original.idContrainte)}}
                    />
                </>);
			},
        },
    ];

    /* ADD FORM */
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [etats, setEtats] = useState([]);
    const[typesDocuments, setTypesDocuments] = useState([]);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(settingsAddCommandeContraint),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setIsLoading(false);
    }
    const handleShowOffCanevas = async () => {
        try {
            setShowOffCanevas(true);
            setIsLoading(true);

            let getDataForSelect = await Axios.get('/select/getEtatsCommandes');
            setEtats(getDataForSelect.data);
            getDataForSelect = await Axios.get('/select/getTypesDocuments');
            setTypesDocuments(getDataForSelect.data);
            
            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    const ajouterEntree = async (data) => {
        try {
            setIsLoading(true);

            const response = await Axios.post('/settingsTechniques/addOneCmdContrainte',{
                libelleContrainte: data.libelleContrainte,
                idEtatInitial: data.idEtatInitial,
                idEtatFinal: data.idEtatFinal,
                fournisseurObligatoire: data.fournisseurObligatoire,
                minDemandeurs: data.minDemandeurs,
                minAffectees: data.minAffectees,
                minObservateurs: data.minObservateurs,
                minValideurs: data.minValideurs,
                centreCoutsObligatoire: data.centreCoutsObligatoire,
                lieuLivraisonObligatoire: data.lieuLivraisonObligatoire,
                minQttMateriel: data.minQttMateriel,
                minMontant: data.minMontant,
                maxMontant: data.maxMontant,
                remarquesGeneralesObligatoires: data.remarquesGeneralesObligatoires,
                idTypeDocumentObligatoire: data.idTypeDocumentObligatoire,
                nbTypeDocumentObligatoire: data.nbTypeDocumentObligatoire,
                remarquesValidationObligatoire: data.remarquesValidationObligatoire,
                referenceCommandeFournisseurObligatoire: data.referenceCommandeFournisseurObligatoire,
                datePassageCommandeObligatoire: data.datePassageCommandeObligatoire,
                datePrevueLivraisonObligatoire: data.datePrevueLivraisonObligatoire,
                dateLivraisonEffectiveObligatoire: data.dateLivraisonEffectiveObligatoire,
                remarquesLivraisonsObligatoire: data.remarquesLivraisonsObligatoire,
                integrationStockObligatoire: data.integrationStockObligatoire,
            });

            setPageNeedsRefresh(true);
            handleCloseOffCanevas();
            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdContrainte, setDeleteModalIdContrainte] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdContrainte();
        setShowDeleteModal(false);
        setIsLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdContrainte(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setIsLoading(true);

            const response = await Axios.post('/settingsTechniques/dropOneCmdContrainte',{
                idContrainte: deleteModalIdContrainte,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setIsLoading(false);
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
            <Modal.Body>{commandeRegleMetierDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouvelle Règle</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Alert variant='danger'>ATTENTION ! Il est essentiel de ne pas cumuler les contraintes dans une même règle. Merci de n'en renseigner qu'une seule.</Alert>
                <Form onSubmit={handleSubmit(ajouterEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé de la contrainte<small>(le plus parlant possible, sera ensuite affiché sur la page des commandes)</small></Form.Label>
                        <Form.Control size="sm" type="text" name='libelleContrainte' id='libelleContrainte' {...register('libelleContrainte')}/>
                        <small className="text-danger">{errors.libelleContrainte?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>S'applique lorsque la commande passe de l'état</Form.Label>
                        <Select
                            id="idEtatInitial"
                            name="idEtatInitial"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun état'
                            options={etats}
                            value={etats.find(c => c.value === watch("idEtatInitial"))}
                            onChange={val => val != null ? setValue("idEtatInitial", val.value) : setValue("idEtatInitial", null)}
                        />
                        <small className="text-danger">{errors.idEtatInitial?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>à l'état</Form.Label>
                        <Select
                            id="idEtatFinal"
                            name="idEtatFinal"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun état'
                            options={etats}
                            value={etats.find(c => c.value === watch("idEtatFinal"))}
                            onChange={val => val != null ? setValue("idEtatFinal", val.value) : setValue("idEtatFinal", null)}
                        />
                        <small className="text-danger">{errors.idEtatFinal?.message}</small>
                    </Form.Group>

                    <hr/>

                    <Form.Group className="mb-3">
                        <Form.Label>Contraintes binaires:</Form.Label>
                        <Form.Check
                            id='fournisseurObligatoire'
                            name='fournisseurObligatoire'
                            type='switch'
                            checked={watch("fournisseurObligatoire")}
                            onClick={(e)=>{
                                setValue("fournisseurObligatoire", !watch("fournisseurObligatoire"))
                            }}
                            label='Fournisseur obligatoire'
                        />
                        <Form.Check
                            id='centreCoutsObligatoire'
                            name='centreCoutsObligatoire'
                            type='switch'
                            checked={watch("centreCoutsObligatoire")}
                            onClick={(e)=>{
                                setValue("centreCoutsObligatoire", !watch("centreCoutsObligatoire"))
                            }}
                            label='Centre de cout obligatoire'
                        />
                        <Form.Check
                            id='lieuLivraisonObligatoire'
                            name='lieuLivraisonObligatoire'
                            type='switch'
                            checked={watch("lieuLivraisonObligatoire")}
                            onClick={(e)=>{
                                setValue("lieuLivraisonObligatoire", !watch("lieuLivraisonObligatoire"))
                            }}
                            label='Lieu de livraison obligatoire'
                        />
                        <Form.Check
                            id='remarquesGeneralesObligatoires'
                            name='remarquesGeneralesObligatoires'
                            type='switch'
                            checked={watch("remarquesGeneralesObligatoires")}
                            onClick={(e)=>{
                                setValue("remarquesGeneralesObligatoires", !watch("remarquesGeneralesObligatoires"))
                            }}
                            label='Remarques générales obligatoire'
                        />
                        <Form.Check
                            id='remarquesValidationObligatoire'
                            name='remarquesValidationObligatoire'
                            type='switch'
                            checked={watch("remarquesValidationObligatoire")}
                            onClick={(e)=>{
                                setValue("remarquesValidationObligatoire", !watch("remarquesValidationObligatoire"))
                            }}
                            label='Remarques de validation obligatoire'
                        />
                        <Form.Check
                            id='referenceCommandeFournisseurObligatoire'
                            name='referenceCommandeFournisseurObligatoire'
                            type='switch'
                            checked={watch("referenceCommandeFournisseurObligatoire")}
                            onClick={(e)=>{
                                setValue("referenceCommandeFournisseurObligatoire", !watch("referenceCommandeFournisseurObligatoire"))
                            }}
                            label='Référence de commande fournisseur obligatoire'
                        />
                        <Form.Check
                            id='datePassageCommandeObligatoire'
                            name='datePassageCommandeObligatoire'
                            type='switch'
                            checked={watch("datePassageCommandeObligatoire")}
                            onClick={(e)=>{
                                setValue("datePassageCommandeObligatoire", !watch("datePassageCommandeObligatoire"))
                            }}
                            label='Date du passage de la commande obligatoire'
                        />
                        <Form.Check
                            id='datePrevueLivraisonObligatoire'
                            name='datePrevueLivraisonObligatoire'
                            type='switch'
                            checked={watch("datePrevueLivraisonObligatoire")}
                            onClick={(e)=>{
                                setValue("datePrevueLivraisonObligatoire", !watch("datePrevueLivraisonObligatoire"))
                            }}
                            label='Date prévisionnelle de livraison obligatoire'
                        />
                        <Form.Check
                            id='dateLivraisonEffectiveObligatoire'
                            name='dateLivraisonEffectiveObligatoire'
                            type='switch'
                            checked={watch("dateLivraisonEffectiveObligatoire")}
                            onClick={(e)=>{
                                setValue("dateLivraisonEffectiveObligatoire", !watch("dateLivraisonEffectiveObligatoire"))
                            }}
                            label='Date effective de livraison obligatoire'
                        />
                        <Form.Check
                            id='remarquesLivraisonsObligatoire'
                            name='remarquesLivraisonsObligatoire'
                            type='switch'
                            checked={watch("remarquesLivraisonsObligatoire")}
                            onClick={(e)=>{
                                setValue("remarquesLivraisonsObligatoire", !watch("remarquesLivraisonsObligatoire"))
                            }}
                            label='Remarques sur la livraison obligatoire'
                        />
                        <Form.Check
                            id='integrationStockObligatoire'
                            name='integrationStockObligatoire'
                            type='switch'
                            checked={watch("integrationStockObligatoire")}
                            onClick={(e)=>{
                                setValue("integrationStockObligatoire", !watch("integrationStockObligatoire"))
                            }}
                            label='Intégration de tout le matériel dans le stock obligatoire'
                        />
                    </Form.Group>

                    <hr/>

                    <Form.Group className="mb-3">
                        <Form.Label>Contraintes sur les pièces jointes:</Form.Label>
                        <Select
                            id="idTypeDocumentObligatoire"
                            name="idTypeDocumentObligatoire"
                            size="sm"
                            className='mb-1'
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun type'
                            options={typesDocuments}
                            value={typesDocuments.find(c => c.value === watch("idTypeDocumentObligatoire"))}
                            onChange={val => val != null ? setValue("idTypeDocumentObligatoire", val.value) : setValue("idTypeDocumentObligatoire", null)}
                        />
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Quantité requise"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="1" name='nbTypeDocumentObligatoire' id='nbTypeDocumentObligatoire' {...register('nbTypeDocumentObligatoire')}/>
                            <small className="text-danger">{errors.nbTypeDocumentObligatoire?.message}</small>
                        </FloatingLabel>
                    </Form.Group>
                    
                    <hr/>

                    <Form.Group className="mb-3">
                        <Form.Label>Contraintes numériques:</Form.Label>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Nombre minimal de demandeurs"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="1" name='minDemandeurs' id='minDemandeurs' {...register('minDemandeurs')}/>
                            <small className="text-danger">{errors.minDemandeurs?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Nombre minimal de personnes affectées"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="1" name='minAffectees' id='minAffectees' {...register('minAffectees')}/>
                            <small className="text-danger">{errors.minAffectees?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Nombre minimal d'observateurs"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="1" name='minObservateurs' id='minObservateurs' {...register('minObservateurs')}/>
                            <small className="text-danger">{errors.minObservateurs?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Nombre minimal de valideurs"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="1" name='minValideurs' id='minValideurs' {...register('minValideurs')}/>
                            <small className="text-danger">{errors.minValideurs?.message}</small>
                        </FloatingLabel>

                        <FloatingLabel
                            controlId="floatingInput"
                            label="Nombre minimal matériels commandés"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="1" name='minQttMateriel' id='minQttMateriel' {...register('minQttMateriel')}/>
                            <small className="text-danger">{errors.minQttMateriel?.message}</small>
                        </FloatingLabel>

                        <FloatingLabel
                            controlId="floatingInput"
                            label="Montant minimal de la commande"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.01" name='minMontant' id='minMontant' {...register('minMontant')}/>
                            <small className="text-danger">{errors.minMontant?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Montant maximal de la commande"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.01" name='maxMontant' id='maxMontant' {...register('maxMontant')}/>
                            <small className="text-danger">{errors.maxMontant?.message}</small>
                        </FloatingLabel>
                    </Form.Group>

                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Créer la règle'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <GPMtable
            columns={colonnes}
            data={reglesCmd}
            topButtonShow={true}
            topButton={
                <IconButton
                    icon='plus'
                    size = 'sm'
                    variant="outline-success"
                    onClick={handleShowOffCanevas}
                >Nouvelle règle</IconButton>
            }
        />
    </>)
};

ConfigGeneraleTabReglesCmd.propTypes = {};

export default ConfigGeneraleTabReglesCmd;