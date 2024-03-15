import React, { useEffect, useState } from 'react';
import { Button, Card, Col, OverlayTrigger, Row, Tooltip, Modal, ListGroup, Table } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Flex from 'components/common/Flex';
import SoftBadge from 'components/common/SoftBadge';
import Lottie from 'lottie-react';
import lottieClos from 'components/widgets/lottie-commandeClose';

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

    //Modal de détails
    const [showDetailsModal, setShowDetailsModal] = useState(false);
    const [detailsModalContent, setDetailsModalContent] = useState([]);

    const handleCloseDetailsModal = () => {
        setDetailsModalContent([]);
        setShowDetailsModal(false);
    };
    const handleShowDetailsModal = (content) => {
        setDetailsModalContent(content);
        setShowDetailsModal(true);
    };

    return (<>
        <ListGroup className="mb-3">
            {checkListItems.map((item, i)=>{return(
                <ListGroup.Item variant={item.nbAlertes > 0 ? 'warning' : 'success'} onClick={()=>{handleShowDetailsModal(item.alertes)}}>
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

        <Modal show={showDetailsModal} onHide={handleCloseDetailsModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Détails de l'alerte</Modal.Title>
                <FalconCloseButton onClick={handleCloseDetailsModal}/>
            </Modal.Header>
            <Modal.Body>
                {detailsModalContent.length == 0 ?
                    <Lottie animationData={lottieClos} loop={false} />
                :
                    <ul>
                        {detailsModalContent.map((alerte, i)=>{
                            let listeItem = "";
                            for(let key in alerte){
                                alerte[key] != null ? listeItem += alerte[key] : null
                                alerte[key] != null && key != Object.keys(alerte)[Object.keys(alerte).length - 1] ? listeItem+= ' > ' : null
                            }
                            return <li>{listeItem}</li>;
                        })}
                    </ul>
                }
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDetailsModal}>
                    Fermer
                </Button>
            </Modal.Footer>
        </Modal>
    </>);
};

CheckList.propTypes = {};

export default CheckList;
