import React from 'react';
import { Card, Tabs, Tab, Alert } from 'react-bootstrap';
import moment from 'moment-timezone';

import AesFournisseursService from 'services/aesFournisseursService';

import AesFournisseursUnlock from '../aesFournisseurs/aesFournisseursUnlock';
import AesFournisseursLock from '../aesFournisseurs/aesFournisseursLock';
import AesFournisseursChangeKey from '../aesFournisseurs/aesFournisseursChangeKey';
import AesFournisseursDisable from '../aesFournisseurs/aesFournisseursDisable';
import AesFournisseursInitKey from '../aesFournisseurs/aesFournisseursInitKey';

const ConfigGeneraleTabAES = ({
    config,
    setPageNeedsRefresh,
    isLoading,
    setIsLoading,
}) => {
    return (<>
        {config.aesFournisseurTemoin == null ?
            <AesFournisseursInitKey
                setPageNeedsRefresh={setPageNeedsRefresh}
                isLoading={isLoading}
                setIsLoading={setIsLoading}
            />
        :
            AesFournisseursService.aesTokenValidUntil && moment(AesFournisseursService.aesTokenValidUntil) > new Date() ?
                <>
                    <Alert variant='warning'>Avant toute manipulation, il est très fortement recommandé D'ACTIVER LE MODE DE MAINTENANCE et de s'assurer que PLUS PERSONNE N'EST CONNECTE SUR LA PLATEFORME.</Alert>
                    <AesFournisseursChangeKey
                        setPageNeedsRefresh={setPageNeedsRefresh}
                        isLoading={isLoading}
                        setIsLoading={setIsLoading}
                    />
                    <AesFournisseursDisable
                        setPageNeedsRefresh={setPageNeedsRefresh}
                        isLoading={isLoading}
                        setIsLoading={setIsLoading}
                    />
                    <AesFournisseursLock setPageNeedsRefresh={setPageNeedsRefresh} />
                </>
            :
                <AesFournisseursUnlock
                    setPageNeedsRefresh={setPageNeedsRefresh}
                    isLoading={isLoading}
                    setIsLoading={setIsLoading}
                />
        }
    </>);
};

ConfigGeneraleTabAES.propTypes = {};

export default ConfigGeneraleTabAES;
