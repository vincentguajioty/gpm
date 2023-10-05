import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { Card, Col, Row } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import IconAlert from 'components/common/IconAlert';

import {Axios} from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';
import ConfigurationService from 'services/configurationService';

import moment from 'moment-timezone';

const Home = () => {

	const reloadForNavBar = async () => {
		if(localStorage.getItem("homeNeedRefresh") != 0)
		{
			localStorage.setItem("homeNeedRefresh", 0);
			location.reload();
		}
	}
	const reloadForConfig = async () => {
		if(localStorage.getItem("configNeedRefresh") != 0  || !ConfigurationService.config)
		{
			localStorage.setItem("configNeedRefresh", 0);
			location.reload();
		}
	}
	useEffect(() => {
		reloadForNavBar();
		reloadForConfig();
	}, [])
	

    return(<>
		<h2 className='mb-4'>Bienvenue {HabilitationService.habilitations.prenomPersonne} !</h2>
	</>);
}

Home.propTypes = {};

export default Home;