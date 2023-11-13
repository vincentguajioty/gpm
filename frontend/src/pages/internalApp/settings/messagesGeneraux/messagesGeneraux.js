import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import { Alert } from 'react-bootstrap';
import nl2br from 'react-nl2br';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { messagesGenerauxForm } from 'helpers/yupValidationSchema';

const MessagesGeneraux = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [messages, setMessages] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/messagesGeneraux/getAllMessages');
            setMessages(getData.data);  
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {accessor: 'redacteur'   , Header: 'Rédacteur'},
        {accessor: 'corpsMessage', Header: 'Message'},
        {accessor: 'isPublic'    , Header: 'Public'},
        {accessor: 'actions'     , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of messages)
        {
            tempTable.push({
                redacteur: item.prenomPersonne + ' ' + item.nomPersonne,
                corpsMessage: <Alert variant={item.couleurMessageType}>{nl2br(item.corpsMessage)}</Alert>,
                isPublic: item.isPublic == true ? <SoftBadge bg='warning'>Public</SoftBadge> : <SoftBadge bg='secondary'>Privé</SoftBadge>,
                actions:
                    <>
                        {HabilitationService.habilitations['messages_ajout'] ?
                            <IconButton
                                icon='pen'
                                size = 'sm'
                                variant="warning"
                                className="me-1"
                                onClick={()=>{handleShowOffCanevas(item.idMessage)}}
                            />
                        : null}

                        {HabilitationService.habilitations['messages_suppression'] ?
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(item.idMessage)}}
                            />
                        : null}
                    </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [messages])

    //formulaire d'ajout
    const [typesMessages, setTypesMessages] = useState([]);
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdMessage, setOffCanevasIdMessage] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(messagesGenerauxForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdMessage();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdMessage(id);
        if(id>0)
        {
            let tempArray = messages.filter(mess => mess.idMessage == id);
            setValue('corpsMessage', tempArray[0].corpsMessage);
            setValue('idMessageType', tempArray[0].idMessageType);
            setValue('isPublic', tempArray[0].isPublic);
        }
        let result = await Axios.get('/select/getMessagesTypes');
        setTypesMessages(result.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdMessage > 0)
            {
                const response = await Axios.post('/messagesGeneraux/updateMessage',{
                    corpsMessage: data.corpsMessage,
                    idMessageType: data.idMessageType,
                    isPublic: data.isPublic || false,
                    idMessage: offCanevasIdMessage,
                });
            }else{
                const response = await Axios.post('/messagesGeneraux/addMessage',{
                    corpsMessage: data.corpsMessage,
                    idMessageType: data.idMessageType,
                    isPublic: data.isPublic || false,
                });
            }
            
            initPage();
            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdMessage, setDeleteModalIdMessage] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdMessage();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdMessage(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/messagesGeneraux/deleteMessage',{
                idMessage: deleteModalIdMessage,
            });
            
            initPage();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return (<>
        <PageHeader
            preTitle="Gestion d'équipe"
            title="Messages généraux"
            className="mb-3"
        />
        
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdMessage > 0 ? 'Modification d\'un' : 'Nouveau'} message général</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Type</Form.Label>
                        <Form.Select size="sm" name="idMessageType" id="idMessageType" {...register("idMessageType")}>
                            {typesMessages.map((item, i) => {
                                return (<option key={item.value} value={item.value}>{item.label}</option>);
                            })}
                        </Form.Select>
                        <small className="text-danger">{errors.idMessageType?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Message</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"corpsMessage"} id={"corpsMessage"} {...register("corpsMessage")}/>
                        <small className="text-danger">{errors.corpsMessage?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Message public</Form.Label>
                        <Form.Check
                            type='switch'
                            id="isPublic"
                            name="isPublic"
                            label='Afficher sur les pages non-authentifiées'
                            checked={watch("isPublic")}
                            onClick={(e)=>{setValue("isPublic", !watch("isPublic"))}}
                        />
                        <small className="text-danger">{errors.isPublic?.message}</small>
                    </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : offCanevasIdMessage > 0 ? 'Modifier' : 'Ajouter'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un message général (id: {deleteModalIdMessage}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
        
        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ?
                    <GPMtable
                        columns={colonnes}
                        data={lignes}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['messages_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={()=>{handleShowOffCanevas(0)}}
                                >Nouveau message</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

MessagesGeneraux.propTypes = {};

export default MessagesGeneraux;
