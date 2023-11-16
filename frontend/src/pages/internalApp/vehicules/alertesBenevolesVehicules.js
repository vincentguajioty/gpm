import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import AlertesBenevolesVehiculesTable from './alertesBenevolesTable';

const AlertesBenevolesVehicules = () => {
    return (<>
        <PageHeader
            preTitle="Véhicules"
            title="Alertes remontées par les bénévoles"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <AlertesBenevolesVehiculesTable />
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

AlertesBenevolesVehicules.propTypes = {};

export default AlertesBenevolesVehicules;
