import React, {useState, useEffect} from 'react';
import { Card, Row, Col, Accordion } from 'react-bootstrap';
import  { Breakpoint } from 'react-socks';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';
import CentresDeCoursFilter from './centresDeCoutsFilter';
import CentresDeCoutsTable from './centresDeCoutsTable';

const CentresDeCouts = () => {
    const [pageReady, setPageReady] = useState(false);
    const [centresArray, setCentresArray] = useState([]);
    const [centresArrayFiltered, setCentresArrayFiltered] = useState([]);

    const initPage = async () => {
        try {
            const getCentres = await Axios.get('/centresCouts/getCentres');
            setCentresArray(getCentres.data);

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
            title="Centes de couts"
            className="mb-3"
        />

        {pageReady ?
            <Row>
                <Col md={3}>
                    <Breakpoint large up>
                        <FalconComponentCard noGuttersBottom className="mb-3">
                            <FalconComponentCard.Body
                                scope={{ ActionButton }}
                                noLight
                            >
                                <CentresDeCoursFilter
                                    centresArray={centresArray}
                                    setCentresArrayFiltered={setCentresArrayFiltered}
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
                                        <CentresDeCoursFilter
                                            centresArray={centresArray}
                                            setCentresArrayFiltered={setCentresArrayFiltered}
                                        />
                                    </Accordion.Body>
                                </Accordion.Item>
                            </Accordion>
                        </Card>
                    </Breakpoint>
                </Col>
                <Col md={9}>
                    <FalconComponentCard noGuttersBottom className="mb-3">
                        <FalconComponentCard.Body
                            scope={{ ActionButton }}
                            noLight
                        >
                            <CentresDeCoutsTable
                                centresArrayFiltered={centresArrayFiltered}
                            />
                        </FalconComponentCard.Body>
                    </FalconComponentCard>
                </Col>
            </Row>
        : <LoaderInfiniteLoop/>}
    </>);
};

CentresDeCouts.propTypes = {};

export default CentresDeCouts;
