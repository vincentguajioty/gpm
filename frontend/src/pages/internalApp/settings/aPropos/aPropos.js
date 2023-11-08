import React, { useState, useEffect } from 'react';
import PageHeader from 'components/common/PageHeader';
import { Row, Col, } from 'react-bootstrap';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { version } from 'config';
import SoftBadge from 'components/common/SoftBadge';

import ConfigurationService from 'services/configurationService';
import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';
import moment from 'moment-timezone';

const APropos = () => {
    const[cgu, setCgu] = useState("");

    const initPage = async () => {
        try {
            let dbQuery = await Axios.get('/getCGU')
            setCgu(dbQuery.data[0].cnilDisclaimer)
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    },[]);

    const nl2br = require('react-nl2br');
    return (<>
        <PageHeader
            preTitle="GPM - Gestionnaire de Parc Matériel"
            title="A propos"
            className="mb-3"
        />

        <Row>
            <Col md={6} className="mb-3">
                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title="Vos Suggestions"
                    />
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                        className="p-0"
                    >
                        <p className='mb-3 ms-3 me-3'>
                            Rendez-vous sur <a href="https://github.com/vincentguajioty/gpm">GitHub</a> pour en savoir plus sur les développements en cours, les projets, et les mises à jour!
                        </p>
                        <p className='mb-3 ms-3 me-3'>
                            Pour contacter le developpeur de la solution, merci d'utiliser CE FORMULAIRE EN CLIQUANT ICI
                        </p>
                        <p className='mb-3 ms-3 me-3'>
                            Les <a href="https://github.com/vincentguajioty/gpm/issues/new/choose">tickets GitHub</a> sont également un bon moyen de communiquer !
                        </p>
                    </FalconComponentCard.Body>
                </FalconComponentCard>
            </Col>
            <Col md={6} className="mb-3">
                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title="Copyright et Version"
                    />
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                        className="p-0"
                    >
                        <p className='mb-3 ms-3 me-3'>
                            Creative Commons License Elements graphiques du site: AdminLTE ; Application: Vincent Guajioty
                            <br/><br/>
                            Version de l'application: {version}
                            <br/><br/>
                            Adresse mail de l'administrateur de cette instance de GPM: {ConfigurationService.config['mailserver']}
                            <br/><br/>
                            Adresse mail du référent CNIL de cette instance de GPM: {ConfigurationService.config['mailcnil']}
                        </p>
                    </FalconComponentCard.Body>
                </FalconComponentCard>
            </Col>
            <Col md={12} className="mb-3">
                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title="Conditions Générales d'Utilisation"
                    />
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                        className="p-0"
                    >
                        <p className='mb-3 ms-3 me-3'>
                            <SoftBadge bg='success'>Acceptées par {HabilitationService.habilitations['identifiant']} le {moment(HabilitationService.habilitations['disclaimerAccept']).format('DD/MM/YYYY')}</SoftBadge>
                        </p>
                        <p className='mb-3 ms-3 me-3'>
                            {cgu != "" ? nl2br(cgu) : <LoaderInfiniteLoop />}
                        </p>
                    </FalconComponentCard.Body>
                </FalconComponentCard>
            </Col>
        </Row>
    </>);
};

APropos.propTypes = {};

export default APropos;
