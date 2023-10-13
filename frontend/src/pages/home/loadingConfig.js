import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Card } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import HabilitationService from 'services/habilitationsService';
import ConfigurationService from 'services/configurationService';

import { Axios } from 'helpers/axios';

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

const LoadingConfig = () => {

    const navigate = useNavigate();

    const getConfig = async () => {
        const response = await Axios.get('getConfig');
        ConfigurationService.setConfig(response.data[0]);
        await sleep(1000);
        navigate('/');
    }

    useEffect(() => {
		getConfig();
	}, [])

    return (
        <Card className="text-center">
            <Card.Body className="p-5">
                <LoaderInfiniteLoop/>
                <hr />
                <p className="lead mt-4 text-800 font-sans-serif fw-semi-bold">
                Chargement de la configuration en cours
                </p>
            </Card.Body>
        </Card>
    );
};

export default LoadingConfig;
