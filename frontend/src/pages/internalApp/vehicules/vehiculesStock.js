import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';

import VehiculesStockTable from './vehiculesStock/vehiculesStockTable';

const VehiculesStock = () => {
    return (<>
        <PageHeader
            preTitle="Véhicules"
            title="Stock de consommables"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <VehiculesStockTable/>
            </FalconComponentCard.Body>
        </FalconComponentCard>

    </>);
};

VehiculesStock.propTypes = {};

export default VehiculesStock;
