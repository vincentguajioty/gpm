import React from 'react';
import { useNavigate, Navigate } from 'react-router-dom';
import { Card, Row, Col } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';
import HabilitationService from 'services/habilitationsService';
import ConfigurationService from 'services/configurationService';

import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

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

	if(ConfigurationService.config)
	{
		return (
			<Card>
				{!ConfigurationService.config['alertes_benevoles_lots'] && !ConfigurationService.config['alertes_benevoles_vehicules'] && !ConfigurationService.config['consommation_benevoles'] ? <Navigate replace to="/home" /> : null}
	
				<Card.Footer className="bg-light text-center pt-4">
					<Row className="justify-content-center">
						<Col xs={11} sm={10}>
							<h4 className="fw-normal mb-0 fs-1 fs-md-2">
								Bienvenue sur {window.__ENV__.APP_NAME}
							</h4>
	
							<Row className="gx-2 my-4">
								{ConfigurationService.config['consommation_benevoles'] ? 
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
	
	
								{ConfigurationService.config['alertes_benevoles_lots'] || ConfigurationService.config['alertes_benevoles_vehicules'] ? 
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
	}
	else
	{
		return(<LoaderInfiniteLoop/>)
	}

	
};

export default Landing;
