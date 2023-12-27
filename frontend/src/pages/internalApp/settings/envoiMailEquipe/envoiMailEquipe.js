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

    const colonnes = [
        {accessor: 'envoi'      , Header: 'Demande d\'envoi'},
        {accessor: 'typeMail'   , Header: 'Type de mail'},
        {accessor: 'idPersonne' , Header: 'Destinataire'},
        {accessor: 'idObject'   , Header: 'Porte sur'},
    ];
    const [lignes, setLignes] = useState([]);

    const initPage = async () => {
        try {
            let getData = await Axios.get('/select/getPersonnesWithMail');
            setDestinatairesIndividuels(getData.data);
            getData = await Axios.get('/select/getProfils');
            setDestinatairesGroupes(getData.data);

            if(HabilitationService.habilitations.appli_conf)
            {
                getData = await Axios.get('/settingsTechniques/getMailQueue');
                let tempTable  = [];
                for(const item of getData.data)
                {
                    let idObject = null;
                    switch(item.typeMail)
                    {
                        case 'autoResetPwd':
                            idObject = <SoftBadge>Demande de réinitialisation #{item.idObject}</SoftBadge>
                        break;

                        case 'confirmationAlerteLot':
                            idObject = <SoftBadge>Alerte de lot #{item.idObject}</SoftBadge>
                        break;

                        case 'alerteBenevolesLot':
                            idObject = <SoftBadge>Alerte de lot #{item.idObject}</SoftBadge>
                        break;

                        case 'confirmationAlerteVehicule':
                            idObject = <SoftBadge>Alerte de véhicule #{item.idObject}</SoftBadge>
                        break;

                        case 'alerteBenevolesVehicule':
                            idObject = <SoftBadge>Alerte de véhicule #{item.idObject}</SoftBadge>
                        break;

                        case 'finDeclarationConso':
                            idObject = <SoftBadge>Rapport de consommation #{item.idObject}</SoftBadge>
                        break;

                        default:
                        break;
                    }

                    tempTable.push({
                        envoi:<>
                            <SoftBadge className='me-1 mb-1'>Demandé le {moment(item.sendRequest).format('DD/MM/YYYY HH:mm:ss')}</SoftBadge>
                            <br/>
                            {item.successSend ?
                                <SoftBadge className='me-1 mb-1' bg='success'>Envoi réussi le {moment(item.successSend).format('DD/MM/YYYY HH:mm:ss')}</SoftBadge>
                            :
                                item.abordSend ?
                                    <SoftBadge className='me-1 mb-1' bg='danger'>Envoi échoué le {moment(item.abordSend).format('DD/MM/YYYY HH:mm:ss')}</SoftBadge>
                                :
                                    <SoftBadge className='me-1 mb-1' bg='warning'>Envoi en cours</SoftBadge>
                            }
                        </>,
                        typeMail: item.typeMail,
                        idPersonne: item.idPersonne ? 'Utilisateur: '+item.identifiant : 'Externe: '+item.otherMail,
                        idObject: idObject,
                    });
                }
                setLignes(tempTable);
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
                                data={lignes}
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
