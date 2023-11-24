import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import SacsTable from './sacs/sacsTable';

const Sacs = () => {
    return (<>
        <PageHeader
            preTitle="Lots opérationnels"
            title="Gestion des sacs"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <SacsTable/>
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Sacs.propTypes = {};

export default Sacs;
