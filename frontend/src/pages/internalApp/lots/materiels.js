import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import MaterielsTable from './materiels/materielsTable';

const Materiels = () => {
    return (<>
        <PageHeader
            preTitle="Lots opérationnels"
            title="Gestion du matériel et des consommables"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <MaterielsTable/>
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Materiels.propTypes = {};

export default Materiels;
