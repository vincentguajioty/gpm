import React, {useState, useEffect} from 'react';
import { Accordion, Button, Card } from 'react-bootstrap';
import moment from 'moment-timezone';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import { Axios } from 'helpers/axios';
import TenuesPublicService from 'services/tenuesPublicService';

const TenuesAfficherPublic = () => {
    const[pageReady, setPageReady] = useState(false);
    const[externesAvecDetails, setExternesAvecDetails] = useState([]);

    const initPage = async () => {
        try {
            let getFromDb = await Axios.post('/tenuesPublic/getTenuesDetailsPublic',{
                publicToken: TenuesPublicService.tenuesPublicToken,
            });

            setExternesAvecDetails(getFromDb.data);
            setPageReady(true);
        } catch (error) {
            console.log(error)
        }
    }

    const colonnesForGPMTableTenues = [
        {
            accessor: 'libelleMateriel',
            Header: 'Element',
        },
        {
            accessor: 'taille',
            Header: 'Taille',
        },
        {
            accessor: 'dateAffectation',
            Header: 'Affecté le',
            Cell: ({ value, row }) => {
                return(
                    value != null ? moment(value).format('DD/MM/YYYY') : null
                );
            },
        },
        {
            accessor: 'dateRetour',
            Header: 'Retour prévu le',
            Cell: ({ value, row }) => {
                return(<>
                    {value != null ? 
                        new Date(value) < new Date() ?
                            <SoftBadge bg='danger'>{moment(value).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{moment(value).format('DD/MM/YYYY')}</SoftBadge>
                    : null}
                    {row.original.notifPersonne == true ? <SoftBadge bg='info' className='ms-1'><FontAwesomeIcon icon='bell'/></SoftBadge> : null}
                </>);
            },
        },
    ];

    const colonnesForGPMTableCautions = [
        {
            accessor: 'dateEmissionCaution',
            Header: 'Emise le',
            Cell: ({ value, row }) => {
                return(
                    value != null ? moment(value).format('DD/MM/YYYY') : null
                );
            },
        },
        {
            accessor: 'dateExpirationCaution',
            Header: 'Expire le',
            Cell: ({ value, row }) => {
                return(
                    value != null ? 
                        new Date(value) < new Date() ?
                            <SoftBadge bg='danger'>{moment(value).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{moment(value).format('DD/MM/YYYY')}</SoftBadge>
                    : null
                );
            },
        },
        {
            accessor: 'detailsMoyenPaiement',
            Header: 'Détails',
        },
        {
            accessor: 'montantCaution',
            Header: 'Montant',
            Cell: ({ value, row }) => {
                return(
                    value + " €"
                );
            },
        },
    ];

    useEffect(()=>{
        initPage();
    },[]);

    const [isLoading, setIsLoading] = useState(false);
    const quitterPage = async () => {
        try {
            setIsLoading(true);

            let getFromDb = await Axios.post('/tenuesPublic/seDeconnecterTenuesPublic',{
                publicToken: TenuesPublicService.tenuesPublicToken,
            }); 

            TenuesPublicService.disconnect();
            location.reload();
        } catch (error) {
            console.log(error)
        }
    }

    if(pageReady)
    {
        return (<>
            <Card className="mb-3">
                <Card.Body>
                    <Accordion>
                        {externesAvecDetails.map((externe, i)=>{
                            return(<>
                                <Accordion.Item eventKey={externe.idExterne} flush="true">
                                    <Accordion.Header>
                                        {externe.nomPrenomExterne}
                                    </Accordion.Header>
                                    <Accordion.Body>
                                        <GPMtable
                                            columns={colonnesForGPMTableTenues}
                                            data={externe.tenues}
                                            topButton='Mes affectations de tenue'
                                            topButtonShow={true}
                                        />
                                        <br/>
                                        <GPMtable
                                            columns={colonnesForGPMTableCautions}
                                            data={externe.cautions}
                                            topButton='Mes cautions'
                                            topButtonShow={true}
                                        />
                                    </Accordion.Body>
                                </Accordion.Item>
                            </>);
                        })}
                    </Accordion>
                </Card.Body>
            </Card>
            <div className="d-grid gap-2 mt-3">
                <Button variant='primary' className='me-2 mb-1' onClick={quitterPage} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
            </div>
        </>);
    }else{
        return <LoaderInfiniteLoop/>;
    }
    
};

TenuesAfficherPublic.propTypes = {};

export default TenuesAfficherPublic;
