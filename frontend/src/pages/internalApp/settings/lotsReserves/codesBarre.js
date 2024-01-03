import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import CodesBarreModal from 'components/widgets/CodeBarreModal';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { codesBarreFormFournisseur, codesBarreFormInterne } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const CodesBarre = () => {
    /*PAGE BASICS*/
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [catalogue, setCatalogue] = useState([]);
    const [codesBarre, setCodesBarres] = useState([]);

    const initTable = async () => {
        try {
            let getFromDb = await Axios.get('/select/getCatalogueMateriel');
            setCatalogue(getFromDb.data);

            getFromDb = await Axios.get('/settingsMetiers/getCodesBarres');
            setCodesBarres(getFromDb.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
		initTable();
	}, [])

    /* TABLE FOR DISPLAY */
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'codeBarre',
            Header: 'Code Barre',
            Cell: ({ value, row }) => {
				return(<>
                    <CodesBarreModal
                        valeurCodeBarre={value}
                    />
                    {value}
                </>);
			},
        },
        {
            accessor: 'internalReference',
            Header: 'Base',
            Cell: ({ value, row }) => {
				return(value == true ? <SoftBadge bg='primary'>Interne</SoftBadge> : <SoftBadge bg='info'>Fournisseur</SoftBadge>);
			},
        },
        {
            accessor: 'libelleMateriel',
            Header: 'Elément du catalogue',
        },
        {
            accessor: 'peremptionConsommable',
            Header: 'Péremption spécifiée',
            Cell: ({ value, row }) => {
				return(value && value != null ? moment(value).format('DD/MM/YYYY') : null);
			},
        },
        {
            accessor: 'commentairesCode',
            Header: 'Commentaires',
            Cell: ({ value, row }) => {
				return(nl2br(value));
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {HabilitationService.habilitations['codeBarre_modification'] && row.original.internalReference == true ? 
                            <ActionButton onClick={() => handleShowOffCanevasInternal(row.original.idCode)} icon="pen" title="Modifier" variant="action" className="p-0 me-2" />
                        : null }
                        {HabilitationService.habilitations['codeBarre_modification'] && row.original.internalReference == false ? 
                            <ActionButton onClick={() => handleShowOffCanevasExternal(row.original.idCode)} icon="pen" title="Modifier" variant="action" className="p-0 me-2" />
                        : null }

                        {HabilitationService.habilitations['codeBarre_suppression'] ? 
                            <ActionButton onClick={() => handleShowDeleteModal(row.original.idCode)} icon="trash" title="Supprimer" variant="action" className="p-0" />
                        : null }
                    </>
                );
			},
        },
    ];

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdCode, setDeleteModalIdCode] = useState();
    const [isLoading, setLoading] = useState(false);

    const handleCloseDeleteModal = () => {
        setDeleteModalIdCode();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdCode(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/settingsMetiers/codesBarreDelete',{
                idCode: deleteModalIdCode,
            });
            
            initTable();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* FORM FOR INTERNAL */
    const [showOffCanevasInternal, setShowOffCanevasInternal] = useState(false);
    const [offCanevasInternalIdMaterielCatalogue, setOffCanevasInternalIdMaterielCatalogue] = useState();
    const { register: registerInternal, handleSubmit: handleSubmitInternal, formState: { errors: errorsInternal }, setValue: setValueInternal, reset: resetInternal, watch: watchInternal } = useForm({
        resolver: yupResolver(codesBarreFormInterne),
    });
    const handleCloseOffCanevasInternal = () => {
        setShowOffCanevasInternal(false);
        resetInternal();
        setLoading(false);
        setOffCanevasInternalIdMaterielCatalogue();
    }
    const handleShowOffCanevasInternal = async (idCode) => {
        try {
            setLoading(true);
            setShowOffCanevasInternal(true);
            setOffCanevasInternalIdMaterielCatalogue(idCode);

            if(idCode > 0)
            {
                let oneElement = codesBarre.filter(code => code.idCode == idCode)[0];
                setValueInternal("codeBarre", oneElement.codeBarre);
                setValueInternal("internalReference", oneElement.internalReference);
                setValueInternal("peremptionConsommable", oneElement.peremptionConsommable && oneElement.peremptionConsommable != null ? new Date(oneElement.peremptionConsommable) : null);
                setValueInternal("commentairesCode", oneElement.commentairesCode);
                setValueInternal("idMaterielCatalogue", oneElement.idMaterielCatalogue);
            }else{
                setValueInternal("internalReference", true);
            }

            initTable();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* FORM FOR EXTERNAL */
    const [showOffCanevasExternal, setShowOffCanevasExternal] = useState(false);
    const [offCanevasExternalIdMaterielCatalogue, setOffCanevasExternalIdMaterielCatalogue] = useState();
    const { register: registerExternal, handleSubmit: handleSubmitExternal, formState: { errors: errorsExternal }, setValue: setValueExternal, reset: resetExternal, watch: watchExternal } = useForm({
        resolver: yupResolver(codesBarreFormFournisseur),
    });
    const handleCloseOffCanevasExternal = () => {
        setShowOffCanevasExternal(false);
        resetExternal();
        setLoading(false);
        setOffCanevasExternalIdMaterielCatalogue();
    }
    const handleShowOffCanevasExternal = async (idCode) => {
        try {
            setLoading(true);
            setShowOffCanevasExternal(true);
            setOffCanevasExternalIdMaterielCatalogue(idCode);

            if(idCode > 0)
            {
                let oneElement = codesBarre.filter(code => code.idCode == idCode)[0];
                setValueExternal("codeBarre", oneElement.codeBarre);
                setValueExternal("internalReference", oneElement.internalReference);
                setValueExternal("peremptionConsommable", oneElement.peremptionConsommable && oneElement.peremptionConsommable != null ? new Date(oneElement.peremptionConsommable) : null);
                setValueExternal("commentairesCode", oneElement.commentairesCode);
                setValueExternal("idMaterielCatalogue", oneElement.idMaterielCatalogue);
            }else{
                setValueExternal("internalReference", false);
            }

            initTable();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }


    const ajouterModifierCommun = async (data) => {
        try {
            setLoading(true);

            if(offCanevasExternalIdMaterielCatalogue > 0 || offCanevasInternalIdMaterielCatalogue > 0)    
            {
                const response = await Axios.post('/settingsMetiers/updateCodeBarres',{
                    idCode: offCanevasExternalIdMaterielCatalogue > 0 ? offCanevasExternalIdMaterielCatalogue : offCanevasInternalIdMaterielCatalogue,
                    codeBarre: data.codeBarre,
                    internalReference: data.internalReference,
                    peremptionConsommable: data.peremptionConsommable,
                    commentairesCode: data.commentairesCode,
                    idMaterielCatalogue: data.idMaterielCatalogue,
                });
            }
            else
            {
                const response = await Axios.post('/settingsMetiers/addCodeBarres',{
                    codeBarre: data.codeBarre,
                    internalReference: data.internalReference,
                    peremptionConsommable: data.peremptionConsommable,
                    commentairesCode: data.commentairesCode,
                    idMaterielCatalogue: data.idMaterielCatalogue,
                });
            }

            initTable();

            handleCloseOffCanevasExternal();
            handleCloseOffCanevasInternal();

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    /* RENDER */
    return (
        readyToDisplay ?
            <>
                <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
                    <Modal.Header>
                        <Modal.Title>Suppression</Modal.Title>
                        <FalconCloseButton onClick={handleCloseDeleteModal}/>
                    </Modal.Header>
                    <Modal.Body>
                        Attention, vous allez supprimer une entrée (id: {deleteModalIdCode}). Etes-vous certain de vouloir continuer ?
                    </Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={handleCloseDeleteModal}>
                            Annuler
                        </Button>
                        <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
                    </Modal.Footer>
                </Modal>

                <Offcanvas show={showOffCanevasExternal} onHide={handleCloseOffCanevasExternal} placement='end'>
                    <Offcanvas.Header closeButton >
                        <Offcanvas.Title>{offCanevasExternalIdMaterielCatalogue > 0 ? 'Modification' : 'Ajout'} d'un code barre fournisseur</Offcanvas.Title>
                    </Offcanvas.Header>
                    <Offcanvas.Body>
                        <Form onSubmit={handleSubmitExternal(ajouterModifierCommun)}>
                            <Form.Group className="mb-3">
                                <Form.Label>Matériel</Form.Label>
                                <Select
                                    id="idMaterielCatalogue"
                                    name="idMaterielCatalogue"
                                    size="sm"
                                    classNamePrefix="react-select"
                                    closeMenuOnSelect={true}
                                    isClearable={true}
                                    isSearchable={true}
                                    isDisabled={isLoading}
                                    placeholder='Aucun item selectionné'
                                    options={catalogue}
                                    value={catalogue.find(c => c.value === watchExternal("idMaterielCatalogue"))}
                                    onChange={val => val != null ? setValueExternal("idMaterielCatalogue", val.value) : setValueExternal("idMaterielCatalogue", null)}
                                />
                                <small className="text-danger">{errorsExternal.idMaterielCatalogue?.message}</small>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Code Barre</Form.Label>
                                <Form.Control size="sm" type="text" name='codeBarre' id='codeBarre' {...registerExternal('codeBarre')}/>
                                <small className="text-danger">{errorsExternal.codeBarre?.message}</small>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Commentaires</Form.Label>
                                <Form.Control size="sm" as="textarea" rows={3} name='commentairesCode' id='commentairesCode' {...registerExternal('commentairesCode')}/>
                                <small className="text-danger">{errorsExternal.commentairesCode?.message}</small>
                            </Form.Group>
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : offCanevasExternalIdMaterielCatalogue > 0 ? 'Modifier' : 'Ajouter'}</Button>
                        </Form>
                    </Offcanvas.Body>
                </Offcanvas>

                <Offcanvas show={showOffCanevasInternal} onHide={handleCloseOffCanevasInternal} placement='end'>
                    <Offcanvas.Header closeButton >
                        <Offcanvas.Title>{offCanevasInternalIdMaterielCatalogue > 0 ? 'Modification' : 'Ajout'} d'un code barre interne</Offcanvas.Title>
                    </Offcanvas.Header>
                    <Offcanvas.Body>
                        <Form onSubmit={handleSubmitInternal(ajouterModifierCommun)}>
                            <Form.Group className="mb-3">
                                <Form.Label>Matériel</Form.Label>
                                <Select
                                    id="idMaterielCatalogue"
                                    name="idMaterielCatalogue"
                                    size="sm"
                                    classNamePrefix="react-select"
                                    closeMenuOnSelect={true}
                                    isClearable={true}
                                    isSearchable={true}
                                    isDisabled={isLoading}
                                    placeholder='Aucun item selectionné'
                                    options={catalogue}
                                    value={catalogue.find(c => c.value === watchInternal("idMaterielCatalogue"))}
                                    onChange={val => val != null ? setValueInternal("idMaterielCatalogue", val.value) : setValueInternal("idMaterielCatalogue", null)}
                                />
                                <small className="text-danger">{errorsInternal.idMaterielCatalogue?.message}</small>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Date de péremption</Form.Label>
                                <DatePicker
                                    selected={watchInternal("peremptionConsommable")}
                                    onChange={(date)=>setValueInternal("peremptionConsommable", date)}
                                    formatWeekDay={day => day.slice(0, 3)}
                                    className='form-control'
                                    placeholderText="Choisir une date"
                                    dateFormat="dd/MM/yyyy"
                                    fixedHeight
                                    locale="fr"
                                />
                                <small className="text-danger">{errorsInternal.peremptionConsommable?.message}</small>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Commentaires</Form.Label>
                                <Form.Control size="sm" as="textarea" rows={3} name='commentairesCode' id='commentairesCode' {...registerInternal('commentairesCode')}/>
                                <small className="text-danger">{errorsInternal.commentairesCode?.message}</small>
                            </Form.Group>
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : offCanevasInternalIdMaterielCatalogue > 0 ? 'Modifier' : 'Ajouter'}</Button>
                        </Form>
                    </Offcanvas.Body>
                </Offcanvas>

                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title="Référentiel des codes barre"
                    >
                    </FalconComponentCard.Header>
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                    >
                        <GPMtable
                            columns={colonnes}
                            data={codesBarre}
                            topButtonShow={true}
                            topButton={
                                HabilitationService.habilitations['codeBarre_ajout'] ? <>
                                    <IconButton
                                        className='me-1'
                                        icon='plus'
                                        size = 'sm'
                                        variant="outline-success"
                                        onClick={()=>{handleShowOffCanevasInternal(0)}}
                                    >Nouvelle référence interne</IconButton>
                                    <IconButton
                                        className='me-1'
                                        icon='plus'
                                        size = 'sm'
                                        variant="outline-success"
                                        onClick={()=>{handleShowOffCanevasExternal(0)}}
                                    >Nouvelle référence fournisseur</IconButton>
                                </> : null
                            }
                        />
                    </FalconComponentCard.Body>
                </FalconComponentCard>
            </>
        :
            <FalconComponentCard noGuttersBottom className="mb-3">
                <FalconComponentCard.Header
                    title="Référentiel des codes barre"
                >
                </FalconComponentCard.Header>
                <FalconComponentCard.Body
                    scope={{ ActionButton }}
                    noLight
                    className="p-0"
                >
                    <LoaderInfiniteLoop />
                </FalconComponentCard.Body>
            </FalconComponentCard>
    );

};

CodesBarre.propTypes = {};

export default CodesBarre;