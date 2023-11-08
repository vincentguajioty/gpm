import React, {useEffect, useState} from 'react';
import { Card, Form , Row, Col } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import PageHeader from 'components/common/PageHeader';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

const EquipementVhfPJ = ({equipement, setPageNeedsRefresh}) => {

    return(<>
        <Card className="mb-3">
            <Card.Header className="p-2 border-bottom">
                <Flex>
                    <div className="p-2 flex-grow-1">
                        Pièces jointes
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
            </Card.Body>
        </Card>
    </>);
};

EquipementVhfPJ.propTypes = {};

export default EquipementVhfPJ;