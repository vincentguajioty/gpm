import React, { useContext, useEffect, useRef, useState } from 'react';
import { Button, Card, Col, OverlayTrigger, Row, Tooltip, Modal } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import FullCalendar from '@fullcalendar/react';
import allLocales from '@fullcalendar/core/locales-all'
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import DropdownFilter from 'components/common/DropdownFilter';
import AppContext from 'context/Context';
import { Link } from 'react-router-dom';
import Flex from 'components/common/Flex';
import moment from 'moment-timezone';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const getCircleStackIcon = (icon, transform) => (
    <span className="fa-stack ms-n1 me-3">
        <FontAwesomeIcon icon="circle" className="text-200 fa-stack-2x" />
        <FontAwesomeIcon
            icon={icon}
            transform={transform ?? ''}
            className="text-primary fa-stack-1x"
            inverse
        />
    </span>
);
  
const EventModalMediaContent = ({ icon, heading, content, children }) => (
    <Flex className="mt-3">
        {getCircleStackIcon(icon)}
        <div className="flex-1">
            <h6>{heading}</h6>
            {children || <p className="mb-0 text-justify">{content}</p>}
        </div>
    </Flex>
);
  
const CalendarEventModal = ({
    setIsOpenEventModal,
    isOpenEventModal,
    modalEventContent
}) => {
    const {
        config: { isDark }
    } = useContext(AppContext);
  
    const handleClose = () => {
        setIsOpenEventModal(!isOpenEventModal);
    };
  
    const { title, end, start } = isOpenEventModal && modalEventContent.event;
    const { description, location, organizer, schedules, textForLink, linkTarget } = isOpenEventModal && modalEventContent.event.extendedProps;
  
    return (
        <Modal
            show={isOpenEventModal}
            onHide={handleClose}
            contentClassName="border"
            centered
        >
            <Modal.Header
                closeButton
                closeVariant={isDark ? 'white' : undefined}
                className="bg-light px-x1 border-bottom-0"
            >
                <Modal.Title>
                    <h5 className="mb-0">{title}</h5>
                    {organizer && (
                        <p className="mb-0 fs--1 mt-1 fw-normal">
                            par {organizer}
                        </p>
                    )}
                </Modal.Title>
            </Modal.Header>
            <Modal.Body className="px-x1 pb-x1 pt-1 fs--1">
                {description && (
                    <EventModalMediaContent
                        icon="align-left"
                        heading="Description"
                        content={description}
                    />
                )}
                {(end || start) && (
                    <EventModalMediaContent icon="calendar-check" heading="Date">
                        <p className="mb-1">
                            {moment(start).format('DD/MM/YYYY')}
                            {end && (' - '+ moment(end).format('DD/MM/YYYY'))}
                        </p>
                    </EventModalMediaContent>
                )}
                {location && (
                    <EventModalMediaContent icon="map-marker-alt" heading="Localisation">
                        <div
                            className="mb-1"
                            dangerouslySetInnerHTML={{ __html: location }}
                        />
                    </EventModalMediaContent>
                )}
                {schedules && (
                    <EventModalMediaContent icon="clock" heading="Schedule">
                        <ul className="list-unstyled timeline mb-0">
                            {schedules.map((schedule, index) => (
                                <li key={index}>{schedule.title}</li>
                            ))}
                        </ul>
                    </EventModalMediaContent>
                )}
            </Modal.Body>
            <Modal.Footer className="bg-light px-x1 border-top-0">
                {textForLink && textForLink != '' && textForLink != null ?
                    <Button
                        as={Link}
                        to={linkTarget}
                        variant="falcon-primary"
                        size="sm"
                    >
                        <span>{textForLink}</span>
                        <FontAwesomeIcon icon="angle-right" className="fs--2 ms-1" />
                    </Button>
                : null}
            </Modal.Footer>
        </Modal>
    );
  };

