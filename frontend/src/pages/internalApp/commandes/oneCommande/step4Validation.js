import React, {useState, useEffect} from 'react';
import { Link, useNavigate, } from 'react-router-dom';
import { Card, Offcanvas, Button, Form, Tab, Nav, Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Select from 'react-select'
import findOptionForSelect from 'helpers/selectHelpers';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeStep4ValidationCheck } from 'helpers/yupValidationSchema';

const Preparation = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    let ok = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 2)[0].passagePossible && !forceReadOnly;

    const demandeValidation = async () => {
        try {
            await Axios.post('/commandes/demandeValidation',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    return(<>
        <center className='mt-2'>
            <p><small>
                <u>Critères à respecter pour soumettre à validation:</u><br/>
                {commande.verificationContraintes.contraintes.map((ctr, i) => {return(
                    <>
                        <FontAwesomeIcon icon={ctr.contrainteRespectee == true ? 'check' : 'ban'} color={ctr.contrainteRespectee == true ? 'green' : 'red'} className='me-2' />
                        {ctr.libelleContrainte}
                        <br/>
                    </>
                )})}
            </small></p>

            <IconButton
                disabled={!ok}
                icon='forward'
                variant={ok ? 'success' : 'outline-success'}
                onClick={demandeValidation}
            >
                Demander la validation
            </IconButton>
        </center>

        {commande.detailsCommande.remarquesValidation && commande.detailsCommande.remarquesValidation != null && commande.detailsCommande.remarquesValidation !="" ?
            <Form.Group className="mt-3 mb-3">
                <Form.Label>Remarques sur la validation:</Form.Label>
                <Form.Control size="sm" as="textarea" rows={5} name="remarquesValidation" id="remarquesValidation" value={commande.detailsCommande.remarquesValidation} disabled={true}/>
            </Form.Group>
        : null }
    </>);
};

const Arbitrage = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    let okToAccept = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 3)[0]?.passagePossible && !forceReadOnly;
    let okToReject = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 1)[0]?.passagePossible && !forceReadOnly;
    
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeStep4ValidationCheck),
    });

    const initView = async () => {
        try {
            setValue("remarquesValidation", commande.detailsCommande.remarquesValidation);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initView();
    },[])
    
    const [isLoading, setLoading] = useState(false);
    const saveForm = async (data) => {
        try {
            setLoading(true);

            await Axios.post('/commandes/updateRemarquesValidation',{
                idCommande: idCommande,
                remarquesValidation: data.remarquesValidation,
            })
            
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const rejeter = async () => {
        try {
            await Axios.post('/commandes/rejeterCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const accepter = async () => {
        try {
            await Axios.post('/commandes/approuverCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }
    
    return(<>
        <Row className='mt-2'>
            <Col md={12}>
                <Form.Group className="mb-3">
                    <Form.Label>Valideurs potentiels</Form.Label>
                    <Select
                        id="idValideur"
                        name="idValideur"
                        size="sm"
                        classNamePrefix="react-select"
                        isDisabled={true}
                        placeholder='Aucun valideur'
                        value={commande.valideurs}
                        isMulti
                    />
                    <small className="text-danger">{errors.nomCommande?.message}</small>
                </Form.Group>
            </Col>
            
            <Col md={12} className="mb-3">
                <Form onSubmit={handleSubmit(saveForm)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Remarques sur la validation</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={5} name="remarquesValidation" id="remarquesValidation" {...register("remarquesValidation")} disabled={forceReadOnly}/>
                        <small className="text-danger">{errors.remarquesValidation?.message}</small>
                    </Form.Group>
                    {commande.detailsCommande.idEtat == 2 ?
                        <div className="d-grid gap-2 mt-3">
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading || forceReadOnly}>{isLoading ? 'Patientez...' : 'Enregistrer les remarques'}</Button>
                        </div>
                    :null}
                </Form>
            </Col>

            {commande.detailsCommande.idEtat == 2 ? <>
                <hr/>

                <Col md={6}>
                    <center className='mt-2'>
                        <IconButton
                            disabled={!okToReject}
                            icon='ban'
                            variant={okToReject ? 'warning' : 'outline-warning'}
                            onClick={rejeter}
                        >
                            Rejeter
                        </IconButton>
                        <p><small>
                            <u>Critères à respecter pour rejeter:</u><br/>
                            {commande.verificationContraintes.contraintes.filter(ctr => ctr.idEtatFinal == 1).map((ctr, i) => {return(
                                <>
                                    <FontAwesomeIcon icon={ctr.contrainteRespectee == true ? 'check' : 'ban'} color={ctr.contrainteRespectee == true ? 'green' : 'red'} className='me-2' />
                                    {ctr.libelleContrainte}
                                    <br/>
                                </>
                            )})}
                        </small></p>
                    </center>
                </Col>
                <Col md={6}>
                    <center className='mt-2'>
                        <IconButton
                            disabled={!okToAccept}
                            icon='check'
                            variant={okToAccept ? 'success' : 'outline-success'}
                            onClick={accepter}
                        >
                            Accepter
                        </IconButton>
                        <p><small>
                            <u>Critères à respecter pour accepter:</u><br/>
                            {commande.verificationContraintes.contraintes.filter(ctr => ctr.idEtatFinal == 3).map((ctr, i) => {return(
                                <>
                                    <FontAwesomeIcon icon={ctr.contrainteRespectee == true ? 'check' : 'ban'} color={ctr.contrainteRespectee == true ? 'green' : 'red'} className='me-2' />
                                    {ctr.libelleContrainte}
                                    <br/>
                                </>
                            )})}
                        </small></p>
                    </center>
                </Col>
            </>: null}
        </Row>
    </>);
};

const OneCommandeStep4Validation = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    if(commande.detailsCommande.idEtat == 1)
    {
        return(<Preparation
            idCommande={idCommande}
            commande={commande}
            forceReadOnly={forceReadOnly}
            setPageNeedsRefresh={setPageNeedsRefresh}
        />);
    }else{
        return(<Arbitrage
            idCommande={idCommande}
            commande={commande}
            forceReadOnly={forceReadOnly}
            setPageNeedsRefresh={setPageNeedsRefresh}
        />);
    }
};

OneCommandeStep4Validation.propTypes = {};

export default OneCommandeStep4Validation;
