import React, { useEffect } from 'react';
import { Form, Button, Table } from 'react-bootstrap';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { configNotifCommandesForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';

const ConfigGeneraleTabNotifs = ({
    config,
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(configNotifCommandesForm),
    });

    const modifierEnBase = async (data) => {
        try {
            setIsLoading(true);

            const getConfig = await Axios.post('/settingsTechniques/saveNotifsCommandesConfig',{
                notifications_commandes_demandeur_validation     : data.notifications_commandes_demandeur_validation,
                notifications_commandes_demandeur_validationOK   : data.notifications_commandes_demandeur_validationOK,
                notifications_commandes_demandeur_validationNOK  : data.notifications_commandes_demandeur_validationNOK,
                notifications_commandes_demandeur_passee         : data.notifications_commandes_demandeur_passee,
                notifications_commandes_demandeur_livraisonOK    : data.notifications_commandes_demandeur_livraisonOK,
                notifications_commandes_demandeur_livraisonNOK   : data.notifications_commandes_demandeur_livraisonNOK,
                notifications_commandes_demandeur_savOK          : data.notifications_commandes_demandeur_savOK,
                notifications_commandes_demandeur_cloture        : data.notifications_commandes_demandeur_cloture,
                notifications_commandes_demandeur_abandon        : data.notifications_commandes_demandeur_abandon,
                notifications_commandes_valideur_validation      : data.notifications_commandes_valideur_validation,
                notifications_commandes_valideur_validationOK    : data.notifications_commandes_valideur_validationOK,
                notifications_commandes_valideur_validationNOK   : data.notifications_commandes_valideur_validationNOK,
                notifications_commandes_valideur_passee          : data.notifications_commandes_valideur_passee,
                notifications_commandes_valideur_centreCout      : data.notifications_commandes_valideur_centreCout,
                notifications_commandes_valideur_livraisonOK     : data.notifications_commandes_valideur_livraisonOK,
                notifications_commandes_valideur_livraisonNOK    : data.notifications_commandes_valideur_livraisonNOK,
                notifications_commandes_valideur_savOK           : data.notifications_commandes_valideur_savOK,
                notifications_commandes_valideur_cloture         : data.notifications_commandes_valideur_cloture,
                notifications_commandes_valideur_abandon         : data.notifications_commandes_valideur_abandon,
                notifications_commandes_affectee_validation      : data.notifications_commandes_affectee_validation,
                notifications_commandes_affectee_validationOK    : data.notifications_commandes_affectee_validationOK,
                notifications_commandes_affectee_validationNOK   : data.notifications_commandes_affectee_validationNOK,
                notifications_commandes_affectee_passee          : data.notifications_commandes_affectee_passee,
                notifications_commandes_affectee_livraisonOK     : data.notifications_commandes_affectee_livraisonOK,
                notifications_commandes_affectee_livraisonNOK    : data.notifications_commandes_affectee_livraisonNOK,
                notifications_commandes_affectee_savOK           : data.notifications_commandes_affectee_savOK,
                notifications_commandes_affectee_cloture         : data.notifications_commandes_affectee_cloture,
                notifications_commandes_affectee_abandon         : data.notifications_commandes_affectee_abandon,
                notifications_commandes_observateur_validation   : data.notifications_commandes_observateur_validation,
                notifications_commandes_observateur_validationOK : data.notifications_commandes_observateur_validationOK,
                notifications_commandes_observateur_validationNOK: data.notifications_commandes_observateur_validationNOK,
                notifications_commandes_observateur_passee       : data.notifications_commandes_observateur_passee,
                notifications_commandes_observateur_livraisonOK  : data.notifications_commandes_observateur_livraisonOK,
                notifications_commandes_observateur_livraisonNOK : data.notifications_commandes_observateur_livraisonNOK,
                notifications_commandes_observateur_savOK        : data.notifications_commandes_observateur_savOK,
                notifications_commandes_observateur_cloture      : data.notifications_commandes_observateur_cloture,
                notifications_commandes_observateur_abandon      : data.notifications_commandes_observateur_abandon,
            });

            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const initFormData = () => {
        setValue("notifications_commandes_demandeur_validation"     , config.notifications_commandes_demandeur_validation);
        setValue("notifications_commandes_demandeur_validationOK"   , config.notifications_commandes_demandeur_validationOK);
        setValue("notifications_commandes_demandeur_validationNOK"  , config.notifications_commandes_demandeur_validationNOK);
        setValue("notifications_commandes_demandeur_passee"         , config.notifications_commandes_demandeur_passee);
        setValue("notifications_commandes_demandeur_livraisonOK"    , config.notifications_commandes_demandeur_livraisonOK);
        setValue("notifications_commandes_demandeur_livraisonNOK"   , config.notifications_commandes_demandeur_livraisonNOK);
        setValue("notifications_commandes_demandeur_savOK"          , config.notifications_commandes_demandeur_savOK);
        setValue("notifications_commandes_demandeur_cloture"        , config.notifications_commandes_demandeur_cloture);
        setValue("notifications_commandes_demandeur_abandon"        , config.notifications_commandes_demandeur_abandon);
        setValue("notifications_commandes_valideur_validation"      , config.notifications_commandes_valideur_validation);
        setValue("notifications_commandes_valideur_validationOK"    , config.notifications_commandes_valideur_validationOK);
        setValue("notifications_commandes_valideur_validationNOK"   , config.notifications_commandes_valideur_validationNOK);
        setValue("notifications_commandes_valideur_passee"          , config.notifications_commandes_valideur_passee);
        setValue("notifications_commandes_valideur_centreCout"      , config.notifications_commandes_valideur_centreCout);
        setValue("notifications_commandes_valideur_livraisonOK"     , config.notifications_commandes_valideur_livraisonOK);
        setValue("notifications_commandes_valideur_livraisonNOK"    , config.notifications_commandes_valideur_livraisonNOK);
        setValue("notifications_commandes_valideur_savOK"           , config.notifications_commandes_valideur_savOK);
        setValue("notifications_commandes_valideur_cloture"         , config.notifications_commandes_valideur_cloture);
        setValue("notifications_commandes_valideur_abandon"         , config.notifications_commandes_valideur_abandon);
        setValue("notifications_commandes_affectee_validation"      , config.notifications_commandes_affectee_validation);
        setValue("notifications_commandes_affectee_validationOK"    , config.notifications_commandes_affectee_validationOK);
        setValue("notifications_commandes_affectee_validationNOK"   , config.notifications_commandes_affectee_validationNOK);
        setValue("notifications_commandes_affectee_passee"          , config.notifications_commandes_affectee_passee);
        setValue("notifications_commandes_affectee_livraisonOK"     , config.notifications_commandes_affectee_livraisonOK);
        setValue("notifications_commandes_affectee_livraisonNOK"    , config.notifications_commandes_affectee_livraisonNOK);
        setValue("notifications_commandes_affectee_savOK"           , config.notifications_commandes_affectee_savOK);
        setValue("notifications_commandes_affectee_cloture"         , config.notifications_commandes_affectee_cloture);
        setValue("notifications_commandes_affectee_abandon"         , config.notifications_commandes_affectee_abandon);
        setValue("notifications_commandes_observateur_validation"   , config.notifications_commandes_observateur_validation);
        setValue("notifications_commandes_observateur_validationOK" , config.notifications_commandes_observateur_validationOK);
        setValue("notifications_commandes_observateur_validationNOK", config.notifications_commandes_observateur_validationNOK);
        setValue("notifications_commandes_observateur_passee"       , config.notifications_commandes_observateur_passee);
        setValue("notifications_commandes_observateur_livraisonOK"  , config.notifications_commandes_observateur_livraisonOK);
        setValue("notifications_commandes_observateur_livraisonNOK" , config.notifications_commandes_observateur_livraisonNOK);
        setValue("notifications_commandes_observateur_savOK"        , config.notifications_commandes_observateur_savOK);
        setValue("notifications_commandes_observateur_cloture"      , config.notifications_commandes_observateur_cloture);
        setValue("notifications_commandes_observateur_abandon"      , config.notifications_commandes_observateur_abandon);
    }

    useEffect(() => {
        initFormData();
    },[])
    useEffect(() => {
        initFormData();
    },[config])

    return (<>
        <Form onSubmit={handleSubmit(modifierEnBase)}>
            <Table>
                <thead>
                    <tr>
                        <td></td>
                        <th>Demandeur</th>
                        <th>Valideur (responsable du centre de couts)</th>
                        <th>Gérant</th>
                        <th>Observateur</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>La demande de validation est faite</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_validation' name="notifications_commandes_demandeur_validation" {...register('notifications_commandes_demandeur_validation')} />
                            <small className="text-danger">{errors.notifications_commandes_demandeur_validation?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_validation' name="notifications_commandes_valideur_validation" {...register('notifications_commandes_valideur_validation')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_validation?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_validation' name="notifications_commandes_affectee_validation" {...register('notifications_commandes_affectee_validation')} />
                            <small className="text-danger">{errors.notifications_commandes_affectee_validation?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_validation' name="notifications_commandes_observateur_validation" {...register('notifications_commandes_observateur_validation')} />
                            <small className="text-danger">{errors.notifications_commandes_observateur_validation?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>La validation est positive</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_validationOK' name="notifications_commandes_demandeur_validationOK" {...register('notifications_commandes_demandeur_validationOK')} />
                            <small className="text-danger">{errors.notifications_commandes_demandeur_validationOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_validationOK' name="notifications_commandes_valideur_validationOK" {...register('notifications_commandes_valideur_validationOK')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_validationOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_validationOK' name="notifications_commandes_affectee_validationOK" {...register('notifications_commandes_affectee_validationOK')} />
                            <small className="text-danger">{errors.notifications_commandes_affectee_validationOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_validationOK' name="notifications_commandes_observateur_validationOK" {...register('notifications_commandes_observateur_validationOK')} />
                            <small className="text-danger">{errors.notifications_commandes_observateur_validationOK?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>La validation est négative</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_validationNOK' name="notifications_commandes_demandeur_validationNOK" {...register('notifications_commandes_demandeur_validationNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_demandeur_validationNOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_validationNOK' name="notifications_commandes_valideur_validationNOK" {...register('notifications_commandes_valideur_validationNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_validationNOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_validationNOK' name="notifications_commandes_affectee_validationNOK" {...register('notifications_commandes_affectee_validationNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_affectee_validationNOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_validationNOK' name="notifications_commandes_observateur_validationNOK" {...register('notifications_commandes_observateur_validationNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_observateur_validationNOK?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>La commande est passée chez le fournisseur</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_passee' name="notifications_commandes_demandeur_passee" {...register('notifications_commandes_demandeur_passee')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_passee' name="notifications_commandes_valideur_passee" {...register('notifications_commandes_valideur_passee')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_passee' name="notifications_commandes_affectee_passee" {...register('notifications_commandes_affectee_passee')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_passee' name="notifications_commandes_observateur_passee" {...register('notifications_commandes_observateur_passee')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>La commande est prête à être intégrée au centre de couts</td>
                        <td></td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_centreCout' name="notifications_commandes_valideur_centreCout" {...register('notifications_commandes_valideur_centreCout')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_centreCout?.message}</small>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>La commande est receptionnée sans SAV</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_livraisonOK' name="notifications_commandes_demandeur_livraisonOK" {...register('notifications_commandes_demandeur_livraisonOK')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_livraisonOK' name="notifications_commandes_valideur_livraisonOK" {...register('notifications_commandes_valideur_livraisonOK')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_livraisonOK' name="notifications_commandes_affectee_livraisonOK" {...register('notifications_commandes_affectee_livraisonOK')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_livraisonOK' name="notifications_commandes_observateur_livraisonOK" {...register('notifications_commandes_observateur_livraisonOK')} />
                            <small className="text-danger">{errors.xxx?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>La commande est receptionnée avec SAV</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_livraisonNOK' name="notifications_commandes_demandeur_livraisonNOK" {...register('notifications_commandes_demandeur_livraisonNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_demandeur_livraisonNOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_livraisonNOK' name="notifications_commandes_valideur_livraisonNOK" {...register('notifications_commandes_valideur_livraisonNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_livraisonNOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_livraisonNOK' name="notifications_commandes_affectee_livraisonNOK" {...register('notifications_commandes_affectee_livraisonNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_affectee_livraisonNOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_livraisonNOK' name="notifications_commandes_observateur_livraisonNOK" {...register('notifications_commandes_observateur_livraisonNOK')} />
                            <small className="text-danger">{errors.notifications_commandes_observateur_livraisonNOK?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>Le SAV est clos</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_savOK' name="notifications_commandes_demandeur_savOK" {...register('notifications_commandes_demandeur_savOK')} />
                            <small className="text-danger">{errors.notifications_commandes_demandeur_savOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_savOK' name="notifications_commandes_valideur_savOK" {...register('notifications_commandes_valideur_savOK')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_savOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_savOK' name="notifications_commandes_affectee_savOK" {...register('notifications_commandes_affectee_savOK')} />
                            <small className="text-danger">{errors.notifications_commandes_affectee_savOK?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_savOK' name="notifications_commandes_observateur_savOK" {...register('notifications_commandes_observateur_savOK')} />
                            <small className="text-danger">{errors.notifications_commandes_observateur_savOK?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>La commande est close</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_cloture' name="notifications_commandes_demandeur_cloture" {...register('notifications_commandes_demandeur_cloture')} />
                            <small className="text-danger">{errors.notifications_commandes_demandeur_cloture?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_cloture' name="notifications_commandes_valideur_cloture" {...register('notifications_commandes_valideur_cloture')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_cloture?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_cloture' name="notifications_commandes_affectee_cloture" {...register('notifications_commandes_affectee_cloture')} />
                            <small className="text-danger">{errors.notifications_commandes_affectee_cloture?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_cloture' name="notifications_commandes_observateur_cloture" {...register('notifications_commandes_observateur_cloture')} />
                            <small className="text-danger">{errors.notifications_commandes_observateur_cloture?.message}</small>
                        </td>
                    </tr>
                    <tr>
                        <td>La commande est abandonnée</td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_demandeur_abandon' name="notifications_commandes_demandeur_abandon" {...register('notifications_commandes_demandeur_abandon')} />
                            <small className="text-danger">{errors.notifications_commandes_demandeur_abandon?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_valideur_abandon' name="notifications_commandes_valideur_abandon" {...register('notifications_commandes_valideur_abandon')} />
                            <small className="text-danger">{errors.notifications_commandes_valideur_abandon?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_affectee_abandon' name="notifications_commandes_affectee_abandon" {...register('notifications_commandes_affectee_abandon')} />
                            <small className="text-danger">{errors.notifications_commandes_affectee_abandon?.message}</small>
                        </td>
                        <td>
                            <Form.Check type='checkbox' id='notifications_commandes_observateur_abandon' name="notifications_commandes_observateur_abandon" {...register('notifications_commandes_observateur_abandon')} />
                            <small className="text-danger">{errors.notifications_commandes_observateur_abandon?.message}</small>
                        </td>
                    </tr>
                </tbody>
            </Table>
            
            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
        </Form>
    </>);

};

ConfigGeneraleTabNotifs.propTypes = {};

export default ConfigGeneraleTabNotifs;
