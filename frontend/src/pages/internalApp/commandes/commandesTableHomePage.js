import React, {useEffect, useState,} from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form } from 'react-bootstrap';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeAddForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const CommandesTableHomePage = () => {
    const [commandes, setCommandes] = useState([]);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const colonnes = [
        {
            accessor: 'nomCommande',
            Header: 'Nom de la commande',
            Cell: ({ value, row }) => {
				return(<Link to={'/commandes/'+row.original.idCommande}>{row.original.nomCommande}</Link>);
			},
        },
        {
            accessor: 'montantTotal',
            Header: 'Montant',
            Cell: ({ value, row }) => {
				return(value+' €');
			},
        },
        {
            accessor: 'libelleCentreDecout',
            Header: 'Centre de couts',
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
                let rejectPossible = row.original.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 1)[0].passagePossible;
                let approuvePossible = row.original.verificationContraintes.possiblesMovesTo.filter(move => move.idEtat == 3)[0].passagePossible;

                return(<>
                    <IconButton
                        disabled={!approuvePossible}
                        icon='check'
                        variant={approuvePossible ? 'success' : 'outline-success'}
                        onClick={()=>{accepter(row.original.idCommande)}}
                        size='sm'
                        className='me-1 mb-1'
                    >
                        Accepter
                    </IconButton>
                    <br/>
                    <IconButton
                        disabled={!rejectPossible}
                        icon='ban'
                        variant={rejectPossible ? 'warning' : 'outline-warning'}
                        onClick={()=>{rejeter(row.original.idCommande)}}
                        size='sm'
                        className='me-1 mb-1'
                    >
                        Rejeter
                    </IconButton>
                </>);
			},
        },
    ];

    const initPage = async () => {
        try {
            let getData = await Axios.get('/commandes/getCommandesToApprouveOnePerson');
            setCommandes(getData.data);
        } catch (error) {
            console.log(error)
        }
    }

    const rejeter = async (idCommande) => {
        try {
            await Axios.post('/commandes/rejeterCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    const accepter = async (idCommande) => {
        try {
            await Axios.post('/commandes/approuverCommande',{
                idCommande: idCommande,
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    useEffect(()=>{
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
        
    },[pageNeedsRefresh])

    return (<>
        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                <GPMtable
                    columns={colonnes}
                    data={commandes}
                    topButtonShow={true}
                    topButton={<h5>Commandes à valider</h5>}
                />
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

CommandesTableHomePage.propTypes = {};

export default CommandesTableHomePage;
