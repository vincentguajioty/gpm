import React from 'react';
import PageHeader from 'components/common/PageHeader';

import TenuesPublicService from 'services/tenuesPublicService';

import TenuesDemandeToken from './demandeToken';
import TenuesAfficherPublic from './afficherTenues';

const GestionTenues = () => {
    
    return (<>
        <PageHeader
            preTitle="Espace public"
            title="Gérer mes éléments de tenue"
            className="mb-3"
        />

        {TenuesPublicService.tenuesPublicToken && TenuesPublicService.tenuesPublicTokenValidUntil && new Date(TenuesPublicService.tenuesPublicTokenValidUntil) > new Date() ?
            <TenuesAfficherPublic />
        :
            <TenuesDemandeToken />}
        
    </>);
};

GestionTenues.propTypes = {};

export default GestionTenues;
