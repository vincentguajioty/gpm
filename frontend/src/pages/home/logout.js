import React, { useEffect } from 'react';
import LogoutContent from 'components/connexion/LogoutContent';

import {Axios} from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const Logout = () => {
    const clean = async () => {
		// const dropSession = await Axios.post('dropSession',
		// {
		// 	jwtToken: 'HabilitationService.token',
		// });

		localStorage.clear();
	}
	
	useEffect(() => {
		clean();
	},[])

	return (
		<div className="text-center">
			<LogoutContent layout="split" titleTag="h3" />
		</div>
    );
};

export default Logout;
