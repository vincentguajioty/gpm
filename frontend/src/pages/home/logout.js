import React, { useEffect } from 'react';
import LogoutContent from 'components/connexion/LogoutContent';

import {Axios} from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const Logout = () => {
    const clean = async () => {
		let jwtToken = HabilitationService.token;
		localStorage.clear();
		const dropSession = await Axios.post('dropSession',
		{
			jwtToken: jwtToken,
		});
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
