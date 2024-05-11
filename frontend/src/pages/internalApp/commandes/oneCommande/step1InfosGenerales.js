import React, {useState, useEffect} from 'react';
import { useNavigate, } from 'react-router-dom';
import { Button, Form, Row, Col, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconButton from 'components/common/IconButton';
import Select from 'react-select'
import findOptionForSelect from 'helpers/selectHelpers';
import { commandesDelete } from 'helpers/deleteModalWarningContent';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeStep1InfosGeneralesCheck } from 'helpers/yupValidationSchema';

const OneCommandeStep1InfosGenerales = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeStep1InfosGeneralesCheck),
    });
    
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const [readOnly, setReadOnly] = useState(true);

    const [tousUsers, setTousUsers] = useState([]);
    const [tousUsersActifs, setTousUsersActifs] = useState([]);
    const [usersAffectation, setUsersAffectation] = useState([]);
    const [fournisseurs, setFournisseurs] = useState([]);
    const [etats, setEtats] = useState([]);
    const [centresCouts, setCentresCouts] = useState([]);
    const [lieux, setLieux] = useState([]);

    const initView = async () => {
        try {
            setValue("nomCommande", commande.detailsCommande.nomCommande);
            setValue("idEtat", commande.detailsCommande.idEtat);
            setValue("idCentreDeCout", commande.detailsCommande.idCentreDeCout);
            setValue("idFournisseur", commande.detailsCommande.idFournisseur);
            setValue("idLieuLivraison", commande.detailsCommande.idLieuLivraison);
            setValue("dateCreation", new Date(commande.detailsCommande.dateCreation));
            setValue("dateCloture", commande.detailsCommande.dateCloture ? new Date(commande.detailsCommande.dateCloture) : null);
            setValue("remarquesGenerales", commande.detailsCommande.remarquesGenerales);

            setValue("idDemandeur", commande.demandeurs);
            setValue("idObservateur", commande.observateurs);
            setValue("idAffectee", commande.affectees);

            let getData = await Axios.get('/select/getPersonnes');
            setTousUsers(getData.data);
            getData = await Axios.get('/select/getActivePersonnes');
            setTousUsersActifs(getData.data);
            getData = await Axios.get('/select/getActivePersonnesForCmdAffectation');
            setUsersAffectation(getData.data);
            getData = await Axios.get('/select/getFournisseurs');
            setFournisseurs(getData.data);
            getData = await Axios.get('/select/getEtatsCommandes');
            setEtats(getData.data);
            getData = await Axios.get('/select/getCentresCouts');
            setCentresCouts(getData.data);
            getData = await Axios.get('/select/getLieux');
            setLieux(getData.data);

            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    const calculateReadOnly = () => {
        if(forceReadOnly || isLoading){
            setReadOnly(true);
        }else{
            setReadOnly(!HabilitationService.habilitations.commande_ajout);
        }
    }

    useEffect(()=>{
        initView();
    },[commande])

    useEffect(()=>{
        calculateReadOnly();
    },[commande, forceReadOnly, isLoading])

    const saveForm = async (data) => {
        try {
            setLoading(true);

            await Axios.post('/commandes/updateInfoGenerales',{
                idCommande: idCommande,
                nomCommande: data.nomCommande,
                idCentreDeCout: data.idCentreDeCout,
                idFournisseur: data.idFournisseur,
                idLieuLivraison: data.idLieuLivraison,
                remarquesGenerales: data.remarquesGenerales,
                idDemandeur: data.idDemandeur,
                idObservateur: data.idObservateur,
                idAffectee: data.idAffectee,
            })
            
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* DELETE MODAL */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const handleCloseDeleteModal = () => {
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = () => {
        setShowDeleteModal(true);
    };
    const abandonnerCommande = async () => {
        try {
            setLoading(true);
            
            await Axios.post('/commandes/abandonnerCommande',{
                idCommande: idCommande,
            })

            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
        } catch (error) {
            console.log(error)
        }
    }
    const navigate = useNavigate();
    const supprimerCommande = async () => {
        try {
            setLoading(true);
            
            await Axios.post('/commandes/commandesDelete',{
                idCommande: idCommande,
            })

            navigate('/commandes');
            handleCloseDeleteModal();
        } catch (error) {
            console.log(error)
        }
    }

    if(readyToDisplay)
    {
        return (<>
            <Form onSubmit={handleSubmit(saveForm)} className='mt-1'>
                <Form.Group className="mb-3">
                    <Form.Label>Titre de la commande</Form.Label>
                    <Form.Control size="sm" type="text" name='nomCommande' id='nomCommande' {...register('nomCommande')} disabled={readOnly}/>
                    <small className="text-danger">{errors.nomCommande?.message}</small>
                </Form.Group>
                <Row>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Fournisseur</Form.Label>
                            <Select
                                id="idFournisseur"
                                name="idFournisseur"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={readOnly}
                                placeholder='Aucun fournisseur'
                                options={fournisseurs}
                                value={fournisseurs.find(c => c.value === watch("idFournisseur"))}
                                onChange={val => val != null ? setValue("idFournisseur", val.value) : setValue("idFournisseur", null)}
                            />
                            <small className="text-danger">{errors.idFournisseur?.message}</small>
                        </Form.Group>
                    </Col>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Etat</Form.Label>
                            <Select
                                id="idEtat"
                                name="idEtat"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isDisabled={true}
                                options={etats}
                                value={etats.find(c => c.value === watch("idEtat"))}
                                onChange={val => val != null ? setValue("idEtat", val.value) : setValue("idEtat", null)}
                            />
                            <small className="text-danger">{errors.idEtat?.message}</small>
                        </Form.Group>
                    </Col>
                </Row>
                <hr/>
                <Row>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Demandeur</Form.Label>
                            <Select
                                id="idDemandeur"
                                name="idDemandeur"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={readOnly}
                                placeholder='Aucun demandeur'
                                options={!readOnly ? tousUsersActifs : tousUsers}
                                value={watch("idDemandeur")}
                                onChange={selected => setValue("idDemandeur", selected)}
                                isMulti
                            />
                            <small className="text-danger">{errors.idDemandeur?.message}</small>
                        </Form.Group>
                    </Col>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Affectation</Form.Label>
                            <Select
                                id="idAffectee"
                                name="idAffectee"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={readOnly}
                                placeholder='Aucune personne affectée'
                                options={!readOnly ? tousUsersActifs : usersAffectation}
                                value={watch("idAffectee")}
                                onChange={selected => setValue("idAffectee", selected)}
                                isMulti
                            />
                            <small className="text-danger">{errors.idAffectee?.message}</small>
                        </Form.Group>
                    </Col>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Observateurs</Form.Label>
                            <Select
                                id="idObservateur"
                                name="idObservateur"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={readOnly}
                                placeholder='Aucun observateur à notifier'
                                options={!readOnly ? tousUsersActifs : tousUsers}
                                value={watch("idObservateur")}
                                onChange={selected => setValue("idObservateur", selected)}
                                isMulti
                            />
                            <small className="text-danger">{errors.idObservateur?.message}</small>
                        </Form.Group>
                    </Col>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Valideurs potentiels</Form.Label>
                            <Select
                                id="idValideur"
                                name="idValideur"
                                size="sm"
                                classNamePrefix="react-select"
                                isDisabled={true}
                                placeholder='Aucun valideur'
                                options={tousUsers}
                                value={commande.valideurs}
                                isMulti
                            />
                            <small className="text-danger">{errors.nomCommande?.message}</small>
                        </Form.Group>
                    </Col>
                </Row>
                <hr/>
                <Row>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Centre de couts</Form.Label>
                            <Select
                                id="idCentreDeCout"
                                name="idCentreDeCout"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={readOnly}
                                placeholder='Aucun centre de cout'
                                options={centresCouts}
                                value={findOptionForSelect(centresCouts, watch("idCentreDeCout"))}
                                onChange={val => val != null ? setValue("idCentreDeCout", val.value) : setValue("idCentreDeCout", null)}
                            />
                            <small className="text-danger">{errors.idCentreDeCout?.message}</small>
                        </Form.Group>
                    </Col>
                    <Col md={6}>
                        <Form.Group className="mb-3">
                            <Form.Label>Lieu de livraison</Form.Label>
                            <Select
                                id="idLieuLivraison"
                                name="idLieuLivraison"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={readOnly}
                                placeholder='Aucun lieu selectionné'
                                options={lieux}
                                value={lieux.find(c => c.value === watch("idLieuLivraison"))}
                                onChange={val => val != null ? setValue("idLieuLivraison", val.value) : setValue("idLieuLivraison", null)}
                            />
                            <small className="text-danger">{errors.idLieuLivraison?.message}</small>
                        </Form.Group>
                    </Col>
                </Row>
                <hr/>
                <Form.Group className="mb-3">
                    <Form.Label>Remarques générales</Form.Label>
                    <Form.Control size="sm" as="textarea" rows={5} name="remarquesGenerales" id="remarquesGenerales" {...register("remarquesGenerales")} disabled={readOnly}/>
                    <small className="text-danger">{errors.remarquesGenerales?.message}</small>
                </Form.Group>

                <div className="d-grid gap-2 mt-3 mb-1">
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={readOnly}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </div>

                {HabilitationService.habilitations.commande_abandonner ?
                    <div className="d-grid gap-2 mt-3 mb-1">
                        <Button variant='outline-danger' className='me-2 mb-1' onClick={handleShowDeleteModal}>{isLoading ? 'Patientez...' : 'Abandonner/Supprimer'}</Button>
                    </div>
                : null}
            </Form>

            <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
                <Modal.Header>
                    <Modal.Title>Abandon ou Suppression</Modal.Title>
                    <FalconCloseButton onClick={handleCloseDeleteModal}/>
                </Modal.Header>
                <Modal.Body>
                    {commandesDelete}
                    <Row className='mt-2'>
                        <Col md={6}>
                            <center>
                                <IconButton
                                    variant='outline-danger'
                                    icon='ban'
                                    onClick={abandonnerCommande}
                                    disabled={commande.detailsCommande.idEtat >= 7}
                                >Abandonner</IconButton>
                            </center>
                        </Col>
                        <Col md={6}>
                            <center>
                                <IconButton
                                    variant='outline-danger'
                                    icon='trash'
                                    onClick={supprimerCommande}
                                >Supprimer</IconButton>
                            </center>
                        </Col>
                    </Row>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleCloseDeleteModal}>
                        Annuler
                    </Button>
                </Modal.Footer>
            </Modal>
        </>);
    }else{
        return <LoaderInfiniteLoop />
    }
};

OneCommandeStep1InfosGenerales.propTypes = {};

export default OneCommandeStep1InfosGenerales;
