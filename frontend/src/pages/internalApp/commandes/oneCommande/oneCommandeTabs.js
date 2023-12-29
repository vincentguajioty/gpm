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

import OneCommandeStep1InfosGenerales from './step1InfosGenerales';
import OneCommandeStep2Contenu from './step2Contenu';
import OneCommandeStep3PJ from './step3PJ';
import OneCommandeStep4Validation from './step4Validation';
import OneCommandeStep5Passage from './step5Passage';
import OneCommandeStep6Livraison from './step6Livraison';
import OneCommandeStep7Stock from './step7Stock';
import OneCommandeStep8Cloture from './step8Cloture';

const OneCommandeTabs = ({
    idCommande,
    commande,
    setPageNeedsRefresh,
}) => {
    const checkUserInRole = (acceptedRolesPerStage) => {
        // [
        //     {
        //         idEtat: NUMBER,
        //         acceptedRoles: ['valideurs', 'demandeurs', 'affectees', 'observateurs']
        //     }
        // ]
        let accepted = false;
        for(const stage of acceptedRolesPerStage)
        {
            if(commande.detailsCommande.idEtat == stage.idEtat)
            {
                for(const role of stage.acceptedRoles)
                {
                    for(const personne of commande[role])
                    {
                        if(personne.value == HabilitationService.habilitations.idPersonne){accepted = true}
                    }
                }
            }
        }

        return accepted;
    }

    const tabs = [
        {
            id: 1,
            title: "Informations Générales",
            icon: commande.detailsCommande.idEtat > 1 ? 'check' : 'forward',
            disabled: false,
            content: <OneCommandeStep1InfosGenerales
                idCommande = {idCommande}
                commande = {commande}
                forceReadOnly = {commande.detailsCommande.idEtat > 1 ? true : false}
                setPageNeedsRefresh = {setPageNeedsRefresh}
            />,
        },
        {
            id: 2,
            title: "Contenu",
            icon: commande.detailsCommande.idEtat > 1 ? 'check' : 'forward',
            disabled: false,
            content: <OneCommandeStep2Contenu
                idCommande = {idCommande}
                commande = {commande}
                forceReadOnly = {commande.detailsCommande.idEtat > 1 ? true : false}
                setPageNeedsRefresh = {setPageNeedsRefresh}
            />,
        },
        {
            id: 3,
            title: "Pièces jointes",
            icon: commande.detailsCommande.idEtat > 6 ? 'check' : 'forward',
            disabled: false,
            content: <OneCommandeStep3PJ
                idCommande = {idCommande}
                commande = {commande}
                forceReadOnly = {commande.detailsCommande.idEtat > 6 ? true : false}
                avoidDelete = {commande.detailsCommande.idEtat > 1 ? true : false}
                setPageNeedsRefresh = {setPageNeedsRefresh}
            />,
        },
        {
            id: 4,
            title: "Validation",
            icon: commande.detailsCommande.idEtat > 2 ? 'check' : 'forward',
            disabled: false,
            content: <OneCommandeStep4Validation
                idCommande = {idCommande}
                commande = {commande}
                forceReadOnly = {commande.detailsCommande.idEtat > 2 ? true : checkUserInRole([{idEtat:1, acceptedRoles:['affectees','valideurs']},{idEtat:2, acceptedRoles:['valideurs']}]) ? false : true}
                setPageNeedsRefresh = {setPageNeedsRefresh}
            />,
        },
        {
            id: 5,
            title: "Passage de la commande",
            icon: commande.detailsCommande.idEtat < 3 ? 'spinner' : commande.detailsCommande.idEtat > 3 ? 'check' : 'forward',
            disabled: commande.detailsCommande.idEtat < 3 ? true : false,
            content: <OneCommandeStep5Passage
                idCommande = {idCommande}
                commande = {commande}
                forceReadOnly = {commande.detailsCommande.idEtat > 3 ? true : checkUserInRole([{idEtat:3, acceptedRoles:['affectees']}]) ? false : true}
                setPageNeedsRefresh = {setPageNeedsRefresh}
            />,
        },
        {
            id: 6,
            title: "Livraison",
            icon: commande.detailsCommande.idEtat < 4 ? 'spinner' : commande.detailsCommande.idEtat == 6 ? 'exclamation-triangle' : commande.detailsCommande.idEtat > 4 ? 'check' : 'forward',
            disabled: commande.detailsCommande.idEtat < 4 ? true : false,
            content: <OneCommandeStep6Livraison
                idCommande = {idCommande}
                commande = {commande}
                forceReadOnly = {commande.detailsCommande.idEtat > 4 && commande.detailsCommande.idEtat != 6 ? true : checkUserInRole([{idEtat:4, acceptedRoles:['affectees']}, {idEtat:6, acceptedRoles:['affectees']}]) ? false : true}
                setPageNeedsRefresh = {setPageNeedsRefresh}
            />,
        },
        {
            id: 7,
            title: "Intégration au stock",
            icon: commande.detailsCommande.idEtat < 5 || commande.detailsCommande.idEtat == 6 ? 'spinner' : commande.detailsCommande.idEtat > 5 && commande.detailsCommande.idEtat != 6 ? 'check' : 'forward',
            disabled: commande.detailsCommande.idEtat < 5 || commande.detailsCommande.idEtat == 6 ? true : false,
            content: <OneCommandeStep7Stock
                idCommande = {idCommande}
                commande = {commande}
                forceReadOnly = {commande.detailsCommande.idEtat > 5 && commande.detailsCommande.idEtat != 6 ? true : checkUserInRole([{idEtat:5, acceptedRoles:['affectees']}]) ? false : true}
                setPageNeedsRefresh = {setPageNeedsRefresh}
            />,
        },
        {
            id: 8,
            title: "Cloture",
            icon: commande.detailsCommande.idEtat < 7 ? 'spinner' : 'check',
            disabled: commande.detailsCommande.idEtat < 7 ? true : false,
            content: <OneCommandeStep8Cloture
                commande = {commande}
            />,
        },
    ];

    const getDefaultTab = () => {
        switch (commande.detailsCommande.idEtat) {
            case 1:
                return 1;
            break;
            case 2:
                return 4;
            break;
            case 3:
                return 5;
            break;
            case 4:
                return 6;
            break;
            case 5:
                return 7;
            break;
            case 6:
                return 6;
            break;
            case 7:
                return 8;
            break;
            case 8:
                return 8;
            break;        
            default:
                return 1;
            break;
        }
    }

    return (<>
        <Tab.Container id="left-tabs-example" defaultActiveKey={getDefaultTab}>
            <Row>
                <Col md={3}>
                    <Nav variant="pills" className="flex-column">
                        {tabs.map((item, i)=>{return(
                            <Nav.Item>
                                <Nav.Link eventKey={item.id} disabled={item.disabled}>
                                    <FontAwesomeIcon icon={item.icon} className='me-2'/>
                                    {item.title}
                                </Nav.Link>
                            </Nav.Item>
                        )})}
                    </Nav>
                </Col>
                <Col md={9} className='border'>
                    <Tab.Content>
                        {tabs.filter(item => item.disabled == false).map((item, i)=>{return(
                            <Tab.Pane eventKey={item.id}>
                               {item.content}
                            </Tab.Pane>
                        )})}
                    </Tab.Content>
                </Col>
            </Row>
        </Tab.Container>
    </>);
};

OneCommandeTabs.propTypes = {};

export default OneCommandeTabs;
