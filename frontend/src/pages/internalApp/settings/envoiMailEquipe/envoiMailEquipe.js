import React, {useState, useEffect} from 'react';
import { Form, Col, Row, Button } from 'react-bootstrap';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';
import moment from 'moment-timezone';
import SoftBadge from 'components/common/SoftBadge';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { mailEquipeForm } from 'helpers/yupValidationSchema';

const EnvoiMailEquipe = () => {
    const [pageReadyToDisplay, setPageReadyToDisplay] = useState(false);
    const [destinatairesIndividuels, setDestinatairesIndividuels] = useState([]);
    const [destinatairesGroupes, setDestinatairesGroupes] = useState([]);

    const [mailHistorique, setMailHistorique] = useState([]);
    const translateMailTypeToLabel = (item) => {
        
        let finalLabel = null;
        switch(item.typeMail)
        {
            case 'autoResetPwd':
                finalLabel = <SoftBadge>Demande de réinitialisation #{item.idObject}</SoftBadge>
            break;

            case 'confirmationAlerteLot':
                finalLabel = <SoftBadge>Alerte de lot #{item.idObject}</SoftBadge>
            break;

            case 'alerteBenevolesLot':
                finalLabel = <SoftBadge>Alerte de lot #{item.idObject}</SoftBadge>
            break;

            case 'confirmationAlerteVehicule':
                finalLabel = <SoftBadge>Alerte de véhicule #{item.idObject}</SoftBadge>
            break;

            case 'alerteBenevolesVehicule':
                finalLabel = <SoftBadge>Alerte de véhicule #{item.idObject}</SoftBadge>
            break;

            case 'finDeclarationConso':
                finalLabel = <SoftBadge>Rapport de consommation #{item.idObject}</SoftBadge>
            break;

            default:
            break;
        }

        return finalLabel;
    }
    const colonnes = [
        {
            accessor: 'envoi',
            Header: 'Demande d\'envoi',
            Cell: ({ value, row }) => {
				return(
                    <>
                        <SoftBadge className='me-1 mb-1'>Demandé le {moment(row.original.sendRequest).format('DD/MM/YYYY HH:mm:ss')}</SoftBadge>
                        <br/>
                        {row.original.successSend ?
                            <SoftBadge className='me-1 mb-1' bg='success'>Envoi réussi le {moment(row.original.successSend).format('DD/MM/YYYY HH:mm:ss')}</SoftBadge>
                        :
                            row.original.abordSend ?
                                <SoftBadge className='me-1 mb-1' bg='danger'>Envoi échoué le {moment(row.original.abordSend).format('DD/MM/YYYY HH:mm:ss')}</SoftBadge>
                            :
                                <SoftBadge className='me-1 mb-1' bg='warning'>Envoi en cours</SoftBadge>
                        }
                    </>
                );
			},
        },
        {
            accessor: 'typeMail',
            Header: 'Type de mail',
        },
        {
            accessor: 'idPersonne',
            Header: 'Destinataire',
            Cell: ({ value, row }) => {
				return(row.original.idPersonne ? 'Utilisateur: '+row.original.identifiant : 'Externe: '+row.original.otherMail);
			},
        },
        {
            accessor: 'idObject',
            Header: 'Porte sur',
            Cell: ({ value, row }) => {
				return(translateMailTypeToLabel(row.original));
			},
        },
    ];

    const initPage = async () => {
        try {
            let getData = await Axios.get('/select/getPersonnesWithMail');
            setDestinatairesIndividuels(getData.data);
            getData = await Axios.get('/select/getProfils');
            setDestinatairesGroupes(getData.data);

            if(HabilitationService.habilitations.appli_conf)
            {
                getData = await Axios.get('/settingsTechniques/getMailQueue');
                setMailHistorique(getData.data);
            }

            setPageReadyToDisplay(true);

        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(mailEquipeForm),
    });

    const envoyerMail = async (data) => {
        try {
            setLoading(true);

            await Axios.post('/messagesGeneraux/messageMail',{
                data: data
            });

            setLoading(false);
            reset();
            initPage()
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <PageHeader
            preTitle="Gestion d'équipe"
            title="Envoi de mails"
            className="mb-3"
        />

        {pageReadyToDisplay ?
            <>
                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title='Rédiger un mail'
                    />
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                    >
                        <Form onSubmit={handleSubmit(envoyerMail)}>
                            <Row>
                                <Col md={6}>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Destinataires unitaires:</Form.Label>
                                        <Select
                                            id="idPersonne"
                                            name="idPersonne"
                                            size="sm"
                                            className='mb-2'
                                            closeMenuOnSelect={false}
                                            placeholder='Ajoutez des personnes'
                                            options={destinatairesIndividuels}
                                            isMulti
                                            classNamePrefix="react-select"
                                            value={watch("idPersonne")}
                                            onChange={selected => setValue("idPersonne", selected)}
                                        />
                                        <small className="text-danger">{errors.idPersonne?.message}</small>
                                    </Form.Group>
                                </Col>
                                <Col md={6}>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Destinataires par groupes:</Form.Label>
                                        <Select
                                            id="idProfil"
                                            name="idProfil"
                                            size="sm"
                                            className='mb-2'
                                            closeMenuOnSelect={false}
                                            placeholder='Ajoutez des groupes'
                                            options={destinatairesGroupes}
                                            isMulti
                                            classNamePrefix="react-select"
                                            value={watch("idProfil")}
                                            onChange={selected => setValue("idProfil", selected)}
                                        />
                                        <small className="text-danger">{errors.idProfil?.message}</small>
                                    </Form.Group>
                                </Col>
                            </Row>

                            <Form.Group className="mb-3">
                                <Form.Label>Sujet:</Form.Label>
                                <Form.Control size="sm" type="text" name="sujet" id="sujet" value={watch("sujet")} {...register("sujet")}/>
                                <small className="text-danger">{errors.sujet?.message}</small>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Message:</Form.Label>
                                <Form.Control size="sm" as="textarea" rows={15} name={"message"} id={"message"} {...register("message")}/>
                                <small className="text-danger">{errors.message?.message}</small>
                            </Form.Group>

                            <div className="d-grid gap-2 mt-3">
                                <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Envoyer'}</Button>
                            </div>
                        </Form>
                    </FalconComponentCard.Body>
                </FalconComponentCard>

                {HabilitationService.habilitations.appli_conf ?
                    <FalconComponentCard noGuttersBottom className="mb-3">
                        <FalconComponentCard.Header
                            title='1000 derniers mails'
                        />
                        <FalconComponentCard.Body
                            scope={{ ActionButton }}
                            noLight
                        >
                            <GPMtable
                                columns={colonnes}
                                data={mailHistorique}
                                topButtonShow={true}
                                topButton={
                                    <IconButton
                                        icon='recycle'
                                        size = 'sm'
                                        variant="outline-success"
                                        onClick={initPage}
                                    >Rafraichir</IconButton>
                                }
                            />
                        </FalconComponentCard.Body>
                </FalconComponentCard>
                : null}
            </>
        : <LoaderInfiniteLoop/>}
    </>);
};

EnvoiMailEquipe.propTypes = {};

export default EnvoiMailEquipe;
