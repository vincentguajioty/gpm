import React, {useEffect, useState} from 'react';

import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';

const ReferentielContenuTableau = ({contenu}) => {
    const colonnes = [
        {accessor: 'libelleMateriel'        , Header: 'Matériel'},
        {accessor: 'quantiteReferentiel'    , Header: 'Quantité'},
        {accessor: 'obligatoire'            , Header: 'Obligation'},
        {accessor: 'libelleCategorie'       , Header: 'Catégorie'},
        {accessor: 'commentairesReferentiel', Header: 'Commentaires'},
    ];

    const [lignes, setLignes] = useState([]);
    const initTableLignes = () => {
        let tempTable  = [];
        for(const item of contenu)
        {
            tempTable.push({
                libelleMateriel        : item.libelleMateriel,
                quantiteReferentiel    : item.quantiteReferentiel,
                obligatoire            : item.obligatoire ? <SoftBadge bg="info">Obligatoire</SoftBadge> : <SoftBadge bg="success">Facultatif</SoftBadge>,
                libelleCategorie       : item.libelleCategorie,
                commentairesReferentiel: item.commentairesReferentiel,
            })
        }
        setLignes(tempTable);
    }
    useEffect(()=>{
        initTableLignes();
    },[])
    useEffect(()=>{
        initTableLignes();
    },[contenu])

    return (
        <GPMtable
            columns={colonnes}
            data={lignes}
        />
    );
};

ReferentielContenuTableau.propTypes = {};

export default ReferentielContenuTableau;
