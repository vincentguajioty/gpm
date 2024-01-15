import React, { useEffect } from 'react';
import LogoutContent from 'components/connexion/LogoutContent';

import {Axios} from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const Logout = () => {
    const clean = async () => {
		const tokenToClean = HabilitationService.token;
		HabilitationService.disconnect();
		const dropSession = await Axios.post('dropSession',
		{
			jwtToken: tokenToClean,
		});

		caches.keys().then((names) => {
            names.forEach((name) => {
                caches.delete(name);
            });
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
