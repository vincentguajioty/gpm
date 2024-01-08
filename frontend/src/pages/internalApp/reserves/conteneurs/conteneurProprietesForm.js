import React, { useState, useEffect } from 'react';
import { Button, Form, Table } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { conteneursUpdateForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const ConteneurProprietesForm = ({conteneur, setModeEdition, setPageNeedsRefresh}) => {
    //formulaire d'ajout
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(conteneursUpdateForm),
    });

    const [lieux, setLieux] = useState([]);
    const [personnes, setPersonnes] = useState([]);

    const loadForm = async () => {
        try {
            setLoading(true);

            let getData = await Axios.get('/select/getLieux');
            setLieux(getData.data);

            getData = await Axios.get('/select/getActivePersonnes');
            setPersonnes(getData.data);

            setValue("idLieu", conteneur.idLieu);
            setValue("libelleConteneur", conteneur.libelleConteneur);
            setValue("dateDernierInventaire", conteneur.dateDernierInventaire != null ? new Date(conteneur.dateDernierInventaire) : null);
            setValue("frequenceInventaire", conteneur.frequenceInventaire);
            setValue("dispoBenevoles", conteneur.dispoBenevoles ? true : false);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        loadForm();
    },[])

    const updateConteneur = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/reserves/updateConteneur',{
                idConteneur: conteneur.idConteneur,
                idLieu: data.idLieu,
                libelleConteneur: data.libelleConteneur,
                dateDernierInventaire: data.dateDernierInventaire,
                frequenceInventaire: data.frequenceInventaire,
                dispoBenevoles: data.dispoBenevoles,
            });

            setPageNeedsRefresh(true);
            setModeEdition(false);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    return (
        isLoading ? <LoaderInfiniteLoop/> :
        <Form onSubmit={handleSubmit(updateConteneur)}>
            <Table className="fs--1 mt-3" size='sm' responsive>
                <tbody>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Libellé</td>
                        <td>
                            <Form.Control size="sm" type="text" name='libelleConteneur' id='libelleConteneur' {...register('libelleConteneur')}/>
                            <small className="text-danger">{errors.libelleConteneur?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Lieu de stockage</td>
                        <td>
                            <Select
                                id="idLieu"
                                name="idLieu"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun lieu selectionné'
                                options={lieux}
                                value={lieux.find(c => c.value === watch("idLieu"))}
                                onChange={val => val != null ? setValue("idLieu", val.value) : setValue("idLieu", null)}
                            />
                            <small className="text-danger">{errors.idLieu?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Date du dernier inventaire</td>
                        <td>
                            <DatePicker
                                selected={watch("dateDernierInventaire")}
                                onChange={(date)=>setValue("dateDernierInventaire", date)}
                                formatWeekDay={day => day.slice(0, 3)}
                                className='form-control'
                                placeholderText="Choisir une date"
                                dateFormat="dd/MM/yyyy"
                                fixedHeight
                                locale="fr"
                            />
                            <small className="text-danger">{errors.dateDernierInventaire?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Fréquence d'inventaire (jours)</td>
                        <td>
                            <Form.Control size="sm" type="text" name='frequenceInventaire' id='frequenceInventaire' {...register('frequenceInventaire')}/>
                            <small className="text-danger">{errors.frequenceInventaire?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Disponible aux bénévoles</td>
                        <td>
                            <Form.Check
                                id='dispoBenevoles'
                                name='dispoBenevoles'
                                label="Réappro de consommables"
                                type='switch'
                                checked={watch("dispoBenevoles")}
                                onClick={(e)=>{
                                    setValue("dispoBenevoles", !watch("dispoBenevoles"))
                                }}
                            />
                            <small className="text-danger">{errors.dispoBenevoles?.message}</small>
                        </td>
                    </tr>
                </tbody>
            </Table>
            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
        </Form>
    );
};

ConteneurProprietesForm.propTypes = {};

export default ConteneurProprietesForm;
