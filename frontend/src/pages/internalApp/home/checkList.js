import React, { useEffect, useState } from 'react';
import { Button, Card, Col, OverlayTrigger, Row, Tooltip, Modal, ListGroup } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Flex from 'components/common/Flex';
import SoftBadge from 'components/common/SoftBadge';

import { Axios } from 'helpers/axios';

const CheckList = () => {
    const [checkListItems, setCheckListItems] = useState([]);

    const initPage = async () => {
        try {
            let getData = await Axios.get('/getHomeCheckList')
            setCheckListItems(getData.data);

        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    return (<>
        <ListGroup className="mb-3">
            {checkListItems.map((item, i)=>{return(
                <ListGroup.Item variant={item.nbAlertes > 0 ? 'warning' : 'success'} onClick={()=>{console.log('test')}}>
                    <Flex justifyContent="between" alignItems="center">
                        <div>
                            <FontAwesomeIcon icon={item.nbAlertes > 0 ? 'exclamation-triangle' : 'check'} className='me-2' />
                            {item.label}
                        </div>
                        {item.nbAlertes > 0 ?
                            <SoftBadge pill bg='danger' className='me-2'>{item.nbAlertes} Alerte{item.nbAlertes > 0 ? 's' : null}</SoftBadge>
                        : null}
                    </Flex>
                </ListGroup.Item>
            )})}
        </ListGroup>
    </>);
};

CheckList.propTypes = {};

export default CheckList;
