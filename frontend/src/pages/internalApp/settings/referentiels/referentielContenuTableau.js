import React, {useEffect, useState} from 'react';

import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';

const ReferentielContenuTableau = ({contenu}) => {
    const colonnes = [
        {
            accessor: 'libelleMateriel',
            Header: 'Matériel',
        },
        {
            accessor: 'quantiteReferentiel',
            Header: 'Quantité',
        },
        {
            accessor: 'obligatoire',
            Header: 'Obligation',
            Cell: ({ value, row }) => {
				return(value ? <SoftBadge bg="info">Obligatoire</SoftBadge> : <SoftBadge bg="success">Facultatif</SoftBadge>);
			},
        },
        {
            accessor: 'libelleCategorie',
            Header: 'Catégorie',
        },
        {
            accessor: 'commentairesReferentiel',
            Header: 'Commentaires',
        },
    ];

    return (
        <GPMtable
            columns={colonnes}
            data={contenu}
        />
    );
};

ReferentielContenuTableau.propTypes = {};

export default ReferentielContenuTableau;
