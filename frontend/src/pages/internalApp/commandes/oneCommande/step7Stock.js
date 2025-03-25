import React, {useState} from 'react';
import { Button, Form, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import {
    commandeStep7StockCheckOPE,
    commandeStep7StockCheckTEN,
    commandeStep7StockCheckVEH,
    commandeStep7StockCheckVHF,
} from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const OneCommandeStep7Stock = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    let okToClose = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 7)[0]?.passagePossible && !forceReadOnly;
    const [isLoading, setLoading] = useState(false);

    const colonnes = [
        {
            accessor: 'libelleMateriel',
            Header: 'Matériel',
        },
        {
            accessor: 'referenceProduitFournisseur',
            Header: 'Référence',
        },
        {
            accessor: 'quantiteCommande',
            Header: 'Intégrer au stock',
            isHidden: forceReadOnly,
            Cell: ({ value, row }) => {
                return(
                    <>
                        {!forceReadOnly && row.original.modules_ope && HabilitationService.habilitations.reserve_cmdVersReserve && row.original.idMaterielCatalogue != null ?
                            <IconButton
                                icon={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0 ? 'check' : 'forward'}
                                size = 'sm'
                                variant="outline-success"
                                className="me-1"
                                disabled={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0}
                                onClick={()=>{handleShowReserveTransfertModalOPE(row.original.idCommandeMateriel)}}
                            >{row.original.quantiteCommande - row.original.quantiteAtransferer} / {row.original.quantiteCommande} intégrés</IconButton>
                        : null}

                        {!forceReadOnly && row.original.modules_vehicules && HabilitationService.habilitations.reserve_cmdVersReserve && row.original.idMaterielCatalogue != null ?
                            <IconButton
                                icon={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0 ? 'check' : 'forward'}
                                size = 'sm'
                                variant="outline-success"
                                className="me-1"
                                disabled={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0}
                                onClick={()=>{handleShowReserveTransfertModalVEH(row.original.idCommandeMateriel)}}
                            >{row.original.quantiteCommande - row.original.quantiteAtransferer} / {row.original.quantiteCommande} intégrés</IconButton>
                        : null}

                        {!forceReadOnly && row.original.modules_tenues && HabilitationService.habilitations.reserve_cmdVersReserve && row.original.idMaterielCatalogue != null ?
                            <IconButton
                                icon={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0 ? 'check' : 'forward'}
                                size = 'sm'
                                variant="outline-success"
                                className="me-1"
                                disabled={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0}
                                onClick={()=>{handleShowReserveTransfertModalTEN(row.original.idCommandeMateriel)}}
                            >{row.original.quantiteCommande - row.original.quantiteAtransferer} / {row.original.quantiteCommande} intégrés</IconButton>
                        : null}

                        {!forceReadOnly && row.original.modules_vhf && HabilitationService.habilitations.reserve_cmdVersReserve && row.original.idMaterielCatalogue != null ?
                            <IconButton
                                icon={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0 ? 'check' : 'forward'}
                                size = 'sm'
                                variant="outline-success"
                                className="me-1"
                                disabled={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0}
                                onClick={()=>{handleShowReserveTransfertModalVHF(row.original.idCommandeMateriel)}}
                            >{row.original.quantiteCommande - row.original.quantiteAtransferer} / {row.original.quantiteCommande} intégrés</IconButton>
                        : null}

                        {!forceReadOnly && HabilitationService.habilitations.reserve_cmdVersReserve && row.original.quantiteAtransferer > 0 ?
                            <IconButton
                                icon='hand-holding-medical'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowManualTransfertModal(row.original.idCommandeMateriel)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    /* Modal transfert manuel */
    const [showManualTransfertModal, setShowManualTransfertModal] = useState(false);
    const [manualTransfertModalIdCommandeMateriel, setManualTransfertModalIdCommandeMateriel] = useState();

    const handleCloseManualTransfertModal = () => {
        setManualTransfertModalIdCommandeMateriel();
        setShowManualTransfertModal(false);
        setLoading(false);
    };
    const handleShowManualTransfertModal = (id) => {
        setManualTransfertModalIdCommandeMateriel(id);
        setShowManualTransfertModal(true);
    };

    const transfertManuel = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/commandes/transfertManuel',{
                idCommandeMateriel: manualTransfertModalIdCommandeMateriel,
                idCommande: idCommande,
            });
            
            setPageNeedsRefresh(true);
            handleCloseManualTransfertModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* Commun aux modal de transfert */
    const [reserveTransfertModalIdCommandeMateriel, setReserveTransfertModalIdCommandeMateriel] = useState();
    const [reserves, setReserves] = useState([]);

    /* Modal transfert vers réserve OPE */
    const { register: registerOPE, handleSubmit: handleSubmitOPE, formState: { errors: errorsOPE }, setValue: setValueOPE, reset: resetOPE, watch: watchOPE } = useForm({
        resolver: yupResolver(commandeStep7StockCheckOPE),
    });
    const [showReserveTransfertModalOPE, setShowReserveTransfertModalOPE] = useState(false);
    
    const handleCloseReserveTransfertModalOPE = () => {
        setReserveTransfertModalIdCommandeMateriel();
        setShowReserveTransfertModalOPE(false);
        setLoading(false);
        resetOPE();
        setReserves([]);
    };
    
    const handleShowReserveTransfertModalOPE = async (id) => {
        try {
            setReserveTransfertModalIdCommandeMateriel(id);
            setShowReserveTransfertModalOPE(true);
            setLoading(true);

            let oneElement = commande.materiels.filter(item => item.idCommandeMateriel == id)[0];

            let getData = await Axios.post('/transferts/getReservesOpeForOneIntegration',{
                idMaterielCatalogue: oneElement.idMaterielCatalogue
            });
            setReserves(getData.data);
            setValueOPE("resteATransferer", oneElement.quantiteAtransferer);
            setValueOPE("qttTransfert", oneElement.quantiteAtransferer);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const enregistrerTransfertOPE = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/transferts/enregistrerTransfertOPE',{
                idCommandeMateriel: reserveTransfertModalIdCommandeMateriel,
                idCommande: idCommande,
                idReserveElement: data.idReserveElement,
                qttTransfert: data.qttTransfert,
                peremptionCmd: data.peremptionCmd,
            });
            
            setPageNeedsRefresh(true);
            handleCloseReserveTransfertModalOPE();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* Modal transfert vers réserve TENUES */
    const { register: registerTEN, handleSubmit: handleSubmitTEN, formState: { errors: errorsTEN }, setValue: setValueTEN, reset: resetTEN, watch: watchTEN } = useForm({
        resolver: yupResolver(commandeStep7StockCheckTEN),
    });
    const [showReserveTransfertModalTEN, setShowReserveTransfertModalTEN] = useState(false);
    
    const handleCloseReserveTransfertModalTEN = () => {
        setReserveTransfertModalIdCommandeMateriel();
        setShowReserveTransfertModalTEN(false);
        setLoading(false);
        resetTEN();
        setReserves([]);
    };
    
    const handleShowReserveTransfertModalTEN = async (id) => {
        try {
            setReserveTransfertModalIdCommandeMateriel(id);
            setShowReserveTransfertModalTEN(true);
            setLoading(true);

            let oneElement = commande.materiels.filter(item => item.idCommandeMateriel == id)[0];

            let getData = await Axios.post('/transferts/getReservesTenForOneIntegration',{
                idMaterielCatalogue: oneElement.idMaterielCatalogue
            });
            setReserves(getData.data);
            setValueTEN("resteATransferer", oneElement.quantiteAtransferer);
            setValueTEN("qttTransfert", oneElement.quantiteAtransferer);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const enregistrerTransfertTEN = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/transferts/enregistrerTransfertTEN',{
                idCommandeMateriel: reserveTransfertModalIdCommandeMateriel,
                idCommande: idCommande,
                idCatalogueTenue: data.idCatalogueTenue,
                qttTransfert: data.qttTransfert,
                peremptionCmd: data.peremptionCmd,
            });
            
            setPageNeedsRefresh(true);
            handleCloseReserveTransfertModalTEN();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* Modal transfert vers réserve VEHICULES STOCK */
    const { register: registerVEH, handleSubmit: handleSubmitVEH, formState: { errors: errorsVEH }, setValue: setValueVEH, reset: resetVEH, watch: watchVEH } = useForm({
        resolver: yupResolver(commandeStep7StockCheckVEH),
    });
    const [showReserveTransfertModalVEH, setShowReserveTransfertModalVEH] = useState(false);
    
    const handleCloseReserveTransfertModalVEH = () => {
        setReserveTransfertModalIdCommandeMateriel();
        setShowReserveTransfertModalVEH(false);
        setLoading(false);
        resetVEH();
        setReserves([]);
    };
    
    const handleShowReserveTransfertModalVEH = async (id) => {
        try {
            setReserveTransfertModalIdCommandeMateriel(id);
            setShowReserveTransfertModalVEH(true);
            setLoading(true);

            let oneElement = commande.materiels.filter(item => item.idCommandeMateriel == id)[0];

            let getData = await Axios.post('/transferts/getReservesVehiculesForOneIntegration',{
                idMaterielCatalogue: oneElement.idMaterielCatalogue
            });
            setReserves(getData.data);
            setValueVEH("resteATransferer", oneElement.quantiteAtransferer);
            setValueVEH("qttTransfert", oneElement.quantiteAtransferer);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const enregistrerTransfertVEH = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/transferts/enregistrerTransfertVehicules',{
                idCommandeMateriel: reserveTransfertModalIdCommandeMateriel,
                idCommande: idCommande,
                idVehiculesStock: data.idVehiculesStock,
                qttTransfert: data.qttTransfert,
                peremptionCmd: data.peremptionCmd,
            });
            
            setPageNeedsRefresh(true);
            handleCloseReserveTransfertModalVEH();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* Modal transfert vers réserve VHF STOCK */
    const { register: registerVHF, handleSubmit: handleSubmitVHF, formState: { errors: errorsVHF }, setValue: setValueVHF, reset: resetVHF, watch: watchVHF } = useForm({
        resolver: yupResolver(commandeStep7StockCheckVHF),
    });
    const [showReserveTransfertModalVHF, setShowReserveTransfertModalVHF] = useState(false);
    
    const handleCloseReserveTransfertModalVHF = () => {
        setReserveTransfertModalIdCommandeMateriel();
        setShowReserveTransfertModalVHF(false);
        setLoading(false);
        resetVHF();
        setReserves([]);
    };
    
    const handleShowReserveTransfertModalVHF = async (id) => {
        try {
            setReserveTransfertModalIdCommandeMateriel(id);
            setShowReserveTransfertModalVHF(true);
            setLoading(true);

            let oneElement = commande.materiels.filter(item => item.idCommandeMateriel == id)[0];

            let getData = await Axios.post('/transferts/getReservesVhfForOneIntegration',{
                idMaterielCatalogue: oneElement.idMaterielCatalogue
            });
            setReserves(getData.data);
            setValueVHF("resteATransferer", oneElement.quantiteAtransferer);
            setValueVHF("qttTransfert", oneElement.quantiteAtransferer);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const enregistrerTransfertVHF = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/transferts/enregistrerTransfertVhf',{
                idCommandeMateriel: reserveTransfertModalIdCommandeMateriel,
                idCommande: idCommande,
                idVhfStock: data.idVhfStock,
                qttTransfert: data.qttTransfert,
                peremptionCmd: data.peremptionCmd,
            });
            
            setPageNeedsRefresh(true);
            handleCloseReserveTransfertModalVHF();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }


    /* Clore */
    const cloreCommande = async () => {
        try {
            await Axios.post('/commandes/cloreCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <Modal show={showManualTransfertModal} onHide={handleCloseManualTransfertModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Intégration manuelle</Modal.Title>
                <FalconCloseButton onClick={handleCloseManualTransfertModal}/>
            </Modal.Header>
            <Modal.Body>
                Confirmez vous avoir bien intégré ce matériel manuellement dans une réserve et avoir mis à jour le stock dans {window.__ENV__.APP_NAME} ? Ou alors confirmez vous que l'intégration de ce matériel n'est pas possible car absent de toute réserve ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseManualTransfertModal}>
                    Annuler
                </Button>
                <Button variant='success' onClick={transfertManuel} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Je confirme'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showReserveTransfertModalOPE} onHide={handleCloseReserveTransfertModalOPE} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Intégration à une réserve opé</Modal.Title>
                <FalconCloseButton onClick={handleCloseReserveTransfertModalOPE}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmitOPE(enregistrerTransfertOPE)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Réserve à approvisionner:</Form.Label>
                        <Select
                            id="idReserveElement"
                            name="idReserveElement"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            placeholder='Aucun élément selectionné'
                            options={reserves}
                            isOptionDisabled={(option) => option.inventaireEnCours}
                            value={reserves.find(c => c.value === watchOPE("idReserveElement"))}
                            onChange={val => val != null ? setValueOPE("idReserveElement", val.value) : setValueOPE("idReserveElement", null)}
                        />
                        <small className="text-danger">{errorsOPE.idReserveElement?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Quantité à transférer (max: {watchOPE("resteATransferer")})</Form.Label>
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            name='qttTransfert'
                            id='qttTransfert'
                            type="number"
                            min={1}
                            max={watchOPE("resteATransferer")}
                            step='1'
                            {...registerOPE("qttTransfert")}
                        />
                        <small className="text-danger">{errorsOPE.qttTransfert?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Le matériel reçu a une date de péremption ?</Form.Label>
                        <DatePicker
                            selected={watchOPE("peremptionCmd")}
                            onChange={(date)=>setValueOPE("peremptionCmd", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errorsOPE.peremptionCmd?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='success' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Intégrer'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseReserveTransfertModalOPE}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showReserveTransfertModalVEH} onHide={handleCloseReserveTransfertModalVEH} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Intégration à une réserve véhicules</Modal.Title>
                <FalconCloseButton onClick={handleCloseReserveTransfertModalVEH}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmitVEH(enregistrerTransfertVEH)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Réserve à approvisionner:</Form.Label>
                        <Select
                            id="idVehiculesStock"
                            name="idVehiculesStock"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            placeholder='Aucun élément selectionné'
                            options={reserves}
                            value={reserves.find(c => c.value === watchVEH("idVehiculesStock"))}
                            onChange={val => val != null ? setValueVEH("idVehiculesStock", val.value) : setValueVEH("idVehiculesStock", null)}
                        />
                        <small className="text-danger">{errorsVEH.idVehiculesStock?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Quantité à transférer (max: {watchVEH("resteATransferer")})</Form.Label>
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            name='qttTransfert'
                            id='qttTransfert'
                            type="number"
                            min={1}
                            max={watchVEH("resteATransferer")}
                            step='1'
                            {...registerVEH("qttTransfert")}
                        />
                        <small className="text-danger">{errorsVEH.qttTransfert?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Le matériel reçu a une date de péremption ?</Form.Label>
                        <DatePicker
                            selected={watchVEH("peremptionCmd")}
                            onChange={(date)=>setValueVEH("peremptionCmd", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errorsVEH.peremptionCmd?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='success' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Intégrer'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseReserveTransfertModalVEH}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showReserveTransfertModalVHF} onHide={handleCloseReserveTransfertModalVHF} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Intégration à une réserve transmissions</Modal.Title>
                <FalconCloseButton onClick={handleCloseReserveTransfertModalVHF}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmitVHF(enregistrerTransfertVHF)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Réserve à approvisionner:</Form.Label>
                        <Select
                            id="idVhfStock"
                            name="idVhfStock"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            placeholder='Aucun élément selectionné'
                            options={reserves}
                            value={reserves.find(c => c.value === watchVHF("idVhfStock"))}
                            onChange={val => val != null ? setValueVHF("idVhfStock", val.value) : setValueVHF("idVhfStock", null)}
                        />
                        <small className="text-danger">{errorsVHF.idVhfStock?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Quantité à transférer (max: {watchVHF("resteATransferer")})</Form.Label>
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            name='qttTransfert'
                            id='qttTransfert'
                            type="number"
                            min={1}
                            max={watchVHF("resteATransferer")}
                            step='1'
                            {...registerVHF("qttTransfert")}
                        />
                        <small className="text-danger">{errorsVHF.qttTransfert?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Le matériel reçu a une date de péremption ?</Form.Label>
                        <DatePicker
                            selected={watchVHF("peremptionCmd")}
                            onChange={(date)=>setValueVHF("peremptionCmd", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errorsVHF.peremptionCmd?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='success' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Intégrer'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseReserveTransfertModalVHF}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showReserveTransfertModalTEN} onHide={handleCloseReserveTransfertModalTEN} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Intégration au stock des tenues</Modal.Title>
                <FalconCloseButton onClick={handleCloseReserveTransfertModalTEN}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmitTEN(enregistrerTransfertTEN)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Element à approvisionner:</Form.Label>
                        <Select
                            id="idCatalogueTenue"
                            name="idCatalogueTenue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            placeholder='Aucun élément selectionné'
                            options={reserves}
                            isOptionDisabled={(option) => option.inventaireEnCours}
                            value={reserves.find(c => c.value === watchTEN("idCatalogueTenue"))}
                            onChange={val => val != null ? setValueTEN("idCatalogueTenue", val.value) : setValueTEN("idCatalogueTenue", null)}
                        />
                        <small className="text-danger">{errorsTEN.idCatalogueTenue?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Quantité à transférer (max: {watchTEN("resteATransferer")})</Form.Label>
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            name='qttTransfert'
                            id='qttTransfert'
                            type="number"
                            min={1}
                            max={watchTEN("resteATransferer")}
                            step='1'
                            {...registerTEN("qttTransfert")}
                        />
                        <small className="text-danger">{errorsTEN.qttTransfert?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='success' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Intégrer'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseReserveTransfertModalTEN}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <div className='mt-2 mb-2'>
            <GPMtable
                columns={colonnes}
                data={commande.materiels}
                topButtonShow={false}
            />

            {commande.detailsCommande.idEtat == 5 ? <>
                <hr/>
                <center className='mt-2'>
                    <IconButton
                        disabled={!okToClose}
                        icon='forward'
                        variant={okToClose ? 'success' : 'outline-success'}
                        onClick={cloreCommande}
                    >
                        Clôturer la commande
                    </IconButton>
                    <p><small>
                        <u>Critères à respecter pour clore:</u><br/>
                        {commande.verificationContraintes.contraintes.filter(ctr => ctr.idEtatFinal == 7).map((ctr, i) => {return(
                            <>
                                <FontAwesomeIcon icon={ctr.contrainteRespectee == true ? 'check' : 'ban'} color={ctr.contrainteRespectee == true ? 'green' : 'red'} className='me-2' />
                                {ctr.libelleContrainte}
                                <br/>
                            </>
                        )})}
                    </small></p>
                </center>
            </>: null}
        </div>
    </>);
};

OneCommandeStep7Stock.propTypes = {};

export default OneCommandeStep7Stock;
