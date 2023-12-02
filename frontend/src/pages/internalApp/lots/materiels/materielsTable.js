import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import moment from 'moment-timezone';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import MaterielsForm from './materielsForm';
import MaterielsDeleteModal from './materielsDeleteModal';
import TransfertsMaterielsOpe from '../transferts/transfertsMaterielsOpe';

const MaterielsTable = ({
    displayLibelleMateriel = true,
    displayLibelleEmplacement = true,
    displayLibelleSac = true,
    displayLibelleLot = true,
    displayQuantite = true,
    displayPeremption = true,
    displayLibelleMaterielsEtat = true,
    displayNotif = true,
    displayActions = true,
    filterIdEmplacement = null,
    hideAddButton = false,
}) => {
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [materiels, setMateriels] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.post('/materiels/getMateriels',{
                filterIdEmplacement: filterIdEmplacement
            });
            setMateriels(getData.data);  
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])
    useEffect(() => {
        setReadyToDisplay(false);
        initPage();
    }, [filterIdEmplacement])
    useEffect(() => {
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            setReadyToDisplay(false);
            initPage();
        }
    }, [pageNeedsRefresh])

    const colonnes = [
        {accessor: 'libelleMateriel'     , Header: 'Libellé'      , isHidden: !displayLibelleMateriel},
        {accessor: 'libelleEmplacement'  , Header: 'Emplacement'  , isHidden: !displayLibelleEmplacement},
        {accessor: 'libelleSac'          , Header: 'Sac'          , isHidden: !displayLibelleSac},
        {accessor: 'libelleLot'          , Header: 'Lot'          , isHidden: !displayLibelleLot},
        {accessor: 'quantite'            , Header: 'Quantité'     , isHidden: !displayQuantite},
        {accessor: 'peremption'          , Header: 'Péremption'   , isHidden: !displayPeremption},
        {accessor: 'libelleMaterielsEtat', Header: 'Etat'         , isHidden: !displayLibelleMaterielsEtat},
        {accessor: 'notif'               , Header: 'Notifications', isHidden: !displayNotif},
        {accessor: 'actions'             , Header: 'Actions'      , isHidden: !displayActions},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of materiels)
        {
            tempTable.push({
                libelleMateriel     : item.libelleMateriel,
                libelleEmplacement  : item.libelleEmplacement,
                libelleSac          : item.libelleSac,
                libelleLot          : item.libelleLot,
                quantite: 
                    item.quantite < item.quantiteAlerte ?
                        <SoftBadge bg='danger'>{item.quantite}</SoftBadge>
                    :
                        item.quantite == item.quantiteAlerte ?
                            <SoftBadge bg='warning'>{item.quantite}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{item.quantite}</SoftBadge>
                ,
                peremption:
                    item.peremption != null ?
                        item.peremption < new Date() ?
                            <SoftBadge bg='danger'>{moment(item.peremption).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            item.peremptionNotification < new Date() ?
                                <SoftBadge bg='warning'>{moment(item.peremption).format('DD/MM/YYYY')}</SoftBadge>
                            :
                                <SoftBadge bg='success'>{moment(item.peremption).format('DD/MM/YYYY')}</SoftBadge>
                    :
                        null
                ,
                libelleMaterielsEtat: item.libelleMaterielsEtat,
                notif               : item.idNotificationEnabled != 1 ? <FontAwesomeIcon icon='bell-slash' /> : <FontAwesomeIcon icon='bell' />,
                actions             : <>
                    {item.inventaireEnCours ?
                        <SoftBadge bg='danger'>INVENTAIRE EN COURS</SoftBadge>    
                    :
                        <>
                            {HabilitationService.habilitations['reserve_ReserveVersLot'] ? 
                                <TransfertsMaterielsOpe idElement={item.idElement} setPageNeedsRefresh={setPageNeedsRefresh} />
                            : null}
                            {HabilitationService.habilitations['materiel_modification'] ? 
                                <MaterielsForm idElement={item.idElement} element={item} setPageNeedsRefresh={setPageNeedsRefresh} />
                            : null}
                            {HabilitationService.habilitations['materiel_suppression'] ? 
                                <MaterielsDeleteModal idElement={item.idElement} setPageNeedsRefresh={setPageNeedsRefresh} />
                            : null}
                        </>
                    }
                </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [materiels])

    return (
    <>
        {readyToDisplay ?
            <GPMtable
                columns={colonnes}
                data={lignes}
                topButtonShow={true && !hideAddButton}
                topButton={
                    HabilitationService.habilitations['materiel_ajout'] ?
                        <MaterielsForm
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            idEmplacement={filterIdEmplacement}
                        />
                    : null
                }
            />
        : <LoaderInfiniteLoop />}
    </>);
};

MaterielsTable.propTypes = {};

export default MaterielsTable;
