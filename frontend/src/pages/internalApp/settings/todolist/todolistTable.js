import React, { useState, useEffect } from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import nl2br from 'react-nl2br';

import HabilitationService from 'services/habilitationsService';
import ToDoListForm from './todoListForm';

import { Axios } from 'helpers/axios';

const ToDoListTable = ({
    filtre = 'all', //all unaffected finished individual
    idPersonne,
    titreBox = 'Taches',
    componentsHaveToReload,
    setComponentsHaveToReload,
}) => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [colonnes, setColonnes] = useState([]);
    const [taches, setTaches] = useState([]);
    const [showAddButton, setShowAddButton] = useState(false);

    const initPage = async () => {
        try {
            let getData;
            let tempTable  = [];
            switch (filtre) {
                case 'all':
                    getData = await Axios.get('todolist/getAllTDL');
                    setTaches(getData.data);
                    setColonnes([
                        {
                            accessor: 'titre',
                            Header: 'Titre',
                        },
                        {
                            accessor: 'details',
                            Header: 'Description',
                            Cell: ({ value, row }) => {
                                return(nl2br(value));
                            },
                        },
                        {
                            accessor: 'dateCreation',
                            Header: 'Crée le',
                            Cell: ({ value, row }) => {
                                return(value != null ? moment(value).format('DD/MM/YYYY HH:mm') : null);
                            },
                        },
                        {
                            accessor: 'dateExecution',
                            Header: 'A faire avant',
                            Cell: ({ value, row }) => {
                                return(value != null ? moment(value).format('DD/MM/YYYY HH:mm') : null);
                            },
                        },
                        {
                            accessor: 'dateCloture',
                            Header: 'Close le',
                            Cell: ({ value, row }) => {
                                return(value != null ? moment(value).format('DD/MM/YYYY HH:mm') : null);
                            },
                        },
                        {
                            accessor: 'libellePriorite',
                            Header: 'Priorite',
                            Cell: ({ value, row }) => {
                                return(row.original.idTDLpriorite > 0 ? <SoftBadge bg={row.original.couleurPriorite}>{row.original.libellePriorite}</SoftBadge> : null);
                            },
                        },
                        {
                            accessor: 'idExecutant',
                            Header: 'Affectée à',
                            Cell: ({ value, row }) => {
                                return(
                                    <>{value.map((personne, i)=>{return(
                                        <SoftBadge bg='secondary' className='me-1'>{personne.prenomPersonne} {personne.nomPersonne}</SoftBadge>
                                    )})}</>
                                );
                            },
                        },
                        {
                            accessor: 'actions',
                            Header: 'Actions',
                            Cell: ({ value, row }) => {
                                return(
                                    <ToDoListForm
                                        idTache={row.original.idTache}
                                        isOwnTDL={row.original.idExecutant.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne).length > 0 ? true : false}
                                        isCompleted={row.original.dateCloture != null ? true : false}
                                        showResolvedButton={true}
                                        showEditButton={true}
                                        showAffectationButton={true}
                                        showDeleteButton={true}
                                        setComponentsHaveToReload={setComponentsHaveToReload}
                                    />
                                );
                            },
                        },
                    ]);
                    setShowAddButton(true);
                break;
                
                case 'unaffected':
                    getData = await Axios.get('todolist/getUnaffectedTDL');
                    setTaches(getData.data);
                    setColonnes([
                        {
                            accessor: 'titre',
                            Header: 'Titre',
                        },
                        {
                            accessor: 'dateExecution',
                            Header: 'A faire avant',
                            Cell: ({ value, row }) => {
                                return(value != null ? moment(value).format('DD/MM/YYYY HH:mm') : null);
                            },
                        },
                        {
                            accessor: 'libellePriorite',
                            Header: 'Priorite',
                            Cell: ({ value, row }) => {
                                return(row.original.idTDLpriorite > 0 ? <SoftBadge bg={row.original.couleurPriorite}>{row.original.libellePriorite}</SoftBadge> : null);
                            },
                        },
                        {
                            accessor: 'actions',
                            Header: 'Actions',
                            Cell: ({ value, row }) => {
                                return(
                                    <ToDoListForm
                                        idTache={row.original.idTache}
                                        isOwnTDL={row.original.idExecutant.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne).length > 0 ? true : false}
                                        isCompleted={row.original.dateCloture != null ? true : false}
                                        showResolvedButton={true}
                                        showEditButton={true}
                                        showAffectationButton={true}
                                        showDeleteButton={true}
                                        setComponentsHaveToReload={setComponentsHaveToReload}
                                    />
                                );
                            },
                        },
                    ]);
                    setShowAddButton(true);
                break;
                
                case 'finished':
                    getData = await Axios.get('todolist/getClosedTDL');
                    setTaches(getData.data);
                    setColonnes([
                        {
                            accessor: 'titre',
                            Header: 'Titre',
                        },
                        {
                            accessor: 'dateCloture',
                            Header: 'Close le',
                            Cell: ({ value, row }) => {
                                return(value != null ? moment(value).format('DD/MM/YYYY HH:mm') : null);
                            },
                        },
                        {
                            accessor: 'libellePriorite',
                            Header: 'Priorite',
                            Cell: ({ value, row }) => {
                                return(row.original.idTDLpriorite > 0 ? <SoftBadge bg={row.original.couleurPriorite}>{row.original.libellePriorite}</SoftBadge> : null);
                            },
                        },
                        {
                            accessor: 'actions',
                            Header: 'Actions',
                            Cell: ({ value, row }) => {
                                return(
                                    <ToDoListForm
                                        idTache={row.original.idTache}
                                        isOwnTDL={row.original.idExecutant.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne).length > 0 ? true : false}
                                        isCompleted={row.original.dateCloture != null ? true : false}
                                        showResolvedButton={true}
                                        showEditButton={true}
                                        showAffectationButton={true}
                                        showDeleteButton={true}
                                        setComponentsHaveToReload={setComponentsHaveToReload}
                                    />
                                );
                            },
                        },
                    ]);
                    setShowAddButton(false);
                break;

                case 'individual':
                    getData = await Axios.post('todolist/getTDLonePerson',{
                        idPersonne: idPersonne,
                    });
                    setTaches(getData.data);
                    setColonnes([
                        {
                            accessor: 'titre',
                            Header: 'Titre',
                        },
                        {
                            accessor: 'details',
                            Header: 'Description',
                            Cell: ({ value, row }) => {
                                return(nl2br(value));
                            },
                        },
                        {
                            accessor: 'dateExecution',
                            Header: 'A faire avant',
                            Cell: ({ value, row }) => {
                                return(value != null ? moment(value).format('DD/MM/YYYY HH:mm') : null);
                            },
                        },
                        {
                            accessor: 'libellePriorite',
                            Header: 'Priorite',
                            Cell: ({ value, row }) => {
                                return(row.original.idTDLpriorite > 0 ? <SoftBadge bg={row.original.couleurPriorite}>{row.original.libellePriorite}</SoftBadge> : null);
                            },
                        },
                        {
                            accessor: 'actions',
                            Header: 'Actions',
                            Cell: ({ value, row }) => {
                                return(
                                    <ToDoListForm
                                        idTache={row.original.idTache}
                                        isOwnTDL={row.original.idExecutant.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne).length > 0 ? true : false}
                                        isCompleted={row.original.dateCloture != null ? true : false}
                                        showResolvedButton={true}
                                        showEditButton={true}
                                        showAffectationButton={true}
                                        showDeleteButton={true}
                                        setComponentsHaveToReload={setComponentsHaveToReload}
                                    />
                                );
                            },
                        },
                    ]);
                    setShowAddButton(false);
                break;
            
                default:
                    console.log('Erreur de chargement du composant')
                    return;
                break;
            }

            setReadyToDisplay(true);

        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    useEffect(()=>{
        if(componentsHaveToReload)
        {initPage();}
    },[componentsHaveToReload])

    return (<>
        <FalconComponentCard noGuttersBottom className="mb-3">
            {showAddButton ?
                <FalconComponentCard.Header
                    title={titreBox}
                />
            : null}
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ?
                    <GPMtable
                        columns={colonnes}
                        data={taches}
                        topButtonShow={true}
                        topButton={showAddButton ? <ToDoListForm idTache={0} showAddButton={true} setComponentsHaveToReload={setComponentsHaveToReload} /> : <h5>{titreBox}</h5>}
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

ToDoListTable.propTypes = {};

export default ToDoListTable;
