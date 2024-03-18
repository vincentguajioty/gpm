import React, {useState, useEffect} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, } from 'react-bootstrap';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

import OneCommandeTabs from './oneCommandeTabs';
import OneCommandeTimeLine from './oneCommandeTimeLine';

const OneCommande = () => {
    let {idCommande} = useParams();
    const [commande, setCommande] = useState([]);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    const [pageReady, setPageReady] = useState(false);

    const initPage = async () => {
        try {
            let getCommande = await Axios.post('/commandes/getOneCommande',{
                idCommande: idCommande,
            })
            setCommande(getCommande.data);
            setPageReady(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    useEffect(()=>{
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    },[pageNeedsRefresh])

    return (
        pageReady ?
            <>
                <PageHeader
                    preTitle='Commandes'
                    title={commande.detailsCommande.nomCommande}
                    description={commande.detailsCommande.libelleEtat + ' | ' + (commande.detailsCommande.idFournisseur ? commande.detailsCommande.nomFournisseur : 'Fournisseur à définir') + ' | Montant ' + commande.detailsCommande.montantTotal+' €'}
                    className="mb-3"
                />

                <Row>
                    <Col md={10}>
                        <FalconComponentCard noGuttersBottom className="mb-3">
                            <FalconComponentCard.Body
                                scope={{ ActionButton }}
                                noLight
                            >
                                <OneCommandeTabs
                                    idCommande={idCommande}
                                    commande={commande}
                                    setPageNeedsRefresh={setPageNeedsRefresh}
                                />
                            </FalconComponentCard.Body>
                        </FalconComponentCard>
                    </Col>
                    <Col md={2}>
                        <FalconComponentCard noGuttersBottom className="mb-3">
                            <FalconComponentCard.Body
                                scope={{ ActionButton }}
                                noLight
                            >
                                <OneCommandeTimeLine
                                    idCommande={idCommande}
                                    commande={commande}
                                    setPageNeedsRefresh={setPageNeedsRefresh}
                                />
                            </FalconComponentCard.Body>
                        </FalconComponentCard>
                    </Col>
                </Row>
            </>
        : <LoaderInfiniteLoop/>
    );
};

OneCommande.propTypes = {};

export default OneCommande;
