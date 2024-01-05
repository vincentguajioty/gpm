import React, { useState, useEffect } from 'react';
import { Card, Form } from 'react-bootstrap';
import PropTypes from 'prop-types';
import FalconLink from 'components/common/FalconLink';
import FalconCardHeader from 'components/common/FalconCardHeader';

import OneCentreCardsGraph from './oneCentreCardsGraph';
import OneCentreCardsDetails from './oneCentreCardsDetails';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

const OneCentreCards = ({
    idCentreDeCout,
    centre,
    setPageNeedsRefresh,
}) => {
    const listData = {
        digitalMarketing: [
          {
            id: 0,
            field: 'Generate Backlinks',
            value: '$91.6k',
            colorOpacity: 100,
            color: 'info'
          },
          {
            id: 1,
            field: 'Email Marketing',
            value: '$183k',
            colorOpacity: 75,
            color: 'info'
          },
        ],
        offlineMarketing: [
          {
            id: 0,
            field: 'Event Sponsorship',
            value: '$91.6k',
            colorOpacity: 75,
            color: 'primary'
          },
          {
            id: 1,
            field: 'Outrich Event',
            value: '$183k',
            colorOpacity: 50,
            color: 'primary'
          },
          {
            id: 2,
            field: 'Ad Campaign',
            value: '$138k',
            colorOpacity: 25,
            color: 'primary'
          }
        ]
    };

    const [data, setData] = useState();
    const [budgetTotalPositif, setBudgetTotalPositif] = useState(0);
    const [readyToDisplay, setReadyToDisplay] = useState(false);

    const initData = () => {
        try {
            let budgetPositifEntrant = 0;
            let commandesIntegrees = 0;
            let operationsHorsCommandes = 0;
            for(const operation of centre.operations)
            {
                budgetPositifEntrant += operation.montantEntrant;

                if(operation.idCommande != null)
                {
                    commandesIntegrees = commandesIntegrees + operation.montantSortant;
                }else{
                    operationsHorsCommandes = operationsHorsCommandes + operation.montantSortant;
                }
            }

            let commandesValideesNonIntegrees = 0;
            for(const cmd of centre.commandesValideesNonIntegrees)
            {
                commandesValideesNonIntegrees += cmd.montantTotal;
            }

            let commandesNonValidees = 0;
            for(const cmd of centre.commandesNonValidees)
            {
                commandesNonValidees += cmd.montantTotal;
            }

            let budgetRestantLibre = Math.round((budgetPositifEntrant - commandesIntegrees - operationsHorsCommandes - commandesValideesNonIntegrees - commandesNonValidees)*100)/100;

            let finalArray = [
                {
                    sectionID: 1,
                    sectionName: 'Disponible',
                    montant: budgetRestantLibre+commandesNonValidees,
                    colorOpacity: 100,
                    color: 'success',
                    sousSections: [
                        {
                            sousSectionID: 1,
                            sousSectionName: 'Non-engagé',
                            montant: budgetRestantLibre,
                            colorOpacity: 50,
                            color: 'success',
                        },
                        {
                            sousSectionID: 2,
                            sousSectionName: 'Commandes en approche',
                            montant: commandesNonValidees,
                            colorOpacity: 25,
                            color: 'success',
                        },
                    ],
                },
                {
                    sectionID: 2,
                    sectionName: 'Dépensé',
                    montant: commandesIntegrees+commandesValideesNonIntegrees+operationsHorsCommandes,
                    colorOpacity: 100,
                    color: 'warning',
                    sousSections: [
                        {
                            sousSectionID: 1,
                            sousSectionName: 'Commandes intégrées',
                            montant: commandesIntegrees,
                            colorOpacity: 75,
                            color: 'warning',
                        },
                        {
                            sousSectionID: 2,
                            sousSectionName: 'Commandes validées non-intégrées',
                            montant: commandesValideesNonIntegrees,
                            colorOpacity: 50,
                            color: 'warning',
                        },
                        {
                            sousSectionID: 3,
                            sousSectionName: 'Hors Commandes',
                            montant: operationsHorsCommandes,
                            colorOpacity: 25,
                            color: 'warning',
                        },
                    ],
                },
            ];
            
            setData(finalArray);
            setBudgetTotalPositif(budgetPositifEntrant);

            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initData();
    },[centre])

    return (
        <Card>
            <Card.Body className="d-flex flex-column justify-content-between">
                {readyToDisplay ? <>
                    <OneCentreCardsGraph data={data} budgetTotalPositif={budgetTotalPositif} />
                    <OneCentreCardsDetails data={data} />
                </>:<LoaderInfiniteLoop/>}
            </Card.Body>
        </Card>
    );
};

OneCentreCards.propTypes = {};

export default OneCentreCards;