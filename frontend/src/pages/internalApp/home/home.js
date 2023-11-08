import React, { useEffect } from 'react';

import HabilitationService from 'services/habilitationsService';


const Home = () => {

	const reloadForNavBar = async () => {
		if(localStorage.getItem("homeNeedRefresh") != 0)
		{
			localStorage.setItem("homeNeedRefresh", 0);
			location.reload();
		}
	}

	useEffect(() => {
		reloadForNavBar();
	}, [])
	

    return(<>
		<h2 className='mb-4'>Bienvenue {HabilitationService.habilitations.prenomPersonne} !</h2>
	</>);
}

Home.propTypes = {};

export default Home;