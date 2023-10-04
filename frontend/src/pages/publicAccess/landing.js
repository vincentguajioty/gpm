import React, { useEffect } from 'react';
import { useNavigate, Navigate } from 'react-router-dom';
import { Card, Row, Col } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import HabilitationService from 'services/habilitationsService';
import ConfigurationService from 'services/configurationService';
import getConfigAndSave from 'helpers/getConfigAndSave';

import { Axios } from 'helpers/axios';

const Landing = () => {
	const navigate = useNavigate();

	const goIncident = () => {
		navigate('/incidentPublic');
	}
	const goDeclaration = () => {
		navigate('/consoPublic');
	}
	const goBackEnd = () => {
		navigate(HabilitationService.habilitations ? '/home' : '/login');
	}

	const reloadForConfig = async () => {
		if(localStorage.getItem("configNeedRefresh") != 0 || !ConfigurationService.appname)
		{
			await getConfigAndSave();
			localStorage.setItem("configNeedRefresh", 0);
			location.reload();
		}
	}

	useEffect(() => {
		reloadForConfig();
	}, [])

	return (
		<Card>
			{ConfigurationService.appname && ConfigurationService.alertes_benevoles_lots == 0 && ConfigurationService.alertes_benevoles_vehicules == 0 && ConfigurationService.consommation_benevoles == 0 ? <Navigate replace to="/home" /> : null}

			<Card.Footer className="bg-light text-center pt-4">
				<Row className="justify-content-center">
					<Col xs={11} sm={10}>
						<h4 className="fw-normal mb-0 fs-1 fs-md-2">
							Bienvenue sur {process.env.REACT_APP_NAME}
						</h4>

						<Row className="gx-2 my-4">
							{ConfigurationService.consommation_benevoles==1 ? 
								<Col lg={4}>
									<IconButton
										className="d-block w-100 mb-2 mb-xl-0"
										iconClassName="me-2"
										variant="falcon-default"
										icon="notes-medical"
										onClick={goDeclaration}
									>
										Tracer une consommation de consommables lors d'un DPS
									</IconButton>
								</Col>
							: null}


							{ConfigurationService.alertes_benevoles_lots==1 || ConfigurationService.alertes_benevoles_vehicules==1 ? 
								<Col lg={4}>
									<IconButton
										className="d-block w-100 mb-2 mb-xl-0"
										iconClassName="me-2"
										variant="falcon-default"
										icon="exclamation-triangle"
										onClick={goIncident}
									>
										Déclarer un incident sur le matériel
									</IconButton>
								</Col>
							: null }

							<Col lg={4}>
								<IconButton
									className="d-block w-100 mb-2 mb-xl-0"
									iconClassName="me-2"
									variant="falcon-default"
									icon="user"
									onClick={goBackEnd}
								>
									Je suis membre de l'équipe logistique et je souhaite me connecter
								</IconButton>
							</Col>
						</Row>
					</Col>
				</Row>
			</Card.Footer>
		</Card>
	);
};

export default Landing;
