import React, { useEffect } from 'react';
import { Form, Button, } from 'react-bootstrap';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { configAlertesBenevolesForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

const ConfigGeneraleTabAlertes = ({
    config,
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(configAlertesBenevolesForm),
    });

    const modifierEnBase = async (data) => {
        try {
            setIsLoading(true);

            const getConfig = await Axios.post('/settingsTechniques/saveAlertesConfig',{
                alertes_benevoles_lots: data.alertes_benevoles_lots,
                alertes_benevoles_vehicules: data.alertes_benevoles_vehicules,
                alertes_benevoles_vhf: data.alertes_benevoles_vhf,
                consommation_benevoles: data.consommation_benevoles,
                consommation_benevoles_auto: data.consommation_benevoles_auto,
            });

            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const initFormData = () => {
        setValue('alertes_benevoles_lots', config.alertes_benevoles_lots);
        setValue('alertes_benevoles_vehicules', config.alertes_benevoles_vehicules);
        setValue('alertes_benevoles_vhf', config.alertes_benevoles_vhf);
        setValue('consommation_benevoles', config.consommation_benevoles);
        setValue('consommation_benevoles_auto', config.consommation_benevoles_auto);
    }

    useEffect(() => {
        initFormData();
    },[])
    useEffect(() => {
        initFormData();
    },[config])

    return (<>
        <Form onSubmit={handleSubmit(modifierEnBase)}>
            <Form.Group className="mb-3">
                <Form.Label>Les utilisateurs non-authentifiés peuvent remonter des alertes sur:</Form.Label>
                <Form.Check
                    type='switch'
                    id="alertes_benevoles_lots"
                    name="alertes_benevoles_lots"
                    label="Les lots"
                    checked={watch("alertes_benevoles_lots")}
                    onClick={(e)=>{setValue("alertes_benevoles_lots", !watch("alertes_benevoles_lots"))}}
                />
                <small className="text-danger">{errors.alertes_benevoles_lots?.message}</small>
                <Form.Check
                    type='switch'
                    id="alertes_benevoles_vehicules"
                    name="alertes_benevoles_vehicules"
                    label="Les véhicules"
                    checked={watch("alertes_benevoles_vehicules")}
                    onClick={(e)=>{setValue("alertes_benevoles_vehicules", !watch("alertes_benevoles_vehicules"))}}
                />
                <small className="text-danger">{errors.alertes_benevoles_vehicules?.message}</small>
                <Form.Check
                    type='switch'
                    id="alertes_benevoles_vhf"
                    name="alertes_benevoles_vhf"
                    label="Les transmissions"
                    checked={watch("alertes_benevoles_vhf")}
                    onClick={(e)=>{setValue("alertes_benevoles_vhf", !watch("alertes_benevoles_vhf"))}}
                />
                <small className="text-danger">{errors.alertes_benevoles_vhf?.message}</small>
            </Form.Group>
            <Form.Group className="mb-3">
                <Form.Label>Les utilisateurs non-authentifiés peuvent saisir des rapports de consommation sur:</Form.Label>
                <Form.Check
                    type='switch'
                    id="consommation_benevoles"
                    name="consommation_benevoles"
                    label="Les lots"
                    checked={watch("consommation_benevoles")}
                    onClick={(e)=>{setValue("consommation_benevoles", !watch("consommation_benevoles"))}}
                />
                <small className="text-danger">{errors.consommation_benevoles?.message}</small>
                <Form.Check
                    type='switch'
                    id="consommation_benevoles_auto"
                    name="consommation_benevoles_auto"
                    label="Les rapports sont automatiquement validés"
                    checked={watch("consommation_benevoles_auto")}
                    onClick={(e)=>{setValue("consommation_benevoles_auto", !watch("consommation_benevoles_auto"))}}
                />
                <small className="text-danger">{errors.consommation_benevoles_auto?.message}</small>
            </Form.Group>

            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
        </Form>
    </>);
};

ConfigGeneraleTabAlertes.propTypes = {};

export default ConfigGeneraleTabAlertes;
