import SoftBadge from 'components/common/SoftBadge';
import { Axios } from 'helpers/axios';
import React, { useEffect, useState } from 'react';
import { Row, Col, Alert } from 'react-bootstrap';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';

import HabilitationService from 'services/habilitationsService';

import CalendrierGeneral from './calendrierGeneral';
import LotsTable from '../lots/lotsOpe/lotsTable';
import VehiculesTable from '../vehicules/vehiculesTable';
import AlertesBenevolesLotsTable from '../lots/alertesBenevolesTable';
import AlertesBenevolesVehiculesTable from '../vehicules/alertesBenevolesTable';
import ToDoListTable from '../settings/todolist/todolistTable';
import CommandesTableHomePage from '../commandes/commandesTableHomePage';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import CheckList from './checkList';

const Home = () => {
	const [pageReadyToDisplay, setPageReadyToDisplay] = useState(false);
	const [modulesToDisplay, setModulesToDisplay] = useState([]);

	const [messagesGeneraux, setMessagesGeneraux] = useState([]);
	

	const [toToListNeedsReload, setToToListNeedsReload] = useState(false);
	const initPage = async () => {
		try {
			let getMessages = await Axios.get('/messagesGeneraux/getAllMessages');
			setMessagesGeneraux(getMessages.data);

			let getModules = await Axios.get('/getIndividualHomePageDetails')
			setModulesToDisplay(getModules.data);

			setPageReadyToDisplay(true);
		} catch (error) {
			console.log(error);
		}
	}

	const reloadForNavBar = async () => {
		if(sessionStorage.getItem("homeNeedRefresh") != 0)
		{
			sessionStorage.setItem("homeNeedRefresh", 0);
			location.reload();
		}else{
			initPage();
		}
	}

	useEffect(() => {
		reloadForNavBar();
	}, [])
	
	const nl2br = require('react-nl2br');
    return(<>
		<h2 className='mb-3'>Bienvenue {HabilitationService.habilitations.prenomPersonne} !</h2>

		{pageReadyToDisplay ?

			<Row>
				<Col md={12}>
					{messagesGeneraux.map((message, i)=>{return(
						<Alert variant={message.couleurMessageType}>{nl2br(message.corpsMessage)} (par: {message.prenomPersonne} {message.nomPersonne}){message.isPublic ? <SoftBadge className='ms-1' bg='primary'>Visibilité publique</SoftBadge> : null}</Alert>
					)})}
				</Col>
				<Col md={8}>
					{modulesToDisplay.lotsEnCharge ?
						<LotsTable
							filterUserAffected={true}
							alternativeTitle="Lots dont j'ai la charge"
							displayLibelleLotsEtat={false}
							displayIdentifiant={false}
							displayNbAlertesEnCours={false}
							displayActions={false}
						/>
					: null}

					{modulesToDisplay.vehiculesEnCharge ?
						<VehiculesTable
							filterUserAffected={true}
							alternativeTitle="Véhicules dont j'ai la charge"
							displayLibelleType={false}
							displayIdentifiant={false}
							displayImmatriculation={false}
							displayMarqueModele={false}
							displayNbAlertesEnCours={false}
							displayActions={false}
						/>
					: null}

					{modulesToDisplay.alertesLots ?
						<FalconComponentCard noGuttersBottom className="mb-3">
							<FalconComponentCard.Body
								scope={{ ActionButton }}
								noLight
							>
								<AlertesBenevolesLotsTable
									filterForHomePage={true}
									boxTitle="Alertes bénévoles sur les lots (nouvelles ou qui me sont affectées)"
									displayNomDeclarant={false}
									displayDateCreationAlerte={false}
								/>
							</FalconComponentCard.Body>
						</FalconComponentCard>
					: null}

					{modulesToDisplay.alertesVehicules ?
						<FalconComponentCard noGuttersBottom className="mb-3">
							<FalconComponentCard.Body
								scope={{ ActionButton }}
								noLight
							>
								<AlertesBenevolesVehiculesTable
									filterForHomePage={true}
									boxTitle="Alertes bénévoles sur les véhicules (nouvelles ou qui me sont affectées)"
									displayNomDeclarant={false}
									displayDateCreationAlerte={false}
								/>
							</FalconComponentCard.Body>
						</FalconComponentCard>
					: null}

					{modulesToDisplay.calendrier ?
						<CalendrierGeneral
							peremptionsLots={true}
							peremptionsReserves={true}
							inventairesPassesLots={true}
							inventairesPassesReserves={true}
							inventairesFutursLots={true}
							inventairesFutursReserves={true}
							commandesLivraisons={true}
							vehiculesMntPonctuelles={true}
							vehiculesMntRegPassees={true}
							vehiculesMntRegFutures={true}
							vehiculesDesinfectionsPassees={true}
							vehiculesDesinfectionsFutures={true}
							tenuesAffectations={true}
							tenuesRetours={true}
							cautionsEmissions={true}
							cautionsExpirations={true}
							toDoListOwn={true}
							toDoListAll={true}
						/>
					: null}
				</Col>
				
				<Col md={4}>
					{modulesToDisplay.checkListParc ?
						<CheckList />
					: null}
					
					{modulesToDisplay.commandesAValider ?
						<CommandesTableHomePage />
					: null}

					{modulesToDisplay.maToDo ?
						<ToDoListTable
							titreBox="Ma ToDoList"
							filtre='individual'
							idPersonne={HabilitationService.habilitations.idPersonne}
							componentsHaveToReload={toToListNeedsReload}
							setComponentsHaveToReload={setToToListNeedsReload}
						/>
					: null}
				</Col>
			</Row>

		: <LoaderInfiniteLoop />}
	</>);
}

Home.propTypes = {};

export default Home;