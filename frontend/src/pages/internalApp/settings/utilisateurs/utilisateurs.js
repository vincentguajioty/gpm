import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { userAddForm } from 'helpers/yupValidationSchema';

import UtilisateursFilter from './utilisateursFilter';

const Utilisateurs = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [personnes, setPersonnes] = useState([]);
    const [personnesFiltered, setPersonnesFiltered] = useState([]);

    const navigate = useNavigate();

    const initPage = async () => {
        try {
            const getData = await Axios.get('/settingsUtilisateurs/getAllUsers');
            setPersonnes(getData.data);
            setPersonnesFiltered(getData.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {accessor: 'identifiant'   , Header: 'Identifiant de connexion'},
        {accessor: 'nomPersonne'   , Header: 'Nom'},
        {accessor: 'prenomPersonne', Header: 'Prénom'},
        {accessor: 'mailPersonne'  , Header: 'Mail'},
        {accessor: 'fonction'      , Header: 'Fonction'},
        {accessor: 'profils'       , Header: 'Profils'},
        {accessor: 'actions'       , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of personnesFiltered)
        {
            tempTable.push({
                identifiant: <Link to={'/teamUtilisateurs/'+item.idPersonne}>{item.identifiant}</Link>,
                nomPersonne: item.nomPersonne,
                prenomPersonne: item.prenomPersonne,
                mailPersonne: item.mailPersonne,
                fonction: item.fonction,
                profils: 
                    <>
                        {item.profils.map((profil, j) => {return(
                            <SoftBadge bg='info' className='me-1'>{profil.libelleProfil}</SoftBadge>
                        )})}
                    </>,
                actions:
                    <>
                        <IconButton
                            icon='eye'
                            size = 'sm'
                            variant="outline-primary"
                            className="me-1"
                            onClick={()=>{navigate('/teamUtilisateurs/'+item.idPersonne)}}
                        />
                    </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [personnesFiltered])

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(userAddForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = () => {
        setShowOffCanevas(true);
    }
    const ajouterEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/settingsUtilisateurs/addUser',{
                identifiant: data.identifiant,
            });

            let idTarget = response.data.idPersonne;
            navigate('/teamUtilisateurs/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }


    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Utilisateurs de l'application"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouvelle Personne</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterEntree)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Identifiant de connexion</Form.Label>
                            <Form.Control size="sm" type="text" name='identifiant' id='identifiant' {...register('identifiant')}/>
                            <small className="text-danger">{errors.identifiant?.message}</small>
                        </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ? <>
                    <UtilisateursFilter
                        personnes={personnes}
                        setPersonnesFiltered={setPersonnesFiltered}
                    />
                    
                    <GPMtable
                        columns={colonnes}
                        data={lignes}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['annuaire_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={handleShowOffCanevas}
                                >Nouvel utilisateur</IconButton>
                            : null
                        }
                    />
                </> : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Utilisateurs.propTypes = {};

export default Utilisateurs;
