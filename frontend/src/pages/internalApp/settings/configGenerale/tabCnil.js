import React, { useEffect } from 'react';
import { Card, Tabs, Tab, Form, Button, Alert, } from 'react-bootstrap';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { configCnilForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

const ConfigGeneraleTabCNIL = ({
    config,
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(configCnilForm),
    });

    const modifierEnBase = async (data) => {
        try {
            setIsLoading(true);

            const getConfig = await Axios.post('/settingsTechniques/saveCnilConfig',{
                mailcnil: data.mailcnil,
                cnilDisclaimer: data.cnilDisclaimer,
            });

            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const initFormData = () => {
        setValue('mailcnil', config.mailcnil);
        setValue('cnilDisclaimer', config.cnilDisclaimer);
    }

    useEffect(() => {
        initFormData();
    },[])
    useEffect(() => {
        initFormData();
    },[config])

    return (<>
        <Alert variant='warning'>La modification de l'un de ces élément entrainera une remise à zéro des acceptation des CGU pour tous les utilisateurs. Tous les utilisateurs seront déconnectés de la plateforme. Lors de leur reconnexion,  ils devront accepter les CGU à nouveau.</Alert>
        
        <Form onSubmit={handleSubmit(modifierEnBase)}>
            <Form.Group className="mb-3">
                <Form.Label>Adresse mail du référent CNIL:</Form.Label>
                <Form.Control size="sm" type="email" name="mailcnil" id="mailcnil" {...register('mailcnil')}/>
                <small className="text-danger">{errors.mailcnil?.message}</small>
            </Form.Group>
            <Form.Group className="mb-3">
                <Form.Label>Conditions générales d'utilisation:</Form.Label>
                <Form.Control size="sm" as="textarea" rows={15} name="cnilDisclaimer" id="cnilDisclaimer" {...register("cnilDisclaimer")}/>
                <small className="text-danger">{errors.cnilDisclaimer?.message}</small>
            </Form.Group>

            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
        </Form>
    </>);
};

ConfigGeneraleTabCNIL.propTypes = {};

export default ConfigGeneraleTabCNIL;
