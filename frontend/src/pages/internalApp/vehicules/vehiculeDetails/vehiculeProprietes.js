import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Card, Form, Table } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import PageHeader from 'components/common/PageHeader';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import VehiculeLotsCharges from './vehiculeLotsCharges';
import VehiculeAttached from './vehiculeAttached';

const VehiculeProprietes = ({vehicule, setPageNeedsRefresh}) => {
    const [modeEdition, setModeEdition] = useState(false);
    const handleEdition = () => {
        if(!modeEdition)
        {
            initForm();
        }
        setModeEdition(!modeEdition);
    }

    const initForm = async () => {
        try {
            
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <Card className="mb-3">
            <Card.Header className="p-2 border-bottom">
                <Flex>
                    <div className="p-2 flex-grow-1">
                        Détails du véhicule
                    </div>
                    <div className="p-2">
                        <Form.Check 
                            type='switch'
                            id='defaultSwitch'
                            label='Modifier'
                            onClick={handleEdition}
                            checked={modeEdition}
                            disabled={!HabilitationService.habilitations['vhf_equipement_modification']}
                        />
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                {modeEdition ?
                    "Le formulaire"
                :
                    <>
                        <Table className="fs--1 mt-3" size='sm' responsive>
                            <tbody>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Libellé/Indicatif</td>
                                    <td>{vehicule.libelleVehicule}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Marque/Modèle</td>
                                    <td>{vehicule.marqueModele}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Carburant</td>
                                    <td>{vehicule.libelleCarburant}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Immatriculation</td>
                                    <td>{vehicule.immatriculation}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Type</td>
                                    <td>{vehicule.libelleType}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Notifications</td>
                                    <td>{vehicule.notifications > 0 ? <FontAwesomeIcon icon='bell' /> : <FontAwesomeIcon icon='bell-slash'/>}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Lieu de parking</td>
                                    <td>{vehicule.libelleLieu}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Dernier relevé kilométrique</td>
                                    <td>{vehicule.relevesKM.length > 0 ? vehicule.relevesKM[0].releveKilometrique+' km ('+moment(vehicule.relevesKM[0].dateReleve).format('DD/MM/YYYY')+')' : null}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Nombre de places</td>
                                    <td>{vehicule.nbPlaces}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Poids</td>
                                    <td>{vehicule.poidsVehicule ? vehicule.poidsVehicule+' tonnes' : null}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Dimensions</td>
                                    <td>{vehicule.dimensions}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Responsable</td>
                                    <td>{vehicule.identifiant}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Date d'achat</td>
                                    <td>{vehicule.dateAchat ? moment(vehicule.dateAchat).format('DD/MM/YYYY') : null}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Numéro d'assurance</td>
                                    <td>{vehicule.assuranceNumero}</td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Equipements embarqués</td>
                                    <td>
                                        {vehicule.pneusAVhivers ?                   <SoftBadge className='me-1' bg='info'>Pneus hivers avant</SoftBadge> : null}
                                        {vehicule.pneusARhivers ?                   <SoftBadge className='me-1' bg='info'>Pneus hivers arriere</SoftBadge> : null}
                                        {vehicule.priseAlimentation220 ?            <SoftBadge className='me-1' bg='info'>Prise d'alimentation 220V</SoftBadge> : null}
                                        {vehicule.climatisation ?                   <SoftBadge className='me-1' bg='info'>Climatisation</SoftBadge> : null}
                                        {vehicule.signaletiqueOrange ?              <SoftBadge className='me-1' bg='info'>Feux oranges</SoftBadge> : null}
                                        {vehicule.signaletiqueBleue ?               <SoftBadge className='me-1' bg='info'>Feux bleus</SoftBadge> : null}
                                        {vehicule.signaletique2tons ?               <SoftBadge className='me-1' bg='info'>Sirène 2 tons 2 temps</SoftBadge> : null}
                                        {vehicule.signaletique3tons ?               <SoftBadge className='me-1' bg='info'>Sirène 2 tons 3 temps</SoftBadge> : null}
                                        {vehicule.pmv ?                             <SoftBadge className='me-1' bg='info'>PMV</SoftBadge> : null}
                                        {vehicule.fleche ?                          <SoftBadge className='me-1' bg='info'>Flèche/Triflash</SoftBadge> : null}
                                        {vehicule.nbCones && vehicule.nbCones > 0 ? <SoftBadge className='me-1' bg='info'>{vehicule.nbCones} cones</SoftBadge> : null}
                                    </td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Lots chargés</td>
                                    <td><VehiculeLotsCharges lots={vehicule.lots}/></td>
                                </tr>
                                <tr>
                                    <td className="bg-100" style={{ width: '30%' }}>Pièces jointes</td>
                                    <td><VehiculeAttached idVehicule={vehicule.idVehicule} libelleVehicule={vehicule.libelleVehicule} documents={vehicule.documents} setPageNeedsRefresh={setPageNeedsRefresh} /></td>
                                </tr>
                            </tbody>
                        </Table>
                        Checkliste des maintenances régulières sous forme de timeline
                    </>
                }
            </Card.Body>
        </Card>
    </>);
};

VehiculeProprietes.propTypes = {};

export default VehiculeProprietes;
