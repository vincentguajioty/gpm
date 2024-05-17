import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SoftBadge from 'components/common/SoftBadge';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

const AffectationsAvecDates = ({
    affectationsFiltered = [],
}) => {
    const colonnesForDetailedDisplay = [
        {
            accessor: 'nomPrenom',
            Header: 'Personne',
            Cell: ({ value, row }) => {
				return(<>
                    {value}
                    {row.original.mailPersonne != null ? <><br/><SoftBadge bg='info'>{row.original.mailPersonne}</SoftBadge></>:null}
                    <br/>{row.original.idPersonne == null ? <SoftBadge bg='secondary'>Externe</SoftBadge>:null}
                </>);
			},
        },
        {
            accessor: 'libelleCatalogueTenue',
            Header: 'Element',
            Cell: ({ value, row }) => {
				return(<>
                    {value}
                    <br/>Taille: {row.original.tailleCatalogueTenue}

                    <br/>{row.original.dateRetour != null ? 
                        new Date(row.original.dateRetour) < new Date() ?
                            <SoftBadge bg='danger'>{moment(row.original.dateRetour).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{moment(row.original.dateRetour).format('DD/MM/YYYY')}</SoftBadge>
                    : null}
                    <br/>{row.original.notifPersonne == true ? <SoftBadge bg='info' className='ms-1'><FontAwesomeIcon icon='bell'/></SoftBadge> : null}
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
                {affectationsFiltered.length == 0 ? <center><i>Aucune date à surveiller</i></center> : null}

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

AffectationsAvecDates.propTypes = {};

export default AffectationsAvecDates;