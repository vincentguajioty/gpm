import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import moment from 'moment-timezone';

import nl2br from 'react-nl2br';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import TransmissionsStockForm from './transmissionsStockForm';
import TransmissionsStockDeleteModal from './transmissionsStockDeleteModal';

const TransmissionsStockTable = ({
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
            const getData = await Axios.get('/vhf/getAllVhfStock');
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
            accessor: 'quantiteVhfStock',
            Header: 'Quantité',
            isHidden: !displayQuantite,
            Cell: ({ value, row }) => {
                return(
                    row.original.quantiteVhfStock < row.original.quantiteAlerteVhfStock ?
                        <SoftBadge bg='danger'>{row.original.quantiteVhfStock}</SoftBadge>
                    :
                        row.original.quantiteVhfStock == row.original.quantiteAlerteVhfStock ?
                            <SoftBadge bg='warning'>{row.original.quantiteVhfStock}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{row.original.quantiteVhfStock}</SoftBadge>
                );
            },
        },
        {
            accessor: 'peremptionVhfStock',
            Header: 'Péremption',
            isHidden: !displayPeremption,
            Cell: ({ value, row }) => {
                return(
                    row.original.peremptionVhfStock != null ?
                        new Date(row.original.peremptionVhfStock) < new Date() ?
                            <SoftBadge bg='danger'>{moment(row.original.peremptionVhfStock).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            new Date(row.original.peremptionNotificationVhfStock) < new Date() ?
                                <SoftBadge bg='warning'>{moment(row.original.peremptionVhfStock).format('DD/MM/YYYY')}</SoftBadge>
                            :
                                <SoftBadge bg='success'>{moment(row.original.peremptionVhfStock).format('DD/MM/YYYY')}</SoftBadge>
                    :
                        null
                );
            },
        },
        {
            accessor: 'commentairesVhfStock',
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
                        {HabilitationService.habilitations['vhf_equipement_modification'] ? 
                            <TransmissionsStockForm idVhfStock={row.original.idVhfStock} element={row.original} setPageNeedsRefresh={setPageNeedsRefresh} />
                        : null}
                        {HabilitationService.habilitations['vhf_equipement_suppression'] ? 
                            <TransmissionsStockDeleteModal idVhfStock={row.original.idVhfStock} setPageNeedsRefresh={setPageNeedsRefresh} />
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
                    HabilitationService.habilitations['vhf_equipement_ajout'] ?
                        <TransmissionsStockForm
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                    : null
                }
            />
        : <LoaderInfiniteLoop />}
    </>);
};

TransmissionsStockTable.propTypes = {};

export default TransmissionsStockTable;
