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
    const [lignes, setLignes] = useState([]);
    const [showAddButton, setShowAddButton] = useState(false);

    const initPage = async () => {
        try {
            let getData;
            let tempTable  = [];
            switch (filtre) {
                case 'all':
                    getData = await Axios.get('todolist/getAllTDL');
                    setColonnes([
                        {accessor: 'titre'                  , Header: 'Titre'},
                        {accessor: 'details'                , Header: 'Description'},
                        {accessor: 'dateCreation'           , Header: 'Crée le'},
                        {accessor: 'dateExecution'          , Header: 'A faire avant'},
                        {accessor: 'dateCloture'            , Header: 'Close le'},
                        {accessor: 'idTDLpriorite'          , Header: 'Priorite'},
                        {accessor: 'idExecutant'            , Header: 'Affectée à'},
                        {accessor: 'actions'                , Header: 'Actions'},
                    ]);
                    for(const item of getData.data)
                    {
                        tempTable.push({
                            titre: item.titre,
                            details: nl2br(item.details),
                            dateCreation: item.dateCreation != null ? moment(item.dateCreation).format('DD/MM/YYYY HH:mm') : null,
                            dateExecution: item.dateExecution != null ? moment(item.dateExecution).format('DD/MM/YYYY HH:mm') : null,
                            dateCloture: item.dateCloture != null ? moment(item.dateCloture).format('DD/MM/YYYY HH:mm') : null,
                            idTDLpriorite: item.idTDLpriorite > 0 ? <SoftBadge bg={item.couleurPriorite}>{item.libellePriorite}</SoftBadge> : null,
                            idExecutant: <>{item.idExecutant.map((personne, i)=>{return(
                                <SoftBadge bg='secondary' className='me-1'>{personne.prenomPersonne} {personne.nomPersonne}</SoftBadge>
                            )})}</>,
                            actions:
                                <ToDoListForm
                                    idTache={item.idTache}
                                    isOwnTDL={item.idExecutant.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne).length > 0 ? true : false}
                                    isCompleted={item.dateCloture != null ? true : false}
                                    showResolvedButton={true}
                                    showEditButton={true}
                                    showAffectationButton={true}
                                    showDeleteButton={true}
                                    setComponentsHaveToReload={setComponentsHaveToReload}
                                />,
                        })
                    }
                    setLignes(tempTable);
                    setShowAddButton(true);
                break;
                
                case 'unaffected':
                    getData = await Axios.get('todolist/getUnaffectedTDL');
                    setColonnes([
                        {accessor: 'titre'                  , Header: 'Titre'},
                        {accessor: 'dateExecution'          , Header: 'A faire avant'},
                        {accessor: 'idTDLpriorite'          , Header: 'Priorite'},
                        {accessor: 'actions'                , Header: 'Actions'},
                    ]);
                    for(const item of getData.data)
                    {
                        tempTable.push({
                            titre: item.titre,
                            dateExecution: item.dateExecution != null ? moment(item.dateExecution).format('DD/MM/YYYY HH:mm') : null,
                            idTDLpriorite: item.idTDLpriorite > 0 ? <SoftBadge bg={item.couleurPriorite}>{item.libellePriorite}</SoftBadge> : null,
                            actions:
                                <ToDoListForm
                                    idTache={item.idTache}
                                    isOwnTDL={false}
                                    isCompleted={item.dateCloture != null ? true : false}
                                    showEditButton={true}
                                    showAffectationButton={true}
                                    showDeleteButton={true}
                                    setComponentsHaveToReload={setComponentsHaveToReload}
                                />,
                        })
                    }
                    setLignes(tempTable);
                    setShowAddButton(true);
                break;
                
                case 'finished':
                    getData = await Axios.get('todolist/getClosedTDL');
                    setColonnes([
                        {accessor: 'titre'                  , Header: 'Titre'},
                        {accessor: 'dateCloture'            , Header: 'Close le'},
                        {accessor: 'idTDLpriorite'          , Header: 'Priorite'},
                        {accessor: 'actions'                , Header: 'Actions'},
                    ]);
                    for(const item of getData.data)
                    {
                        tempTable.push({
                            titre: item.titre,
                            dateCloture: item.dateCloture != null ? moment(item.dateCloture).format('DD/MM/YYYY HH:mm') : null,
                            idTDLpriorite: item.idTDLpriorite > 0 ? <SoftBadge bg={item.couleurPriorite}>{item.libellePriorite}</SoftBadge> : null,
                            actions:
                                <ToDoListForm
                                    idTache={item.idTache}
                                    isOwnTDL={item.idExecutant.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne).length > 0 ? true : false}
                                    isCompleted={item.dateCloture != null ? true : false}
                                    showResolvedButton={true}
                                    showEditButton={true}
                                    showAffectationButton={true}
                                    showDeleteButton={true}
                                    setComponentsHaveToReload={setComponentsHaveToReload}
                                />,
                        })
                    }
                    setLignes(tempTable);
                    setShowAddButton(false);
                break;

                case 'individual':
                    getData = await Axios.post('todolist/getTDLonePerson',{
                        idPersonne: idPersonne,
                    });
                    setColonnes([
                        {accessor: 'titre'                  , Header: 'Titre'},
                        {accessor: 'details'                , Header: 'Description'},
                        {accessor: 'dateExecution'          , Header: 'A faire avant'},
                        {accessor: 'idTDLpriorite'          , Header: 'Priorite'},
                        {accessor: 'actions'                , Header: 'Actions'},
                    ]);
                    for(const item of getData.data)
                    {
                        tempTable.push({
                            titre: item.titre,
                            details: nl2br(item.details),
                            dateExecution: item.dateExecution != null ? moment(item.dateExecution).format('DD/MM/YYYY HH:mm') : null,
                            idTDLpriorite: item.idTDLpriorite > 0 ? <SoftBadge bg={item.couleurPriorite}>{item.libellePriorite}</SoftBadge> : null,
                            actions:
                                <ToDoListForm
                                    idTache={item.idTache}
                                    isOwnTDL={item.idExecutant.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne).length > 0 ? true : false}
                                    isCompleted={item.dateCloture != null ? true : false}
                                    showResolvedButton={true}
                                    showEditButton={true}
                                    showAffectationButton={true}
                                    showDeleteButton={true}
                                    setComponentsHaveToReload={setComponentsHaveToReload}
                                />,
                        })
                    }
                    setLignes(tempTable);
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
            <FalconComponentCard.Header
                title={titreBox}
            />
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ?
                    <GPMtable
                        columns={colonnes}
                        data={lignes}
                        topButtonShow={showAddButton}
                        topButton={<ToDoListForm idTache={0} showAddButton={true} setComponentsHaveToReload={setComponentsHaveToReload} />}
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

ToDoListTable.propTypes = {};

export default ToDoListTable;
