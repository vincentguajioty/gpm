import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Modal, Table } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';

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

    const loadForm = async () => {
        try {
            setLoading(true);

            let getData = await Axios.get('/settingsMetiers/getLieux');
            setLieux(getData.data);

            getData = await Axios.get('/settingsMetiers/getTypesVehicules');
            setVehiculesType(getData.data);

            getData = await Axios.get('/select/getNotificationsEnabled');
            setNotifications(getData.data);

            getData = await Axios.get('/settingsUtilisateurs/getAllUsers');
            setPersonnes(getData.data);

            getData = await Axios.get('/settingsMetiers/getEtatsVehicules');
            setVehiculesEtat(getData.data);

            getData = await Axios.get('/settingsMetiers/getCarburants');
            setCarburants(getData.data);
            
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
            setValue("pneusAVhivers", vehicule.pneusAVhivers);
            setValue("pneusARhivers", vehicule.pneusARhivers);
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
            setValue("affichageSyntheseDesinfections", vehicule.affichageSyntheseDesinfections);
            setValue("affichageSyntheseHealth", vehicule.affichageSyntheseHealth);

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
                pneusAVhivers: data.pneusAVhivers,
                pneusARhivers: data.pneusARhivers,
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
                affichageSyntheseDesinfections: data.affichageSyntheseDesinfections,
                affichageSyntheseHealth: data.affichageSyntheseHealth,
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
                        <td className="bg-100" style={{ width: '30%' }}>Carburant</td>
                        <td>
                            <Form.Select size="sm" name="idCarburant" id="idCarburant" {...register("idCarburant")}>
                                <option key="0" value="">--- Aucune selection ---</option>
                                {carburants.map((item, i) => {
                                    return (<option key={item.idCarburant} value={item.idCarburant}>{item.libelleCarburant}</option>);
                                })}
                            </Form.Select>
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
                            <Form.Select size="sm" name="idVehiculesType" id="idVehiculesType" {...register("idVehiculesType")}>
                                <option key="0" value="">--- Aucune selection ---</option>
                                {vehiculesType.map((item, i) => {
                                    return (<option key={item.idVehiculesType} value={item.idVehiculesType}>{item.libelleType}</option>);
                                })}
                            </Form.Select>
                            <small className="text-danger">{errors.idVehiculesType?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Notifications</td>
                        <td>
                            <Form.Select size="sm" name="idNotificationEnabled" id="idNotificationEnabled" {...register("idNotificationEnabled")}>
                                {notifications.map((item, i) => {
                                    return (<option key={item.idNotificationEnabled} value={item.idNotificationEnabled}>{item.libelleNotificationEnabled}</option>);
                                })}
                            </Form.Select>
                            <small className="text-danger">{errors.idNotificationEnabled?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Lieu de parking</td>
                        <td>
                            <Form.Select size="sm" name="idLieu" id="idLieu" {...register("idLieu")}>
                                <option key="0" value="">--- Aucune selection ---</option>
                                {lieux.map((item, i) => {
                                    return (<option key={item.idLieu} value={item.idLieu}>{item.libelleLieu}</option>);
                                })}
                            </Form.Select>
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
                        <td className="bg-100" style={{ width: '30%' }}>Poids</td>
                        <td>
                            <Form.Control size="sm" type="number" min="0" step="1" name='poidsVehicule' id='poidsVehicule' {...register('poidsVehicule')}/>
                            <small className="text-danger">{errors.poidsVehicule?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Dimensions</td>
                        <td>
                            <Form.Control size="sm" type="number" min="0" step="1" name='dimensions' id='dimensions' {...register('dimensions')}/>
                            <small className="text-danger">{errors.dimensions?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td className="bg-100" style={{ width: '30%' }}>Responsable</td>
                        <td>
                            <Form.Select size="sm" name="idResponsable" id="idResponsable" {...register("idResponsable")}>
                                <option key="0" value="">--- Aucune selection ---</option>
                                {personnes.map((item, i) => {
                                    return (<option key={item.idPersonne} value={item.idPersonne}>{item.identifiant}</option>);
                                })}
                            </Form.Select>
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
                                id='pneusAVhivers'
                                name='pneusAVhivers'
                                label="Pneus hivers avant"
                                type='switch'
                                checked={watch("pneusAVhivers")}
                                onClick={(e)=>{
                                    setValue("pneusAVhivers", !watch("pneusAVhivers"))
                                }}
                            />
                            <small className="text-danger">{errors.pneusAVhivers?.message}</small>
                            <Form.Check
                                id='pneusARhivers'
                                name='pneusARhivers'
                                label="Pneus hivers arriere"
                                type='switch'
                                checked={watch("pneusARhivers")}
                                onClick={(e)=>{
                                    setValue("pneusARhivers", !watch("pneusARhivers"))
                                }}
                            />
                            <small className="text-danger">{errors.pneusARhivers?.message}</small>
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
                </tbody>
            </Table>
            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
        </Form>
    );
};

VehiculeProprietesForm.propTypes = {};

export default VehiculeProprietesForm;
