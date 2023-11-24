import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import moment from 'moment-timezone';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import SacsDeleteModal from './sacsDeleteModal';
import SacsForm from './sacsForm';

const SacsTable = ({
    displayLibelleSac = true,
    displayLibelleLot = true,
    displayQuantiteEmplacements = true,
    displayQuantiteMateriels = true,
    displayActions = true,
    filterIdLot = null,
}) => {
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [sacs, setSacs] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/sacs/getSacs',{
                filterIdLot: filterIdLot
            });
            setSacs(getData.data);  
            
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
        {accessor: 'libelleSac'          , Header: 'Libellé'                 , isHidden: !displayLibelleSac},
        {accessor: 'libelleLot'          , Header: 'Lot'                     , isHidden: !displayLibelleLot},
        {accessor: 'quantiteEmplacements', Header: 'Quantité d\'emplacements', isHidden: !displayQuantiteEmplacements},
        {accessor: 'quantiteMateriels'   , Header: 'Quantité de materiels'   , isHidden: !displayQuantiteMateriels},
        {accessor: 'actions'             , Header: 'Actions'                 , isHidden: !displayActions},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of sacs)
        {
            tempTable.push({
                libelleSac          : item.libelleSac,
                libelleLot          : item.libelleLot,
                quantiteEmplacements: item.quantiteEmplacements,
                quantiteMateriels   : item.quantiteMateriels,
                actions             : <>
                    {item.inventaireEnCours ?
                        <SoftBadge bg='danger'>INVENTAIRE EN COURS</SoftBadge>    
                    :
                        <>
                            {HabilitationService.habilitations['sac_modification'] ? 
                                <SacsForm idSac={item.idSac} element={item} setPageNeedsRefresh={setPageNeedsRefresh} />
                            : null}
                            {HabilitationService.habilitations['sac_suppression'] ? 
                                <SacsDeleteModal idSac={item.idSac} setPageNeedsRefresh={setPageNeedsRefresh} />
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
    }, [sacs])

    return (
    <>
        {readyToDisplay ?
            <GPMtable
                columns={colonnes}
                data={lignes}
                topButtonShow={true}
                topButton={
                    HabilitationService.habilitations['sac_ajout'] ?
                        <SacsForm setPageNeedsRefresh={setPageNeedsRefresh} />
                    : null
                }
            />
        : <LoaderInfiniteLoop />}
    </>);
};

SacsTable.propTypes = {};

export default SacsTable;
