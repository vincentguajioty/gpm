import React from 'react';
import { Table, Accordion } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

import SacsContent from '../sacs/sacsContent';

const LotSacs = ({sacs, inventaireEnCours, setPageNeedsRefresh}) => {
    return (
        <Accordion className="mb-3">
            {sacs.map((sac, i) => {return(
                <Accordion.Item eventKey={i} flush="true">
                    <Accordion.Header>{sac.libelleSac}</Accordion.Header>
                    <Accordion.Body>
                        <SacsContent
                            idSac={sac.idSac}
                            lockIdSac={sac.idSac}
                            inventaireEnCours={inventaireEnCours}
                            fullDisplay={false}
                        />
                    </Accordion.Body>
                </Accordion.Item>
            )})}
        </Accordion>
    );
};

LotSacs.propTypes = {};

export default LotSacs;
