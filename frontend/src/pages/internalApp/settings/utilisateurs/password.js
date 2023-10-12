import React, {useState} from 'react';
import { useNavigate } from 'react-router-dom';
import FalconComponentCard from 'components/common/FalconComponentCard';
import { Form, Button } from 'react-bootstrap'; 

import HabilitationService from 'services/habilitationsService';

import {Axios} from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { utilisateurPasswordChange, utilisateurPasswordChangeWithoutCheck } from 'helpers/yupValidationSchema';

const Password = ({checkOldPassword = true, layout, redirectTarget = false}) => {
	const [isLoading, setLoading] = useState(false);

	const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(checkOldPassword ? utilisateurPasswordChange : utilisateurPasswordChangeWithoutCheck),
    });
	
	const navigate = useNavigate();
	const savePassword = async (data) => {
		setLoading(true);
		try {
			let requeteUpdate;
			if(checkOldPassword)
			{
				requeteUpdate = await Axios.post('updatePassword',{
					idPersonne: HabilitationService.habilitations.idPersonne,
					oldPwd: data.oldPwd,
					newPwd: data.newPwd,
				});
			}
			else
			{
				requeteUpdate = await Axios.post('updatePasswordWithoutCheck',{
					idPersonne: HabilitationService.habilitations.idPersonne,
					newPwd: data.newPwd,
				});
			}
			
			if(requeteUpdate.status == 201)
			{
				reset();
				if(redirectTarget)
				{
					navigate(redirectTarget);
				}
			}
			else
			{
				if(requeteUpdate.data.disclaimerAccept == false)
				{
					navigate('/cguAtLogin');
				}
			}

			setLoading(false);
		} catch (e) {
			console.log(e);
		}
	}

	if(layout == "box")
	{
		return (
			<>
				<FalconComponentCard>
					<FalconComponentCard.Header
						title="Modifier mon mot de passe"
					>
					</FalconComponentCard.Header>
					<FalconComponentCard.Body>
						{HabilitationService.habilitations.isActiveDirectory ? "Merci de mettre à jour votre mot de passe dans l'AD" : (
							<>
								<center><i>Changer ce paramètre va déconnecter toutes vos sessions</i></center>
								<Form onSubmit={handleSubmit(savePassword)} autoComplete="off">
									{checkOldPassword ? (
										<Form.Group className="mb-3">
											<Form.Label>Ancien mot de passe</Form.Label>
											<Form.Control type="password" name="oldPwd" id="oldPwd" {...register("oldPwd")}/>
											<small className="text-danger">{errors.oldPwd?.message}</small>
										</Form.Group>
									) : null}
									
									<Form.Group className="mb-3">
										<Form.Label>Nouveau mot de passe</Form.Label>
										<Form.Control type="password" name="newPwd" id="newPwd" {...register("newPwd")}/>
										<small className="text-danger">{errors.newPwd?.message}</small>
									</Form.Group>
									<Form.Group className="mb-3">
										<Form.Label>Confirmer le nouveau mot de passe</Form.Label>
										<Form.Control type="password" name="newPwdConfirmed" id="newPwdConfirmed" {...register("newPwdConfirmed")}/>
										<small className="text-danger">{errors.newPwdConfirmed?.message}</small>
									</Form.Group>
									<Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Modifier'}</Button>
								</Form>
							</>
						)}
					</FalconComponentCard.Body>
				</FalconComponentCard>
			</>
		);
	}

	if(layout == "flat")
	{
		return (
			<>
				{HabilitationService.habilitations.isActiveDirectory ? "Merci de mettre à jour votre mot de passe dans l'AD" : (
					<Form onSubmit={handleSubmit(savePassword)} autoComplete="off">
						{checkOldPassword ? (
							<Form.Group className="mb-3">
								<Form.Label>Ancien mot de passe</Form.Label>
								<Form.Control type="password" name="oldPwd" id="oldPwd" {...register("oldPwd")}/>
								<small className="text-danger">{errors.oldPwd?.message}</small>
							</Form.Group>
						) : null}
						<Form.Group className="mb-3">
							<Form.Label>Nouveau mot de passe</Form.Label>
							<Form.Control type="password" name="newPwd" id="newPwd" {...register("newPwd")}/>
							<small className="text-danger">{errors.newPwd?.message}</small>
						</Form.Group>
						<Form.Group className="mb-3">
							<Form.Label>Confirmer le nouveau mot de passe</Form.Label>
							<Form.Control type="password" name="newPwdConfirmed" id="newPwdConfirmed" {...register("newPwdConfirmed")}/>
							<small className="text-danger">{errors.newPwdConfirmed?.message}</small>
						</Form.Group>
						<Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
					</Form>
				)}
			</>
		);
	}

	
};

Password.propTypes = {};

export default Password;