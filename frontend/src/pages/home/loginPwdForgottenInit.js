import React, {useState} from 'react';
import { Form, Button } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconAlert from 'components/common/IconAlert';

import {Axios} from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { utilisateurPasswordReinit } from 'helpers/yupValidationSchema';

const LoginPwdForgottenInit = () => {
    const [displayOption, setDisplayOption] = useState('formulaire');
    const [isLoading, setLoading] = useState(false);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(utilisateurPasswordReinit),
    });

    const initReset = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('pwdReinitRequest', {
                identifiant: data.identifiant,
                mailPersonne: data.mailPersonne,
            });

            setDisplayOption(response.data.handleResult);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    switch (displayOption) {
        case 'formulaire':
            return(<>
                <Form onSubmit={handleSubmit(initReset)} className="mb-3">
                    <Form.Group className="mb-3">
                        <Form.Label>Nom d'utilisateur</Form.Label>
                        <Form.Control
                            id="identifiant"
                            name="identifiant"
                            {...register("identifiant")}
                            type="text"
                        />
                        <small className="text-danger">{errors.identifiant?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Email associé</Form.Label>
                        <Form.Control
                            id="mailPersonne"
                            name="mailPersonne"
                            {...register("mailPersonne")}
                            type="text"
                        />
                        <small className="text-danger">{errors.mailPersonne?.message}</small>
                    </Form.Group>
                    <Button variant='warning' className="mt-3 w-100" type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Réinitialiser le mot de passe'}</Button>
                </Form>
            </>);
        break;

        case 'mailEnvoye':
            return <IconAlert variant="success">La demande de réinitialisation de mot de passe est bien prise en compte. Un mail va vous être envoyé avec un lien surlequel cliquer (valable 30 minutes). Vous pouvez fermer cette page.</IconAlert> ;
        break;

        case 'doublon':
            return <IconAlert variant="warning">Une demande de réinitialisation est déjà en cours pour ce compte.</IconAlert> ;
        break;

        case 'chargement':
            return <LoaderInfiniteLoop />
        break;
    
        default:
            return <IconAlert variant="danger">Erreur dans le processus de réinitialisation (compte non-trouvé, envoi d'email en erreur, ...).</IconAlert> ;
        break;
    }
};

LoginPwdForgottenInit.propTypes = {};

export default LoginPwdForgottenInit;