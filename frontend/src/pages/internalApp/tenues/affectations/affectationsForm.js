import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, } from 'react-bootstrap';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { affectationsTenuesForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const AffectationForm = ({
    affectationsRow = [],
    catalogue = [],
    personnesExternes = [],
    setPageNeedsRefresh,
    showAddButton = false,
    showEditButton = false,
    editId = null,
}) => {

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdTenue, setOffCanevasIdTenue] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(affectationsTenuesForm),
    });
    
    const [faculteNotification, setFaculteNotification] = useState(false);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdTenue();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdTenue(id);

        if(id > 0)
        {
            let oneItemFromArray = affectationsRow.filter(ligne => ligne.idTenue == id)[0];
            setValue("idMaterielCatalogueInitial", oneItemFromArray.idMaterielCatalogue);
            setValue("idMaterielCatalogue", oneItemFromArray.idMaterielCatalogue);
            setValue("nomPrenomExterne", oneItemFromArray.nomPrenomExterne);
            setValue("mailExterne", oneItemFromArray.mailExterne);
            setValue("idExterne", oneItemFromArray.idExterne);
            setValue('personneConnue', true);
            setValue("dateAffectation", oneItemFromArray.dateAffectation != null ? new Date(oneItemFromArray.dateAffectation) : null);
            setValue("dateRetour", oneItemFromArray.dateRetour != null ? new Date(oneItemFromArray.dateRetour) : null);
            setValue("notifPersonne", oneItemFromArray.notifPersonne);
        }
        else
        {
            setValue("dateAffectation", new Date());
            setValue('personneConnue', false);
        }

        verifFaculteNotifications();

        setValue("initialLoadOver", true);
        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdTenue > 0)    
            {
                const response = await Axios.post('/tenues/updateAffectations',{
                    idTenue: offCanevasIdTenue,
                    idMaterielCatalogueInitial: data.idMaterielCatalogueInitial,
                    idMaterielCatalogue: data.idMaterielCatalogue,
                    nomPrenomExterne: data.nomPrenomExterne,
                    mailExterne: data.mailExterne,
                    idExterne: data.idExterne,
                    dateAffectation: data.dateAffectation,
                    dateRetour: data.dateRetour,
                    notifPersonne: data.notifPersonne,
                });
            }
            else
            {
                const response = await Axios.post('/tenues/addAffectations',{
                    idMaterielCatalogue: data.idMaterielCatalogue,
                    nomPrenomExterne: data.nomPrenomExterne,
                    mailExterne: data.mailExterne,
                    idExterne: data.idExterne,
                    dateAffectation: data.dateAffectation,
                    dateRetour: data.dateRetour,
                    notifPersonne: data.notifPersonne,
                });
            }

            handleCloseOffCanevas();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    const verifFaculteNotifications = () => {
        try {
            if((watch('dateRetour') != null && watch('dateRetour') != "") && ((watch('mailExterne') != null && watch('mailExterne') != "")))
            {
                setFaculteNotification(true);
                if(offCanevasIdTenue == 0) {setValue("notifPersonne", true)}
            }else{
                setFaculteNotification(false);
                setValue("notifPersonne", false)
            }
        } catch (error) {
            console.error(error)
        }
    }

    useEffect(()=>{
        if(watch('initialLoadOver') && watch('initialLoadOver') == true)
        {
            if(watch('idExterne') && watch('idExterne') != null && watch('idExterne') != 0)
            {
                let personneSelectionnee = personnesExternes.filter(perso => perso.idExterne == watch('idExterne'));
                setValue('nomPrenomExterne', personneSelectionnee[0].nomPrenomExterne || null);
                setValue('mailExterne', personneSelectionnee[0].mailExterne || null);
                setValue('personneConnue', true);
            }else{
                setValue('personneConnue', false);
                setValue('idExterne', null);
                setValue('nomPrenomExterne', null);
                setValue('mailExterne', null);
            }
        }
    },[watch('idExterne')])

    useEffect(()=>{
        if(watch('initialLoadOver') && watch('initialLoadOver') == true)
        {
            if(watch('nomPrenomExterne') && watch('nomPrenomExterne') != null && watch('nomPrenomExterne') != "")
            {
                let personnesChercheesSurNom = personnesExternes.filter(perso => perso.nomPrenomExterne == watch('nomPrenomExterne'));
                if(personnesChercheesSurNom.length > 0)
                {
                    let personnesChercheesSurMail = personnesChercheesSurNom.filter(perso => perso.mailExterne == watch('mailExterne'));
                    if(personnesChercheesSurMail.length == 1)
                    {
                        setValue('idExterne', personnesChercheesSurMail[0].idExterne);
                    }else{
                        setValue('idExterne', null);
                    }
                }else{
                    setValue('idExterne', null);
                }
            }else{
                setValue('idExterne', null);
                setValue('mailExterne', null);
            }
        }
    },[
        watch('nomPrenomExterne'),
        watch('mailExterne'),
    ])

    useEffect(()=>{
        verifFaculteNotifications();
    },[
        watch('dateRetour'),
        watch('mailExterne'),
    ])

    return(<>
        {showAddButton ?
            <center>
                <IconButton
                    icon='plus'
                    size = 'sm'
                    variant="success"
                    onClick={()=>{handleShowOffCanevas(0)}}
                    className="mb-1"
                >Nouvelle affectation de tenue</IconButton>
            </center>
        : null}

        {showEditButton ?
            <IconButton
                icon='pen'
                size = 'sm'
                variant="outline-warning"
                className="me-1"
                onClick={()=>{handleShowOffCanevas(editId)}}
            />
        : null}

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdTenue > 0 ? "Modification" : "Ajout"} d'une affectation de tenue</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Element de tenue</Form.Label>
                        <Select
                            id="idMaterielCatalogue"
                            name="idMaterielCatalogue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun élément selectionné'
                            options={catalogue}
                            value={catalogue.find(c => c.value === watch("idMaterielCatalogue"))}
                            onChange={val => val != null ? setValue("idMaterielCatalogue", val.value) : setValue("idMaterielCatalogue", null)}
                        />
                        <small className="text-danger">{errors.idMaterielCatalogue?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Affecté à <SoftBadge bg={watch('personneConnue') ? "success":"warning"}>{watch('personneConnue') ? "Personne connue":"Inconnu"}</SoftBadge></Form.Label>
                        <Select
                            id="idExterne"
                            name="idExterne"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucune personne connue selectionnée'
                            options={personnesExternes}
                            value={personnesExternes.find(c => c.value === watch("idExterne"))}
                            onChange={val => val != null ? setValue("idExterne", val.value) : setValue("idExterne", null)}
                        />
                        <small className="text-danger">{errors.idExterne?.message}</small>

                        <Form.Control size="sm" type="text" name='nomPrenomExterne' id='nomPrenomExterne' {...register('nomPrenomExterne')} placeholder='NOM Prénom'/>
                        <small className="text-danger">{errors.nomPrenomExterne?.message}</small>
                        <Form.Control size="sm" type="text" name='mailExterne' id='mailExterne' {...register('mailExterne')} placeholder='Mail'/>
                        <small className="text-danger">{errors.mailExterne?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date d'affectation</Form.Label>
                        <DatePicker
                            selected={watch("dateAffectation")}
                            onChange={(date)=>setValue("dateAffectation", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateAffectation?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date prévisionnelle de retour</Form.Label>
                        <DatePicker
                            selected={watch("dateRetour")}
                            onChange={(date)=>setValue("dateRetour", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateRetour?.message}</small>

                        <Form.Check
                            id='notifPersonne'
                            name='notifPersonne'
                            label="Notification régulière à l'utilisateur"
                            type='switch'
                            checked={watch("notifPersonne")}
                            onClick={(e)=>{
                                setValue("notifPersonne", !watch("notifPersonne"))
                            }}
                            disabled={!faculteNotification}
                        />
                        <small className="text-danger">{errors.notifPersonne?.message}</small>
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>
    </>)
}

AffectationForm.propTypes = {};

export default AffectationForm;