import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import AlertesBenevolesLotsTable from './alertesBenevolesTable';

const AlertesBenevolesVehicules = () => {
    return (<>
        <PageHeader
            preTitle="Lots opérationnels"
            title="Alertes remontées par les bénévoles"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <AlertesBenevolesLotsTable />
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

AlertesBenevolesVehicules.propTypes = {};

export default AlertesBenevolesVehicules;
