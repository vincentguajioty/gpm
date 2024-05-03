import React, { useState, useEffect } from 'react';
import { Button, Form, Table } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import Select from 'react-select';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vehiculeUpdateForm } from 'helpers/yupValidationSchema';

const VehiculeProprietesForm = ({vehicule, setModeEdition, setPageNeedsRefresh}) => {
    //formulaire d'ajout
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vehiculeUpdateForm),
    });

    const [lieux, setLieux] = useState([]);
    const [vehiculesType, setVehiculesType] = useState([]);
    const [notifications, setNotifications] = useState([]);
    const [personnes, setPersonnes] = useState([]);
    const [vehiculesEtat, setVehiculesEtat] = useState([]);
    const [carburants, setCarburants] = useState([]);
    const [pneumatiques, setPneumatiques] = useState([]);

    const loadForm = async () => {
        try {
            setLoading(true);

            let getData = await Axios.get('/select/getLieux');
            setLieux(getData.data);

            getData = await Axios.get('/select/getTypesVehicules');
            setVehiculesType(getData.data);

            getData = await Axios.get('/select/getNotificationsEnabled');
            setNotifications(getData.data);

            getData = await Axios.get('/select/getActivePersonnes');
            setPersonnes(getData.data);

            getData = await Axios.get('/select/getEtatsVehicules');
            setVehiculesEtat(getData.data);

            getData = await Axios.get('/select/getCarburants');
            setCarburants(getData.data);

            getData = await Axios.get('/select/getPneumatiques');
            setPneumatiques(getData.data);
            
            setValue("libelleVehicule", vehicule.libelleVehicule);
            setValue("immatriculation", vehicule.immatriculation);
            setValue("marqueModele", vehicule.marqueModele);
            setValue("idLieu", vehicule.idLieu);
            setValue("nbPlaces", vehicule.nbPlaces);
            setValue("dimensions", vehicule.dimensions);
            setValue("idVehiculesType", vehicule.idVehiculesType);
            setValue("idNotificationEnabled", vehicule.idNotificationEnabled);
            setValue("idResponsable", vehicule.idResponsable);
            setValue("dateAchat", vehicule.dateAchat ? new Date(vehicule.dateAchat) : null);
            setValue("assuranceNumero", vehicule.assuranceNumero);
            setValue("climatisation", vehicule.climatisation);
            setValue("signaletiqueOrange", vehicule.signaletiqueOrange);
            setValue("signaletiqueBleue", vehicule.signaletiqueBleue);
            setValue("signaletique2tons", vehicule.signaletique2tons);
            setValue("signaletique3tons", vehicule.signaletique3tons);
            setValue("pmv", vehicule.pmv);
            setValue("fleche", vehicule.fleche);
            setValue("nbCones", vehicule.nbCones);
            setValue("poidsVehicule", vehicule.poidsVehicule);
            setValue("priseAlimentation220", vehicule.priseAlimentation220);
            setValue("remarquesVehicule", vehicule.remarquesVehicule);
            setValue("idVehiculesEtat", vehicule.idVehiculesEtat);
            setValue("idCarburant", vehicule.idCarburant);
            setValue("idPneumatiqueAvant", vehicule.idPneumatiqueAvant);
            setValue("idPneumatiqueArriere", vehicule.idPneumatiqueArriere);
            setValue("dimensionPneuAvant", vehicule.dimensionPneuAvant);
            setValue("dimensionPneuArriere", vehicule.dimensionPneuArriere);
            setValue("pressionPneuAvant", vehicule.pressionPneuAvant);
            setValue("pressionPneuArriere", vehicule.pressionPneuArriere);
            setValue("affichageSyntheseDesinfections", vehicule.affichageSyntheseDesinfections);
            setValue("affichageSyntheseHealth", vehicule.affichageSyntheseHealth);
            setValue("dispoBenevoles", vehicule.dispoBenevoles == true ? true : false);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        loadForm();
    },[])

    const updateVehicule = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/vehicules/updateVehicule',{
                idVehicule: vehicule.idVehicule,
                libelleVehicule: data.libelleVehicule,
                immatriculation: data.immatriculation,
                marqueModele: data.marqueModele,
                idLieu: data.idLieu,
                nbPlaces: data.nbPlaces,
                dimensions: data.dimensions,
                idVehiculesType: data.idVehiculesType,
                idNotificationEnabled: data.idNotificationEnabled,
                idResponsable: data.idResponsable,
                dateAchat: data.dateAchat,
                assuranceNumero: data.assuranceNumero,
                climatisation: data.climatisation,
                signaletiqueOrange: data.signaletiqueOrange,
                signaletiqueBleue: data.signaletiqueBleue,
                signaletique2tons: data.signaletique2tons,
                signaletique3tons: data.signaletique3tons,
                pmv: data.pmv,
                fleche: data.fleche,
                nbCones: data.nbCones,
                poidsVehicule: data.poidsVehicule,
                priseAlimentation220: data.priseAlimentation220,
                remarquesVehicule: data.remarquesVehicule,
                idVehiculesEtat: data.idVehiculesEtat,
                idCarburant: data.idCarburant,
                idPneumatiqueAvant: data.idPneumatiqueAvant,
                idPneumatiqueArriere: data.idPneumatiqueArriere,
                dimensionPneuAvant: data.dimensionPneuAvant,
                dimensionPneuArriere: data.dimensionPneuArriere,
                pressionPneuAvant: data.pressionPneuAvant,
                pressionPneuArriere: data.pressionPneuArriere,
                affichageSyntheseDesinfections: data.affichageSyntheseDesinfections,
                affichageSyntheseHealth: data.affichageSyntheseHealth,
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
        <Form onSubmit={handleSubmit(updateVehicule)}>
            <Table className="fs--1 mt-3" size='sm' responsive>
                <tbody>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Libellé/Indicatif</td>
                        <td>
                            <Form.Control size="sm" type="text" name='libelleVehicule' id='libelleVehicule' {...register('libelleVehicule')}/>
                            <small className="text-danger">{errors.libelleVehicule?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Marque/Modèle</td>
                        <td>
                            <Form.Control size="sm" type="text" name='marqueModele' id='marqueModele' {...register('marqueModele')}/>
                            <small className="text-danger">{errors.marqueModele?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Etat</td>
                        <td>
                            <Select
                                id="idVehiculesEtat"
                                name="idVehiculesEtat"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun état selectionné'
                                options={vehiculesEtat}
                                value={vehiculesEtat.find(c => c.value === watch("idVehiculesEtat"))}
                                onChange={val => val != null ? setValue("idVehiculesEtat", val.value) : setValue("idVehiculesEtat", null)}
                            />
                            <small className="text-danger">{errors.idVehiculesEtat?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Carburant</td>
                        <td>
                            <Select
                                id="idCarburant"
                                name="idCarburant"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun carburant selectionné'
                                options={carburants}
                                value={carburants.find(c => c.value === watch("idCarburant"))}
                                onChange={val => val != null ? setValue("idCarburant", val.value) : setValue("idCarburant", null)}
                            />
                            <small className="text-danger">{errors.idCarburant?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Immatriculation</td>
                        <td>
                            <Form.Control size="sm" type="text" name='immatriculation' id='immatriculation' {...register('immatriculation')}/>
                            <small className="text-danger">{errors.immatriculation?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Type</td>
                        <td>
                            <Select
                                id="idVehiculesType"
                                name="idVehiculesType"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun type selectionné'
                                options={vehiculesType}
                                value={vehiculesType.find(c => c.value === watch("idVehiculesType"))}
                                onChange={val => val != null ? setValue("idVehiculesType", val.value) : setValue("idVehiculesType", null)}
                            />
                            <small className="text-danger">{errors.idVehiculesType?.message}</small>
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
                                placeholder='Aucune notification selectionnée'
                                options={notifications}
                                value={notifications.find(c => c.value === watch("idNotificationEnabled"))}
                                onChange={val => val != null ? setValue("idNotificationEnabled", val.value) : setValue("idNotificationEnabled", null)}
                            />
                            <small className="text-danger">{errors.idNotificationEnabled?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Lieu de parking</td>
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
                        <td className="bg-100" style={{ width: '30%' }}>Nombre de places</td>
                        <td>
                            <Form.Control size="sm" type="number" min="0" step="1" name='nbPlaces' id='nbPlaces' {...register('nbPlaces')}/>
                            <small className="text-danger">{errors.nbPlaces?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Poids (tonnes)</td>
                        <td>
                            <Form.Control size="sm" type="number" min="0" step="0.001" name='poidsVehicule' id='poidsVehicule' {...register('poidsVehicule')}/>
                            <small className="text-danger">{errors.poidsVehicule?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Dimensions</td>
                        <td>
                            <Form.Control size="sm" type="text" name='dimensions' id='dimensions' {...register('dimensions')}/>
                            <small className="text-danger">{errors.dimensions?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Responsable</td>
                        <td>
                            <Select
                                id="idResponsable"
                                name="idResponsable"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucune personne selectionnée'
                                options={personnes}
                                value={personnes.find(c => c.value === watch("idResponsable"))}
                                onChange={val => val != null ? setValue("idResponsable", val.value) : setValue("idResponsable", null)}
                            />
                            <small className="text-danger">{errors.idResponsable?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Date d'achat</td>
                        <td>
                            <DatePicker
                                selected={watch("dateAchat")}
                                onChange={(date)=>setValue("dateAchat", date)}
                                formatWeekDay={day => day.slice(0, 3)}
                                className='form-control'
                                placeholderText="Choisir une date"
                                dateFormat="dd/MM/yyyy"
                                fixedHeight
                                locale="fr"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Numéro d'assurance</td>
                        <td>
                            <Form.Control size="sm" type="text" name='assuranceNumero' id='assuranceNumero' {...register('assuranceNumero')}/>
                            <small className="text-danger">{errors.assuranceNumero?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Equipements embarqués</td>
                        <td>
                            <Form.Check
                                id='priseAlimentation220'
                                name='priseAlimentation220'
                                label="Prise d'alimentation 220V"
                                type='switch'
                                checked={watch("priseAlimentation220")}
                                onClick={(e)=>{
                                    setValue("priseAlimentation220", !watch("priseAlimentation220"))
                                }}
                            />
                            <small className="text-danger">{errors.priseAlimentation220?.message}</small>
                            <Form.Check
                                id='climatisation'
                                name='climatisation'
                                label="Climatisation"
                                type='switch'
                                checked={watch("climatisation")}
                                onClick={(e)=>{
                                    setValue("climatisation", !watch("climatisation"))
                                }}
                            />
                            <small className="text-danger">{errors.climatisation?.message}</small>
                            <Form.Check
                                id='signaletiqueOrange'
                                name='signaletiqueOrange'
                                label="Feux oranges"
                                type='switch'
                                checked={watch("signaletiqueOrange")}
                                onClick={(e)=>{
                                    setValue("signaletiqueOrange", !watch("signaletiqueOrange"))
                                }}
                            />
                            <small className="text-danger">{errors.signaletiqueOrange?.message}</small>
                            <Form.Check
                                id='signaletiqueBleue'
                                name='signaletiqueBleue'
                                label="Feux bleus"
                                type='switch'
                                checked={watch("signaletiqueBleue")}
                                onClick={(e)=>{
                                    setValue("signaletiqueBleue", !watch("signaletiqueBleue"))
                                }}
                            />
                            <small className="text-danger">{errors.signaletiqueBleue?.message}</small>
                            <Form.Check
                                id='signaletique2tons'
                                name='signaletique2tons'
                                label="Sirène 2 tons 2 temps"
                                type='switch'
                                checked={watch("signaletique2tons")}
                                onClick={(e)=>{
                                    setValue("signaletique2tons", !watch("signaletique2tons"))
                                }}
                            />
                            <small className="text-danger">{errors.signaletique2tons?.message}</small>
                            <Form.Check
                                id='signaletique3tons'
                                name='signaletique3tons'
                                label="Sirène 2 tons 3 temps"
                                type='switch'
                                checked={watch("signaletique3tons")}
                                onClick={(e)=>{
                                    setValue("signaletique3tons", !watch("signaletique3tons"))
                                }}
                            />
                            <small className="text-danger">{errors.signaletique3tons?.message}</small>
                            <Form.Check
                                id='pmv'
                                name='pmv'
                                label="PMV"
                                type='switch'
                                checked={watch("pmv")}
                                onClick={(e)=>{
                                    setValue("pmv", !watch("pmv"))
                                }}
                            />
                            <small className="text-danger">{errors.pmv?.message}</small>
                            <Form.Check
                                id='fleche'
                                name='fleche'
                                label="Flèche/Triflash"
                                type='switch'
                                checked={watch("fleche")}
                                onClick={(e)=>{
                                    setValue("fleche", !watch("fleche"))
                                }}
                            />
                            <small className="text-danger">{errors.fleche?.message}</small>
                            <Form.Label>Cones:</Form.Label>
                            <Form.Control size="sm" type="number" min="0" step="1" name='nbCones' id='nbCones' {...register('nbCones')}/>
                            <small className="text-danger">{errors.nbCones?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Pneumatiques Train avant</td>
                        <td>
                            <Form.Label>Type:</Form.Label>
                            <Select
                                id="idPneumatiqueAvant"
                                name="idPneumatiqueAvant"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun type selectionné'
                                options={pneumatiques}
                                value={pneumatiques.find(c => c.value === watch("idPneumatiqueAvant"))}
                                onChange={val => val != null ? setValue("idPneumatiqueAvant", val.value) : setValue("idPneumatiqueAvant", null)}
                            />
                            <small className="text-danger">{errors.idPneumatiqueAvant?.message}</small>
                            
                            <Form.Label>Dimensions:</Form.Label>
                            <Form.Control size="sm" type="text" name='dimensionPneuAvant' id='dimensionPneuAvant' {...register('dimensionPneuAvant')}/>
                            <small className="text-danger">{errors.dimensionPneuAvant?.message}</small>
                            
                            <Form.Label>Pression (bar):</Form.Label>
                            <Form.Control size="sm" type="number" step="0.01" name='pressionPneuAvant' id='pressionPneuAvant' {...register('pressionPneuAvant')}/>
                            <small className="text-danger">{errors.pressionPneuAvant?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Pneumatiques Train arrière</td>
                        <td>
                            <Form.Label>Type:</Form.Label>
                            <Select
                                id="idPneumatiqueArriere"
                                name="idPneumatiqueArriere"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun type selectionné'
                                options={pneumatiques}
                                value={pneumatiques.find(c => c.value === watch("idPneumatiqueArriere"))}
                                onChange={val => val != null ? setValue("idPneumatiqueArriere", val.value) : setValue("idPneumatiqueArriere", null)}
                            />
                            <small className="text-danger">{errors.idPneumatiqueArriere?.message}</small>
                            
                            <Form.Label>Dimensions:</Form.Label>
                            <Form.Control size="sm" type="text" name='dimensionPneuArriere' id='dimensionPneuArriere' {...register('dimensionPneuArriere')}/>
                            <small className="text-danger">{errors.dimensionPneuArriere?.message}</small>
                            
                            <Form.Label>Pression (bar):</Form.Label>
                            <Form.Control size="sm" type="number" step="0.01" name='pressionPneuArriere' id='pressionPneuArriere' {...register('pressionPneuArriere')}/>
                            <small className="text-danger">{errors.pressionPneuArriere?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Panneaux de synthèse</td>
                        <td>
                            <Form.Check
                                id='affichageSyntheseDesinfections'
                                name='affichageSyntheseDesinfections'
                                label="Afficher le véhicule sur la synthèse des désinfections"
                                type='switch'
                                checked={watch("affichageSyntheseDesinfections")}
                                onClick={(e)=>{
                                    setValue("affichageSyntheseDesinfections", !watch("affichageSyntheseDesinfections"))
                                }}
                            />
                            <small className="text-danger">{errors.affichageSyntheseDesinfections?.message}</small>
                            <Form.Check
                                id='affichageSyntheseHealth'
                                name='affichageSyntheseHealth'
                                label="Afficher le véhicule sur la synthèse des maintenances techniques"
                                type='switch'
                                checked={watch("affichageSyntheseHealth")}
                                onClick={(e)=>{
                                    setValue("affichageSyntheseHealth", !watch("affichageSyntheseHealth"))
                                }}
                            />
                            <small className="text-danger">{errors.affichageSyntheseHealth?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Disponible aux bénévoles</td>
                        <td>
                            <Form.Check
                                id='dispoBenevoles'
                                name='dispoBenevoles'
                                label="Incidents"
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

VehiculeProprietesForm.propTypes = {};

export default VehiculeProprietesForm;
