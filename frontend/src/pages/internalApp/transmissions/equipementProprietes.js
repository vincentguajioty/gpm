import React, {useState} from 'react';
import { Card, Form , Table, Button } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import PlansCanaux from './plansCanaux';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vhfEquipementsUpdateForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const EquipementVhfProprietes = ({equipement, setPageNeedsRefresh}) => {
    const [modeEdition, setModeEdition] = useState(false);
    const handleEdition = () => {
        if(!modeEdition)
        {
            initForm();
        }
        setModeEdition(!modeEdition);
    }

    //Edit Form
    const [isLoading, setLoading] = useState(false);
    const [formReady, setFormReady] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vhfEquipementsUpdateForm),
    });
    const [etats, setEtats] = useState([]);
    const [types, setTypes] = useState([]);
    const [technologies, setTechnologies] = useState([]);
    const [plans, setPlans] = useState([]);
    const [personnes, setPersonnes] = useState([]);
    const initForm = async () => {
        try {
            setFormReady(false);

            setValue("vhfMarqueModele", equipement.vhfMarqueModele);
            setValue("vhfSN", equipement.vhfSN);
            setValue("vhfIndicatif", equipement.vhfIndicatif);
            setValue("idVhfEtat", equipement.idVhfEtat);
            setValue("idVhfType", equipement.idVhfType);
            setValue("idVhfTechno", equipement.idVhfTechno);
            setValue("idVhfPlan", equipement.idVhfPlan);
            setValue("dateDerniereProg", equipement.dateDerniereProg ? new Date(equipement.dateDerniereProg) : null);
            setValue("idResponsable", equipement.idResponsable);
            setValue("remarquesVhfEquipement", equipement.remarquesVhfEquipement);

            let getData = await Axios.get('/settingsMetiers/getEtatsVHF');
            setEtats(getData.data);
            getData = await Axios.get('/settingsMetiers/getVHFTypesEquipements');
            setTypes(getData.data);
            getData = await Axios.get('/settingsMetiers/getTechnologiesVHF');
            setTechnologies(getData.data);
            getData = await Axios.get('/vhf/getPlans');
            setPlans(getData.data);
            getData = await Axios.get('/settingsUtilisateurs/getAllUsers');
            setPersonnes(getData.data);

            setFormReady(true);
        } catch (error) {
            console.log(error)
        }
    }
    const modifierEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/vhf/updateEquipement',{
                idVhfEquipement: equipement.idVhfEquipement,
                vhfMarqueModele: data.vhfMarqueModele,
                vhfSN: data.vhfSN,
                vhfIndicatif: data.vhfIndicatif,
                idVhfEtat: data.idVhfEtat,
                idVhfType: data.idVhfType,
                idVhfTechno: data.idVhfTechno,
                idVhfPlan: data.idVhfPlan,
                dateDerniereProg: data.dateDerniereProg,
                idResponsable: data.idResponsable,
                remarquesVhfEquipement: data.remarquesVhfEquipement,
            });

            setPageNeedsRefresh(true);
            setModeEdition(false);
            setFormReady(false);
            reset();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    const nl2br = require('react-nl2br');
    return(<>
        <Card className="mb-3">
            <Card.Header className="p-2 border-bottom">
                <Flex>
                    <div className="p-2 flex-grow-1">
                        Détails de l'équipement
                    </div>
                    <div className="p-2">
                        <Form.Check 
                            type='switch'
                            id='defaultSwitch'
                            label='Modifier'
                            onClick={handleEdition}
                            checked={modeEdition}
                            disabled={!HabilitationService.habilitations['vhf_equipement_modification']}
                        />
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                {modeEdition ?
                    <Form onSubmit={handleSubmit(modifierEntree)}>
                        {formReady ? <>
                            <Table className="fs--1 mt-3" responsive>
                                <tbody>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Indicatif</td>
                                        <td>
                                            <Form.Control size="sm" type="text" name='vhfIndicatif' id='vhfIndicatif' {...register('vhfIndicatif')}/>
                                            <small className="text-danger">{errors.vhfIndicatif?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Marque/Modèle</td>
                                        <td>
                                            <Form.Control size="sm" type="text" name='vhfMarqueModele' id='vhfMarqueModele' {...register('vhfMarqueModele')}/>
                                            <small className="text-danger">{errors.vhfMarqueModele?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>SN</td>
                                        <td>
                                            <Form.Control size="sm" type="text" name='vhfSN' id='vhfSN' {...register('vhfSN')}/>
                                            <small className="text-danger">{errors.vhfSN?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Etat</td>
                                        <td>
                                            <Form.Select size="sm" name="idVhfEtat" id="idVhfEtat" {...register("idVhfEtat")}>
                                                <option key="0" value="">--- Aucun état ---</option>
                                                {etats.map((item, i) => {
                                                    return (<option key={item.idVhfEtat} value={item.idVhfEtat}>{item.libelleVhfEtat}</option>);
                                                })}
                                            </Form.Select>
                                            <small className="text-danger">{errors.idVhfEtat?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Type</td>
                                        <td>
                                            <Form.Select size="sm" name="idVhfType" id="idVhfType" {...register("idVhfType")}>
                                                <option key="0" value="">--- Aucun type ---</option>
                                                {types.map((item, i) => {
                                                    return (<option key={item.idVhfType} value={item.idVhfType}>{item.libelleType}</option>);
                                                })}
                                            </Form.Select>
                                            <small className="text-danger">{errors.idVhfType?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Technologie</td>
                                        <td>
                                            <Form.Select size="sm" name="idVhfTechno" id="idVhfTechno" {...register("idVhfTechno")}>
                                                <option key="0" value="">--- Aucune technologie ---</option>
                                                {technologies.map((item, i) => {
                                                    return (<option key={item.idVhfTechno} value={item.idVhfTechno}>{item.libelleTechno}</option>);
                                                })}
                                            </Form.Select>
                                            <small className="text-danger">{errors.idVhfTechno?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Plan de programmation</td>
                                        <td>
                                            <Form.Select size="sm" name="idVhfPlan" id="idVhfPlan" {...register("idVhfPlan")}>
                                                <option key="0" value="">--- Aucune technologie ---</option>
                                                {plans.map((item, i) => {
                                                    return (<option key={item.idVhfPlan} value={item.idVhfPlan}>{item.libellePlan}</option>);
                                                })}
                                            </Form.Select>
                                            <small className="text-danger">{errors.idVhfPlan?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Date de dernière programmation</td>
                                        <td>
                                            <DatePicker
                                                selected={watch("dateDerniereProg")}
                                                onChange={(date)=>setValue("dateDerniereProg", date)}
                                                formatWeekDay={day => day.slice(0, 3)}
                                                className='form-control'
                                                placeholderText="Choisir une date"
                                                dateFormat="dd/MM/yyyy"
                                                fixedHeight
                                                locale="fr"
                                            />
                                            <small className="text-danger">{errors.idResponsable?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Responsable</td>
                                        <td>
                                            <Form.Select size="sm" name="idResponsable" id="idResponsable" {...register("idResponsable")}>
                                                <option key="0" value="">--- Aucun responsable ---</option>
                                                {personnes.map((item, i) => {
                                                    return (<option key={item.idPersonne} value={item.idPersonne}>{item.identifiant}</option>);
                                                })}
                                            </Form.Select>
                                            <small className="text-danger">{errors.idResponsable?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Remarques</td>
                                        <td>
                                            <Form.Control size="sm" as="textarea" rows={3} name="remarquesVhfEquipement" id="remarquesVhfEquipement" {...register("remarquesVhfEquipement")}/>
                                            <small className="text-danger">{errors.remarquesVhfEquipement?.message}</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </Table>
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                        </>: <LoaderInfiniteLoop/>}
                    </Form>
                :
                    <Table className="fs--1 mt-3" responsive>
                        <tbody>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Indicatif</td>
                                <td>{equipement.vhfIndicatif}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Marque/Modèle</td>
                                <td>{equipement.vhfMarqueModele}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>SN</td>
                                <td>{equipement.vhfSN}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Etat</td>
                                <td>{equipement.libelleVhfEtat}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Type</td>
                                <td>{equipement.libelleType}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Technologie</td>
                                <td>{equipement.libelleTechno}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Plan de programmation</td>
                                <td>{equipement.idVhfPlan > 0 ?
                                    <PlansCanaux
                                        vhfPlan={{idVhfPlan: equipement.idVhfPlan, libellePlan: equipement.libellePlan}}
                                        lockEdit={true}
                                        displayNameInButton={true}
                                    />
                                    : <SoftBadge bg='danger'>Non Programmé</SoftBadge>}
                                </td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Date de dernière programmation</td>
                                <td>{equipement.dateDerniereProg ? moment(equipement.dateDerniereProg).format('DD/MM/YYYY') : null}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Responsable</td>
                                <td>{equipement.identifiant}</td>
                            </tr>
                            <tr>
                                <td className="bg-100" style={{ width: '30%' }}>Remarques</td>
                                <td>{nl2br(equipement.remarquesVhfEquipement)}</td>
                            </tr>
                        </tbody>
                    </Table>
                }
            </Card.Body>
        </Card>
    </>);
};

EquipementVhfProprietes.propTypes = {};

export default EquipementVhfProprietes;