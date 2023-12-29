import React, {useState, useEffect} from 'react';
import { Link, useNavigate, } from 'react-router-dom';
import { Card, Offcanvas, Button, Form, Tab, Nav, Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const Preparation = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    let ok = commande.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 2)[0].passagePossible && !forceReadOnly;

    const demandeValidation = async () => {
        try {
            await Axios.post('/commandes/demandeValidation',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    return(
        <center>
            <p><small>
                Critères à respecter pour soumettre à validation:<br/>
                {commande.verificationContraintes.contraintes.map((ctr, i) => {return(
                    <>
                        <FontAwesomeIcon icon={ctr.contrainteRespectee == true ? 'check' : 'ban'} color={ctr.contrainteRespectee == true ? 'green' : 'red'} className='me-2' />
                        {ctr.libelleContrainte}
                        <br/>
                    </>
                )})}
            </small></p>

            <IconButton
                className='mt-2'
                disabled={!ok}
                icon='forward'
                variant={ok ? 'success' : 'outline-success'}
                onClick={demandeValidation}
            >
                Demander la validation
                
            </IconButton>
        </center>
    );
};

const Arbitrage = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    return('Je dis go ou pas');
};

const OneCommandeStep4Validation = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
}) => {
    if(commande.detailsCommande.idEtat == 1)
    {
        return(<Preparation
            idCommande={idCommande}
            commande={commande}
            forceReadOnly={forceReadOnly}
            setPageNeedsRefresh={setPageNeedsRefresh}
        />);
    }else{
        return(<Arbitrage
            idCommande={idCommande}
            commande={commande}
            forceReadOnly={forceReadOnly}
            setPageNeedsRefresh={setPageNeedsRefresh}
        />);
    }
};

OneCommandeStep4Validation.propTypes = {};

export default OneCommandeStep4Validation;