const CalendrierGeneral = ({
    pageNeedsRefresh              = false,
    peremptionsLots               = false,
    peremptionsReserves           = false,
    inventairesPassesLots         = false,
    inventairesPassesReserves     = false,
    inventairesFutursLots         = false,
    inventairesFutursReserves     = false,
    commandesLivraisons           = false,
    vehiculesMntPonctuelles       = false,
    vehiculesMntRegPassees        = false,
    vehiculesMntRegFutures        = false,
    vehiculesDesinfectionsPassees = false,
    vehiculesDesinfectionsFutures = false,
    tenuesAffectations            = false,
    tenuesRetours                 = false,
    cautionsEmissions             = false,
    cautionsExpirations           = false,
    toDoListOwn                   = false,
    toDoListAll                   = false,
}) => {
    const calendarRef = useRef();
    const [title, setTitle] = useState('');
    const [calendarApi, setCalendarApi] = useState({});
    const [currentFilter, setCurrentFilter] = useState('Vue mensuelle');
    const [isOpenEventModal, setIsOpenEventModal] = useState(false);
    const [modalEventContent, setModalEventContent] = useState({});
    const [events, setEvents] = useState([]);

    const getCalendarEvents = async () => {
        try {
            let getData;
            let tempArray = [];
            if(peremptionsLots == true && HabilitationService.habilitations.materiel_lecture)
            {
                getData = await Axios.get('/calendrier/peremptionsLots');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'peremptionsLots-'+item.idElement,
                        title: 'Peremption dans un lot opérationnel',
                        start: item.peremption,
                        description: item.libelleMateriel,
                        className: 'bg-soft-warning',
                        location: item.libelleLot+' > '+item.libelleSac+' > '+item.libelleEmplacement,
                        allDay: true,
                        textForLink: item.libelleLot,
                        linkTarget:'/lots/'+item.idLot,
                    })
                }
            }
            if(peremptionsReserves == true && HabilitationService.habilitations.reserve_lecture)
            {
                getData = await Axios.get('/calendrier/peremptionsReserves');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'peremptionsReserves-'+item.idReserveElement,
                        title: 'Peremption dans une réserve',
                        start: item.peremptionReserve,
                        description: item.libelleMateriel,
                        className: 'bg-soft-warning',
                        location: item.libelleConteneur,
                        allDay: true,
                        textForLink:item.libelleConteneur,
                        linkTarget:'/reservesConteneurs/'+item.idConteneur,
                    })
                }
            }
            if(inventairesPassesLots == true && HabilitationService.habilitations.lots_lecture)
            {
                getData = await Axios.get('/calendrier/inventairesPassesLots');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'inventairesPassesLots-'+item.idInventaire,
                        title: 'Inventaire de lot terminé',
                        start: item.dateInventaire,
                        description: item.commentairesInventaire,
                        className: 'bg-soft-success',
                        location: item.libelleLot,
                        organizer: item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink:'Accéder à l\'inventaire',
                        linkTarget:'/lots/'+item.idLot
                    })
                }
            }
            if(inventairesPassesReserves == true && HabilitationService.habilitations.reserve_lecture)
            {
                getData = await Axios.get('/calendrier/inventairesPassesReserves');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'inventairesPassesReserves-'+item.idReserveInventaire,
                        title: 'Inventaire de réserve terminé',
                        start: item.dateInventaire,
                        description: item.commentairesInventaire,
                        className: 'bg-soft-success',
                        location: item.libelleConteneur,
                        organizer: item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink:'Accéder à l\'inventaire',
                        linkTarget:'/reservesConteneurs/'+item.idConteneur,
                    })
                }
            }
            if(inventairesFutursLots == true && HabilitationService.habilitations.lots_lecture)
            {
                getData = await Axios.get('/calendrier/inventairesFutursLots');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'inventairesFutursLots-'+item.idLot,
                        title: 'Inventaire de lot à faire',
                        start: item.nextInventaire,
                        className: 'bg-soft-primary',
                        location: item.libelleLot,
                        allDay: true,
                        textForLink: item.libelleLot,
                        linkTarget:'/lots/'+item.idLot,
                    })
                }
            }
            if(inventairesFutursReserves == true && HabilitationService.habilitations.reserve_lecture)
            {
                getData = await Axios.get('/calendrier/inventairesFutursReserves');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'inventairesFutursReserves-'+item.idConteneur,
                        title: 'Inventaire de réserve à faire',
                        start: item.nextInventaire,
                        className: 'bg-soft-primary',
                        location: item.libelleConteneur,
                        allDay: true,
                        textForLink: item.libelleConteneur,
                        linkTarget:'/reservesConteneurs/'+item.idConteneur,
                    })
                }
            }
            if(commandesLivraisons == true && HabilitationService.habilitations.commande_lecture)
            {
                getData = await Axios.get('/calendrier/commandesLivraisons');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'commandesLivraisons-'+item.idCommande,
                        title: 'Livraison prévue d\'une commande',
                        start: item.dateLivraisonPrevue,
                        end: item.dateLivraisonPrevue,
                        description: item.nomCommande,
                        className: 'bg-soft-info',
                        location: item.libelleLieu,
                        organizer: item.nomFournisseur,
                        textForLink:'Accéder à la commande',
                        linkTarget:'/commandes/'+item.idCommande,
                    })
                }
            }
            if(vehiculesMntPonctuelles == true && HabilitationService.habilitations.vehicules_lecture)
            {
                getData = await Axios.get('/calendrier/vehiculesMntPonctuelles');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'vehiculesMntPonctuelles-'+item.idMaintenance,
                        title: 'Maintenance ponctuelle',
                        start: item.dateMaintenance,
                        description: item.detailsMaintenance,
                        className: 'bg-soft-success',
                        location: item.libelleVehicule,
                        organizer: item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink: item.libelleVehicule,
                        linkTarget:'/vehicules/'+item.idVehicule,
                    })
                }
            }
            if(vehiculesMntRegPassees == true && HabilitationService.habilitations.vehiculeHealth_lecture)
            {
                getData = await Axios.get('/calendrier/vehiculesMntRegPassees');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'vehiculesMntRegPassees-'+item.idVehiculeHealth,
                        title: 'Maintenance régulière terminée',
                        start: item.dateHealth,
                        description: <>{item.contenu.map((content, i)=>{return(content.libelleHealthType+'; ')})}</>,
                        className: 'bg-soft-success',
                        location: item.libelleVehicule,
                        organizer: item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink: item.libelleVehicule,
                        linkTarget:'/vehicules/'+item.idVehicule,
                    })
                }
            }
            if(vehiculesMntRegFutures == true && HabilitationService.habilitations.vehiculeHealth_lecture)
            {
                getData = await Axios.get('/calendrier/vehiculesMntRegFutures');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'vehiculesMntRegFutures-'+item.idHealthAlerte,
                        title: 'Maintenance régulière à faire',
                        start: item.nextHealth,
                        description: item.libelleHealthType,
                        className: 'bg-soft-primary',
                        location: item.libelleVehicule,
                        allDay: true,
                        textForLink: item.libelleVehicule,
                        linkTarget:'/vehicules/'+item.idVehicule,
                    })
                }
            }
            if(vehiculesDesinfectionsPassees == true && HabilitationService.habilitations.desinfections_lecture)
            {
                getData = await Axios.get('/calendrier/vehiculesDesinfectionsPassees');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'vehiculesDesinfectionsPassees-'+item.idVehiculesDesinfection,
                        title: 'Désinfection terminée',
                        start: item.dateDesinfection,
                        description: item.libelleVehiculesDesinfectionsType,
                        className: 'bg-soft-success',
                        location: item.libelleVehicule,
                        organizer: item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink: item.libelleVehicule,
                        linkTarget:'/vehicules/'+item.idVehicule,
                    })
                }
            }
            if(vehiculesDesinfectionsFutures == true && HabilitationService.habilitations.desinfections_lecture)
            {
                getData = await Axios.get('/calendrier/vehiculesDesinfectionsFutures');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'vehiculesDesinfectionsFutures-'+item.idDesinfectionsAlerte,
                        title: 'Désinfection à faire',
                        start: item.nextDesinfection,
                        description: item.libelleVehiculesDesinfectionsType,
                        className: 'bg-soft-primary',
                        location: item.libelleVehicule,
                        allDay: true,
                        textForLink: item.libelleVehicule,
                        linkTarget:'/vehicules/'+item.idVehicule,
                    })
                }
            }
            if(tenuesAffectations == true && HabilitationService.habilitations.tenues_lecture)
            {
                getData = await Axios.get('/calendrier/tenuesAffectations');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'tenuesAffectations-'+item.idTenue,
                        title: 'Affectation d\'une tenue',
                        start: item.dateAffectation,
                        description: item.libelleCatalogueTenue,
                        className: 'bg-soft-info',
                        location: item.personneNonGPM ? item.personneNonGPM : item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink:'Accès aux affectations',
                        linkTarget:'/tenuesAffectation'
                    })
                }
            }
            if(tenuesRetours == true && HabilitationService.habilitations.tenues_lecture)
            {
                getData = await Axios.get('/calendrier/tenuesRetours');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'tenuesRetours-'+item.idTenue,
                        title: 'Retour d\'une tenue',
                        start: item.dateRetour,
                        description: item.libelleCatalogueTenue,
                        className: 'bg-soft-info',
                        location: item.personneNonGPM ? item.personneNonGPM : item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink:'Accès aux affectations',
                        linkTarget:'/tenuesAffectation'
                    })
                }
            }
            if(cautionsEmissions == true && HabilitationService.habilitations.cautions_lecture)
            {
                getData = await Axios.get('/calendrier/cautionsEmissions');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'cautionsEmissions-'+item.idCaution,
                        title: 'Versement d\'une caution',
                        start: item.dateEmissionCaution,
                        description: item.detailsMoyenPaiement,
                        className: 'bg-soft-info',
                        location: item.personneNonGPM != null ? item.personneNonGPM : item.prenomPersonne != null ? item.prenomPersonne+' '+item.nomPersonne : "Aucune personne renseignée",
                        allDay: true,
                        textForLink:'Accès aux cautions',
                        linkTarget:'/tenuesCautions'
                    })
                }
            }
            if(cautionsExpirations == true && HabilitationService.habilitations.cautions_lecture)
            {
                getData = await Axios.get('/calendrier/cautionsExpirations');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'cautionsExpirations-'+item.idCaution,
                        title: 'Fin de validité d\'une caution',
                        start: item.dateExpirationCaution,
                        description: item.detailsMoyenPaiement,
                        className: 'bg-soft-info',
                        location: item.personneNonGPM != null ? item.personneNonGPM : item.prenomPersonne != null ? item.prenomPersonne+' '+item.nomPersonne : "Aucune personne renseignée",
                        allDay: true,
                        textForLink:'Accès aux cautions',
                        linkTarget:'/tenuesCautions'
                    })
                }
            }
            if(toDoListOwn == true && HabilitationService.habilitations.connexion_connexion)
            {
                getData = await Axios.get('/calendrier/toDoListOwn');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'toDoListOwn-'+item.idTache,
                        title: item.titre,
                        start: item.dateExecution,
                        description: item.details,
                        className: 'bg-soft-dark',
                        organizer: item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink:'Accéder aux ToDoList',
                        linkTarget:'/teamToDoList'
                    })
                }
            }
            if(toDoListAll == true && HabilitationService.habilitations.todolist_lecture)
            {
                getData = await Axios.get('/calendrier/toDoListAll');
                for(const item of getData.data)
                {
                    tempArray.push({
                        id: 'toDoListAll-'+item.idTache,
                        title: item.titre,
                        start: item.dateExecution,
                        description: item.details,
                        className: 'bg-soft-dark',
                        organizer: item.prenomPersonne+' '+item.nomPersonne,
                        allDay: true,
                        textForLink:'Accéder aux ToDoList',
                        linkTarget:'/teamToDoList'
                    })
                }
            }

            setEvents(tempArray);
        } catch (error) {
            console.log(error)
        }
    }
    useEffect(()=>{
        getCalendarEvents();
    },[])
    useEffect(()=>{
        if(pageNeedsRefresh == true){getCalendarEvents();}
    },[pageNeedsRefresh])

    const eventTimeFormat = {
        hour: 'numeric',
        minute: '2-digit',
        omitZeroMinute: true,
        meridiem: true
    };

    const handleEventClick = info => {
        if (info.event.url)
        {
            window.open(info.event.url);
            info.jsEvent.preventDefault();
        } else {
            setModalEventContent(info);
            setIsOpenEventModal(true);
        }
    };

    const viewName = [
        'Vue mensuelle',
        'Vue hebdomadaire',
        'Vue journalière',
        'Vue liste',
        'Vue annuelle'
    ];

    const handleFilter = filter => {
        setCurrentFilter(filter);
        switch (filter) {
            case 'Vue mensuelle':
                calendarApi.changeView('dayGridMonth');
                setTitle(calendarApi.getCurrentData().viewTitle);
            break;

            case 'Vue hebdomadaire':
                calendarApi.changeView('timeGridWeek');
                setTitle(calendarApi.getCurrentData().viewTitle);
            break;

            case 'Vue journalière':
                calendarApi.changeView('timeGridDay');
                setTitle(calendarApi.getCurrentData().viewTitle);
            break;

            case 'Vue liste':
                calendarApi.changeView('listWeek');
                setTitle(calendarApi.getCurrentData().viewTitle);
            break;

            default:
                calendarApi.changeView('listYear');
                setTitle(calendarApi.getCurrentData().viewTitle);
        }
    };

    useEffect(() => {
        setCalendarApi(calendarRef.current.getApi());
    }, []);

    return (
        <>
            <Card>
                <Card.Header>
                    <Row className="align-items-center gx-0">
                        <Col xs="auto" className="d-flex justify-content-end order-md-1">
                            <OverlayTrigger
                                placement="bottom"
                                overlay={
                                    <Tooltip style={{ position: 'fixed' }} id="nextTooltip">
                                        Précédent
                                    </Tooltip>
                                }
                            >
                                <Button
                                    variant="link"
                                    className="icon-item icon-item-sm icon-item-hover shadow-none p-0 me-1 ms-md-2"
                                    onClick={() => {
                                        calendarApi.prev();
                                        setTitle(calendarApi.getCurrentData().viewTitle);
                                    }}
                                >
                                    <FontAwesomeIcon icon="arrow-left" />
                                </Button>
                            </OverlayTrigger>
                            <OverlayTrigger
                                placement="bottom"
                                overlay={
                                    <Tooltip style={{ position: 'fixed' }} id="previousTooltip">
                                        Suivant
                                    </Tooltip>
                                }
                            >
                                <Button
                                    variant="link"
                                    className="icon-item icon-item-sm icon-item-hover shadow-none p-0 me-lg-2"
                                    onClick={() => {
                                        calendarApi.next();
                                        setTitle(calendarApi.getCurrentData().viewTitle);
                                    }}
                                >
                                    <FontAwesomeIcon icon="arrow-right" />
                                </Button>
                            </OverlayTrigger>
                        </Col>
                        <Col xs="auto" className="d-flex justify-content-end order-md-2">
                            <h4 className="mb-0 fs-0 fs-sm-1 fs-lg-2">
                                {title || `${calendarApi.currentDataManager?.data?.viewTitle}`}
                            </h4>
                        </Col>
                        <Col xs md="auto" className="d-flex justify-content-end order-md-3">
                            <Button
                                size="sm"
                                variant="falcon-primary"
                                onClick={() => {
                                    calendarApi.today();
                                    setTitle(calendarApi.getCurrentData().viewTitle);
                                }}
                            >
                                Aujourd'hui
                            </Button>
                        </Col>
                        <Col md="auto" className="d-md-none">
                            <hr />
                        </Col>
                        
                        <Col className="d-flex justify-content-end order-md-2">
                            <DropdownFilter
                                className="me-2"
                                filters={viewName}
                                currentFilter={currentFilter}
                                handleFilter={handleFilter}
                                icon="sort"
                                right
                            />
                        </Col>
                    </Row>
                </Card.Header>

                <Card.Body className="p-0">
                    <FullCalendar
                        ref={calendarRef}
                        headerToolbar={false}
                        plugins={[
                            dayGridPlugin,
                            timeGridPlugin,
                            interactionPlugin,
                            listPlugin
                        ]}
                        initialView="dayGridMonth"
                        locales={allLocales}
                        locale={'fr'}
                        themeSystem="bootstrap"
                        dayMaxEvents={2}
                        direction='ltr'
                        height={800}
                        stickyHeaderDates={false}
                        eventTimeFormat={eventTimeFormat}
                        eventClick={handleEventClick}
                        events={events}
                    />
                </Card.Body>
            </Card>

            <CalendarEventModal
                isOpenEventModal={isOpenEventModal}
                setIsOpenEventModal={setIsOpenEventModal}
                modalEventContent={modalEventContent}
            />
        </>
    );
};

export default CalendrierGeneral;
