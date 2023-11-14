import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Tabs, Tab, } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import VehiculeProprietes from './vehiculeDetails/vehiculeProprietes';
import VehiculeAlertesBenevoles from './vehiculeDetails/vehiculeAlertesBenevoles';
import VehiculeDesinfections from './vehiculeDetails/vehiculeDesinfections';
import VehiculeMaintenancesRegulieres from './vehiculeDetails/vehiculeMntRegulieres';
import VehiculeMaintenancesPonctuelles from './vehiculeDetails/vehiculeMntPonctuelles';
import VehiculeRelevesKM from './vehiculeDetails/vehiculeRelevesKM';

const VehiculeDetails = () => {
    let {idVehicule} = useParams();
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [vehicule, setVehicule] = useState([]);
    const initPage = async () => {
        try {
            const getData = await Axios.post('/vehicules/getOneVehicule',{
                idVehicule: idVehicule
            });
            setVehicule(getData.data[0]);  
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
        return (<>
            <PageHeader
                preTitle="Véhicules"
                title={vehicule.libelleVehicule}
                className="mb-3"
            />

            <Row>
                <Col md={4}>
                    <VehiculeProprietes
                        vehicule={vehicule}
                        setPageNeedsRefresh={setPageNeedsRefresh}
                    />
                </Col>
                <Col md={8}>
                    <Card>
                        <Tabs defaultActiveKey="mntPonct" transition={true}>
                            <Tab eventKey="mntPonct" title="Maintenance ponctuelle" className='border-bottom border-x p-3'>
                                <VehiculeMaintenancesPonctuelles
                                    idVehicule={idVehicule}
                                    setPageNeedsRefresh={setPageNeedsRefresh}
                                    maintenancesPonctuelles={vehicule.maintenancesPonctuelles}
                                />
                            </Tab>
                            {HabilitationService.habilitations.vehiculeHealth_lecture ?
                                <Tab eventKey="mntReg" title="Maintenance régulière" className='border-bottom border-x p-3'>
                                    <VehiculeMaintenancesRegulieres
                                        idVehicule={idVehicule}
                                        setPageNeedsRefresh={setPageNeedsRefresh}
                                        maintenancesRegulieres={vehicule.maintenancesRegulieres}
                                        maintenancesRegulieresAlertes={vehicule.maintenancesRegulieresAlertes}
                                    />
                                </Tab>
                            : null}
                            {HabilitationService.habilitations.desinfections_lecture ?
                                <Tab eventKey="desinf" title="Désinfections" className='border-bottom border-x p-3'>
                                    <VehiculeDesinfections
                                        idVehicule={idVehicule}
                                        setPageNeedsRefresh={setPageNeedsRefresh}
                                        desinfections={vehicule.desinfections}
                                        desinfectionsAlertes={vehicule.desinfectionsAlertes}
                                    />
                                </Tab>
                            : null}
                            <Tab eventKey="km" title="Relevés Kilométriques" className='border-bottom border-x p-3'>
                                <VehiculeRelevesKM
                                    idVehicule={idVehicule}
                                    setPageNeedsRefresh={setPageNeedsRefresh}
                                    relevesKM={vehicule.relevesKM}
                                />
                            </Tab>
                            {HabilitationService.habilitations.alertesBenevolesVehicules_lecture ?
                                <Tab eventKey="alertes" title="Alertes de bénévoles" className='border-bottom border-x p-3'>
                                    <VehiculeAlertesBenevoles
                                        idVehicule={idVehicule}
                                        setPageNeedsRefresh={setPageNeedsRefresh}
                                        alertesBenevoles={vehicule.alertesBenevoles}
                                    />
                                </Tab>
                            : null}
                        </Tabs>
                    </Card>
                </Col>
            </Row>
        </>);
    }
    else
    {
        return(<LoaderInfiniteLoop/>)
    }
};

VehiculeDetails.propTypes = {};

export default VehiculeDetails;
