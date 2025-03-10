import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Row, Col, FloatingLabel, Alert, } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vhfStockForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const TransmissionsStockForm = ({
    idVhfStock = 0,
    element = null,
    setPageNeedsRefresh
}) => {
    
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vhfStockForm),
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

        if(idVhfStock > 0)
        {
            if(element == null)
            {
                let getElement = await Axios.post('/vhf/getOneVhfStock',{
                    idVhfStock: idVhfStock
                })
                element= getElement.data[0];
            }
            setValue("idMaterielCatalogue", element.idMaterielCatalogue);
            setValue("idFournisseur", element.idFournisseur);
            setValue("quantiteVhfStock", element.quantiteVhfStock);
            setValue("quantiteAlerteVhfStock", element.quantiteAlerteVhfStock);
            setValue("peremptionVhfStock", element.peremptionVhfStock ? new Date(element.peremptionVhfStock) : null);
            setValue("peremptionAnticipationVhfStock", element.peremptionAnticipationVhfStock);
            setValue("commentairesVhfStock", element.commentairesVhfStock);
        }

        let getForSelect = await Axios.get('/select/getCatalogueMaterielVhfFull');
        setCatalogue(getForSelect.data);
        getForSelect = await Axios.get('/select/getFournisseurs');
        setFournisseurs(getForSelect.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(idVhfStock > 0)    
            {
                const response = await Axios.post('/vhf/updateVhfStock',{
                    idVhfStock: idVhfStock,
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idFournisseur : data.idFournisseur,
                    quantiteVhfStock : data.quantiteVhfStock,
                    quantiteAlerteVhfStock : data.quantiteAlerteVhfStock,
                    peremptionVhfStock : data.peremptionVhfStock,
                    peremptionAnticipationVhfStock : data.peremptionAnticipationVhfStock,
                    commentairesVhfStock : data.commentairesVhfStock,
                });
            }
            else
            {
                const response = await Axios.post('/vhf/addVhfStock',{
                    idMaterielCatalogue : data.idMaterielCatalogue,
                    idFournisseur : data.idFournisseur,
                    quantiteVhfStock : data.quantiteVhfStock,
                    quantiteAlerteVhfStock : data.quantiteAlerteVhfStock,
                    peremptionVhfStock : data.peremptionVhfStock,
                    peremptionAnticipationVhfStock : data.peremptionAnticipationVhfStock,
                    commentairesVhfStock : data.commentairesVhfStock,
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
            if(itemFromCatalogue.peremptionAnticipationVHF && itemFromCatalogue.peremptionAnticipationVHF != null)
            {
                setLockAnticipation(true);
                setValue("peremptionAnticipationVhfStock", itemFromCatalogue.peremptionAnticipationVHF)
            }else{
                setLockAnticipation(false);
            }
        }
    },[watch("idMaterielCatalogue"), catalogue])

    return (
    <>
        {idVhfStock > 0 ?
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
                <Offcanvas.Title>{idVhfStock > 0 ? "Modification" : "Ajout"} d'un élément dans le stock de consommables transmissions</Offcanvas.Title>
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
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantiteVhfStock' id='quantiteVhfStock' {...register('quantiteVhfStock')}/>
                                    <small className="text-danger">{errors.quantiteVhfStock?.message}</small>
                                </FloatingLabel>
                            </Col>
                            <Col md={6}>
                                <FloatingLabel
                                    controlId="floatingInput"
                                    label="Quantité d'alerte"
                                    className="mb-3"
                                >
                                    <Form.Control size="sm" type="number" min="0" step="1" name='quantiteAlerteVhfStock' id='quantiteAlerteVhfStock' {...register('quantiteAlerteVhfStock')}/>
                                    <small className="text-danger">{errors.quantiteAlerteVhfStock?.message}</small>
                                </FloatingLabel>
                            </Col>
                        </Row>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Date de péremption</Form.Label>
                        <DatePicker
                            selected={watch("peremptionVhfStock")}
                            onChange={(date)=>setValue("peremptionVhfStock", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.peremptionVhfStock?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Anticipation de la notification (j)</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='peremptionAnticipationVhfStock' id='peremptionAnticipationVhfStock' {...register('peremptionAnticipationVhfStock')} disabled={lockAnticipation}/>
                        <small className="text-danger">{errors.peremptionAnticipationVhfStock?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"commentairesVhfStock"} id={"commentairesVhfStock"} {...register("commentairesVhfStock")}/>
                        <small className="text-danger">{errors.commentairesVhfStock?.message}</small>
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>
    </>);
};

TransmissionsStockForm.propTypes = {};

export default TransmissionsStockForm;
