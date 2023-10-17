import React, { useState, useEffect } from 'react';
import QRCode from "react-qr-code"; 
import FalconComponentCard from 'components/common/FalconComponentCard';
import { Form, Button, InputGroup } from 'react-bootstrap'; 
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';

import {Axios} from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { mfaInit } from 'helpers/yupValidationSchema';

const MFA = () => {
    const [loading, setLoading] = useState(true);
    const [utilisateur, setUtilisateur] = useState();
    const [enableUrl, setEnableUrl] = useState();

    //init
    const getUtilisateur = async () => {
        try {
            const response = await Axios.post('settingsUtilisateurs/getOneUser',{
				idPersonne: HabilitationService.habilitations.idPersonne,
			});
            setUtilisateur(response.data[0]);

            if(response.data[0].mfaEnabled == false)
            {
                const URLtoEnable = await Axios.post('settingsUtilisateurs/getMfaUrl',{
                    idPersonne: HabilitationService.habilitations.idPersonne,
                });
                
                setEnableUrl(URLtoEnable.data.url);
            }

            setLoading(false);

        } catch (error) {
            console.log(error);
        }
    }

    //Boutton désactivation
    const disableMfa = async () => {
        try {
            const response = await Axios.post('settingsUtilisateurs/disableMfa',{
				idPersonne: HabilitationService.habilitations.idPersonne,
			});
            setLoading(true);
            setUtilisateur();
            getUtilisateur();
        } catch (error) {
            console.log(error);
        }
    }

    //Processus d'activation
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(mfaInit),
    });
    const enableMfa = async (data) => {
        try {
            const response = await Axios.post('settingsUtilisateurs/enableMfa',{
				idPersonne: HabilitationService.habilitations.idPersonne,
                confirmation: data.confirmation,
			});
            getUtilisateur();
        } catch (error) {
            console.log(error)
        }
    }
    
    useEffect(() => {
        getUtilisateur();
    }, [])

	return (
		<>
		<FalconComponentCard>
			<FalconComponentCard.Header
				title="MFA"
			>
			</FalconComponentCard.Header>
			<FalconComponentCard.Body>
                <center className='mb-2'><i>Changer ce paramètre va déconnecter toutes vos sessions</i></center>
				{loading ? 'Chargement en cours' : 
                    utilisateur.mfaEnabled ? <Button onClick={disableMfa}>Désactiver le MFA</Button> : 
                        <>
                            <Form.Label>1 - Téléchargez sur votre appareil mobile une application MFA (Google Authenticator, Microsoft Authenticator, ...)</Form.Label>
                            <Form.Label>2 - Dans l'application mobile, scannez le QRCode ci-dessous pour ajouter le compte (ou ajoutez le code manuellement):</Form.Label>
                            
                            <center>
                                <QRCode
                                    value={enableUrl}
                                    style={{ height: "auto", maxWidth: "50%", width: "50%"}}
                                />
                            </center>
                            
                            <Form onSubmit={handleSubmit(enableMfa)} autoComplete="off">
                                <Form.Group className="mb-3">
                                    <Form.Label>3 - Saisissez le code de sécurité obtenu:</Form.Label>
                                    <InputGroup className="mb-3">
                                        <Form.Control
                                            id="confirmation"
                                            name="confirmation"
                                            type="text"
                                            placeholder="Entrer le code affiché dans l'application mobile"
                                            aria-describedby="basic-addon1"
                                            {...register("confirmation")}
                                        />
                                        <Button id="button-addon2" type="submit"><FontAwesomeIcon icon='save' /></Button>
                                    </InputGroup>
                                    <small className="text-danger">{errors.confirmation?.message}</small>
                                </Form.Group>
                            </Form>
                        </>
                }
			</FalconComponentCard.Body>
		</FalconComponentCard>
		</>
	);
};

MFA.propTypes = {};

export default MFA;