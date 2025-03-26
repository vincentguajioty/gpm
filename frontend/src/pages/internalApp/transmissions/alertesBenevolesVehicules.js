import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import AlertesBenevolesVhfTable from './alertesBenevolesTable';

const AlertesBenevolesVhf = () => {
    return (<>
        <PageHeader
            preTitle="Transmissions"
            title="Alertes remontées par les bénévoles"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <AlertesBenevolesVhfTable />
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

AlertesBenevolesVhf.propTypes = {};

export default AlertesBenevolesVhf;
