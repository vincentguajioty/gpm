import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';

import { Axios } from 'helpers/axios';

import AffectationDetails from './affectations/affectationDetails';

const AffectationsTenues = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [affectations, setAffectations] = useState([]);
    const [affectationsRow, setAffectationsRow] = useState([]);

    const [catalogue, setCatalogue] = useState([]);
    const [personnesExternes, setPersonnesExternes] = useState([]);

    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const initPage = async () => {
        try {
            let getData = await Axios.get('/tenues/getAffectations');
            setAffectations(getData.data);
            getData = await Axios.get('/tenues/getAffectationsRow');
            setAffectationsRow(getData.data);

            getData = await Axios.get('/select/getCatalogueMaterielTenues');
            setCatalogue(getData.data);

            getData = await Axios.get('/tenues/getPersonnesSuggested');
            setPersonnesExternes(getData.data);
            
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
            <AffectationDetails
                affectations={affectations}
                affectationsRow={affectationsRow}
                catalogue={catalogue}
                personnesExternes={personnesExternes}
                setPageNeedsRefresh={setPageNeedsRefresh}
            />
        : <LoaderInfiniteLoop />}

    </>);
};

AffectationsTenues.propTypes = {};

export default AffectationsTenues;
