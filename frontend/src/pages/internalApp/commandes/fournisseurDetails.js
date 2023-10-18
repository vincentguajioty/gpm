import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Form } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import PageHeader from 'components/common/PageHeader';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import FournisseurInfosGenerales from './fournisseurInfosGenerales';
import FournisseurInfosAes from './fournisseurInfosAes';

import AesFournisseursService from 'services/aesFournisseursService';

import { Axios } from 'helpers/axios';

const FournisseurDetails = () => {
    let {idFournisseur} = useParams();

    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [fournisseur, setFournisseur] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.post('/fournisseurs/getOneFournisseur',{
                idFournisseur: idFournisseur,
                aesToken: AesFournisseursService.aesToken || null,
            });
            setFournisseur(getData.data[0]);  
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage()
    }, [])
    useEffect(() => {
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    }, [pageNeedsRefresh])

    if(readyToDisplay)
    {
        return(<>
            <PageHeader
                preTitle="Fiche fournisseur"
                title={fournisseur.nomFournisseur}
                className="mb-3"
            />

            <Row>
                <Col md={4}>
                    <FournisseurInfosGenerales
                        fournisseur={fournisseur}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                    <FournisseurInfosAes
                        fournisseur={fournisseur}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
                <Col md={8}>Une boite avec deux tabs pour les produits référencés et les commandes passées</Col>
            </Row>
        </>);
    }else{
        return(<LoaderInfiniteLoop/>)
    }
};

FournisseurDetails.propTypes = {};

export default FournisseurDetails;
