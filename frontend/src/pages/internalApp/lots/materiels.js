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

import moment from 'moment-timezone';

const Materiels = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [materiels, setMateriels] = useState([]);

    const navigate = useNavigate();

    const initPage = async () => {
        try {
            const getData = await Axios.get('/materiels/getMateriels');
            setMateriels(getData.data);  
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {accessor: 'libelleMateriel'     , Header: 'Libellé'},
        {accessor: 'libelleEmplacement'  , Header: 'Emplacement'},
        {accessor: 'libelleSac'          , Header: 'Sac'},
        {accessor: 'libelleLot'          , Header: 'Lot'},
        {accessor: 'quantite'            , Header: 'Quantité'},
        {accessor: 'peremption'          , Header: 'Péremption'},
        {accessor: 'libelleMaterielsEtat', Header: 'Etat'},
        {accessor: 'notif'               , Header: 'Notifications'},
        {accessor: 'actions'             , Header: 'Actions'},
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
                notif               : item.idNotificationEnabled != 1 ? 'non' : 'oui',
                actions             : <></>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [materiels])

    return (<>
        <PageHeader
            preTitle="Lots opérationnels"
            title="Gestion du matériel et des consommables"
            className="mb-3"
        />

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ?
                    <GPMtable
                        columns={colonnes}
                        data={lignes}
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Materiels.propTypes = {};

export default Materiels;
