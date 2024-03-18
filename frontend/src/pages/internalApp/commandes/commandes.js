import React, {useState, useEffect} from 'react';
import { Card, Row, Col, Accordion } from 'react-bootstrap';
import  { Breakpoint } from 'react-socks';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import CommandesTable from './commandesTable';
import CommandesFilter from './commandesFilter';

import { Axios } from 'helpers/axios';

const Commandes = () => {
    const [pageReady, setPageReady] = useState(false);
    const [commandesArray, setCommandesArray] = useState([]);
    const [commandesArrayFiltered, setCommandesArrayFiltered] = useState([]);

    const initPage = async () => {
        try {
            const getCommandes = await Axios.get('/commandes/getCommandes');
            setCommandesArray(getCommandes.data);

            setPageReady(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    return (<>
        <PageHeader
            title="Commandes"
            className="mb-3"
        />

        {pageReady ?
            <Row>
                <Col md={2}>
                    <Breakpoint large up>
                        <FalconComponentCard noGuttersBottom className="mb-3">
                            <FalconComponentCard.Body
                                scope={{ ActionButton }}
                                noLight
                            >
                                <CommandesFilter
                                    commandesArray={commandesArray}
                                    setCommandesArrayFiltered={setCommandesArrayFiltered}
                                />
                            </FalconComponentCard.Body>
                        </FalconComponentCard>
                    </Breakpoint>

                    <Breakpoint medium down>
                        <Card className="mb-3">
                            <Accordion className="mb-3">
                                <Accordion.Item eventKey="filtres" flush="true">
                                    <Accordion.Header>
                                        <FontAwesomeIcon icon="filter"/> Filtres
                                    </Accordion.Header>
                                    <Accordion.Body>
                                        <CommandesFilter
                                            commandesArray={commandesArray}
                                            setCommandesArrayFiltered={setCommandesArrayFiltered}
                                        />
                                    </Accordion.Body>
                                </Accordion.Item>
                            </Accordion>
                        </Card>
                    </Breakpoint>
                </Col>
                <Col md={10}>
                    <FalconComponentCard noGuttersBottom className="mb-3">
                        <FalconComponentCard.Body
                            scope={{ ActionButton }}
                            noLight
                        >
                            <CommandesTable
                                commandesArrayFiltered={commandesArrayFiltered}
                            />
                        </FalconComponentCard.Body>
                    </FalconComponentCard>
                </Col>
            </Row>
        : <LoaderInfiniteLoop/>}
    </>);
};

Commandes.propTypes = {};

export default Commandes;
