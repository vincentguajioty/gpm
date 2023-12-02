import React, {useState, useEffect} from 'react';
import { Table, Accordion, Alert } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

import GPMtable from 'components/gpmTable/gpmTable';

const LotConformiteReferentiel = ({analyseRef}) => {
    const [nbAlerte, setNbAlerte] = useState();
    const colonnes = [
        {accessor: 'libelleMateriel'    , Header: 'Matériel'},
        {accessor: 'sterilite'          , Header: 'Stérilité'},
        {accessor: 'quantiteReferentiel', Header: 'Quantité requise'},
        {accessor: 'qttLot'             , Header: 'Quantité présente'},
        {accessor: 'peremptionLot'      , Header: 'Péremption'},
        {accessor: 'analyse'            , Header: 'Analyse'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        let nbAlertesTemp = 0;

        for(const item of analyseRef)
        {
            let analyse = [];

            if(item.qttLot < item.quantiteReferentiel)
            {
                analyse.push({
                    bg: 'danger',
                    text: "Quantité",
                });
                nbAlertesTemp += 1;
            }

            if(item.sterilite == true && (
                item.peremptionLot == null
                ||
                new Date(item.peremptionLot) < new Date()
            ))
            {
                analyse.push({
                    bg: 'danger',
                    text: "Péremption",
                });
                nbAlertesTemp += 1;
            }

            tempTable.push({
                libelleMateriel: item.libelleMateriel,
                sterilite: item.sterilite == true ? 'Stérile' : 'Non-stérile',
                quantiteReferentiel: item.quantiteReferentiel,
                qttLot: item.qttLot,
                peremptionLot: item.peremptionLot != null ? moment(item.peremptionLot).format('DD/MM/YYYY') : null,
                analyse: analyse.length == 0 ? <SoftBadge bg='success'>OK</SoftBadge> : analyse.map((alerte, i)=>{return(
                    <SoftBadge bg={alerte.bg} className='me-1'>{alerte.text}</SoftBadge>
                )}),
            })
        }
        setLignes(tempTable);
        setNbAlerte(nbAlertesTemp);
    }
    useEffect(() => {
        initTableau();
    }, [analyseRef])

    useEffect(() => {
        initTableau();
    }, [])

    return (
        <GPMtable
            columns={colonnes}
            data={lignes}
            topButtonShow={true}
            topButton={<Alert variant={nbAlerte > 0 ? 'danger' : 'success'} >{nbAlerte > 0 ? nbAlerte+' erreurs détectées - Lot non-conforme' : 'Aucune alerte détectée'}</Alert>}
        />
    );
};

LotConformiteReferentiel.propTypes = {};

export default LotConformiteReferentiel;
