import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import moment from 'moment-timezone';

import nl2br from 'react-nl2br';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import VehiculesStockForm from './vehiculesStockForm';
import VehiculesStockDeleteModal from './vehiculesStockDeleteModal';

const VehiculesStockTable = ({
    displayLibelleMateriel = true,
    displayQuantite = true,
    displayPeremption = true,
    displayRemarques = true,
    displayActions = true,
    hideAddButton = false,
}) => {
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [materiels, setMateriels] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vehicules/getAllVehiculesStock');
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
            setReadyToDisplay(false);
            initPage();
        }
    }, [pageNeedsRefresh])

    const colonnes = [
        {
            accessor: 'libelleMateriel',
            Header: 'Libellé',
            isHidden: !displayLibelleMateriel,
        },
        {
            accessor: 'quantiteVehiculesStock',
            Header: 'Quantité',
            isHidden: !displayQuantite,
            Cell: ({ value, row }) => {
                return(
                    row.original.quantiteVehiculesStock < row.original.quantiteAlerteVehiculesStock ?
                        <SoftBadge bg='danger'>{row.original.quantiteVehiculesStock}</SoftBadge>
                    :
                        row.original.quantiteVehiculesStock == row.original.quantiteAlerteVehiculesStock ?
                            <SoftBadge bg='warning'>{row.original.quantiteVehiculesStock}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{row.original.quantiteVehiculesStock}</SoftBadge>
                );
            },
        },
        {
            accessor: 'peremptionVehiculesStock',
            Header: 'Péremption',
            isHidden: !displayPeremption,
            Cell: ({ value, row }) => {
                return(
                    row.original.peremptionVehiculesStock != null ?
                        new Date(row.original.peremptionVehiculesStock) < new Date() ?
                            <SoftBadge bg='danger'>{moment(row.original.peremptionVehiculesStock).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            new Date(row.original.peremptionNotificationVehiculesStock) < new Date() ?
                                <SoftBadge bg='warning'>{moment(row.original.peremptionVehiculesStock).format('DD/MM/YYYY')}</SoftBadge>
                            :
                                <SoftBadge bg='success'>{moment(row.original.peremptionVehiculesStock).format('DD/MM/YYYY')}</SoftBadge>
                    :
                        null
                );
            },
        },
        {
            accessor: 'commentairesVehiculesStock',
            Header: 'Remarques',
            isHidden: !displayRemarques,
            Cell: ({ value, row }) => {
                return(nl2br(value));
            },
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            isHidden: !displayActions,
            Cell: ({ value, row }) => {
                return(
                    <>
                        {HabilitationService.habilitations['vehicules_modification'] ? 
                            <VehiculesStockForm idVehiculesStock={row.original.idVehiculesStock} element={row.original} setPageNeedsRefresh={setPageNeedsRefresh} />
                        : null}
                        {HabilitationService.habilitations['vehicules_suppression'] ? 
                            <VehiculesStockDeleteModal idVehiculesStock={row.original.idVehiculesStock} setPageNeedsRefresh={setPageNeedsRefresh} />
                        : null}
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
                data={materiels}
                topButtonShow={true && !hideAddButton}
                topButton={
                    HabilitationService.habilitations['vehicules_ajout'] ?
                        <VehiculesStockForm
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                    : null
                }
            />
        : <LoaderInfiniteLoop />}
    </>);
};

VehiculesStockTable.propTypes = {};

export default VehiculesStockTable;
