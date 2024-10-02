import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Row, Col, FloatingLabel, Alert, } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { materielsForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const MaterielsForm = ({
    idElement = 0,
    idEmplacement = null,
    element = null,
    setPageNeedsRefresh
}) => {
    
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(materielsForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const[catalogue, setCatalogue] = useState([]);
    const[emplacements, setEmplacements] = useState([]);
    const[fournisseurs, setFournisseurs] = useState([]);
    const[etats, setEtats] = useState([]);
    const[lockAnticipation, setLockAnticipation] = useState(false);
    const handleShowOffCanevas = async () => {

        if(idElement > 0)
        {
            if(element == null)
            {
                let getElement = await Axios.post('/materiels/getOneMateriel',{
                    idElement: idElement
                })
                element= getElement.data[0];
            }
            setValue("idMaterielCatalogue", element.idMaterielCatalogue);
            setValue("idEmplacement", element.idEmplacement);
            setValue("idFournisseur", element.idFournisseur);
            setValue("quantite", element.quantite);
            setValue("quantiteAlerte", element.quantiteAlerte);
            setValue("peremption", element.peremption ? new Date(element.peremption) : null);
            setValue("peremptionAnticipation", element.peremptionAnticipation);
            setValue("numeroSerie", element.numeroSerie);
            setValue("commentairesElement", element.commentairesElement);
            setValue("idMaterielsEtat", element.idMaterielsEtat);
        }

        if(idEmplacement != null && idEmplacement > 0)
        {
            setValue("idEmplacement", idEmplacement);
        }

        let getForSelect = await Axios.get('/select/getCatalogueMaterielOpeFull');
        setCatalogue(getForSelect.data);
        getForSelect = await Axios.get('/select/getEmplacementsFull');
        setEmplacements(getForSelect.data);
        getForSelect = await Axios.get('/select/getFournisseurs');
        setFournisseurs(getForSelect.data);
        getForSelect = await Axios.get('/select/getEtatsMateriels');
        setEtats(getForSelect.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(idElement > 0)    
            {
                const response = await Axios.post('/materiels/updateMateriels',{
                    idElement: idElement,
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idEmplacement : data.idEmplacement,
                    idFournisseur : data.idFournisseur,
                    quantite : data.quantite,
                    quantiteAlerte : data.quantiteAlerte,
                    peremption : data.peremption,
                    peremptionAnticipation : data.peremptionAnticipation,
                    numeroSerie : data.numeroSerie,
                    commentairesElement : data.commentairesElement,
                    idMaterielsEtat : data.idMaterielsEtat,
                });
            }
            else
            {
                const response = await Axios.post('/materiels/addMateriels',{
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idEmplacement : data.idEmplacement,
                    idFournisseur : data.idFournisseur,
                    quantite : data.quantite,
                    quantiteAlerte : data.quantiteAlerte,
                    peremption : data.peremption,
                    peremptionAnticipation : data.peremptionAnticipation,
                    numeroSerie : data.numeroSerie,
                    commentairesElement : data.commentairesElement,
                    idMaterielsEtat : data.idMaterielsEtat,
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
            if(itemFromCatalogue.peremptionAnticipationOpe && itemFromCatalogue.peremptionAnticipationOpe != null)
            {
                setLockAnticipation(true);
                setValue("peremptionAnticipation", itemFromCatalogue.peremptionAnticipationOpe)
            }else{
                setLockAnticipation(false);
            }
        }
    },[watch("idMaterielCatalogue"), catalogue])

    return (
    <>
        {idElement > 0 ?
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
                <Offcanvas.Title>{idElement > 0 ? "Modification" : "Ajout"} d'un élément de matériel</Offcanvas.Title>
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
                        <Form.Label>Emplacement</Form.Label>
                        <Select
                            id="idEmplacement"
                            name="idEmplacement"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading || idEmplacement > 0}
                            placeholder='Aucun emplacement selectionné'
                            options={emplacements}
                            isOptionDisabled={(option) => option.inventaireEnCours}
                            value={emplacements.find(c => c.value === watch("idEmplacement"))}
                            onChange={val => val != null ? setValue("idEmplacement", val.value) : setValue("idEmplacement", null)}
                        />
                        <small className="text-danger">{errors.idEmplacement?.message}</small>
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
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantite' id='quantite' {...register('quantite')}/>
                                    <small className="text-danger">{errors.quantite?.message}</small>
                                </FloatingLabel>
                            </Col>
                            <Col md={6}>
                                <FloatingLabel
                                    controlId="floatingInput"
                                    label="Quantité d'alerte"
                                    className="mb-3"
                                >
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantiteAlerte' id='quantiteAlerte' {...register('quantiteAlerte')}/>
                                    <small className="text-danger">{errors.quantiteAlerte?.message}</small>
                                </FloatingLabel>
                            </Col>
                            {watch("quantite") != 1 && watch("numeroSerie") != null && watch("numeroSerie") != "" ?
                                <Col md={12}>
                                    <Alert variant='warning'>Un numéro de série est renseigné, ce matériel est donc logiquement unique, sa quantité devrait donc être de 1.</Alert>
                                </Col>
                            : null}
                        </Row>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Date de péremption</Form.Label>
                        <DatePicker
                            selected={watch("peremption")}
                            onChange={(date)=>setValue("peremption", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.peremption?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Anticipation de la notification (j)</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='peremptionAnticipation' id='peremptionAnticipation' {...register('peremptionAnticipation')} disabled={lockAnticipation}/>
                        <small className="text-danger">{errors.peremptionAnticipation?.message}</small>
                    </Form.Group>
                    
                    <Form.Group className="mb-3">
                        <Form.Label>Etat</Form.Label>
                        <Select
                            id="idMaterielsEtat"
                            name="idMaterielsEtat"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun emplacement selectionné'
                            options={etats}
                            value={etats.find(c => c.value === watch("idMaterielsEtat"))}
                            onChange={val => val != null ? setValue("idMaterielsEtat", val.value) : setValue("idMaterielsEtat", null)}
                        />
                        <small className="text-danger">{errors.idMaterielsEtat?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Numéro de série</Form.Label>
                        <Form.Control size="sm" type="text" name={"numeroSerie"} id={"numeroSerie"} {...register("numeroSerie")}/>
                        <small className="text-danger">{errors.numeroSerie?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"commentairesElement"} id={"commentairesElement"} {...register("commentairesElement")}/>
                        <small className="text-danger">{errors.commentairesElement?.message}</small>
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>
    </>);
};

MaterielsForm.propTypes = {};

export default MaterielsForm;
