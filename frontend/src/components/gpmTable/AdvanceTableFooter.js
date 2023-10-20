/* eslint-disable react/prop-types */
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classNames from 'classnames';
import React, { useState } from 'react';
import { Button, Form } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import IconButton from 'components/common/IconButton';

export const AdvanceTableFooter = ({
  page,
  pageSize,
  pageIndex,
  rowCount,
  setPageSize,
  canPreviousPage,
  canNextPage,
  viewAllBtn,
  nextPage,
  previousPage,
  rowInfo,
  perPage,
  rowsPerPageSelection,
  navButtons,
  rowsPerPageOptions = [10, 25, 75, 100],
  className
}) => {
  const [isAllVisible, setIsAllVisible] = useState(false);
  return (
    <Flex
      className={classNames(
        className,
        'align-items-center justify-content-between'
      )}
    >
      <Flex alignItems="center" className="fs--1">
        {rowInfo && (
          <p className="mb-0">
            <span className="d-none d-sm-inline-block me-2">
              {pageSize * pageIndex + 1} à {pageSize * pageIndex + page.length}{' '}
              sur {rowCount}
            </span>
            {viewAllBtn && (
              <>
                <span className="d-none d-sm-inline-block me-2">&mdash;</span>
                <Button
                  variant="link"
                  size="sm"
                  className="py-2 px-0 fw-semi-bold"
                  onClick={() => {
                    setIsAllVisible(!isAllVisible);
                    setPageSize(isAllVisible ? perPage : rowCount);
                  }}
                >
                  Voir {isAllVisible ? 'moins' : 'tout'}
                  <FontAwesomeIcon
                    icon="chevron-right"
                    className="ms-1 fs--2"
                  />
                </Button>
              </>
            )}
          </p>
        )}
        {rowsPerPageSelection && (
          <>
            <p className="mb-0 mx-2">Lignes par pages:</p>
            <Form.Select
              size="sm"
              className="w-auto"
              onChange={e => setPageSize(Number(e.target.value))}
              defaultValue={pageSize}
            >
              {rowsPerPageOptions.map(value => (
                <option value={value} key={value}>
                  {value}
                </option>
              ))}
            </Form.Select>
          </>
        )}
      </Flex>
      {navButtons && (
        <Flex>
          <IconButton
            size="sm"
            variant={canPreviousPage ? 'primary' : 'light'}
            onClick={() => previousPage()}
            className={classNames({ disabled: !canPreviousPage }, 'me-1')}
            icon='chevron-left'
          />
          <IconButton
            size="sm"
            variant={canNextPage ? 'primary' : 'light'}
            className={classNames({disabled: !canNextPage})}
            onClick={() => nextPage()}
            icon='chevron-right'
          />
        </Flex>
      )}
    </Flex>
  );
};

export default AdvanceTableFooter;
