import React, {useState, useEffect} from 'react';
import { Button, Form, Row, Col, Alert } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeStep6LivraisonCheck } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const OneCommandeStep6Livraison = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    let okToContinue = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 5)[0]?.passagePossible && !forceReadOnly;
    let okToSAV = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 6)[0]?.passagePossible && !forceReadOnly;

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeStep6LivraisonCheck),
    });

    const initView = async () => {
        try {
            setValue("dateLivraisoneffective", commande.detailsCommande.dateLivraisoneffective ? new Date(commande.detailsCommande.dateLivraisoneffective) : null);
            setValue("remarquesLivraison", commande.detailsCommande.remarquesLivraison);
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

            await Axios.post('/commandes/updateInfosLivraison',{
                idCommande: idCommande,
                dateLivraisoneffective: data.dateLivraisoneffective,
                remarquesLivraison: data.remarquesLivraison,
            })
            
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const livraisonOK = async () => {
        try {
            await Axios.post('/commandes/livraisonOKCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const livraisonSAV = async () => {
        try {
            await Axios.post('/commandes/livraisonSAVCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <Row className='mt-2'>
            {commande.detailsCommande.savHistorique ?
                <Col md={12}>
                    <Alert variant='warning'>Cette commande fait ou a fait l'objet d'un SAV.</Alert>
                </Col>
            : null}
            <Col md={12} className="mb-3">
                <Form onSubmit={handleSubmit(saveForm)}>
                <Form.Group className="mb-3">
                        <Form.Label>Date réelle de livraison</Form.Label><br/>
                        <DatePicker
                            selected={watch("dateLivraisoneffective")}
                            onChange={(date)=>setValue("dateLivraisoneffective", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date et heure"
                            timeIntervals={15}
                            dateFormat="dd/MM/yyyy HH:mm"
                            showTimeSelect
                            fixedHeight
                            locale="fr"
                            disabled={forceReadOnly}
                        />
                        <small className="text-danger">{errors.dateLivraisoneffective?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Remarques sur la livraison</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={5} name="remarquesLivraison" id="remarquesLivraison" {...register("remarquesLivraison")} disabled={forceReadOnly}/>
                        <small className="text-danger">{errors.remarquesLivraison?.message}</small>
                    </Form.Group>
                    {commande.detailsCommande.idEtat == 4 || commande.detailsCommande.idEtat == 6 ? 
                        <div className="d-grid gap-2 mt-3">
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading || forceReadOnly}>{isLoading ? 'Patientez...' : 'Enregistrer les remarques'}</Button>
                        </div>
                    :null}
                </Form>
            </Col>
            
            {commande.detailsCommande.idEtat == 4 || commande.detailsCommande.idEtat == 6 ? <>
                <hr/>
                <Col md={6}>
                    <center className='mt-2'>
                        <IconButton
                            disabled={!okToSAV}
                            icon='exclamation-triangle'
                            variant={okToSAV ? 'warning' : 'outline-warning'}
                            onClick={livraisonSAV}
                        >
                            Engager un SAV
                        </IconButton>
                        <p><small>
                            <u>Critères à respecter pour passer en SAV:</u><br/>
                            {commande.verificationContraintes.contraintes.filter(ctr => ctr.idEtatFinal == 6).map((ctr, i) => {return(
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
                            disabled={!okToContinue}
                            icon='check'
                            variant={okToContinue ? 'success' : 'outline-success'}
                            onClick={livraisonOK}
                        >
                            Livraison conforme
                        </IconButton>
                        <p><small>
                            <u>Critères à respecter pour accepter la livraison:</u><br/>
                            {commande.verificationContraintes.contraintes.filter(ctr => ctr.idEtatFinal == 5).map((ctr, i) => {return(
                                <>
                                    <FontAwesomeIcon icon={ctr.contrainteRespectee == true ? 'check' : 'ban'} color={ctr.contrainteRespectee == true ? 'green' : 'red'} className='me-2' />
                                    {ctr.libelleContrainte}
                                    <br/>
                                </>
                            )})}
                        </small></p>
                    </center>
                </Col>
            </> :null}
        </Row>
    </>);
};

OneCommandeStep6Livraison.propTypes = {};

export default OneCommandeStep6Livraison;
