import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Row, Col, FloatingLabel, Alert, } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { reservesMaterielsForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const ReservesMaterielsForm = ({
    idReserveElement = 0,
    idConteneur = null,
    element = null,
    setPageNeedsRefresh
}) => {
    
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(reservesMaterielsForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const[catalogue, setCatalogue] = useState([]);
    const[conteneurs, setConteneurs] = useState([]);
    const[fournisseurs, setFournisseurs] = useState([]);
    const[lockAnticipation, setLockAnticipation] = useState(false);
    const handleShowOffCanevas = async () => {

        if(idReserveElement > 0)
        {
            if(element == null)
            {
                let getElement = await Axios.post('/reserves/getOneReservesMateriel',{
                    idReserveElement: idReserveElement
                })
                element= getElement.data[0];
            }
            setValue("idMaterielCatalogue", element.idMaterielCatalogue);
            setValue("idConteneur", element.idConteneur);
            setValue("idFournisseur", element.idFournisseur);
            setValue("quantiteReserve", element.quantiteReserve);
            setValue("quantiteAlerteReserve", element.quantiteAlerteReserve);
            setValue("peremptionReserve", element.peremptionReserve ? new Date(element.peremptionReserve) : null);
            setValue("peremptionReserveAnticipation", element.peremptionReserveAnticipation);
            setValue("numeroSerie", element.numeroSerie);
            setValue("commentairesReserveElement", element.commentairesReserveElement);
        }

        if(idConteneur != null && idConteneur > 0)
        {
            setValue("idConteneur", idConteneur);
        }

        let getForSelect = await Axios.get('/select/getCatalogueMaterielOpeFull');
        setCatalogue(getForSelect.data);
        getForSelect = await Axios.get('/select/getConteneurs');
        setConteneurs(getForSelect.data);
        getForSelect = await Axios.get('/select/getFournisseurs');
        setFournisseurs(getForSelect.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(idReserveElement > 0)    
            {
                const response = await Axios.post('/reserves/updateReservesMateriels',{
                    idReserveElement: idReserveElement,
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idConteneur : data.idConteneur,
                    idFournisseur : data.idFournisseur,
                    quantiteReserve : data.quantiteReserve,
                    quantiteAlerteReserve : data.quantiteAlerteReserve,
                    peremptionReserve : data.peremptionReserve,
                    peremptionReserveAnticipation : data.peremptionReserveAnticipation,
                    numeroSerie : data.numeroSerie,
                    commentairesReserveElement : data.commentairesReserveElement,
                });
            }
            else
            {
                const response = await Axios.post('/reserves/addReservesMateriels',{
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idConteneur : data.idConteneur,
                    idFournisseur : data.idFournisseur,
                    quantiteReserve : data.quantiteReserve,
                    quantiteAlerteReserve : data.quantiteAlerteReserve,
                    peremptionReserve : data.peremptionReserve,
                    peremptionReserveAnticipation : data.peremptionReserveAnticipation,
                    numeroSerie : data.numeroSerie,
                    commentairesReserveElement : data.commentairesReserveElement,
                });
            }

            handleCloseOffCanevas();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    useEffect(()=>{
        if(watch("idMaterielCatalogue") > 0 && catalogue.length>0)
        {
            let itemFromCatalogue = catalogue.find(item => item.value == watch("idMaterielCatalogue"));
            if(itemFromCatalogue.peremptionAnticipationRes && itemFromCatalogue.peremptionAnticipationRes != null)
            {
                setLockAnticipation(true);
                setValue("peremptionAnticipation", itemFromCatalogue.peremptionAnticipationRes)
            }else{
                setLockAnticipation(false);
            }
        }
    },[watch("idMaterielCatalogue"), catalogue])

    return (
    <>
        {idReserveElement > 0 ?
            <IconButton
                icon='pen'
                size = 'sm'
                variant="outline-warning"
                className="me-1"
                onClick={handleShowOffCanevas}
            />
        :
            <IconButton
                icon='plus'
                size = 'sm'
                variant="outline-success"
                className="me-1"
                onClick={handleShowOffCanevas}
            >Nouvel élément</IconButton>
        }
        
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{idReserveElement > 0 ? "Modification" : "Ajout"} d'un élément de matériel</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Référence du catalogue</Form.Label>
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
                        <Form.Label>Conteneur</Form.Label>
                        <Select
                            id="idConteneur"
                            name="idConteneur"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading || idConteneur > 0}
                            placeholder='Aucun emplacement selectionné'
                            options={conteneurs}
                            isOptionDisabled={(option) => option.inventaireEnCours}
                            value={conteneurs.find(c => c.value === watch("idConteneur"))}
                            onChange={val => val != null ? setValue("idConteneur", val.value) : setValue("idConteneur", null)}
                        />
                        <small className="text-danger">{errors.idConteneur?.message}</small>
                    </Form.Group>

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
                            isDisabled={isLoading}
                            placeholder='Aucun emplacement selectionné'
                            options={fournisseurs}
                            value={fournisseurs.find(c => c.value === watch("idFournisseur"))}
                            onChange={val => val != null ? setValue("idFournisseur", val.value) : setValue("idFournisseur", null)}
                        />
                        <small className="text-danger">{errors.idFournisseur?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Quantités</Form.Label>
                        <Row>
                            <Col md={6}>
                                <FloatingLabel
                                    controlId="floatingInput"
                                    label="Quantité présente"
                                    className="mb-3"
                                >
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantiteReserve' id='quantiteReserve' {...register('quantiteReserve')}/>
                                    <small className="text-danger">{errors.quantiteReserve?.message}</small>
                                </FloatingLabel>
                            </Col>
                            <Col md={6}>
                                <FloatingLabel
                                    controlId="floatingInput"
                                    label="Quantité d'alerte"
                                    className="mb-3"
                                >
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantiteAlerteReserve' id='quantiteAlerteReserve' {...register('quantiteAlerteReserve')}/>
                                    <small className="text-danger">{errors.quantiteAlerteReserve?.message}</small>
                                </FloatingLabel>
                            </Col>
                            {watch("quantiteReserve") != 1 && watch("numeroSerie") != null && watch("numeroSerie") != "" ?
                                <Col md={12}>
                                    <Alert variant='warning'>Un numéro de série est renseigné, ce matériel est donc logiquement unique, sa quantité devrait donc être de 1.</Alert>
                                </Col>
                            : null}
                        </Row>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Date de péremption</Form.Label>
                        <DatePicker
                            selected={watch("peremptionReserve")}
                            onChange={(date)=>setValue("peremptionReserve", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.peremptionReserve?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Anticipation de la notification (j)</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='peremptionReserveAnticipation' id='peremptionReserveAnticipation' {...register('peremptionReserveAnticipation')} disabled={lockAnticipation}/>
                        <small className="text-danger">{errors.peremptionReserveAnticipation?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Numéro de série</Form.Label>
                        <Form.Control size="sm" type="text" name={"numeroSerie"} id={"numeroSerie"} {...register("numeroSerie")}/>
                        <small className="text-danger">{errors.numeroSerie?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"commentairesReserveElement"} id={"commentairesReserveElement"} {...register("commentairesReserveElement")}/>
                        <small className="text-danger">{errors.commentairesReserveElement?.message}</small>
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>
    </>);
};

ReservesMaterielsForm.propTypes = {};

export default ReservesMaterielsForm;
