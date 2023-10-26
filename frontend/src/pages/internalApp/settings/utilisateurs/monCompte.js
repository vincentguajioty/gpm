import React from 'react';
import PageHeader from 'components/common/PageHeader';
import { Row, Col } from 'react-bootstrap'; 

import HabilitationService from 'services/habilitationsService';

import Password from './password';
import MFA from './mfa';
import UserInfo from './userInfo';
import ProfilRecapDroitsUtilisateurBox from '../profils/profilRecapDroitsUtilisateurBox';

const Moncompte = () => {
    return(<>
        <PageHeader
            preTitle="Paramètres"
            title="Mon compte"
            description={HabilitationService.habilitations.identifiant}
            className="mb-3"
        />

        <Row>
			<Col md={8}>
				<UserInfo idPersonne={HabilitationService.habilitations.idPersonne} />
			</Col>
			<Col md={4}>
				<Password checkOldPassword={true} layout="box" />
				<MFA />
			</Col>
			<Col md={12}>
				<ProfilRecapDroitsUtilisateurBox idPersonne={HabilitationService.habilitations.idPersonne} />
			</Col>
		</Row>
    </>);
}

Moncompte.propTypes = {};

export default Moncompte;