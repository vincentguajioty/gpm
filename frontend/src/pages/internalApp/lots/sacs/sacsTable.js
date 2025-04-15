import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import SacsDeleteModal from './sacsDeleteModal';
import SacsForm from './sacsForm';
import SacsContentModal from './sacsContentModal';

const SacsTable = ({
    displayLibelleSac = true,
    displayLibelleLot = true,
    displayContenu = true,
    displayActions = true,
    filterIdLot = null,
}) => {
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [sacs, setSacs] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.post('/sacs/getSacs',{
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
        {
            accessor: 'libelleSac',
            Header: 'Libellé',
            isHidden: !displayLibelleSac,
        },
        {
            accessor: 'libelleLot',
            Header: 'Lot',
            isHidden: !displayLibelleLot,
        },
        {
            accessor: 'contenu',
            Header: 'Contenu',
            isHidden: !displayContenu,
            Cell: ({ value, row }) => {
				return(
                    <SacsContentModal
                        idSac={row.original.idSac}
                        libelleSac={row.original.libelleSac}
                        inventaireEnCours={row.original.inventaireEnCours}
                    />
                );
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            isHidden: !displayActions,
            Cell: ({ value, row }) => {
				return(
                    <>
                        {row.original.inventaireEnCours ?
                            <SoftBadge bg='danger'>INVENTAIRE EN COURS</SoftBadge>    
                        :
                            <>
                                {HabilitationService.habilitations['sac_modification'] ? 
                                    <SacsForm idSac={row.original.idSac} element={row.original} setPageNeedsRefresh={setPageNeedsRefresh} />
                                : null}
                                {HabilitationService.habilitations['sac_suppression'] ? 
                                    <SacsDeleteModal idSac={row.original.idSac} setPageNeedsRefresh={setPageNeedsRefresh} />
                                : null}
                            </>
                        }
                    </>
                );
			},
        },
    ];

    return (
    <>
        {readyToDisplay ?
            <GPMtable
                columns={colonnes}
                data={sacs}
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
