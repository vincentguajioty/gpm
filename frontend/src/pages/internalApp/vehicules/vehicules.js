import React from 'react';

import PageHeader from 'components/common/PageHeader';
import VehiculesTable from './vehiculesTable';

const Vehicules = () => {
    return (<>
        <PageHeader
            preTitle="Véhicules"
            title="Gestion du parc de véhicules"
            className="mb-3"
        />

        <VehiculesTable />
    </>);
};

Vehicules.propTypes = {};

export default Vehicules;
