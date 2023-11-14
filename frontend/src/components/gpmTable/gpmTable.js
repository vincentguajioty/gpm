import React from 'react';
import { Row, Col } from 'react-bootstrap';
import AdvanceTable from './AdvanceTable';
import AdvanceTableSearchBox from './AdvanceTableSearchBox';
import AdvanceTableWrapper from './AdvanceTableWrapper';
import AdvanceTableFooter from './AdvanceTableFooter';

const GPMtable = ({
    columns,
    data,
    topButton,
    topButtonShow = false,
}) => {
    return (
        <AdvanceTableWrapper
            columns={columns}
            data={data}
            sortable={true}
            selection={false}
            pagination
        >
            <Row className="flex-end-center mb-3">
                <Col xs="auto" sm={6} lg={8}>
                    {topButtonShow == true ? <>{topButton}</> : null}
                </Col>
                <Col xs="auto" sm={6} lg={4}>
                    <AdvanceTableSearchBox table/>
                </Col>
            </Row>
            <AdvanceTable
                table
                headerClassName="bg-200 text-900 text-nowrap align-middle"
                rowClassName="align-middle white-space-nowrap"
                tableProps={{
                    bordered: true,
                    striped: true,
                    className: 'fs--1 mb-0 overflow-hidden'
                }}
            />
            <div className="mt-3">
                <AdvanceTableFooter
                    table
                    page
                    pageIndex
                    rowCount={data.length}
                    viewAllBtn={true}
                    nextPage
                    previousPage
                    rowInfo={true}
                    perPage
                    rowsPerPageSelection={true}
                    navButtons={true}
                />
            </div>
        </AdvanceTableWrapper>
    );
};

GPMtable.propTypes = {};

export default GPMtable;
