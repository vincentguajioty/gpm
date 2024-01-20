import React, {useState, useEffect} from 'react';
import { Table, Modal, Button, Accordion } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import { lotsConsommationsDelete } from 'helpers/deleteModalWarningContent';

import { Axios } from 'helpers/axios';

import HabilitationService from 'services/habilitationsService';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

const RapportsConsoOneAccordion = ({
    idConsommation,
    setPageNeedsRefesh,
}) => {
    const [isLoading, setLoading] = useState(true);
    const [consommation, setConsommation] = useState();

    const colonnes = [
        {
            accessor: 'libelleLot',
            Header: 'Lot',
        },
        {
            accessor: 'libelleMateriel',
            Header: 'Matériel',
        },
        {
            accessor: 'quantiteConsommation',
            Header: 'Quantité consommée',
        },
        {
            accessor: 'libelleConteneur',
            Header: 'Réapprovisionné via',
            Cell: ({ value, row }) => {
				return(value && value != "" ?<SoftBadge bg='success'>{value}</SoftBadge> : <SoftBadge bg='warning'>Non-réapprovisionné</SoftBadge>);
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {consommation.consommation.declarationEnCours && consommation.consommation.reapproEnCours ?
                            <SoftBadge bg="info" className='me-1'>Element verrouillé car réappro en cours</SoftBadge>
                            : consommation.consommation.declarationEnCours ? <SoftBadge bg="info" className='me-1'>Element verrouillé car saisie en cours</SoftBadge>
                            : row.original.traiteOperateur == true ? <SoftBadge bg="success">Traité</SoftBadge>
                            : HabilitationService.habilitations['consommationLots_affectation'] ? <>
                                {row.original.idConteneur != null && row.original.idConteneur > 0 ?
                                    <IconButton
                                        variant='outline-info'
                                        size='sm'
                                        className='me-1 mb-1'
                                        icon='cube'
                                        onClick={()=>{decompterActionDefaut(row.original.idConsommationMateriel)}}
                                    >Décompter la réserve</IconButton>
                                :
                                    <IconButton
                                        variant='outline-info'
                                        size='sm'
                                        className='me-1 mb-1'
                                        icon='briefcase-medical'
                                        onClick={()=>{decompterActionDefaut(row.original.idConsommationMateriel)}}
                                    >Décompter le lot</IconButton>
                                }
                                <IconButton
                                    variant='outline-danger'
                                    size='sm'
                                    className='me-1 mb-1'
                                    icon='ban'
                                    onClick={()=>{annulerTouteAction(row.original.idConsommationMateriel)}}
                                >Ne rien faire</IconButton>
                            </>
                            : null
                        }
                    </>
                );
			},
        },
    ];
    const loadConso = async () => {
        try {
            const getFromDB = await Axios.post('/consommations/getOneConso',{
                idConsommation: idConsommation
            });
            setConsommation(getFromDB.data);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        loadConso();
    },[])

    const decompterToutesActionsDefaut = async () => {
        try {
            setLoading(true);

            let updateDB = await Axios.post('/consommations/decompterToutesActionsDefaut',{
                idConsommation: idConsommation,
            })
            
            setPageNeedsRefesh(true);
            loadConso();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const decompterActionDefaut = async (idConsommationMateriel) => {
        try {
            setLoading(true);

            let updateDB = await Axios.post('/consommations/decompterActionDefaut',{
                idConsommationMateriel: idConsommationMateriel,
            })
            
            setLoading(false);
            loadConso();
        } catch (error) {
            console.log(error)
        }
    }

    const annulerTouteAction = async (idConsommationMateriel) => {
        try {
            setLoading(true);

            let updateDB = await Axios.post('/consommations/annulerTouteAction',{
                idConsommationMateriel: idConsommationMateriel,
            })
            
            setLoading(false);
            loadConso();
        } catch (error) {
            console.log(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);

    const handleCloseDeleteModal = () => {
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = () => {
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/consommations/lotsConsommationsDelete',{
                idConsommation: idConsommation,
            });
            
            setPageNeedsRefesh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    const nl2br = require('react-nl2br');
    if(isLoading)
    {
        return <LoaderInfiniteLoop/>
    }else{
        return(<>
            <Accordion.Item eventKey={consommation.consommation.idConsommation} flush="true">
                <Accordion.Header>
                    <SoftBadge bg={consommation.consommation.statut.color} className='me-1'>{consommation.consommation.statut.label}</SoftBadge>
                    {moment(consommation.consommation.dateConsommation).format('DD/MM/YYYY')} - {consommation.consommation.evenementConsommation}
                </Accordion.Header>
                <Accordion.Body>
                    <Table className="fs--1 mt-3 mb-3" size='sm' responsive>
                        <tbody>
                            <tr>
                                <th className="bg-100" style={{ width: '30%' }}>Evènement</th>
                                <td>{consommation.consommation.evenementConsommation}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '30%' }}>Date</th>
                                <td>{moment(consommation.consommation.dateConsommation).format('DD/MM/YYYY')}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '30%' }}>Responsable</th>
                                <td>{consommation.consommation.nomDeclarantConsommation}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '30%' }}>Commentaires</th>
                                <td>{nl2br(consommation.consommation.commentairesConsommation)}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '30%' }}>Lots impactés</th>
                                <td>
                                    {consommation.lotsImpactes.map((lot, i)=>{return(
                                        <SoftBadge className='me-1'>{lot.libelleLot}</SoftBadge>
                                    )})}
                                </td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '30%' }}>Consommables</th>
                                <td>
                                    <SoftBadge bg={consommation.consommation.qttMaterielsNonTraites > 0 ? 'danger' : 'success'} className='me-1'>{consommation.consommation.qttMaterielsNonTraites} à traiter</SoftBadge>
                                    <SoftBadge bg={consommation.consommation.qttMaterielsTraites == 0 ? 'warning' : 'success'} className='me-1'>{consommation.consommation.qttMaterielsTraites} traités</SoftBadge>
                                </td>
                            </tr>
                        </tbody>
                    </Table>

                    <GPMtable
                        columns={colonnes}
                        data={consommation?.elements}
                        topButtonShow={consommation.consommation.statut.label=='A TRAITER' && consommation.consommation.qttMaterielsNonTraites > 0 && HabilitationService.habilitations['consommationLots_affectation']}
                        topButton={
                            <IconButton
                                icon='check'
                                size = 'sm'
                                variant="outline-info"
                                className="me-1"
                                onClick={decompterToutesActionsDefaut}
                            >Valider toutes les actions</IconButton>
                        }
                    />

                    {HabilitationService.habilitations['consommationLots_supression'] ? <>
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={handleShowDeleteModal}
                        >Supprimer l'évènement</IconButton>

                        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
                            <Modal.Header>
                                <Modal.Title>Suppression</Modal.Title>
                                <FalconCloseButton onClick={handleCloseDeleteModal}/>
                            </Modal.Header>
                            <Modal.Body>{lotsConsommationsDelete}</Modal.Body>
                            <Modal.Footer>
                                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                                    Annuler
                                </Button>
                                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
                            </Modal.Footer>
                        </Modal>
                    </>: null}
                </Accordion.Body>
            </Accordion.Item>
        </>);
    }
};

RapportsConsoOneAccordion.propTypes = {};

export default RapportsConsoOneAccordion;
