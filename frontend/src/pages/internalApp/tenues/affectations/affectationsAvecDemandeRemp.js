import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';

import AffectationGestionRemplacement from './affectationGestRempl';

const AffectationsAvecDemandeRemplacement = ({
    affectationsFiltered = [],
    setPageNeedsRefresh,
}) => {
    const colonnesForDetailedDisplay = [
        {
            accessor: 'nomPrenomExterne',
            Header: 'Personne',
            Cell: ({ value, row }) => {
                return(<>
                    {value}
                    {row.original.mailExterne != null ? <><br/><SoftBadge bg='info'>{row.original.mailExterne}</SoftBadge></>:null}
                </>);
            },
        },
        {
            accessor: 'libelleMateriel',
            Header: 'Element',
            Cell: ({ value, row }) => {
                return(<>
                    {value}
                    <br/>Taille: {row.original.taille}
                    <br/>

                    {HabilitationService.habilitations['tenues_modification'] && row.original.demandeBenevoleRemplacement == true ? <>
                        <AffectationGestionRemplacement
                            idTenue={row.original.idTenue}
                            tenue={row.original}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                        <br/>
                    </>: null}
                </>);
            },
        },
    ];

    return(<>
        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {affectationsFiltered.length == 0 ? <center><i>Aucune demande en cours</i></center> : null}

                {affectationsFiltered.length > 0 ?
                    <GPMtable
                        columns={colonnesForDetailedDisplay}
                        data={affectationsFiltered}
                        topButtonShow={false}
                    />
                : null}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>)
}

AffectationsAvecDemandeRemplacement.propTypes = {};

export default AffectationsAvecDemandeRemplacement;