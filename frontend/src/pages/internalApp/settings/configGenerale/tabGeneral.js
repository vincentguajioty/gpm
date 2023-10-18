import React, { useEffect } from 'react';
import { Card, Tabs, Tab, Form, Button, } from 'react-bootstrap';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { configGeneraleForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

const ConfigGeneraleTabGeneral = ({
    config,
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(configGeneraleForm),
    });

    const modifierEnBase = async (data) => {
        try {
            setIsLoading(true);

            const getConfig = await Axios.post('/settingsTechniques/saveGlobalConfig',{
                appname: data.appname,
                urlsite: data.urlsite,
                mailserver: data.mailserver,
                mailcopy: data.mailcopy,
                maintenance: data.maintenance,
                resetPassword: data.resetPassword,
            });

            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const initFormData = () => {
        setValue('appname', config.appname);
        setValue('urlsite', config.urlsite);
        setValue('mailserver', config.mailserver);
        setValue('mailcopy', config.mailcopy);
        setValue('maintenance', config.maintenance);
        setValue('resetPassword', config.resetPassword);
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
                <Form.Label>Nom de l'application:</Form.Label>
                <small className="text-warning ms-1">Attention configuration à faire également dans les .env compilés de l'application</small>
                <Form.Control size="sm" type="text" name="appname" id="appname" {...register('appname')}/>
                <small className="text-danger">{errors.appname?.message}</small>
            </Form.Group>
            <Form.Group className="mb-3">
                <Form.Label>URL de l'application:</Form.Label>
                <Form.Control size="sm" type="text" name="urlsite" id="urlsite" {...register('urlsite')}/>
                <small className="text-danger">{errors.urlsite?.message}</small>
            </Form.Group>
            <Form.Group className="mb-3">
                <Form.Label>Adresse email de l'expéditeur:</Form.Label>
                <small className="text-warning ms-1">Attention il est fortement recommandé d'aligner cette configuration avec l'expéditeur SMTP paramétré dans le .env</small>
                <Form.Control size="sm" type="email" name="mailserver" id="mailserver" {...register('mailserver')}/>
                <small className="text-danger">{errors.mailserver?.message}</small>
            </Form.Group>
            <Form.Group className="mb-3">
                <Form.Label>L'adresse mail expéditrice doit être en copie de tous les emails sortants:</Form.Label>
                <Form.Check
                    type='switch'
                    id="mailcopy"
                    name="mailcopy"
                    label={"Mettre l'adresse mail en copie ("+watch("mailserver")+")"}
                    checked={watch("mailcopy")}
                    onClick={(e)=>{setValue("mailcopy", !watch("mailcopy"))}}
                />
                <small className="text-danger">{errors.appname?.message}</small>
            </Form.Group>
            <Form.Group className="mb-3">
                <Form.Label>Mise en maintenance:</Form.Label>
                <Form.Check
                    type='switch'
                    id="maintenance"
                    name="maintenance"
                    label={"Mettre l'application en mode maintenance"}
                    checked={watch("maintenance")}
                    onClick={(e)=>{setValue("maintenance", !watch("maintenance"))}}
                />
                <small className="text-danger">{errors.maintenance?.message}</small>
            </Form.Group>
            <Form.Group className="mb-3">
                <Form.Label>Les utilisateurs locaux peuvent réinitialiser leur mot de passe:</Form.Label>
                <Form.Check
                    type='switch'
                    id="resetPassword"
                    name="resetPassword"
                    label={watch("resetPassword") ? "Réinitiaisation activée" : "Réinitiaisation désactivée"}
                    checked={watch("resetPassword")}
                    onClick={(e)=>{setValue("resetPassword", !watch("resetPassword"))}}
                />
                <small className="text-danger">{errors.appname?.message}</small>
            </Form.Group>

            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
        </Form>
    </>);
};

ConfigGeneraleTabGeneral.propTypes = {};

export default ConfigGeneraleTabGeneral;
