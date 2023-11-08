import React, {useState, useEffect} from 'react';
import { useNavigate } from 'react-router-dom';
import FalconComponentCard from 'components/common/FalconComponentCard';
import { Form, Button, Alert } from 'react-bootstrap';
import Select from 'react-select';
import IconButton from 'components/common/IconButton';

import {Axios} from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

const UtilisateurAccess = ({personne, setPageNeedsRefresh}) => {

    const [profils, setProfils] = useState([])
    const initPage = async () => {
        try {
            const getData = await Axios.get('/profils/getProfils');
            setProfils(getData.data);
            setValue("profils", personne.profils);
            
            personne.onGoingToken != null ? setResetFeedBack('resetSansMail') : null;
        } catch (error) {
            console.log(error)
        }
    }
    useEffect(()=>{
        setResetFeedBack();
        initPage();
    },[])

    const unlinkAD = async () => {
        try {
            const response = await Axios.post('settingsUtilisateurs/unlinkAD',{
				idPersonne: personne.idPersonne,
			});
            setPageNeedsRefresh(true);
            initPage();
        } catch (error) {
            console.log(error);
        }
    }

    const linkAD = async () => {
        try {
            const response = await Axios.post('settingsUtilisateurs/linkAD',{
				idPersonne: personne.idPersonne,
			});
            setPageNeedsRefresh(true);
            initPage();
        } catch (error) {
            console.log(error);
        }
    }

    const [resetFeedBack, setResetFeedBack] = useState();
    const resetPassword = async () => {
        try {
            setResetFeedBack();
            const response = await Axios.post('settingsUtilisateurs/pwdReinitRequest',{
				identifiant: personne.identifiant,
                mailPersonne: personne.mailPersonne,
			});
            setResetFeedBack(response.data.handleResult);
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error);
        }
    }
    const validateReset = async () => {
        try {
            const response = await Axios.post('pwdReinitValidate',{
				token: personne.onGoingToken
			});
            
            setResetFeedBack(response.data.handleResult);

            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error);
        }
    }
    const renderResetFeedback = () => {
        switch(resetFeedBack) {
            case 'userInconnu':
                return <Alert variant='danger'>Erreur technique lors de la réinitialisation.</Alert>;
                break;
            
            case 'userAD':
                return <Alert variant='warning'>Cet utilisateur est rattaché à l'AD, son mot de passe ne peut donc pas être réinitialisé.</Alert>;
                break;
            
            case 'doublon':
                return <Alert variant='warning'>Cet utilisateur a déjà demandé une réinitialisation de mot de passe. Il faut qu'il finalise cette procédure avant de pouvoir en engager une autre.</Alert>;
                break;
            
            case 'mailEnvoye':
                return <Alert variant='success'>Un email a été envoyé à l'utilisateur pour qu'il confirme sa réinitialisation de mot de passe.</Alert>;
                break;
            
            case 'resetSansMail':
                return(<>
                    <Alert variant='success'>
                        <p>Une procédure de réinitialisation de mot de passe est en cours. L'utilisateur doit cliquer sur le lien reçu par email pour valider la réinitialisation. Son nouveau mot de passe sera ensuite <i>{personne.identifiant}</i></p>
                        <hr/>
                        <p>Si l'utilisateur ne peut pas accéder à ses mails, merci de valider la réinitialisation vous même, réinitialisation dont vous certifier avoir eu la demande explicite de l'utilisateur.</p>
                        <IconButton
                            variant='outline-info'
                            size='sm'
                            onClick={validateReset}
                        >
                            Valider à la place de l'utilisateur
                        </IconButton>
                    </Alert></>)
                break;
            
                case 'echec':
                    return <Alert variant='danger'>Erreur technique lors de la réinitialisation.</Alert>;
                    break;
                
                case 'reussite':
                    return <Alert variant='success'>Le mot de passe a été réinitialisé</Alert>;
                    break;

            default:
                return null
                break;
        }
            
    }

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm();
    const updateProfils = async (data) => {
        try {

            const response = await Axios.post('settingsUtilisateurs/updateProfils',
            {
                idPersonne: personne.idPersonne,
                profils: data.profils,
            });
            
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const navigate = useNavigate();
    const [delegationLoading, setDelegationLoading] = useState(false);
    const delegation = async () => {
        try {
            setDelegationLoading(true);
            HabilitationService.saveBeforeDelegate();

            const response = await Axios.post('settingsUtilisateurs/delegate',
            {
                idPersonne: personne.idPersonne,
            });

            if(!response.data.auth==true) {
                HabilitationService.backToInitialSession();
                setDelegationLoading(false);
            }else{
                HabilitationService.setToken(response.data.token);
                HabilitationService.setTokenValidUntil(response.data.tokenValidUntil);
                HabilitationService.setRefreshToken(response.data.refreshToken);
                HabilitationService.setHabilitations(response.data.habilitations);
                
                localStorage.setItem("homeNeedRefresh", 1);
                
                await sleep(1000);
                navigate('/home');
            }

        } catch (error) {
            console.log(error)
            HabilitationService.backToInitialSession();
            setDelegationLoading(false);
            location.reload();
        }
    }

	return (
		<>
            <FalconComponentCard>
                <FalconComponentCard.Header
                    title="Accès et profils"
                >
                </FalconComponentCard.Header>
                <FalconComponentCard.Body>
                    {personne.isActiveDirectory ?
                        <>
                            <Alert variant='info'>Ce compte est lié à l'AD. La gestion des profils et du mot de passe est faite directement dans l'AD.</Alert>
                            
                            <Form.Group className="mb-3">
                                <Form.Label>Profils actifs récupérés de l'AD:</Form.Label>
                                    <Select
                                        id="profils"
                                        name="profils"
                                        size="sm"
                                        className='mb-2'
                                        closeMenuOnSelect={false}
                                        placeholder='Ajoutez des balises ici'
                                        options={profils}
                                        isMulti
                                        classNamePrefix="react-select"
                                        value={watch("profils")}
                                        onChange={selected => setValue("profils", selected)}
                                        isDisabled={true}
                                    />
                            </Form.Group>
                            <IconButton
                                icon='unlink'
                                variant='warning'
                                onClick={unlinkAD}
                            >
                                Délier de l'AD
                            </IconButton>
                        </>
                    :
                        <>
                            <Alert variant='secondary'>Compte local</Alert>
                            <IconButton
                                icon='link'
                                variant='info'
                                onClick={linkAD}
                                className='me-1 mb-1'
                            >
                                Lier à l'AD
                            </IconButton>
                            <IconButton
                                icon='key'
                                variant='info'
                                onClick={resetPassword}
                                className='me-1 mb-1'
                                disabled={personne.onGoingToken != null ? true : false}
                            >
                                Réinitialiser le mot de passe
                            </IconButton>
                            {renderResetFeedback()}
                            <hr/>
                            <Form onSubmit={handleSubmit(updateProfils)}>
                                <Form.Group className="mb-3">
                                    <Form.Label>Profils actifs:</Form.Label>
                                    <Select
                                        id="profils"
                                        name="profils"
                                        size="sm"
                                        className='mb-2'
                                        closeMenuOnSelect={false}
                                        placeholder='Ajoutez des balises ici'
                                        options={profils}
                                        isMulti
                                        classNamePrefix="react-select"
                                        value={watch("profils")}
                                        onChange={selected => setValue("profils", selected)}
                                    />
                                </Form.Group>
                                <Button variant='primary' className='me-2 mb-1' size="sm" type="submit" disabled={!HabilitationService.habilitations['annuaire_modification']}>Appliquer</Button>
                            </Form>
                        </>
                    }

                    {HabilitationService.habilitations['delegation'] && !HabilitationService.delegationActive ?
                        <>
                            <hr/>
                            <IconButton
                                icon='user-secret'
                                variant='outline-warning'
                                onClick={delegation}
                                className='me-1 mb-1'
                                disabled={delegationLoading}
                            >
                                {delegationLoading ? 'Chargement ...' : 'Se connecter entant que'}
                            </IconButton>
                        </>
                    : null}
                </FalconComponentCard.Body>
            </FalconComponentCard>
		</>
	);
};

UtilisateurAccess.propTypes = {};

export default UtilisateurAccess;