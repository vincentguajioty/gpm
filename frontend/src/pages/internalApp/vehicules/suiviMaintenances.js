import React, { useState, useEffect } from 'react';
import { Row, Col, Card, Table, } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

import VehiculeMaintenancesRegulieresForm from './vehiculeDetails/vehiculeMntRegulieresForm';
import Calendar from '../home/calendrierGeneral';

import { Axios } from 'helpers/axios';

const SuiviMaintenances = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    const [tableauMaintenances, setTableauMaintenances] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vehicules/getMaintenancesRegulieresDashoard');
            setTableauMaintenances(getData.data);
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
            preTitle="Véhicules"
            title="Tableau de bord des maintenances régulières"
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
                                        {tableauMaintenances.maintenances.map((colonne, i)=>{return(
                                            <td>{colonne.libelleHealthType}</td>
                                        )})}
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tableauMaintenances.vehicules.map((vehicule, i)=>{return(
                                        <tr>
                                            <td>{vehicule.libelleVehicule}</td>
                                            {tableauMaintenances.maintenances.map((colonne, i)=>{
                                                let mntUnitaire = vehicule.mntDashboard.filter(mnt => mnt.idHealthType == colonne.idHealthType)
                                                
                                                if(mntUnitaire.length == 1)
                                                {
                                                    mntUnitaire = mntUnitaire[0];

                                                    let derniereMntLabel = 'Aucune';
                                                    let prochaineMntLabel = 'Aucune';
                                                    let derniereMntColor = 'secondary';

                                                    if(mntUnitaire.dateHealth != null)
                                                    {
                                                        derniereMntLabel = mntUnitaire.dateHealth;
                                                    }
                                                    if(mntUnitaire.alerte != null)
                                                    {
                                                        if(mntUnitaire.dateHealth == null)
                                                        {
                                                            derniereMntColor = 'danger';
                                                            prochaineMntLabel = 'Au plus vite';
                                                        }
                                                        else
                                                        {
                                                            if(new Date(mntUnitaire.nextMaintenance) > new Date())
                                                            {
                                                                derniereMntColor = 'success';
                                                                prochaineMntLabel = mntUnitaire.nextMaintenance;
                                                            }
                                                            else
                                                            {
                                                                derniereMntColor = 'warning';
                                                                prochaineMntLabel = 'Au plus vite';
                                                            }
                                                        }
                                                    }

                                                    return(
                                                        <td>
                                                            <SoftBadge bg={derniereMntColor} className='me-1'>Dernièrement: {derniereMntLabel}</SoftBadge><br/>
                                                            {prochaineMntLabel != 'Aucune' ? <SoftBadge bg={derniereMntColor} className='me-1'>Prochaine: {prochaineMntLabel}</SoftBadge> : null}
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
                                                <VehiculeMaintenancesRegulieresForm
                                                    idVehicule = {vehicule.idVehicule}
                                                    idVehiculeHealth = {0}
                                                    maintenancesRegulieresAlertes={vehicule.maintenancesRegulieresAlertes}
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
                    vehiculesMntPonctuelles={true}
                    vehiculesMntRegPassees={true}
                    vehiculesMntRegFutures={true}
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
                                        <td>Taches</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tableauMaintenances.lastThreeMonths.map((item, i)=>{return(
                                        <tr>
                                            <td>{moment(item.dateHealth).format('DD/MM/YYYY')}</td>
                                            <td>{item.libelleVehicule}</td>
                                            <td>{item.details.map((tache, i)=>{return(<>{tache.libelleHealthType}<br/></>)})}</td>
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

SuiviMaintenances.propTypes = {};

export default SuiviMaintenances;
