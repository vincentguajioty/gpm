import React, { useState, useEffect } from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import moment from 'moment-timezone';

import nl2br from 'react-nl2br';

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
    displayRemarques = true,
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
        {
            accessor: 'libelleMateriel',
            Header: 'Libellé',
            isHidden: !displayLibelleMateriel,
            Cell: ({ value, row }) => {
				return(<>
                    {value}
                    {row.original.numeroSerie ? <><br/><SoftBadge className='mt-1'>SN: {row.original.numeroSerie}</SoftBadge></> : null}
                </>);
			},
        },
        {
            accessor: 'libelleEmplacement',
            Header: 'Emplacement',
            isHidden: !displayLibelleEmplacement,
        },
        {
            accessor: 'libelleSac',
            Header: 'Sac',
            isHidden: !displayLibelleSac,
        },
        {
            accessor: 'libelleLot',
            Header: 'Lot',
            isHidden: !displayLibelleLot,
        },
        {
            accessor: 'quantite',
            Header: 'Quantité',
            isHidden: !displayQuantite,
            Cell: ({ value, row }) => {
				return(
                    row.original.quantite < row.original.quantiteAlerte ?
                        <SoftBadge bg='danger'>{row.original.quantite}</SoftBadge>
                    :
                        row.original.quantite == row.original.quantiteAlerte ?
                            <SoftBadge bg='warning'>{row.original.quantite}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{row.original.quantite}</SoftBadge>
                );
			},
        },
        {
            accessor: 'peremption',
            Header: 'Péremption',
            isHidden: !displayPeremption,
            Cell: ({ value, row }) => {
				return(
                    row.original.peremption != null ?
                        row.original.peremption < new Date() ?
                            <SoftBadge bg='danger'>{moment(row.original.peremption).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            row.original.peremptionNotification < new Date() ?
                                <SoftBadge bg='warning'>{moment(row.original.peremption).format('DD/MM/YYYY')}</SoftBadge>
                            :
                                <SoftBadge bg='success'>{moment(row.original.peremption).format('DD/MM/YYYY')}</SoftBadge>
                    :
                        null
                );
			},
        },
        {
            accessor: 'libelleMaterielsEtat',
            Header: 'Etat',
            isHidden: !displayLibelleMaterielsEtat,
        },
        {
            accessor: 'notif',
            Header: 'Notifications',
            isHidden: !displayNotif,
            Cell: ({ value, row }) => {
				return(row.original.idNotificationEnabled != 1 ? <FontAwesomeIcon icon='bell-slash' /> : <FontAwesomeIcon icon='bell' />);
			},
        },
        {
            accessor: 'commentairesElement',
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
                        {row.original.inventaireEnCours ?
                            <SoftBadge bg='danger'>INVENTAIRE EN COURS</SoftBadge>    
                        :
                            <>
                                {HabilitationService.habilitations['reserve_ReserveVersLot'] ? 
                                    <TransfertsMaterielsOpe idElement={row.original.idElement} setPageNeedsRefresh={setPageNeedsRefresh} />
                                : null}
                                {HabilitationService.habilitations['materiel_modification'] ? 
                                    <MaterielsForm idElement={row.original.idElement} element={row.original} setPageNeedsRefresh={setPageNeedsRefresh} />
                                : null}
                                {HabilitationService.habilitations['materiel_suppression'] ? 
                                    <MaterielsDeleteModal idElement={row.original.idElement} setPageNeedsRefresh={setPageNeedsRefresh} />
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
                data={materiels}
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
