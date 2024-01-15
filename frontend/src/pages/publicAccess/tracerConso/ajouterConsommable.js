import React, {useState, useEffect} from 'react';
import { Card, Form, Button, } from 'react-bootstrap';
import moment from 'moment-timezone';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import FalconComponentCard from 'components/common/FalconComponentCard';
import Select from 'react-select';
import dynamicSort from 'helpers/dynamicSort';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { consommationPubliqueAjoutMateriel } from 'helpers/yupValidationSchema';

const AjouterConsommable = ({
    socket,
    idConsommation,
    lotsImpactes = [],
}) => {
    const [isLoading, setLoading] = useState(true);
    const [catalogue, setCatalogue] = useState([]);
    const [catalogueQuickSelect, setCatalogueQuickSelect] = useState([]);
    const [lots, setLots] = useState([]);

    const initPage = async () => {
        try {
            let getForSelect = await Axios.get('/select/getPublicCatalogueMateriel');
            setCatalogue(getForSelect.data);
            setCatalogueQuickSelect(getForSelect.data.filter(elem => elem.frequenceRapportConso >= 10).sort(dynamicSort('-frequenceRapportConso')).slice(0,5));

            getForSelect = await Axios.get('/select/getLotsPublics');
            setLots(getForSelect.data);
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(consommationPubliqueAjoutMateriel),
    });

    useEffect(()=>{
        initPage();
    },[])

    useEffect(()=>{
        setValue("idLot", null);
    },[watch("idMaterielCatalogue")])
    useEffect(()=>{
        setValue("quantiteConsommation", null);
    },[watch("idLot")])

    const ajouterConso = async (data) => {
        try {
            setLoading(true);

            await socket.emit("consommation_addElement",{
                idConsommation: idConsommation,
                idMaterielCatalogue: data.idMaterielCatalogue,
                idLot: data.idLot,
                quantiteConsommation: data.quantiteConsommation,
            });

            reset();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return(
        <FalconComponentCard>
            <FalconComponentCard.Header
                title="J'ai utilisé du matériel"
            />
            <FalconComponentCard.Body>
                {isLoading ? <LoaderInfiniteLoop/> :
                    <Form onSubmit={handleSubmit(ajouterConso)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Qu'avez-vous utilisé ?</Form.Label>
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
                            {watch("idMaterielCatalogue") == null ? catalogueQuickSelect.map((cat, i)=>{return(
                                    <Button className='mt-1 me-1' variant='outline-info' size='sm' onClick={()=>{setValue("idMaterielCatalogue", cat.value)}}>{cat.label}</Button>
                            )}) : null}
                        </Form.Group>

                        {watch("idMaterielCatalogue") && watch("idMaterielCatalogue") > 0 ?
                            <>
                                <Form.Group className="mb-3">
                                    <Form.Label>Où l'avez vous pris ?</Form.Label>
                                    <Select
                                        id="idLot"
                                        name="idLot"
                                        size="sm"
                                        classNamePrefix="react-select"
                                        closeMenuOnSelect={true}
                                        isClearable={true}
                                        isSearchable={false}
                                        isDisabled={isLoading}
                                        placeholder='Aucun emplacement selectionné'
                                        options={lots}
                                        value={lots.find(c => c.value === watch("idLot"))}
                                        onChange={val => val != null ? setValue("idLot", val.value) : setValue("idLot", null)}
                                    />
                                    <small className="text-danger">{errors.idLot?.message}</small>
                                    {watch("idLot") == null ? lotsImpactes.map((lot, i)=>{return(
                                            <Button className='mt-1 me-1' variant='outline-info' size='sm' onClick={()=>{setValue("idLot", lot.value)}}>{lot.label}</Button>
                                    )}) : null}
                                </Form.Group>

                                {watch("idLot") && watch("idLot") > 0 ?
                                    <>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Quelle quantité ?</Form.Label>
                                            <Form.Control size="sm" type="number" min="1" name='quantiteConsommation' id='quantiteConsommation' {...register('quantiteConsommation')}/>
                                            <small className="text-danger">{errors.quantiteConsommation?.message}</small>
                                            {watch("quantiteConsommation") == null ? [1,2,5,10].map((value, i)=>{return(
                                                    <Button className='mt-1 me-1' variant='outline-info' size='sm' onClick={()=>{setValue("quantiteConsommation", value)}}>{value}</Button>
                                            )}) : null}
                                        </Form.Group>
                                        
                                        {watch("quantiteConsommation") && watch("quantiteConsommation") > 0 ?
                                            <div className="d-grid gap-2 mt-3">
                                                <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                                            </div>
                                        :null }
                                    </>
                                : null}
                            </>
                        : null}

                    </Form>
                }
            </FalconComponentCard.Body>
        </FalconComponentCard>
    );
};

AjouterConsommable.propTypes = {};

export default AjouterConsommable;
