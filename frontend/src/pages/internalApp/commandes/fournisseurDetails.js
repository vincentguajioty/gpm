import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Tabs, Tab, Table } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import FournisseurInfosGenerales from './fournisseurInfosGenerales';
import FournisseurInfosAes from './fournisseurInfosAes';

import AesFournisseursService from 'services/aesFournisseursService';

import { Axios } from 'helpers/axios';

import moment from 'moment-timezone';

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
            location.reload();
        }
    }, [pageNeedsRefresh])

    const nl2br = require('react-nl2br');
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
                <Col md={8}>
                    <Card>
                        <Tabs defaultActiveKey="produits" id="uncontrolled-tab-example">
                            <Tab eventKey="produits" title="Produits référencés" className='border-bottom border-x p-3'>
                                <Table>
                                    <thead>
                                        <tr>
                                            <th>Libellé</th>
                                            <th>Catégorie</th>
                                            <th>Commentaires</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {fournisseur.catalogue.map((item, i) => {return(
                                            <tr>
                                                <td>{item.libelleMateriel}</td>
                                                <td>{item.libelleCategorie}</td>
                                                <td>{nl2br(item.commentairesMateriel)}</td>
                                            </tr>
                                        )})}
                                    </tbody>
                                </Table>
                            </Tab>
                            <Tab eventKey="commandes" title="Commandes" className='border-bottom border-x p-3'>
                                <Table>
                                    <thead>
                                        <tr>
                                            <th>Date de création</th>
                                            <th>Nom</th>
                                            <th>Etat</th>
                                            <th>Référence fournisseur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {fournisseur.commandes.map((item, i) => {return(
                                            <tr>
                                                <td>{moment(item.dateCreation).format('DD/MM/YYYY')}</td>
                                                <td>{item.nomCommande}</td>
                                                <td>{item.libelleEtat}</td>
                                                <td>{item.numCommandeFournisseur}</td>
                                            </tr>
                                        )})}
                                    </tbody>
                                </Table>
                            </Tab>
                        </Tabs>
                    </Card>
                </Col>
            </Row>
        </>);
    }else{
        return(<LoaderInfiniteLoop/>)
    }
};

FournisseurDetails.propTypes = {};

export default FournisseurDetails;
