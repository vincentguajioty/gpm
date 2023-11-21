import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import ReservesMaterielsForm from './materielsForm';
import ReservesMaterielsDeleteModal from './materielsDeleteModal';

const ReservesMaterielsTable = ({
    displayLibelleMateriel = true,
    displayLibelleConteneur = true,
    displayQuantiteReserve = true,
    displayPeremptionReserve = true,
    displayActions = true,
    filterIdConteneur = null,
}) => {
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [materiels, setMateriels] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/reserves/getReservesMateriels',{
                filterIdConteneur: filterIdConteneur
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
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    }, [pageNeedsRefresh])

    const colonnes = [
        {accessor: 'libelleMateriel'     , Header: 'Libellé'      , isHidden: !displayLibelleMateriel},
        {accessor: 'libelleConteneur'    , Header: 'Conteneur'    , isHidden: !displayLibelleConteneur},
        {accessor: 'quantiteReserve'     , Header: 'Quantité'     , isHidden: !displayQuantiteReserve},
        {accessor: 'peremptionReserve'   , Header: 'Péremption'   , isHidden: !displayPeremptionReserve},
        {accessor: 'actions'             , Header: 'Actions'      , isHidden: !displayActions},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of materiels)
        {
            tempTable.push({
                libelleMateriel   : item.libelleMateriel,
                libelleConteneur  : item.libelleConteneur,
                quantiteReserve: 
                    item.quantiteReserve < item.quantiteAlerteReserve ?
                        <SoftBadge bg='danger'>{item.quantiteReserve}</SoftBadge>
                    :
                        item.quantiteReserve == item.quantiteAlerteReserve ?
                            <SoftBadge bg='warning'>{item.quantiteReserve}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{item.quantiteReserve}</SoftBadge>
                ,
                peremptionReserve:
                    item.peremptionReserve != null ?
                        item.peremptionReserve < new Date() ?
                            <SoftBadge bg='danger'>{moment(item.peremptionReserve).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            item.peremptionNotificationReserve < new Date() ?
                                <SoftBadge bg='warning'>{moment(item.peremptionReserve).format('DD/MM/YYYY')}</SoftBadge>
                            :
                                <SoftBadge bg='success'>{moment(item.peremptionReserve).format('DD/MM/YYYY')}</SoftBadge>
                    :
                        null
                ,
                actions             : <>
                    {item.inventaireEnCours ?
                        <SoftBadge bg='danger'>INVENTAIRE EN COURS</SoftBadge>    
                    :
                        <>
                            {HabilitationService.habilitations['materiel_modification'] ? 
                                <ReservesMaterielsForm idReserveElement={item.idReserveElement} element={item} setPageNeedsRefresh={setPageNeedsRefresh} />
                            : null}
                            {HabilitationService.habilitations['materiel_suppression'] ? 
                                <ReservesMaterielsDeleteModal idReserveElement={item.idReserveElement} setPageNeedsRefresh={setPageNeedsRefresh} />
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
                topButtonShow={true}
                topButton={
                    HabilitationService.habilitations['materiel_ajout'] ?
                        <ReservesMaterielsForm setPageNeedsRefresh={setPageNeedsRefresh} />
                    : null
                }
            />
        : <LoaderInfiniteLoop />}
    </>);
};

ReservesMaterielsTable.propTypes = {};

export default ReservesMaterielsTable;
