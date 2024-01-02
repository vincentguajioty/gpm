import React, {useState, useEffect} from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Card, Offcanvas, Button, Form } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeAddForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const CommandesTable = ({
    commandesArrayFiltered
}) => {
    const colonnes = [
        {
            accessor: 'dateCreation',
            Header: 'Date création',
            Cell: ({ value, row }) => {
				return(moment(value).format('DD/MM/YYYY HH:mm'));
			},
        },
        {
            accessor: 'nomCommande',
            Header: 'Nom de la commande',
            Cell: ({ value, row }) => {
				return(<Link to={'/commandes/'+row.original.idCommande}>{row.original.nomCommande}</Link>);
			},
        },
        {
            accessor: 'nomFournisseur',
            Header: 'Fournisseur',
        },
        {
            accessor: 'montantTotal',
            Header: 'Montant',
            Cell: ({ value, row }) => {
				return(value+' €');
			},
        },
        {
            accessor: 'libelleEtat',
            Header: 'Etat',
        },
        {
            accessor: 'libelleCentreDecout',
            Header: 'Centre de couts',
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
                return(
                    <IconButton
                        icon='eye'
                        size = 'sm'
                        variant="outline-primary"
                        className="me-1"
                        onClick={()=>{navigate('/commandes/'+row.original.idCommande)}}
                    />
                );
			},
        },
    ];

    // Add offCanevas
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeAddForm),
    });
    const handleShowOffCanevas = () => {
        setShowOffCanevas(true);
    }
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
    }
    
    const navigate = useNavigate();
    const ajouterCommande = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/commandes/addCommande',{
                nomCommande: data.nomCommande,
            });

            let idTarget = response.data.idCommande;
            navigate('/commandes/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouvelle demande d'achat</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterCommande)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Titre de la future commande</Form.Label>
                        <Form.Control size="sm" type="text" name='nomCommande' id='nomCommande' {...register('nomCommande')}/>
                        <small className="text-danger">{errors.nomCommande?.message}</small>
                    </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <GPMtable
            columns={colonnes}
            data={commandesArrayFiltered}
            topButtonShow={true}
            topButton={HabilitationService.habilitations.commande_ajout ?
                <IconButton
                    icon='plus'
                    size = 'sm'
                    variant="outline-success"
                    onClick={handleShowOffCanevas}
                >Nouvelle demande d'achat</IconButton>
            : null}
        />
    </>);
};

CommandesTable.propTypes = {};

export default CommandesTable;
