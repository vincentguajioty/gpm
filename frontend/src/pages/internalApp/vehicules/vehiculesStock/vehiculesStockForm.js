import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Row, Col, FloatingLabel, Alert, } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vehiculesStockForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const VehiculesStockForm = ({
    idVehiculesStock = 0,
    element = null,
    setPageNeedsRefresh
}) => {
    
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vehiculesStockForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const[catalogue, setCatalogue] = useState([]);
    const[fournisseurs, setFournisseurs] = useState([]);
    const[lockAnticipation, setLockAnticipation] = useState(false);
    const handleShowOffCanevas = async () => {

        if(idVehiculesStock > 0)
        {
            if(element == null)
            {
                let getElement = await Axios.post('/vehicules/getOneVehiculesStock',{
                    idVehiculesStock: idVehiculesStock
                })
                element= getElement.data[0];
            }
            setValue("idMaterielCatalogue", element.idMaterielCatalogue);
            setValue("idFournisseur", element.idFournisseur);
            setValue("quantiteVehiculesStock", element.quantiteVehiculesStock);
            setValue("quantiteAlerteVehiculesStock", element.quantiteAlerteVehiculesStock);
            setValue("peremptionVehiculesStock", element.peremptionVehiculesStock ? new Date(element.peremptionVehiculesStock) : null);
            setValue("peremptionAnticipationVehiculesStock", element.peremptionAnticipationVehiculesStock);
            setValue("commentairesVehiculesStock", element.commentairesVehiculesStock);
        }

        let getForSelect = await Axios.get('/select/getCatalogueMaterielVehiculesFull');
        setCatalogue(getForSelect.data);
        getForSelect = await Axios.get('/select/getFournisseurs');
        setFournisseurs(getForSelect.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(idVehiculesStock > 0)    
            {
                const response = await Axios.post('/vehicules/updateVehiculesStock',{
                    idVehiculesStock: idVehiculesStock,
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idFournisseur : data.idFournisseur,
                    quantiteVehiculesStock : data.quantiteVehiculesStock,
                    quantiteAlerteVehiculesStock : data.quantiteAlerteVehiculesStock,
                    peremptionVehiculesStock : data.peremptionVehiculesStock,
                    peremptionAnticipationVehiculesStock : data.peremptionAnticipationVehiculesStock,
                    commentairesVehiculesStock : data.commentairesVehiculesStock,
                });
            }
            else
            {
                const response = await Axios.post('/vehicules/addVehiculesStock',{
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idFournisseur : data.idFournisseur,
                    quantiteVehiculesStock : data.quantiteVehiculesStock,
                    quantiteAlerteVehiculesStock : data.quantiteAlerteVehiculesStock,
                    peremptionVehiculesStock : data.peremptionVehiculesStock,
                    peremptionAnticipationVehiculesStock : data.peremptionAnticipationVehiculesStock,
                    commentairesVehiculesStock : data.commentairesVehiculesStock,
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
            if(itemFromCatalogue.peremptionAnticipationVehicule && itemFromCatalogue.peremptionAnticipationVehicule != null)
            {
                setLockAnticipation(true);
                setValue("peremptionAnticipationVehiculesStock", itemFromCatalogue.peremptionAnticipationVehicule)
            }else{
                setLockAnticipation(false);
            }
        }
    },[watch("idMaterielCatalogue"), catalogue])

    return (
    <>
        {idVehiculesStock > 0 ?
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
                <Offcanvas.Title>{idVehiculesStock > 0 ? "Modification" : "Ajout"} d'un élément dans le stock de consommables des véhicules</Offcanvas.Title>
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
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantiteVehiculesStock' id='quantiteVehiculesStock' {...register('quantiteVehiculesStock')}/>
                                    <small className="text-danger">{errors.quantiteVehiculesStock?.message}</small>
                                </FloatingLabel>
                            </Col>
                            <Col md={6}>
                                <FloatingLabel
                                    controlId="floatingInput"
                                    label="Quantité d'alerte"
                                    className="mb-3"
                                >
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantiteAlerteVehiculesStock' id='quantiteAlerteVehiculesStock' {...register('quantiteAlerteVehiculesStock')}/>
                                    <small className="text-danger">{errors.quantiteAlerteVehiculesStock?.message}</small>
                                </FloatingLabel>
                            </Col>
                        </Row>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Date de péremption</Form.Label>
                        <DatePicker
                            selected={watch("peremptionVehiculesStock")}
                            onChange={(date)=>setValue("peremptionVehiculesStock", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.peremptionVehiculesStock?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Anticipation de la notification (j)</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='peremptionAnticipationVehiculesStock' id='peremptionAnticipationVehiculesStock' {...register('peremptionAnticipationVehiculesStock')} disabled={lockAnticipation}/>
                        <small className="text-danger">{errors.peremptionAnticipationVehiculesStock?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"commentairesVehiculesStock"} id={"commentairesVehiculesStock"} {...register("commentairesVehiculesStock")}/>
                        <small className="text-danger">{errors.commentairesVehiculesStock?.message}</small>
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>
    </>);
};

VehiculesStockForm.propTypes = {};

export default VehiculesStockForm;
