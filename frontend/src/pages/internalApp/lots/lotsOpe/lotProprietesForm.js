import React, { useState, useEffect } from 'react';
import { Button, Form, Table } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { lotsUpdateForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const LotProprietesForm = ({lot, setModeEdition, setPageNeedsRefresh}) => {
    //formulaire d'ajout
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(lotsUpdateForm),
    });

    const [lieux, setLieux] = useState([]);
    const [lotsType, setLotsType] = useState([]);
    const [notifications, setNotifications] = useState([]);
    const [personnes, setPersonnes] = useState([]);
    const [lotsEtat, setLotsEtat] = useState([]);
    const [vehicules, setVehicules] = useState([]);

    const loadForm = async () => {
        try {
            setLoading(true);

            let getData = await Axios.get('/select/getLieux');
            setLieux(getData.data);

            getData = await Axios.get('/select/getTypesLots');
            setLotsType(getData.data);

            getData = await Axios.get('/select/getNotificationsEnabled');
            setNotifications(getData.data);

            getData = await Axios.get('/select/getPersonnes');
            setPersonnes(getData.data);

            getData = await Axios.get('/select/getEtatsLots');
            setLotsEtat(getData.data);

            getData = await Axios.get('/select/getVehicules');
            setVehicules(getData.data);

            setValue("libelleLot", lot.libelleLot);
            setValue("idTypeLot", lot.idTypeLot);
            setValue("idNotificationEnabled", lot.idNotificationEnabled);
            setValue("idLieu", lot.idLieu);
            setValue("idPersonne", lot.idPersonne);
            setValue("dateDernierInventaire", lot.dateDernierInventaire != null ? new Date(lot.dateDernierInventaire) : null);
            setValue("frequenceInventaire", lot.frequenceInventaire);
            setValue("commentairesLots", lot.commentairesLots);
            setValue("idVehicule", lot.idVehicule);
            setValue("idLotsEtat", lot.idLotsEtat);
            setValue("dispoBenevoles", lot.dispoBenevoles ? true : false);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        loadForm();
    },[])

    const updateLot = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/lots/updateLot',{
                idLot: lot.idLot,
                libelleLot: data.libelleLot,
                idTypeLot: data.idTypeLot,
                idNotificationEnabled: data.idNotificationEnabled,
                idLieu: data.idLieu,
                idPersonne: data.idPersonne,
                dateDernierInventaire: data.dateDernierInventaire,
                frequenceInventaire: data.frequenceInventaire,
                commentairesLots: data.commentairesLots,
                idVehicule: data.idVehicule,
                idLotsEtat: data.idLotsEtat,
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
        <Form onSubmit={handleSubmit(updateLot)}>
            <Table className="fs--1 mt-3" size='sm' responsive>
                <tbody>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Libellé</td>
                        <td>
                            <Form.Control size="sm" type="text" name='libelleLot' id='libelleLot' {...register('libelleLot')}/>
                            <small className="text-danger">{errors.libelleLot?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Type/Référentiel</td>
                        <td>
                            <Select
                                id="idTypeLot"
                                name="idTypeLot"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun type/ref selectionné'
                                options={lotsType}
                                value={lotsType.find(c => c.value === watch("idTypeLot"))}
                                onChange={val => val != null ? setValue("idTypeLot", val.value) : setValue("idTypeLot", null)}
                            />
                            <small className="text-danger">{errors.idTypeLot?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Etat</td>
                        <td>
                            <Select
                                id="idLotsEtat"
                                name="idLotsEtat"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun état selectionnée'
                                options={lotsEtat}
                                value={lotsEtat.find(c => c.value === watch("idLotsEtat"))}
                                onChange={val => val != null ? setValue("idLotsEtat", val.value) : setValue("idLotsEtat", null)}
                            />
                            <small className="text-danger">{errors.idLotsEtat?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Notifications</td>
                        <td>
                            <Select
                                id="idNotificationEnabled"
                                name="idNotificationEnabled"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucune notif selectionnée'
                                options={notifications}
                                value={notifications.find(c => c.value === watch("idNotificationEnabled"))}
                                onChange={val => val != null ? setValue("idNotificationEnabled", val.value) : setValue("idNotificationEnabled", null)}
                            />
                            <small className="text-danger">{errors.idNotificationEnabled?.message}</small>
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
                        <td className="bg-100" style={{ width: '30%' }}>Personne responsable</td>
                        <td>
                            <Select
                                id="idPersonne"
                                name="idPersonne"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun responsable selectionné'
                                options={personnes}
                                value={personnes.find(c => c.value === watch("idPersonne"))}
                                onChange={val => val != null ? setValue("idPersonne", val.value) : setValue("idPersonne", null)}
                            />
                            <small className="text-danger">{errors.idPersonne?.message}</small>
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
                        <td className="bg-100" style={{ width: '30%' }}>Véhicule d'affectation</td>
                        <td>
                            <Select
                                id="idVehicule"
                                name="idVehicule"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun véhicule selectionné'
                                options={vehicules}
                                value={vehicules.find(c => c.value === watch("idVehicule"))}
                                onChange={val => val != null ? setValue("idVehicule", val.value) : setValue("idVehicule", null)}
                            />
                            <small className="text-danger">{errors.idVehicule?.message}</small>
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
                        <td className="bg-100" style={{ width: '30%' }}>Disponible aux bénéboles</td>
                        <td>
                            <Form.Check
                                id='dispoBenevoles'
                                name='dispoBenevoles'
                                label="Rapports de consommation"
                                type='switch'
                                checked={watch("dispoBenevoles")}
                                onClick={(e)=>{
                                    setValue("dispoBenevoles", !watch("dispoBenevoles"))
                                }}
                            />
                            <small className="text-danger">{errors.dispoBenevoles?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Commentaires</td>
                        <td>
                        <Form.Control size="sm" as="textarea" rows={5} name={"commentairesLots"} id={"commentairesLots"} {...register("commentairesLots")}/>
                            <small className="text-danger">{errors.commentairesLots?.message}</small>
                        </td>
                    </tr>
                </tbody>
            </Table>
            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
        </Form>
    );
};

LotProprietesForm.propTypes = {};

export default LotProprietesForm;
