import React, {useState, useEffect} from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import { Form, Button, Row, Col, Alert } from 'react-bootstrap';
import Select from 'react-select';
import IconButton from 'components/common/IconButton';
import moment from 'moment-timezone';

import {Axios} from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";

const UtilisateurMFA = ({personne, setPageNeedsRefresh}) => {

    const disableMFA = async () => {
        try {
            const response = await Axios.post('settingsUtilisateurs/disableMfa',{
				idPersonne: personne.idPersonne,
			});
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error);
        }
    }

	return (
		<>
            <FalconComponentCard>
                <FalconComponentCard.Header
                    title="MFA"
                >
                </FalconComponentCard.Header>
                <FalconComponentCard.Body>
                    {personne.mfaEnabled ?
                        <>
                            <Alert variant='success'>MFA actif pour cet utilisateur</Alert>
                            <IconButton
                                icon='unlink'
                                variant='warning'
                                onClick={disableMFA}
                            >
                                Désactiver le MFA
                            </IconButton>
                        </>
                    :
                        <Alert variant='secondary'>MFA inactif pour cet utilisateur</Alert>
                    }
                </FalconComponentCard.Body>
            </FalconComponentCard>
		</>
	);
};

UtilisateurMFA.propTypes = {};

export default UtilisateurMFA;