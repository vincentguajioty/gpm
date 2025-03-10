import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import TransmissionsStockTable from './transmissionsStock/transmissionsStockTable';

const TransmissionsStock = () => {
    return (<>
        <PageHeader
            preTitle="Transmissions"
            title="Stock de consommables"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <TransmissionsStockTable/>
            </FalconComponentCard.Body>
        </FalconComponentCard>

    </>);
};

TransmissionsStock.propTypes = {};

export default TransmissionsStock;
