import React, {useState, useEffect} from 'react';
import { Accordion, Alert, Button, Card } from 'react-bootstrap';
import moment from 'moment-timezone';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import { Axios } from 'helpers/axios';
import TenuesPublicService from 'services/tenuesPublicService';

import TenuesPretPublic from './demanderPret';
import TenuesEchangePublic from './demanderEchange';

const TenuesAfficherPublic = () => {
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

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

    useEffect(()=>{
        initPage();
    },[]);

    useEffect(()=>{
    if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    },[pageNeedsRefresh]);

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
                                        {externe.tenuesWarning.length > 0 ? <>
                                            <Alert variant='warning'>
                                                <Alert.Heading>Eléments à restituer</Alert.Heading>
                                                <ul>
                                                    {externe.tenuesWarning.map((tenue, i)=>{return(
                                                        <li>
                                                            {tenue.libelleMateriel}
                                                            {tenue.taille ? ' ('+tenue.taille+')' : null}
                                                            {tenue.dateAffectation ? ', Affectation: '+moment(tenue.dateAffectation).format('DD/MM/YYYY') : null}
                                                            {tenue.dateRetour ? ', Retour prévu: '+moment(tenue.dateRetour).format('DD/MM/YYYY') : null}
                                                        </li>
                                                    )})}
                                                </ul>
                                            </Alert>
                                        </> : null}
                                        
                                        {externe.tenues.length > 0 ? <>
                                            <TenuesPretPublic
                                                externe={externe}
                                                setPageNeedsRefresh={setPageNeedsRefresh}
                                            />
                                            <TenuesEchangePublic
                                                externe={externe}
                                                setPageNeedsRefresh={setPageNeedsRefresh}
                                            />
                                            {externe.tenues.filter(ten => ten.dateRetour != null && ten.demandeBenevolePret == true).length > 0 ?
                                                <p>
                                                    <u>Mes demandes de prêts en cours d'étude:</u>
                                                    <ul>
                                                        {externe.tenues.filter(ten => ten.dateRetour != null && ten.demandeBenevolePret == true).map((tenue, i)=>{return(
                                                            <li>
                                                                {tenue.libelleMateriel}
                                                                {tenue.taille ? ' ('+tenue.taille+')' : null}
                                                                {tenue.dateAffectation ? ', Demandé du: '+moment(tenue.dateAffectation).format('DD/MM/YYYY') : null}
                                                                {tenue.dateRetour ? ' Au: '+moment(tenue.dateRetour).format('DD/MM/YYYY') : null}
                                                            </li>
                                                        )})}
                                                    </ul>
                                                </p>
                                            :null}

                                            {externe.tenues.filter(ten => ten.dateRetour != null && ten.demandeBenevolePret == false).length > 0 ?
                                                <p>
                                                    <u>Mes prêts de tenue en cours:</u>
                                                    <ul>
                                                        {externe.tenues.filter(ten => ten.dateRetour != null && ten.demandeBenevolePret == false).map((tenue, i)=>{return(
                                                            <li>
                                                                {tenue.libelleMateriel}
                                                                {tenue.taille ? ' ('+tenue.taille+')' : null}
                                                                {tenue.dateRetour ? ', Retour prévu: '+moment(tenue.dateRetour).format('DD/MM/YYYY') : null}
                                                            </li>
                                                        )})}
                                                    </ul>
                                                </p>
                                            :null}
                                            
                                            <p>
                                                <u>Mes affectations de tenue:</u>
                                                <ul>
                                                    {externe.tenues.filter(ten => ten.dateRetour == null).map((tenue, i)=>{return(
                                                        <li>
                                                            {tenue.libelleMateriel}
                                                            {tenue.taille ? ' ('+tenue.taille+')' : null}
                                                            {tenue.dateRetour ? ', Retour prévu: '+moment(tenue.dateRetour).format('DD/MM/YYYY') : null}
                                                            {tenue.demandeBenevoleRemplacement ? <SoftBadge bg='warning' className='ms-1' ><FontAwesomeIcon icon={'exchange-alt'}/> Remplacement demandé</SoftBadge> : null}
                                                        </li>
                                                    )})}
                                                </ul>
                                            </p>
                                        </>: null}
                                        
                                        {externe.tenues.length > 0 && externe.cautions.length > 0 ?
                                            <hr/>
                                        : null}

                                        {externe.cautions.length > 0 ? <>
                                            <u>Mes cautions:</u>
                                            <ul>
                                                {externe.cautions.map((caution, i)=>{return(
                                                    <li>
                                                        {caution.detailsMoyenPaiement} {caution.montantCaution} €
                                                        {caution.dateEmissionCaution ? ', Versée le: '+moment(caution.dateEmissionCaution).format('DD/MM/YYYY') : null}
                                                        {caution.dateExpirationCaution ? ', Fin de validité: '+moment(caution.dateExpirationCaution).format('DD/MM/YYYY') : null}
                                                    </li>
                                                )})}
                                            </ul>
                                        </>: null}
                                    </Accordion.Body>
                                </Accordion.Item>
                            </>);
                        })}
                    </Accordion>
                </Card.Body>
            </Card>
            <div className="d-grid gap-2 mt-3">
                <Button variant='info' className='me-2 mb-1' onClick={quitterPage} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Quitter'}</Button>
            </div>
        </>);
    }else{
        return <LoaderInfiniteLoop/>;
    }
    
};

TenuesAfficherPublic.propTypes = {};

export default TenuesAfficherPublic;
