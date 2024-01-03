import React, {useState, useEffect} from 'react';
import { Button, Form, Row, Col } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeStep5PassageCheck } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const OneCommandeStep5Passage = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    let ok = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 4)[0]?.passagePossible && !forceReadOnly;

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeStep5PassageCheck),
    });

    const initView = async () => {
        try {
            setValue("numCommandeFournisseur", commande.detailsCommande.numCommandeFournisseur);
            setValue("datePassage", commande.detailsCommande.datePassage ? new Date(commande.detailsCommande.datePassage) : new Date());
            setValue("dateLivraisonPrevue", commande.detailsCommande.dateLivraisonPrevue ? new Date(commande.detailsCommande.dateLivraisonPrevue) : null);
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

            await Axios.post('/commandes/updatePassageCommande',{
                idCommande: idCommande,
                numCommandeFournisseur: data.numCommandeFournisseur,
                datePassage: data.datePassage,
                dateLivraisonPrevue: data.dateLivraisonPrevue,
            })
            
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const passerCommande = async () => {
        try {
            await Axios.post('/commandes/passerCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
            
        <Form onSubmit={handleSubmit(saveForm)}>
            <Row className='mt-2'>
                <Col md={6}>
                    <Form.Label>Fournisseur</Form.Label>
                    <Form.Control size="sm" type="text" value={commande.detailsCommande.nomFournisseur} disabled={true}/>
                </Col>
                <Col md={6}>
                    <Form.Group className="mb-3">
                        <Form.Label>Référence de la commande ({commande.detailsCommande.nomFournisseur || null})</Form.Label>
                        <Form.Control size="sm" type="text" name="numCommandeFournisseur" id="numCommandeFournisseur" {...register("numCommandeFournisseur")} disabled={forceReadOnly}/>
                        <small className="text-danger">{errors.numCommandeFournisseur?.message}</small>
                    </Form.Group>
                </Col>
                <Col md={6}>
                    <Form.Label>Commande passée le</Form.Label><br/>
                    <DatePicker
                        selected={watch("datePassage")}
                        onChange={(date)=>setValue("datePassage", date)}
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
                    <small className="text-danger">{errors.datePassage?.message}</small>
                </Col>
                <Col md={6}>
                    <Form.Label>Date de livraison Prévue</Form.Label><br/>
                    <DatePicker
                        selected={watch("dateLivraisonPrevue")}
                        onChange={(date)=>setValue("dateLivraisonPrevue", date)}
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
                    <small className="text-danger">{errors.dateLivraisonPrevue?.message}</small>
                </Col>
            </Row>
            {commande.detailsCommande.idEtat == 3 ?
                <div className="d-grid gap-2 mt-3">
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading || forceReadOnly}>{isLoading ? 'Patientez...' : 'Enregistrer les remarques'}</Button>
                </div>
            :null}
        </Form>
        
        {commande.detailsCommande.idEtat == 3 ? <>
            <hr/>
            <center className='mt-2'>
                <IconButton
                    disabled={!ok}
                    icon='forward'
                    variant={ok ? 'success' : 'outline-success'}
                    onClick={passerCommande}
                >
                    La commande est passée
                </IconButton>
                <p><small>
                    <u>Critères à respecter pour passer la commande:</u><br/>
                    {commande.verificationContraintes.contraintes.filter(ctr => ctr.idEtatFinal == 4).map((ctr, i) => {return(
                        <>
                            <FontAwesomeIcon icon={ctr.contrainteRespectee == true ? 'check' : 'ban'} color={ctr.contrainteRespectee == true ? 'green' : 'red'} className='me-2' />
                            {ctr.libelleContrainte}
                            <br/>
                        </>
                    )})}
                </small></p>
            </center>
        </>: null}
    </>);
};

OneCommandeStep5Passage.propTypes = {};

export default OneCommandeStep5Passage;
