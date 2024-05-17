import React, { useState, useEffect } from 'react';
import { Row, Col, } from 'react-bootstrap';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import WidgetSectionTitle from 'components/widgets/WidgetSectionTitle';

import { Axios } from 'helpers/axios';

import AffectationsAvecDates from './affectations/affecationsAvecDates';
import AffectationDetails from './affectations/affectationDetails';

const AffectationsTenues = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [affectations, setAffectations] = useState([]);
    const [affectationsRow, setAffectationsRow] = useState([]);

    const [catalogue, setCatalogue] = useState([]);
    const [personnesInternes, setPersonnesInternes] = useState([]);
    const [personnesExternes, setPersonnesExternes] = useState([]);

    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const initPage = async () => {
        try {
            let getData = await Axios.get('/tenues/getAffectations');
            setAffectations(getData.data);
            getData = await Axios.get('/tenues/getAffectationsRow');
            setAffectationsRow(getData.data);

            getData = await Axios.get('/select/getTenuesCatalogue');
            setCatalogue(getData.data);

            getData = await Axios.get('/tenues/getPersonnesSuggested');
            setPersonnesExternes(getData.data);

            getData = await Axios.get('/select/getNonAnonymesPersonnes');
            getData.data.unshift({value: 0, label: '--- Affecté à un externe ---'})
            setPersonnesInternes(getData.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    useEffect(() => {
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    }, [pageNeedsRefresh])
    
    return (<>
        <PageHeader
            preTitle="Gestion des tenues"
            title="Affectation des tenues"
            className="mb-3"
        />

        {readyToDisplay ?
            <Row>
                <Col md={4}>
                    <WidgetSectionTitle
                        icon="eye"
                        title="Vigilance"
                        subtitle="Suivi des dates de retours"
                        transform="shrink-2"
                        className="mb-4 mt-3"
                    />

                    <AffectationsAvecDates
                        affectationsFiltered={affectationsRow.filter(affect => affect.dateRetour != null)}
                    />
                </Col>
                <Col md={8}>
                    <WidgetSectionTitle
                        icon="tshirt"
                        title="Cycle de vie"
                        subtitle="Affectations, suivis, retours"
                        transform="shrink-2"
                        className="mb-4 mt-3"
                    />

                    <AffectationDetails
                        affectations={affectations}
                        affectationsRow={affectationsRow}
                        catalogue={catalogue}
                        personnesInternes={personnesInternes}
                        personnesExternes={personnesExternes}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
            </Row>
        : <LoaderInfiniteLoop />}

    </>);
};

AffectationsTenues.propTypes = {};

export default AffectationsTenues;
