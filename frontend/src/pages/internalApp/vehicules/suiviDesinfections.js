import React, { useState, useEffect } from 'react';
import { Row, Col, Card, Table, } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';

import VehiculeDesinfectionsForm from './vehiculeDetails/vehiculeDesinfectionsForm';
import Calendar from '../home/calendrierGeneral';

import { Axios } from 'helpers/axios';

const SuiviDesinfections = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    const [tableauDesinfections, setTableauDesinfections] = useState([]);
    const [isLoading, setLoading] = useState(false);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vehicules/getDesinfectionsDashoard');
            setTableauDesinfections(getData.data);
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

    //Export
    const requestExport = async () => {
        try {
            setLoading(true);

            let fileRequest = await Axios.get('/vehicules/exporterCarnetDesinfection');

            let documentData = await Axios.post('getSecureFile/temp',
            {
                fileName: fileRequest.data.fileName,
            },
            {
                responseType: 'blob'
            });
            
            // create file link in browser's memory
            const href = URL.createObjectURL(documentData.data);
            
            // create "a" HTML element with href to file & click
            const link = document.createElement('a');
            link.href = href;
            link.setAttribute('download', fileRequest.data.fileName); //or any other extension
            document.body.appendChild(link);
            link.click();

            // clean up "a" element & remove ObjectURL
            document.body.removeChild(link);
            URL.revokeObjectURL(href);
            
            setDownloadGenerated(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <PageHeader
            preTitle="Véhicules"
            title="Tableau de bord des désinfections"
            className="mb-3"
        />

        <Row>
            <Col md={12}>
                <Card className='mb-3'>
                    <Card.Body>
                        {readyToDisplay ?
                            <Table size='sm' responsive>
                                <thead>
                                    <tr>
                                        <td>Véhicule</td>
                                        {tableauDesinfections.desinfections.map((colonne, i)=>{return(
                                            <td>{colonne.libelleVehiculesDesinfectionsType}</td>
                                        )})}
                                        <td>
                                            <IconButton
                                                icon='download'
                                                size = 'sm'
                                                variant="outline-info"
                                                onClick={requestExport}
                                                className='ms-1'
                                                disabled={isLoading}
                                            >{isLoading ? "Génération en cours" : "Carnet"}</IconButton>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tableauDesinfections.vehicules.map((vehicule, i)=>{return(
                                        <tr>
                                            <td>{vehicule.libelleVehicule}</td>
                                            {tableauDesinfections.desinfections.map((colonne, i)=>{
                                                let desinfUnitaire = vehicule.desinfDashboard.filter(desinf => desinf.idVehiculesDesinfectionsType == colonne.idVehiculesDesinfectionsType)

                                                if(desinfUnitaire.length == 1)
                                                {
                                                    desinfUnitaire = desinfUnitaire[0];

                                                    let derniereDesinfLabel = 'Aucune';
                                                    let prochaineDesinfLabel = 'Aucune';
                                                    let derniereDesinfColor = 'secondary';

                                                    if(desinfUnitaire.dateDesinfection != null)
                                                    {
                                                        derniereDesinfLabel = desinfUnitaire.dateDesinfection;
                                                    }
                                                    if(desinfUnitaire.alerte != null)
                                                    {
                                                        if(desinfUnitaire.dateDesinfection == null)
                                                        {
                                                            derniereDesinfColor = 'danger';
                                                            prochaineDesinfLabel = 'Au plus vite';
                                                        }
                                                        else
                                                        {
                                                            if(new Date(desinfUnitaire.nextDesinfection) > new Date())
                                                            {
                                                                derniereDesinfColor = 'success';
                                                                prochaineDesinfLabel = desinfUnitaire.nextDesinfection;
                                                            }
                                                            else
                                                            {
                                                                derniereDesinfColor = 'warning';
                                                                prochaineDesinfLabel = 'Au plus vite';
                                                            }
                                                        }
                                                    }

                                                    return(
                                                        <td>
                                                            <SoftBadge bg={derniereDesinfColor} className='me-1'>Dernièrement: {derniereDesinfLabel}</SoftBadge><br/>
                                                            {derniereDesinfLabel != 'Aucune' ? <SoftBadge bg={derniereDesinfColor} className='me-1'>Prochaine: {prochaineDesinfLabel}</SoftBadge> : null}
                                                        </td>
                                                    )
                                                }
                                                else
                                                {
                                                    return(
                                                        <td>ERREUR</td>
                                                    )
                                                }
                                            })}
                                            <td>
                                                <VehiculeDesinfectionsForm
                                                    idVehicule = {vehicule.idVehicule}
                                                    idVehiculesDesinfection = {0}
                                                    setPageNeedsRefresh = {setPageNeedsRefresh}
                                                    buttonSize = 'small'
                                                />
                                            </td>
                                        </tr>
                                    )})}
                                </tbody>
                            </Table>
                        :<LoaderInfiniteLoop/>}
                    </Card.Body>
                </Card>
            </Col>
            <Col md={8}>
                <Calendar
                    className='mb-3'
                    pageNeedsRefresh={pageNeedsRefresh}
                    vehiculesDesinfectionsPassees={true}
                    vehiculesDesinfectionsFutures={true}
                />
            </Col>
            <Col md={4}>
                <Card className='mb-3'>
                    <Card.Header>Historiques des trois derniers mois</Card.Header>
                    <Card.Body>
                        {readyToDisplay ?
                            <Table size='sm' responsive>
                                <thead>
                                    <tr>
                                        <td>Date</td>
                                        <td>Véhicule</td>
                                        <td>Désinfection</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tableauDesinfections.lastThreeMonths.map((item, i)=>{return(
                                        <tr>
                                            <td>{moment(item.dateDesinfection).format('DD/MM/YYYY')}</td>
                                            <td>{item.libelleVehicule}</td>
                                            <td>{item.libelleVehiculesDesinfectionsType}</td>
                                        </tr>
                                    )})}
                                </tbody>
                            </Table>
                        :<LoaderInfiniteLoop/>}
                    </Card.Body>
                </Card>
            </Col>
        </Row>
    </>);
};

SuiviDesinfections.propTypes = {};

export default SuiviDesinfections;
