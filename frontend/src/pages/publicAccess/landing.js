import React from 'react';
import { useNavigate, Navigate } from 'react-router-dom';
import { Row, Col, } from 'react-bootstrap';
import HabilitationService from 'services/habilitationsService';
import ConfigurationService from 'services/configurationService';
import Flex from 'components/common/Flex';
import imgIncident from 'assets/img/publicLandingPage/imgIncident.png'
import imgConsommation from 'assets/img/publicLandingPage/imgConsommation.png'
import imgConnexion from 'assets/img/publicLandingPage/imgConnexion.png'
import Lottie from 'lottie-react';
import lottieMnt from 'components/widgets/lottie-maintenance';

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
		if(ConfigurationService.config.maintenance)
		{
			return(<>
				<center>
					<div className="display-1 text-300 fs-error w-50">
						<Lottie animationData={lottieMnt} loop={true} />
					</div>
					<hr />
					<p className="lead mt-4 text-800 font-sans-serif fw-semi-bold">
						L'application est actuellement en maintenance. Merci de revenir plus tard.
					</p>
				</center>
			</>);
		}
		else
		{
			return (<>
				{!ConfigurationService.config['alertes_benevoles_lots'] && !ConfigurationService.config['alertes_benevoles_vehicules'] && !ConfigurationService.config['consommation_benevoles'] ? <Navigate replace to="/home" /> : null}
				
				<Row className="justify-content-center text-center mb-3 mt-3">
					<Col lg={8} xl={7} xxl={6} className="col-xxl-6">
						<h1 className="fs-2 fs-sm-4 fs-md-5">Bienvenue sur {window.__ENV__.APP_NAME}</h1>
					</Col>
				</Row>
	
				<Row className="justify-content-center text-center">
					{ConfigurationService.config['consommation_benevoles'] ? 
						<Col lg={4} className='mb-2'>
							<Flex
								alignItems="center"
								className="px-4 py-x1 bg-light rounded-3 border position-relative"
							>
								<img src={imgConsommation} alt="" width="39" />
								<div className="ms-3 my-x1">
								<h5 className="fs-0 fw-semi-bold mb-2">
									<a onClick={goDeclaration} className="text-900 hover-primary stretched-link">
										Rapport de consommation
									</a>
								</h5>
								<h6 className="mb-0 text-600">Je souhaite tracer une consommation de matériel lors d'un évènement.</h6>
								</div>
							</Flex>
						</Col>
					: null}
	
					{ConfigurationService.config['alertes_benevoles_lots'] || ConfigurationService.config['alertes_benevoles_vehicules'] ?
						<Col lg={4} className='mb-2'>
							<Flex
								alignItems="center"
								className="px-4 py-x1 bg-light rounded-3 border position-relative"
							>
								<img src={imgIncident} alt="" width="39" />
								<div className="ms-3 my-x1">
								<h5 className="fs-0 fw-semi-bold mb-2">
									<a onClick={goIncident} className="text-900 hover-primary stretched-link">
										Incident Matériel
									</a>
								</h5>
								<h6 className="mb-0 text-600">J'ai rencontré un incident matériel et souhaite le remonter à l'équipe logistique.</h6>
								</div>
							</Flex>
						</Col>
					: null}
	
					<Col lg={4} className='mb-2'>
						<Flex
							alignItems="center"
							className="px-4 py-x1 bg-light rounded-3 border position-relative"
						>
							<img src={imgConnexion} alt="" width="39" />
							<div className="ms-3 my-x1">
							<h5 className="fs-0 fw-semi-bold mb-2">
								<a onClick={goBackEnd} className="text-900 hover-primary stretched-link">
									Equipe logistique
								</a>
							</h5>
							<h6 className="mb-0 text-600">Je fais partie de l'équipe logistique et je souhaite me connecter à l'outil.</h6>
							</div>
						</Flex>
					</Col>
				</Row>
			</>);
		}
	}
	else
	{
		return(<LoaderInfiniteLoop/>)
	}
	
};

export default Landing;
