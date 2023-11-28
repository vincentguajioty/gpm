import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Row, Col, FloatingLabel, } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { sacsFormSchema } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const SacsForm = ({
    idSac = 0,
    idLot = null,
    element = null,
    setPageNeedsRefresh
}) => {
    
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(sacsFormSchema),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const[fournisseurs, setFournisseurs] = useState([]);
    const[lots, setLots] = useState([]);
    const handleShowOffCanevas = async () => {

        if(idSac > 0)
        {
            if(element == null)
            {
                let getElement = await Axios.post('/sacs/getOneSac',{
                    idSac: idSac
                })
                element= getElement.data[0];
            }

            setValue("idSac", element.idSac);
            setValue("libelleSac", element.libelleSac);
            setValue("idLot", element.idLot);
            setValue("taille", element.taille);
            setValue("couleur", element.couleur);
            setValue("idFournisseur", element.idFournisseur);
        }

        if(idLot != null && idLot > 0)
        {
            setValue("idLot", idLot);
        }

        let getForSelect = await Axios.get('/select/getFournisseurs');
        setFournisseurs(getForSelect.data);
        getForSelect = await Axios.get('/select/getLotsFull');
        setLots(getForSelect.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(idSac > 0)    
            {
                const response = await Axios.post('/sacs/updateSacs',{
                    idSac: idSac,
                    libelleSac: data.libelleSac,
                    idLot: data.idLot,
                    taille: data.taille,
                    couleur: data.couleur,
                    idFournisseur: data.idFournisseur,
                });
            }
            else
            {
                const response = await Axios.post('/sacs/addSacs',{
                    libelleSac: data.libelleSac,
                    idLot: data.idLot,
                    taille: data.taille,
                    couleur: data.couleur,
                    idFournisseur: data.idFournisseur,
                });
            }

            handleCloseOffCanevas();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    return (
    <>
        {idSac > 0 ?
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
            >Nouveau sac</IconButton>
        }
        
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{idSac > 0 ? "Modification" : "Ajout"} d'un sac</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé du sac</Form.Label>
                        <Form.Control size="sm" type="text" name='libelleSac' id='libelleSac' {...register('libelleSac')}/>
                        <small className="text-danger">{errors.libelleSac?.message}</small>
                    </Form.Group>
                    
                    <Form.Group className="mb-3">
                        <Form.Label>Lot de rattachement</Form.Label>
                        <Select
                            id="idLot"
                            name="idLot"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading || idLot > 0}
                            placeholder='Aucun lot selectionné'
                            options={lots}
                            isOptionDisabled={(option) => option.inventaireEnCours}
                            value={lots.find(c => c.value === watch("idLot"))}
                            onChange={val => val != null ? setValue("idLot", val.value) : setValue("idLot", null)}
                        />
                        <small className="text-danger">{errors.idLot?.message}</small>
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
                        <Form.Label>Taille</Form.Label>
                        <Form.Control size="sm" type="text" name='taille' id='taille' {...register('taille')}/>
                        <small className="text-danger">{errors.taille?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Couleur</Form.Label>
                        <Form.Control size="sm" type="text" name='couleur' id='couleur' {...register('couleur')}/>
                        <small className="text-danger">{errors.couleur?.message}</small>
                    </Form.Group>

                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>
    </>);
};

SacsForm.propTypes = {};

export default SacsForm;
