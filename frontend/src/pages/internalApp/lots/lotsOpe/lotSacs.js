import React from 'react';
import { Accordion, Alert } from 'react-bootstrap';

import SacsContent from '../sacs/sacsContent';
import SacsForm from '../sacs/sacsForm';
import LotReferentielImport from './lotReferentielImport';

const LotSacs = ({
    idLot,
    idTypeLot = null,
    sacs,
    qttMateriel = 0,
    inventaireEnCours,
    setPageNeedsRefresh,
}) => {
    return (<>
        {sacs.length == 0 ?
            <Alert className="mb-3" variant='info'>Il semblerait que votre lot ne contienne aucun sac. Pour commencer, créer des sacs et emplacements. Vous pourrez ensuite y ajouter vos matériels.</Alert>
        : null}
        {qttMateriel == 0 && sacs.length > 0 && idTypeLot > 0 ?
            <Alert className="mb-3" variant='info'>Il semblerait que votre lot ne contienne aucun matériel.<br/>Astuce: vous pouvez commencer à populer votre lot en <LotReferentielImport idLot={idLot} idTypeLot={idTypeLot} setPageNeedsRefresh={setPageNeedsRefresh}/></Alert>
        : null}
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

        <SacsForm
            idLot={idLot}
            setPageNeedsRefresh={setPageNeedsRefresh}
        />
    </>);
};

LotSacs.propTypes = {};

export default LotSacs;
