import React, {useEffect, useState} from 'react';
import { Tabs, Tab, } from 'react-bootstrap';

import HabilitationService from 'services/habilitationsService';

import OneCentreLivre from './oneCentreLivre';
import OneCentreProprietes from './oneCentreProps';

const OneCentreTabs = ({
    idCentreDeCout,
    centre,
    setPageNeedsRefresh,
}) => {
    const [tabInReadOnly, setTabInReadOnly] = useState(true);
    const [mesDroits, setMesDroits] = useState(false);

    const checkIfUserCanEdit = () => {
        setTabInReadOnly(true);
        setMesDroits(false)

        let gestionnaireSelected = centre.gestionnaires.filter(personne => personne.idPersonne == HabilitationService.habilitations.idPersonne)
        if(gestionnaireSelected.length == 1)
        {
            gestionnaireSelected = gestionnaireSelected[0];
            setMesDroits(gestionnaireSelected);
            if(gestionnaireSelected.actif == true)
            {
                setTabInReadOnly(false);
            }
        }
    }

    useEffect(()=>{
        checkIfUserCanEdit();
    },[])

    useEffect(()=>{
        checkIfUserCanEdit();
    },[centre])

    return(
        <Tabs defaultActiveKey="livre" transition={true}>
            <Tab eventKey="livre" title="Livre de comptes" className='border-bottom border-x p-3'>
                <OneCentreLivre
                    idCentreDeCout={idCentreDeCout}
                    centre={centre}
                    setPageNeedsRefresh={setPageNeedsRefresh}
                    tabInReadOnly={tabInReadOnly}
                    mesDroits={mesDroits}
                />
            </Tab>
            <Tab eventKey="cmdToDo" title="Commandes à intégrer" className='border-bottom border-x p-3'>
            </Tab>
            <Tab eventKey="cmdRefusees" title="Commandes refusées" className='border-bottom border-x p-3'>
            </Tab>
            <Tab eventKey="pj" title="Pièces jointes" className='border-bottom border-x p-3'>
            </Tab>
            <Tab eventKey="props" title="Propriétés et gestionnaires" className='border-bottom border-x p-3'>
                <OneCentreProprietes
                    idCentreDeCout={idCentreDeCout}
                    centre={centre}
                    setPageNeedsRefresh={setPageNeedsRefresh}
                />
            </Tab>
            <Tab eventKey="export" title="Export" className='border-bottom border-x p-3'>
            </Tab>
        </Tabs>
    )
};

OneCentreTabs.propTypes = {};

export default OneCentreTabs;