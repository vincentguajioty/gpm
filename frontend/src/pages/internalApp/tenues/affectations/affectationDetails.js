import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Table, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import Select from 'react-select';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { tenuesAffectationsDelete, tenuesAffectationsDefinitiveDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { affectationsTenuesForm } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');


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
                    {HabilitationService.habilitations['tenues_modification'] ? 
                        <IconButton
                            icon='pen'
                            size = 'sm'
                            variant="outline-warning"
                            className="me-1"
                            onClick={()=>{handleShowOffCanevas(value)}}
                        />
                    : null}
                    {HabilitationService.habilitations['tenues_suppression'] ? 
                        <IconButton
                            icon='recycle'
                            size = 'sm'
                            variant="outline-primary"
                            className="me-1"
                            onClick={()=>{handleShowDeleteModal(value)}}
                        />
                    : null}
                    {HabilitationService.habilitations['tenues_suppression'] ? 
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{handleShowDefinitiveDeleteModal(value)}}
                        />
                    : null}
                </>);
			},
        },
    ];


    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdTenue, setOffCanevasIdTenue] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(affectationsTenuesForm),
    });
    
    const [faculteNotification, setFaculteNotification] = useState(false);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdTenue();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdTenue(id);

        if(id > 0)
        {
            let oneItemFromArray = affectationsRow.filter(ligne => ligne.idTenue == id)[0];
            setValue("idMaterielCatalogueInitial", oneItemFromArray.idMaterielCatalogue);
            setValue("idMaterielCatalogue", oneItemFromArray.idMaterielCatalogue);
            setValue("nomPrenomExterne", oneItemFromArray.nomPrenomExterne);
            setValue("mailExterne", oneItemFromArray.mailExterne);
            setValue("idExterne", oneItemFromArray.idExterne);
            setValue('personneConnue', true);
            setValue("dateAffectation", oneItemFromArray.dateAffectation != null ? new Date(oneItemFromArray.dateAffectation) : null);
            setValue("dateRetour", oneItemFromArray.dateRetour != null ? new Date(oneItemFromArray.dateRetour) : null);
            setValue("notifPersonne", oneItemFromArray.notifPersonne);
        }
        else
        {
            setValue("dateAffectation", new Date());
            setValue('personneConnue', false);
        }

        verifFaculteNotifications();

        setValue("initialLoadOver", true);
        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdTenue > 0)    
            {
                const response = await Axios.post('/tenues/updateAffectations',{
                    idTenue: offCanevasIdTenue,
                    idMaterielCatalogueInitial: data.idMaterielCatalogueInitial,
                    idMaterielCatalogue: data.idMaterielCatalogue,
                    nomPrenomExterne: data.nomPrenomExterne,
                    mailExterne: data.mailExterne,
                    idExterne: data.idExterne,
                    dateAffectation: data.dateAffectation,
                    dateRetour: data.dateRetour,
                    notifPersonne: data.notifPersonne,
                });
            }
            else
            {
                const response = await Axios.post('/tenues/addAffectations',{
                    idMaterielCatalogue: data.idMaterielCatalogue,
                    nomPrenomExterne: data.nomPrenomExterne,
                    mailExterne: data.mailExterne,
                    idExterne: data.idExterne,
                    dateAffectation: data.dateAffectation,
                    dateRetour: data.dateRetour,
                    notifPersonne: data.notifPersonne,
                });
            }

            handleCloseOffCanevas();
            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    const verifFaculteNotifications = () => {
        try {
            if((watch('dateRetour') != null && watch('dateRetour') != "") && ((watch('mailExterne') != null && watch('mailExterne') != "")))
            {
                setFaculteNotification(true);
                if(offCanevasIdTenue == 0) {setValue("notifPersonne", true)}
            }else{
                setFaculteNotification(false);
                setValue("notifPersonne", false)
            }
        } catch (error) {
            console.error(error)
        }
    }

    useEffect(()=>{
        if(watch('initialLoadOver') && watch('initialLoadOver') == true)
        {
            if(watch('idExterne') && watch('idExterne') != null && watch('idExterne') != 0)
            {
                let personneSelectionnee = personnesExternes.filter(perso => perso.idExterne == watch('idExterne'));
                setValue('nomPrenomExterne', personneSelectionnee[0].nomPrenomExterne || null);
                setValue('mailExterne', personneSelectionnee[0].mailExterne || null);
                setValue('personneConnue', true);
            }else{
                setValue('personneConnue', false);
                setValue('idExterne', null);
                setValue('nomPrenomExterne', null);
                setValue('mailExterne', null);
            }
        }
    },[watch('idExterne')])

    useEffect(()=>{
        if(watch('initialLoadOver') && watch('initialLoadOver') == true)
        {
            if(watch('nomPrenomExterne') && watch('nomPrenomExterne') != null && watch('nomPrenomExterne') != "")
            {
                let personnesChercheesSurNom = personnesExternes.filter(perso => perso.nomPrenomExterne == watch('nomPrenomExterne'));
                if(personnesChercheesSurNom.length > 0)
                {
                    let personnesChercheesSurMail = personnesChercheesSurNom.filter(perso => perso.mailExterne == watch('mailExterne'));
                    if(personnesChercheesSurMail.length == 1)
                    {
                        setValue('idExterne', personnesChercheesSurMail[0].idExterne);
                    }else{
                        setValue('idExterne', null);
                    }
                }else{
                    setValue('idExterne', null);
                }
            }else{
                setValue('idExterne', null);
                setValue('mailExterne', null);
            }
        }
    },[
        watch('nomPrenomExterne'),
        watch('mailExterne'),
    ])

    useEffect(()=>{
        verifFaculteNotifications();
    },[
        watch('dateRetour'),
        watch('mailExterne'),
    ])

    /* DELETE avec reIntégration */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdTenue, setDeleteModalIdTenue] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdTenue();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdTenue(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteAffectations',{
                idTenue: deleteModalIdTenue,
                reintegration: true,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* DELETE définitive */
    const [showDefinitiveDeleteModal, setShowDefinitiveDeleteModal] = useState(false);
    const [definitivedeleteModalIdTenue, setDefinitiveDeleteModalIdTenue] = useState();

    const handleCloseDefinitiveDeleteModal = () => {
        setDefinitiveDeleteModalIdTenue();
        setShowDefinitiveDeleteModal(false);
        setLoading(false);
    };
    const handleShowDefinitiveDeleteModal = (id) => {
        setDefinitiveDeleteModalIdTenue(id);
        setShowDefinitiveDeleteModal(true);
    };

    const supprimerDefinitivementEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteAffectations',{
                idTenue: definitivedeleteModalIdTenue,
                reintegration: false,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDefinitiveDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    //Actions massives - Plannifier date de retour
    const [showRetourMassifModal, setShowRetourMassifModal] = useState(false);
    const [datePourRetourMassif, setDatePourRetourMassif] = useState(new Date());

    const handleCloseRetourMassifModal = () => {
        setShowRetourMassifModal(false);
        setLoading(false);
    };
    const handleShowRetourMassifModal = () => {
        setShowRetourMassifModal(true);
    };

    const plannifierRetourMassifTenue = async () => {
        try {
            setLoading(true);

            if(datePourRetourMassif && datePourRetourMassif!=null)
            {
                const response = await Axios.post('/tenues/plannifierRetourMassifTenue',{
                    idExterne: displayIdExterne || null,
                    dateRetour: datePourRetourMassif,
                });
            }
            
            setPageNeedsRefresh(true);
            handleCloseRetourMassifModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* Actions massives - DELETE avec reIntégration */
    const [showDeleteMassifModal, setShowDeleteMassifModal] = useState(false);

    const handleCloseDeleteMassifModal = () => {
        setShowDeleteMassifModal(false);
        setLoading(false);
    };
    const handleShowDeleteMassifModal = () => {
        setShowDeleteMassifModal(true);
    };

    const supprimerMassivementEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteMassifAffectations',{
                idExterne: displayIdExterne || null,
            });
            
            setPageNeedsRefresh(true);
            handleCloseDeleteMassifModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }
    
    //Export
    const requestExport = async () => {
        try {
            setLoading(true);

            let fileRequest = await Axios.get('/tenues/exporterAffectations');

            let documentData = await Axios.post('getSecureFile/temp',
            {
                fileName: fileRequest.data.fileName,
            },
            {
                responseType: 'blob'
            });
            
            // create file link in browser's memory
            const href = URL.createObjectURL(documentData.data);
            
            // create "a" HTML element with href to file & click
            const link = document.createElement('a');
            link.href = href;
            link.setAttribute('download', fileRequest.data.fileName); //or any other extension
            document.body.appendChild(link);
            link.click();

            // clean up "a" element & remove ObjectURL
            document.body.removeChild(link);
            URL.revokeObjectURL(href);
            
            setDownloadGenerated(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return(<>
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdTenue > 0 ? "Modification" : "Ajout"} d'une affectation de tenue</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Element de tenue</Form.Label>
                        <Select
                            id="idMaterielCatalogue"
                            name="idMaterielCatalogue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun élément selectionné'
                            options={catalogue}
                            value={catalogue.find(c => c.value === watch("idMaterielCatalogue"))}
                            onChange={val => val != null ? setValue("idMaterielCatalogue", val.value) : setValue("idMaterielCatalogue", null)}
                        />
                        <small className="text-danger">{errors.idMaterielCatalogue?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Affecté à <SoftBadge bg={watch('personneConnue') ? "success":"warning"}>{watch('personneConnue') ? "Personne connue":"Inconnu"}</SoftBadge></Form.Label>
                        <Select
                            id="idExterne"
                            name="idExterne"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucune personne connue selectionnée'
                            options={personnesExternes}
                            value={personnesExternes.find(c => c.value === watch("idExterne"))}
                            onChange={val => val != null ? setValue("idExterne", val.value) : setValue("idExterne", null)}
                        />
                        <small className="text-danger">{errors.idExterne?.message}</small>

                        <Form.Control size="sm" type="text" name='nomPrenomExterne' id='nomPrenomExterne' {...register('nomPrenomExterne')} placeholder='NOM Prénom'/>
                        <small className="text-danger">{errors.nomPrenomExterne?.message}</small>
                        <Form.Control size="sm" type="text" name='mailExterne' id='mailExterne' {...register('mailExterne')} placeholder='Mail'/>
                        <small className="text-danger">{errors.mailExterne?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date d'affectation</Form.Label>
                        <DatePicker
                            selected={watch("dateAffectation")}
                            onChange={(date)=>setValue("dateAffectation", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateAffectation?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Date prévisionnelle de retour</Form.Label>
                        <DatePicker
                            selected={watch("dateRetour")}
                            onChange={(date)=>setValue("dateRetour", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateRetour?.message}</small>

                        <Form.Check
                            id='notifPersonne'
                            name='notifPersonne'
                            label="Notification régulière à l'utilisateur"
                            type='switch'
                            checked={watch("notifPersonne")}
                            onClick={(e)=>{
                                setValue("notifPersonne", !watch("notifPersonne"))
                            }}
                            disabled={!faculteNotification}
                        />
                        <small className="text-danger">{errors.notifPersonne?.message}</small>
                    </Form.Group>
                    
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{tenuesAffectationsDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showDefinitiveDeleteModal} onHide={handleCloseDefinitiveDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDefinitiveDeleteModal}/>
            </Modal.Header>
            <Modal.Body>{tenuesAffectationsDefinitiveDelete}</Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDefinitiveDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerDefinitivementEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showRetourMassifModal} onHide={handleCloseRetourMassifModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Prévoir une date de retour générale</Modal.Title>
                <FalconCloseButton onClick={handleCloseRetourMassifModal}/>
            </Modal.Header>
            <Modal.Body>
                Cette personne vous a communiqué une date à laquelle elle souhaite vous retourner tous les éléments de tenues ? Saisissez la date ci-dessous, elle sera appliquée comme date de retour à tous les éléments de tenue, et l'option de notification par email sera activée pour relancer régulièrement cette personne.
                <center>
                    <Form.Group className="mt-3 mb-3">
                        <Form.Label>Date de retour</Form.Label><br/>
                        <DatePicker
                            selected={datePourRetourMassif}
                            onChange={(date)=>setDatePourRetourMassif(date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                    </Form.Group>
                </center>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseRetourMassifModal}>
                    Annuler
                </Button>
                <Button variant='warning' onClick={plannifierRetourMassifTenue} disabled={isLoading || datePourRetourMassif == null}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showDeleteMassifModal} onHide={handleCloseDeleteMassifModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression massive</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteMassifModal}/>
            </Modal.Header>
            <Modal.Body>
                La personne vous a retourné l'intégralité des éléments de tenue qui lui étaient affectés. Tous les éléments vont être réintégrés au stock.
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteMassifModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerMassivementEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
        
        {!displayBoxWithDetails ? <>
            <FalconComponentCard noGuttersBottom className="mb-3 mt-3">
                <FalconComponentCard.Body
                    scope={{ ActionButton }}
                    noLight
                >
                    {HabilitationService.habilitations['tenues_ajout'] ?
                        <center>
                            <IconButton
                                icon='plus'
                                size = 'sm'
                                variant="success"
                                onClick={()=>{handleShowOffCanevas(0)}}
                                className="mb-1"
                            >Nouvelle affectation de tenue</IconButton>
                        </center>
                    : null}

                    {HabilitationService.habilitations['tenues_lecture'] ?
                        <center>
                            <IconButton
                                icon='download'
                                size = 'sm'
                                variant="outline-info"
                                onClick={requestExport}
                                className='ms-1'
                                disabled={isLoading}
                            >{isLoading ? "Génération en cours" : "Télécharger un état des lieux complet"}</IconButton>
                        </center>
                    : null}

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
                            isDisabled={isLoading}
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
                                    <IconButton
                                        icon='calendar-day'
                                        size = 'sm'
                                        variant="outline-warning"
                                        className="me-1"
                                        onClick={handleShowRetourMassifModal}
                                    >Prévoir une date de retour générale</IconButton>
                                : null}
                                {HabilitationService.habilitations['tenues_suppression'] ? 
                                    <IconButton
                                        icon='recycle'
                                        size = 'sm'
                                        variant="outline-primary"
                                        className="me-1"
                                        onClick={handleShowDeleteMassifModal}
                                    >Le bénévole a tout rendu</IconButton>
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