import React, {useState, useEffect} from 'react';
import { Table, Accordion, Alert } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

import GPMtable from 'components/gpmTable/gpmTable';

const LotConformiteReferentiel = ({analyseRef}) => {
    const [nbAlerte, setNbAlerte] = useState();

    const analyseOneItem = (item) => {
        let analyse = [];

        if(item.qttLot < item.quantiteReferentiel)
        {
            analyse.push({
                bg: 'danger',
                text: "Quantité",
            });
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
        }

        return analyse;
    }


    const colonnes = [
        {
            accessor: 'libelleMateriel',
            Header: 'Matériel',
        },
        {
            accessor: 'sterilite',
            Header: 'Stérilité',
            Cell: ({ value, row }) => {
				return(value == true ? 'Stérile' : 'Non-stérile');
			},
        },
        {
            accessor: 'quantiteReferentiel',
            Header: 'Quantité requise',
        },
        {
            accessor: 'qttLot',
            Header: 'Quantité présente',
        },
        {
            accessor: 'peremptionLot',
            Header: 'Péremption',
            Cell: ({ value, row }) => {
				return(value != null ? moment(value).format('DD/MM/YYYY') : null);
			},
        },
        {
            accessor: 'analyse',
            Header: 'Analyse',
            Cell: ({ value, row }) => {
				let analyse = analyseOneItem(row.original);
                return(
                    analyse.length == 0 ? <SoftBadge bg='success'>OK</SoftBadge> : analyse.map((alerte, i)=>{return(
                        <SoftBadge bg={alerte.bg} className='me-1'>{alerte.text}</SoftBadge>
                    )})
                );
			},
        },
    ];

    const calculAlertes = () => {
        let nbAlertesTemp = 0;
        for(const item of analyseRef)
        {
            let analyse = analyseOneItem(item);
            nbAlertesTemp += analyse.length;
        }
        setNbAlerte(nbAlertesTemp);
    }
    useEffect(() => {
        calculAlertes();
    }, [analyseRef])

    useEffect(() => {
        calculAlertes();
    }, [])

    return (
        <GPMtable
            columns={colonnes}
            data={analyseRef}
            topButtonShow={true}
            topButton={<Alert variant={nbAlerte > 0 ? 'danger' : 'success'} >{nbAlerte > 0 ? nbAlerte+' erreurs détectées - Lot non-conforme' : 'Aucune alerte détectée'}</Alert>}
        />
    );
};

LotConformiteReferentiel.propTypes = {};

export default LotConformiteReferentiel;
