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
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeStep7StockCheck } from 'helpers/yupValidationSchema';

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
                    !forceReadOnly && HabilitationService.habilitations.reserve_cmdVersReserve && row.original.idMaterielCatalogue != null ? <>
                        <IconButton
                            icon={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0 ? 'check' : 'forward'}
                            size = 'sm'
                            variant="outline-success"
                            className="me-1"
                            disabled={row.original.idMaterielCatalogue == null || row.original.quantiteAtransferer == 0}
                            onClick={()=>{handleShowReserveTransfertModal(row.original.idCommandeMateriel)}}
                        >{row.original.quantiteCommande - row.original.quantiteAtransferer} / {row.original.quantiteCommande} intégrés</IconButton>
                        
                        {row.original.quantiteAtransferer > 0 ?
                            <IconButton
                                icon='hand-holding-medical'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowManualTransfertModal(row.original.idCommandeMateriel)}}
                            />
                        : null}
                    </> : null
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

    /* Modal transfert vers réserve */
    const [showReserveTransfertModal, setShowReserveTransfertModal] = useState(false);
    const [reserveTransfertModalIdCommandeMateriel, setReserveTransfertModalIdCommandeMateriel] = useState();

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeStep7StockCheck),
    });

    const handleCloseReserveTransfertModal = () => {
        setReserveTransfertModalIdCommandeMateriel();
        setShowReserveTransfertModal(false);
        setLoading(false);
        reset();
    };
    const [reserves, setReserves] = useState([]);
    const handleShowReserveTransfertModal = async (id) => {
        try {
            setReserveTransfertModalIdCommandeMateriel(id);
            setShowReserveTransfertModal(true);
            setLoading(true);

            let oneElement = commande.materiels.filter(item => item.idCommandeMateriel == id)[0];

            let getData = await Axios.post('/transferts/getReservesForOneIntegration',{
                idMaterielCatalogue: oneElement.idMaterielCatalogue
            });
            setReserves(getData.data);

            setValue("resteATransferer", oneElement.quantiteAtransferer);
            setValue("qttTransfert", oneElement.quantiteAtransferer);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const enregistrerTransfert = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/transferts/enregistrerTransfert',{
                idCommandeMateriel: reserveTransfertModalIdCommandeMateriel,
                idCommande: idCommande,
                idReserveElement: data.idReserveElement,
                qttTransfert: data.qttTransfert,
                peremptionCmd: data.peremptionCmd,
            });
            
            setPageNeedsRefresh(true);
            handleCloseReserveTransfertModal();
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

        <Modal show={showReserveTransfertModal} onHide={handleCloseReserveTransfertModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Intégration à une réserve</Modal.Title>
                <FalconCloseButton onClick={handleCloseReserveTransfertModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmit(enregistrerTransfert)}>
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
                            value={reserves.find(c => c.value === watch("idReserveElement"))}
                            onChange={val => val != null ? setValue("idReserveElement", val.value) : setValue("idReserveElement", null)}
                        />
                        <small className="text-danger">{errors.idReserveElement?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Quantité à transférer (max:{watch("resteATransferer")})</Form.Label>
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            name='qttTransfert'
                            id='qttTransfert'
                            type="number"
                            min={1}
                            max={watch("resteATransferer")}
                            step='1'
                            {...register("qttTransfert")}
                        />
                        <small className="text-danger">{errors.qttTransfert?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Le matériel reçu a une date de péremption ?</Form.Label>
                        <DatePicker
                            selected={watch("peremptionCmd")}
                            onChange={(date)=>setValue("peremptionCmd", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.peremptionCmd?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='success' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Intégrer'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseReserveTransfertModal}>
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
