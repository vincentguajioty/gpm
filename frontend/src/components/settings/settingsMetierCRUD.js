import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';

const SettingsMetierCRUD = ({parametre}) => {
    
    /*PAGE BASICS*/
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [dataArray, setDataArray] = useState([]);

    const initTable = async () => {
        try {
            const getFromDb = await Axios.get(parametre.boGetRoute);
            setDataArray(getFromDb.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
		initTable();
	}, [])

    /* FORM */
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasId, setOffCanevasId] = useState();
    const [isLoading, setLoading] = useState(false);
    const validationSchema = require('helpers/yupValidationSchema')[parametre.yupSchema];
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(validationSchema),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasId();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        try {
            setOffCanevasId(id);

            if(id > 0)
            {
                let oneItemFromArray = dataArray.filter(ligne => ligne[parametre.idAccessorForDb] == id)[0];
                for(const property of parametre.fields)
                {
                    if(property.showInForm){setValue(property.dbName, oneItemFromArray[property.dbName]);}
                }
            }

            setShowOffCanevas(true);
        } catch (error) {
            console.error(error)
        }
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            let dataToPost = {};
            for(const property of parametre.fields)
            {
                if(property.showInForm){
                    dataToPost = {...dataToPost, ...{[property.dbName]: watch(property.dbName)}}
                }
            }

            if(offCanevasId > 0)    
            {
                dataToPost = {...dataToPost, ...{[parametre.idAccessorForDb]: offCanevasId}}
                const response = await Axios.post(parametre.boUpdateRoute,dataToPost);
            }
            else
            {
                const response = await Axios.post(parametre.boAddRoute,dataToPost);
            }

            handleCloseOffCanevas();
            initTable();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalid, setDeleteModalid] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalid();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalid(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post(parametre.boDeleteRoute,{
                [parametre.idAccessorForDb]: deleteModalid,
            });
            
            initTable();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* RENDER */
    const nl2br = require('react-nl2br');
    return (
        readyToDisplay ?
            <>
                <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
                    <Modal.Header>
                        <Modal.Title>Suppression</Modal.Title>
                        <FalconCloseButton onClick={handleCloseDeleteModal}/>
                    </Modal.Header>
                    <Modal.Body>
                        Attention, vous allez supprimer une entrée (id: {deleteModalid}). Etes-vous certain de vouloir continuer ?
                    </Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={handleCloseDeleteModal}>
                            Annuler
                        </Button>
                        <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
                    </Modal.Footer>
                </Modal>

                <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
                    <Offcanvas.Header closeButton >
                        <Offcanvas.Title>{offCanevasId > 0 ? "Modification" : "Ajout"} d'un paramètre</Offcanvas.Title>
                    </Offcanvas.Header>
                    <Offcanvas.Body>
                        <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                            {parametre.fields.filter(column => column.showInForm == true).map((column, i) => {return(
                                <Form.Group className="mb-3">
                                    <Form.Label>{column.displayName}</Form.Label>
                                    {column.type == "text" ?
                                        <Form.Control size="sm" type="text" name={column.dbName} id={column.dbName} {...register(column.dbName)}/>
                                    :null }
                                    {column.type == "textLong" ?
                                        <Form.Control size="sm" as="textarea" rows={3} name={column.dbName} id={column.dbName} {...register(column.dbName)}/>
                                    :null }
                                    {column.type == "int" ?
                                        <Form.Control size="sm" type="number" step="1" name={column.dbName} id={column.dbName} {...register(column.dbName)}/>
                                    :null }
                                    {column.type == "boolean" ?
                                        <Form.Check
                                            type='switch'
                                            id={column.dbName}
                                            name={column.dbName}
                                            label='Oui/Non'
                                            checked={watch(column.dbName)}
                                            onClick={(e)=>{setValue(column.dbName, !watch(column.dbName))}}
                                        />
                                    :null }
                                    <small className="text-danger">{errors[column.dbName]?.message}</small>
                                </Form.Group>
                            )})}
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                        </Form>
                    </Offcanvas.Body>
                </Offcanvas>

                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title={parametre.cardTitle}
                    >
                    </FalconComponentCard.Header>
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                        className="p-0"
                    >
                        <SimpleBarReact style={{ height: '26rem' }}>
                            <Table size='sm' responsive>
                                <thead>
                                    <tr>
                                        {parametre.fields.filter(column => column.showInTable == true).map((column, i) => {return(
                                            <th scope="col">{column.displayName}</th>
                                        )})}
                                        <th className="text-end" scope="col">
                                            {HabilitationService.habilitations[parametre.profilAdd] ? 
                                                <ActionButton onClick={() => handleShowOffCanevas(0)} icon="plus" title="Ajouter" variant="action" className="p-0 me-2" />
                                            : null }
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {dataArray.map((data, i) => {
                                        return(
                                            <tr>
                                                {parametre.fields.filter(column => column.showInTable == true).map((column, i) => {return(
                                                    <td>
                                                        {column.type == "text" ?
                                                            data[column.dbName]
                                                        :null }
                                                        {column.type == "textLong" ?
                                                            nl2br(data[column.dbName])
                                                        :null }
                                                        {column.type == "int" ?
                                                            data[column.dbName]
                                                        :null }
                                                        {column.type == "boolean" ?
                                                            data[column.dbName] ? <FontAwesomeIcon icon="check" /> : null
                                                        :null }
                                                    </td>
                                                )})}
                                                <td className="text-end">
                                                    {HabilitationService.habilitations[parametre.profilUpdate] ? 
                                                        <ActionButton onClick={() => handleShowOffCanevas(data[parametre.idAccessorForDb])} icon="pen" title="Modifier" variant="action" className="p-0 me-2" />
                                                    : null }
                                                    {HabilitationService.habilitations[parametre.profilDelete] ? 
                                                        <ActionButton onClick={() => handleShowDeleteModal(data[parametre.idAccessorForDb])} icon="trash" title="Supprimer" variant="action" className="p-0" />
                                                    : null }
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </Table>
                        </SimpleBarReact>

                    </FalconComponentCard.Body>
                </FalconComponentCard>
            </>
        :
            <FalconComponentCard noGuttersBottom className="mb-3">
                <FalconComponentCard.Header
                    title={parametre.cardTitle}
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

SettingsMetierCRUD.propTypes = {};

export default SettingsMetierCRUD;
