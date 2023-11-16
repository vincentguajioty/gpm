import React, {useState} from 'react';
import { Card, Form , Table, Button } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';
import Select from 'react-select';

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

            let getData = await Axios.get('/select/getEtatsVHF');
            setEtats(getData.data);
            getData = await Axios.get('/select/getVHFTypesEquipements');
            setTypes(getData.data);
            getData = await Axios.get('/select/getTechnologiesVHF');
            setTechnologies(getData.data);
            getData = await Axios.get('/select/getVhfPlans');
            setPlans(getData.data);
            getData = await Axios.get('/select/getPersonnes');
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
                                            <Select
                                                id="idVhfEtat"
                                                name="idVhfEtat"
                                                size="sm"
                                                classNamePrefix="react-select"
                                                closeMenuOnSelect={true}
                                                isClearable={true}
                                                isSearchable={true}
                                                isDisabled={isLoading}
                                                placeholder='Aucun état selectionné'
                                                options={etats}
                                                value={etats.find(c => c.value === watch("idVhfEtat"))}
                                                onChange={val => val != null ? setValue("idVhfEtat", val.value) : setValue("idVhfEtat", null)}
                                            />
                                            <small className="text-danger">{errors.idVhfEtat?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Type</td>
                                        <td>
                                            <Select
                                                id="idVhfType"
                                                name="idVhfType"
                                                size="sm"
                                                classNamePrefix="react-select"
                                                closeMenuOnSelect={true}
                                                isClearable={true}
                                                isSearchable={true}
                                                isDisabled={isLoading}
                                                placeholder='Aucun type selectionné'
                                                options={types}
                                                value={types.find(c => c.value === watch("idVhfType"))}
                                                onChange={val => val != null ? setValue("idVhfType", val.value) : setValue("idVhfType", null)}
                                            />
                                            <small className="text-danger">{errors.idVhfType?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Technologie</td>
                                        <td>
                                            <Select
                                                id="idVhfTechno"
                                                name="idVhfTechno"
                                                size="sm"
                                                classNamePrefix="react-select"
                                                closeMenuOnSelect={true}
                                                isClearable={true}
                                                isSearchable={true}
                                                isDisabled={isLoading}
                                                placeholder='Aucune technologie selectionnée'
                                                options={technologies}
                                                value={technologies.find(c => c.value === watch("idVhfTechno"))}
                                                onChange={val => val != null ? setValue("idVhfTechno", val.value) : setValue("idVhfTechno", null)}
                                            />
                                            <small className="text-danger">{errors.idVhfTechno?.message}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td className="bg-100" style={{ width: '30%' }}>Plan de programmation</td>
                                        <td>
                                            <Select
                                                id="idVhfPlan"
                                                name="idVhfPlan"
                                                size="sm"
                                                classNamePrefix="react-select"
                                                closeMenuOnSelect={true}
                                                isClearable={true}
                                                isSearchable={true}
                                                isDisabled={isLoading}
                                                placeholder='Aucun plan selectionné'
                                                options={plans}
                                                value={plans.find(c => c.value === watch("idVhfPlan"))}
                                                onChange={val => val != null ? setValue("idVhfPlan", val.value) : setValue("idVhfPlan", null)}
                                            />
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
                                            <Select
                                                id="idResponsable"
                                                name="idResponsable"
                                                size="sm"
                                                classNamePrefix="react-select"
                                                closeMenuOnSelect={true}
                                                isClearable={true}
                                                isSearchable={true}
                                                isDisabled={isLoading}
                                                placeholder='Aucun responsable selectionné'
                                                options={personnes}
                                                value={personnes.find(c => c.value === watch("idResponsable"))}
                                                onChange={val => val != null ? setValue("idResponsable", val.value) : setValue("idResponsable", null)}
                                            />
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