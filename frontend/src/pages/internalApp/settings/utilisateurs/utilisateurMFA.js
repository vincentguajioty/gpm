import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import { Alert } from 'react-bootstrap';
import IconButton from 'components/common/IconButton';

import {Axios} from 'helpers/axios';


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