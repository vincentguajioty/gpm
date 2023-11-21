import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import ReservesMaterielsTable from './materiels/materielsTable';

const ReservesMateriels = () => {
    return (<>
        <PageHeader
            preTitle="Réserves"
            title="Gestion du matériel et des consommables"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <ReservesMaterielsTable/>
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

ReservesMateriels.propTypes = {};

export default ReservesMateriels;