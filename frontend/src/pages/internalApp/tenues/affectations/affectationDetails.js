import React, { useState, useEffect } from 'react';
import { Form, Table, } from 'react-bootstrap';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import Select from 'react-select';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';

import AffectationExport from './affectationsExport';
import AffectationDateMassive from './affectationsDateMassive';
import AffectationDeleteMassif from './affectationsDeleteMassif';
import AffectationForm from './affectationsForm';
import AffectationReintegrer from './affectationReinteg';
import AffectationDelete from './affectationDelete';
import AffectationGestionRemplacement from './affectationGestRempl';
import AffectationGestionPret from './affectationGestPret';

const AffectationDetails = ({
    affectations = [],
    affectationsRow = [],
    catalogue = [],
    personnesExternes = [],
    setPageNeedsRefresh,
}) => {
    //DisplayDetails
    const [displayIdExterne, setDisplayIdExterne] = useState();
    const [displayBoxWithDetails, setDisplayBoxWithDetails] = useState(false);
    const [affectationToDisplay, setAffectationToDisplay] = useState([]);

    const resetDisplay = () => {
        setDisplayBoxWithDetails(false);
        setAffectationToDisplay([]);
        setDisplayIdExterne();
    }
    
    useEffect(()=>{
        let tempAffect=[];

        if(!displayIdExterne || displayIdExterne == null)
        {
            resetDisplay();
            return;
        }else{
            
            tempAffect = affectations.filter(affect => affect.idExterne == displayIdExterne);
            if(tempAffect.length == 1)
            {
                setAffectationToDisplay(tempAffect[0]);
                setDisplayBoxWithDetails(true);
            }else{
                setAffectationToDisplay([]);
                setDisplayBoxWithDetails(false);
            }
            return;
        }

    },[
        displayIdExterne,
        affectations,
        affectationsRow,
        catalogue,
    ])

    const colonnesForDetailedDisplay = [
        {
            accessor: 'libelleMateriel',
            Header: 'Element',
            Cell: ({ value, row }) => {
				return(<>
                    {row.original.demandeBenevoleRemplacement ? <><SoftBadge bg='warning' className='me-1'>Demande de remplacement</SoftBadge><br/></> : null}
                    {row.original.demandeBenevolePret ? <><SoftBadge bg='warning' className='me-1'>Demande de prêt</SoftBadge><br/></> : null}
                    {value}
                </>);
			},
        },
        {
            accessor: 'taille',
            Header: 'Taille',
        },
        {
            accessor: 'dateAffectation',
            Header: 'Affecté le',
            Cell: ({ value, row }) => {
				return(
                    value != null ? moment(value).format('DD/MM/YYYY') : null
                );
			},
        },
        {
            accessor: 'dateRetour',
            Header: 'Retour prévu le',
            Cell: ({ value, row }) => {
				return(<>
                    {value != null ? 
                        new Date(value) < new Date() ?
                            <SoftBadge bg='danger'>{moment(value).format('DD/MM/YYYY')}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{moment(value).format('DD/MM/YYYY')}</SoftBadge>
                    : null}
                    {row.original.notifPersonne == true ? <SoftBadge bg='info' className='ms-1'><FontAwesomeIcon icon='bell'/></SoftBadge> : null}
                </>);
			},
        },
        {
            accessor: 'idTenue',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(<>
                    {HabilitationService.habilitations['tenues_modification'] && row.original.demandeBenevoleRemplacement == true ? <>
                        <AffectationGestionRemplacement
                            idTenue={value}
                            tenue={row.original}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                        <br/>
                    </>: null}
                    {HabilitationService.habilitations['tenues_modification'] && row.original.demandeBenevolePret == true ? <>
                        <AffectationGestionPret
                            idTenue={value}
                            tenue={row.original}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                        <br/>
                    </>: null}

                    {HabilitationService.habilitations['tenues_modification'] && row.original.demandeBenevolePret == false ? 
                        <AffectationForm
                            affectationsRow={affectationsRow}
                            catalogue={catalogue}
                            personnesExternes={personnesExternes}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            showEditButton={true}
                            editId={value}
                        />
                    : null}
                    {HabilitationService.habilitations['tenues_suppression'] && row.original.demandeBenevolePret == false ? 
                        <AffectationReintegrer
                            idTenue={value}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                    : null}
                    {HabilitationService.habilitations['tenues_suppression'] ? 
                        <AffectationDelete
                            idTenue={value}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                        />
                    : null}
                </>);
			},
        },
    ];

    return(<>        
        {!displayBoxWithDetails ? <>
            <FalconComponentCard noGuttersBottom className="mb-3 mt-3">
                <FalconComponentCard.Body
                    scope={{ ActionButton }}
                    noLight
                >
                    {HabilitationService.habilitations['tenues_ajout'] ?
                        <AffectationForm
                            affectationsRow={affectationsRow}
                            catalogue={catalogue}
                            personnesExternes={personnesExternes}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            showAddButton={true}
                        />
                    : null}

                    {HabilitationService.habilitations['tenues_lecture'] ? <AffectationExport /> : null}

                    <Form.Group className="mb-3">
                        <Form.Label>Rechercher une personne</Form.Label>
                        <Select
                            id="idExterne"
                            name="idExterne"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            placeholder='Aucune personne connue selectionnée'
                            options={personnesExternes}
                            value={personnesExternes.find(c => c.value === displayIdExterne)}
                            onChange={val => val != null ? setDisplayIdExterne(val.value) : setDisplayIdExterne(null)}
                        />
                    </Form.Group>
                </FalconComponentCard.Body>
            </FalconComponentCard>
        </>:null}

        {displayBoxWithDetails ?
            <FalconComponentCard noGuttersBottom className="mb-3 mt-3">
                <FalconComponentCard.Body
                    scope={{ ActionButton }}
                    noLight
                >
                    <IconButton
                        icon='arrow-left'
                        size = 'sm'
                        variant="outline-secondary"
                        onClick={resetDisplay}
                    >Retour</IconButton>

                    <Table className="fs--1 mt-3" size='sm' responsive>
                        <tr>
                            <td className="bg-100" style={{ width: '30%' }}>Personne</td>
                            <td>{affectationToDisplay.nomPrenomExterne}</td>
                        </tr>
                        <tr>
                            <td className="bg-100" style={{ width: '30%' }}>Mail de contact</td>
                            <td>{affectationToDisplay.mailExterne}</td>
                        </tr>
                        <tr>
                            <td className="bg-100" style={{ width: '30%' }}>Actions massives</td>
                            <td>
                            {HabilitationService.habilitations['tenues_modification'] ? 
                                    <AffectationDateMassive
                                        setPageNeedsRefresh={setPageNeedsRefresh}
                                        displayIdExterne={displayIdExterne}
                                    />
                                : null}
                                {HabilitationService.habilitations['tenues_suppression'] ? 
                                    <AffectationDeleteMassif
                                        setPageNeedsRefresh={setPageNeedsRefresh}
                                        displayIdExterne={displayIdExterne}
                                    />
                                : null}
                            </td>
                        </tr>
                    </Table>

                    <GPMtable
                        columns={colonnesForDetailedDisplay}
                        data={affectationToDisplay.affectations}
                        topButtonShow={false}
                    />

                </FalconComponentCard.Body>
            </FalconComponentCard>
        : null}
    </>)
}

AffectationDetails.propTypes = {};

export default AffectationDetails;