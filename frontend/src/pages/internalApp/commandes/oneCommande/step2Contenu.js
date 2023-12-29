import React, {useState, useEffect} from 'react';
import { Link, useNavigate, } from 'react-router-dom';
import { Card, Offcanvas, Button, Form, Tab, Nav, Row, Col, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import Select from 'react-select'
import findOptionForSelect from 'helpers/selectHelpers';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeStep2ContenuCheck } from 'helpers/yupValidationSchema';

const OneCommandeStep2Contenu = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    const colonnes = [
        {accessor: 'libelleMateriel'             , Header: 'Matériel'},
        {accessor: 'referenceProduitFournisseur' , Header: 'Référence'},
        {accessor: 'prixProduitTTC'              , Header: 'Prix Unitaire TTC'},
        {accessor: 'quantiteCommande'            , Header: 'Quantité'},
        {accessor: 'total'                       , Header: 'Sous Total'},
        {accessor: 'actions'                     , Header: 'Actions', isHidden: forceReadOnly || !HabilitationService.habilitations.commande_ajout},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of commande.materiels)
        {
            tempTable.push({
                libelleMateriel: item.libelleMateriel,
                referenceProduitFournisseur: item.referenceProduitFournisseur,
                prixProduitTTC: item.prixProduitTTC + ' €',
                quantiteCommande: item.quantiteCommande,
                total: Math.round(item.prixProduitTTC * item.quantiteCommande*100)/100 + ' €',
                actions: !forceReadOnly && HabilitationService.habilitations.commande_ajout ? <>
                    <IconButton
                        icon='pen'
                        size = 'sm'
                        variant="outline-warning"
                        className="me-1"
                        onClick={()=>{handleShowOffCanevas(item.idCommandeMateriel)}}
                    />
                    <IconButton
                        icon='trash'
                        size = 'sm'
                        variant="outline-danger"
                        className="me-1"
                        onClick={()=>{handleShowDeleteModal(item.idCommandeMateriel)}}
                    />
                </> : null,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [commande.materiels])


    /*offcanevas edit*/
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdCommandeMateriel, setOffCanevasIdCommandeMateriel] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeStep2ContenuCheck),
    });
    const [autoCalcul, setAutoCalcul] = useState(true);
    const [catalogueParFournisseurs, setCatalogueParFournisseurs] = useState([]);

    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setOffCanevasIdCommandeMateriel();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        try {
            setOffCanevasIdCommandeMateriel(id);
            setLoading(true);
            setShowOffCanevas(true);
            
            if(id > 0)
            {
                setAutoCalcul(false);
                let oneItemFromArray = commande.materiels.filter(item => item.idCommandeMateriel == id)[0];
                setValue("idMaterielCatalogue", oneItemFromArray.idMaterielCatalogue);
                setValue("referenceProduitFournisseurNeeded", oneItemFromArray.idMaterielCatalogue > 0 ? false : true);
                setValue("quantiteCommande", oneItemFromArray.quantiteCommande);
                setValue("referenceProduitFournisseur", oneItemFromArray.referenceProduitFournisseur);
                setValue("remiseProduit", oneItemFromArray.remiseProduit);
                setValue("prixProduitHT", oneItemFromArray.prixProduitHT);
                setValue("taxeProduit", oneItemFromArray.taxeProduit);
                setValue("prixProduitTTC", oneItemFromArray.prixProduitTTC);
                setValue("remarqueArticle", oneItemFromArray.remarqueArticle);
            }else{
                setAutoCalcul(true);
                setValue("referenceProduitFournisseurNeeded", true);
            }

            let getFullCatalogue  = await Axios.get('/select/getCatalogueMaterielFull');
            if(commande.detailsCommande.idFournisseur == null)
            {
                setCatalogueParFournisseurs([
                    {
                        label: "Tous les fournisseurs confondus",
                        options: getFullCatalogue.data,
                    }
                ])
            }else{
                setCatalogueParFournisseurs([
                    {
                        label: 'Habituellement chez '+commande.detailsCommande.nomFournisseur,
                        options: getFullCatalogue.data.filter(cat => cat.idFournisseur == commande.detailsCommande.idFournisseur),
                    },
                    {
                        label: "Habituellement chez d'autres fournisseurs",
                        options: getFullCatalogue.data.filter(cat => cat.idFournisseur != commande.detailsCommande.idFournisseur),
                    }
                ])
            }

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const ajouterModifierItem = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdCommandeMateriel > 0)
            {
                const response = await Axios.post('/commandes/updateMateriels',{
                    idCommandeMateriel: offCanevasIdCommandeMateriel,
                    idCommande: idCommande,
                    idMaterielCatalogue: data.idMaterielCatalogue,
                    quantiteCommande: data.quantiteCommande,
                    referenceProduitFournisseur: data.referenceProduitFournisseur,
                    remiseProduit: data.remiseProduit,
                    prixProduitHT: data.prixProduitHT,
                    taxeProduit: data.taxeProduit,
                    prixProduitTTC: data.prixProduitTTC,
                    remarqueArticle: data.remarqueArticle,
                });
            }else{
                const response = await Axios.post('/commandes/addMateriels',{
                    idCommande: idCommande,
                    idMaterielCatalogue: data.idMaterielCatalogue,
                    quantiteCommande: data.quantiteCommande,
                    referenceProduitFournisseur: data.referenceProduitFournisseur,
                    remiseProduit: data.remiseProduit,
                    prixProduitHT: data.prixProduitHT,
                    taxeProduit: data.taxeProduit,
                    prixProduitTTC: data.prixProduitTTC,
                    remarqueArticle: data.remarqueArticle,
                });
            }

            setPageNeedsRefresh(true);
            handleCloseOffCanevas();
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        if(autoCalcul && watch("prixProduitHT") && watch("prixProduitHT") != null)
        {
            if(watch("taxeProduit") && watch("taxeProduit") != null)
            {
                let prixProduitHT = Number(watch("prixProduitHT"));
                let taxeProduit = Number(watch("taxeProduit"));
                let prixProduitTTC = prixProduitHT + (prixProduitHT*(taxeProduit/100));
                prixProduitTTC = Math.round(prixProduitTTC*100)/100;
                setValue("prixProduitTTC", prixProduitTTC);
            }
        }
    },[
        watch("prixProduitHT"),
    ])
    useEffect(()=>{
        if(autoCalcul && watch("taxeProduit") && watch("taxeProduit") != null)
        {
            if(watch("prixProduitHT") && watch("prixProduitHT") != null)
            {
                let prixProduitHT = Number(watch("prixProduitHT"));
                let taxeProduit = Number(watch("taxeProduit"));
                let prixProduitTTC = prixProduitHT + (prixProduitHT*(taxeProduit/100));
                prixProduitTTC = Math.round(prixProduitTTC*100)/100;
                setValue("prixProduitTTC", prixProduitTTC);
            }
        }
    },[
        watch("taxeProduit"),
    ])

    useEffect(()=>{
        setValue("referenceProduitFournisseurNeeded", watch("idMaterielCatalogue") > 0 ? false : true);
        if(!watch("idMaterielCatalogue") || watch("idMaterielCatalogue") == null)
        {
            if(watch("referenceProduitFournisseur") == null || watch("referenceProduitFournisseur") == "")
            {
                setValue("referenceProduitFournisseur", "Frais de port");
            }
        }else{
            if(watch("referenceProduitFournisseur") == "Frais de port")
            {
                setValue("referenceProduitFournisseur", null);
            }
        }
    },[watch("idMaterielCatalogue")])

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdCommandeMateriel, setDeleteModalIdCommandeMateriel] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdCommandeMateriel();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdCommandeMateriel(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/commandes/removeMateriels',{
                idCommandeMateriel: deleteModalIdCommandeMateriel,
                idCommande: idCommande,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return (<>
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdCommandeMateriel > 0 ? 'Modification' : 'Ajout'} d'un matériel</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierItem)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Matériel</Form.Label>
                        <Select
                            id="idMaterielCatalogue"
                            name="idMaterielCatalogue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun matériel'
                            options={catalogueParFournisseurs}
                            value={findOptionForSelect(catalogueParFournisseurs, watch("idMaterielCatalogue"))}
                            onChange={val => val != null ? setValue("idMaterielCatalogue", val.value) : setValue("idMaterielCatalogue", null)}
                        />
                        <small className="text-danger">{errors.idMaterielCatalogue?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Référence fournisseur</Form.Label>
                        <Form.Control size="sm" type="text" name='referenceProduitFournisseur' id='referenceProduitFournisseur' {...register('referenceProduitFournisseur')}/>
                        <small className="text-danger">{errors.referenceProduitFournisseur?.message}</small>
                    </Form.Group>

                    <hr/>

                    <Form.Group className="mb-3">
                        <Form.Label>Quantité</Form.Label>
                        <Form.Control size="sm" type="number" min="1" name='quantiteCommande' id='quantiteCommande' {...register('quantiteCommande')}/>
                        <small className="text-danger">{errors.quantiteCommande?.message}</small>
                    </Form.Group>
                    
                    <Row>
                        <Col md={6}>
                            <Form.Group className="mb-3">
                                <Form.Label>Prix unitaire HT (€)</Form.Label>
                                <Form.Control size="sm" type="number" min="0" step="0.01" name='prixProduitHT' id='prixProduitHT' {...register('prixProduitHT')}/>
                                <small className="text-danger">{errors.prixProduitHT?.message}</small>
                            </Form.Group>
                        </Col>
                        <Col md={6}>
                            <Form.Group className="mb-3">
                                <Form.Label>Taxe (%)</Form.Label>
                                <Form.Control size="sm" type="number" min="0" step="0.01" name='taxeProduit' id='taxeProduit' {...register('taxeProduit')}/>
                                <small className="text-danger">{errors.taxeProduit?.message}</small>
                            </Form.Group>
                        </Col>
                        <Col md={6}>
                            <Form.Group className="mb-3">
                                <Form.Label>Prix unitaire TTC (€)</Form.Label>
                                <Form.Control size="sm" type="number" step="0.01" name='prixProduitTTC' id='prixProduitTTC' {...register('prixProduitTTC')}/>
                                <small className="text-danger">{errors.prixProduitTTC?.message}</small>
                            </Form.Group>
                        </Col>
                        <Col md={6}>
                            <Form.Group className="mb-3">
                                <Form.Label>Remise</Form.Label>
                                <Form.Control size="sm" type="number" min="0" step="0.01" name='remiseProduit' id='remiseProduit' {...register('remiseProduit')}/>
                                <small className="text-danger">{errors.remiseProduit?.message}</small>
                            </Form.Group>
                        </Col>
                        <Col md={12}>
                            <Form.Group className="mb-3">
                                <Form.Label>SousTotal</Form.Label>
                                <Form.Control size="sm" type="number" value={Math.round(Number(watch("prixProduitTTC"))*Number(watch("quantiteCommande"))*100)/100 || null} disabled/>
                            </Form.Group>
                        </Col>
                        <Col md={12}>
                            <Form.Check
                                type='switch'
                                id='autoCalcul'
                                name='autoCalcul'
                                label='Calculs automatiques'
                                checked={autoCalcul}
                                onClick={(e)=>{setAutoCalcul(!autoCalcul)}}
                            />
                        </Col>
                    </Row>

                    <hr/>

                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"remarqueArticle"} id={"remarqueArticle"} {...register("remarqueArticle")}/>
                        <small className="text-danger">{errors.remarqueArticle?.message}</small>
                    </Form.Group>

                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un élément de la commande (id: {deleteModalIdCommandeMateriel}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <div className='mt-2 mb-2'>
            <GPMtable
                columns={colonnes}
                data={lignes}
                topButtonShow={true}
                topButton={
                    !forceReadOnly && HabilitationService.habilitations.commande_ajout ? <>
                        <IconButton
                            className='me-3 mb-1'
                            icon='plus'
                            size = 'sm'
                            variant="outline-success"
                            onClick={()=>{handleShowOffCanevas(0)}}
                        >Ajouter un matériel</IconButton>
                        <SoftBadge className='mb-1'>Total: {commande.detailsCommande.montantTotal} €</SoftBadge>
                    </>:<SoftBadge className='mb-1'>Total: {commande.detailsCommande.montantTotal} €</SoftBadge>
                }
            />
        </div>
    </>);
};

OneCommandeStep2Contenu.propTypes = {};

export default OneCommandeStep2Contenu;
