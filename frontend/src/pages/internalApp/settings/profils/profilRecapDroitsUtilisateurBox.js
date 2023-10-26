import React from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';

import ProfilRecapDroitsUtilisateur from './profilRecapDroitsUtilisateur';

const ProfilRecapDroitsUtilisateurBox = ({idPersonne, pageNeedsRefresh}) => {
    return(
        <FalconComponentCard>
            <FalconComponentCard.Header
                title="Récapitulatif des habilitations"
            >
            </FalconComponentCard.Header>
            <FalconComponentCard.Body>
                <ProfilRecapDroitsUtilisateur idPersonne={idPersonne} pageNeedsRefresh={pageNeedsRefresh} />
            </FalconComponentCard.Body>
        </FalconComponentCard>
    );
};

ProfilRecapDroitsUtilisateurBox.propTypes = {};

export default ProfilRecapDroitsUtilisateurBox;
