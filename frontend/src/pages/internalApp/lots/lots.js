import React from 'react';
import PageHeader from 'components/common/PageHeader';

import LotsTable from './lotsOpe/lotsTable';

const Lots = () => {
    return (<>
        <PageHeader
            preTitle="Lots opérationnels"
            title="Lots"
            className="mb-3"
        />

        <LotsTable />
    </>);
};

Lots.propTypes = {};

export default Lots;
