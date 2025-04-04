import React, { useState, useEffect } from 'react';
import { Row, Col, } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import WidgetSectionTitle from 'components/widgets/WidgetSectionTitle';

import { Axios } from 'helpers/axios';

import AffectationsAvecDates from './affectations/affectationsAvecDates';
import AffectationsAvecDemandeRemplacement from './affectations/affectationsAvecDemandeRemp';
import AffectationsAvecDemandePret from './affectations/affectationsAvecDemandePret';

const AffectationsSuivi = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [affectationsRow, setAffectationsRow] = useState([]);

    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const initPage = async () => {
        try {
            let getData = await Axios.get('/tenues/getAffectationsRow');
            setAffectationsRow(getData.data);
            
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
                <Col md={4}>
                    <WidgetSectionTitle
                        icon="exchange-alt"
                        title="Bénévoles"
                        subtitle="Demandes de remplacement"
                        transform="shrink-2"
                        className="mb-4 mt-3"
                    />

                    <AffectationsAvecDemandeRemplacement
                        affectationsFiltered={affectationsRow.filter(affect => affect.demandeBenevoleRemplacement == true)}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
                <Col md={4}>
                    <WidgetSectionTitle
                        icon="clock"
                        title="Bénévoles"
                        subtitle="Demandes de prêt"
                        transform="shrink-2"
                        className="mb-4 mt-3"
                    />

                    <AffectationsAvecDemandePret
                        affectationsFiltered={affectationsRow.filter(affect => affect.demandeBenevolePret == true)}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
            </Row>
        : <LoaderInfiniteLoop />}

    </>);
};

AffectationsSuivi.propTypes = {};

export default AffectationsSuivi;
