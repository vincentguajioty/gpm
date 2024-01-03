import React, { useState } from 'react';
import { Modal, Button, Form } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { contactDeveloppeur } from 'helpers/yupValidationSchema';

const ContactDev = () => {
    const topics = [
        {
            value: "Lots/Sacs/Emplacements",
            label: "Lots/Sacs/Emplacements",
        },
        {
            value: "Matériel opérationnel",
            label: "Matériel opérationnel",
        },
        {
            value: "Catalogue du matériel",
            label: "Catalogue du matériel",
        },
        {
            value: "Référentiels",
            label: "Référentiels",
        },
        {
            value: "Commandes",
            label: "Commandes",
        },
        {
            value: "Réserve",
            label: "Réserve",
        },
        {
            value: "Transferts de matériel",
            label: "Transferts de matériel",
        },
        {
            value: "Véhicules",
            label: "Véhicules",
        },
        {
            value: "Transmissions",
            label: "Transmissions",
        },
        {
            value: "Tenues",
            label: "Tenues",
        },
        {
            value: "Annuaire",
            label: "Annuaire",
        },
        {
            value: "Profils",
            label: "Profils",
        },
        {
            value: "Délégations",
            label: "Délégations",
        },
        {
            value: "Base documentaire",
            label: "Base documentaire",
        },
        {
            value: "ToDoList",
            label: "ToDoList",
        },
        {
            value: "Messages généraux",
            label: "Messages généraux",
        },
        {
            value: "Messages mails",
            label: "Messages mails",
        },
        {
            value: "Notifications",
            label: "Notifications",
        },
        {
            value: "Actions massives en base",
            label: "Actions massives en base",
        },
        {
            value: "Paramètres fonctionnels (catégories, états , lieux, fournisseurs ...)",
            label: "Paramètres fonctionnels (catégories, états , lieux, fournisseurs ...)",
        },
        {
            value: "Paramètres techniques de l'application",
            label: "Paramètres techniques de l'application",
        },
        {
            value: "Autre",
            label: "Autre",
        },
    ];
    
    const [showMailModal, setShowMailModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const handleCloseMailModal = () => {
        setShowMailModal(false);
        setLoading(false);
        reset();
    };
    const handleShowMailModal = () => {
        setShowMailModal(true);
        setValue("FO_Version", window.__ENV__.APP_VERSION);
    };

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(contactDeveloppeur),
    });

    const sendMail = async (data) => {
        try {
            setLoading(true);

            await Axios.post('contactDeveloppeur',{
                messageFromFront: data,
            })

            handleCloseMailModal();
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <IconButton
            size='sm'
            variant='outline-info'
            icon='envelope'
            onClick={handleShowMailModal}
        >ce formulaire</IconButton>

        <Modal show={showMailModal} onHide={handleCloseMailModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Contacter le developpeur de GPM</Modal.Title>
                <FalconCloseButton onClick={handleCloseMailModal}/>
            </Modal.Header>
            <Modal.Body>
                {isLoading ? 
                    <LoaderInfiniteLoop/>
                :
                    <Form onSubmit={handleSubmit(sendMail)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Catégorie</Form.Label>
                            <Select
                                id="topic"
                                name="topic"
                                size="sm"
                                classNamePrefix="react-select"
                                closeMenuOnSelect={true}
                                isClearable={true}
                                isSearchable={true}
                                isDisabled={isLoading}
                                placeholder='Aucun élément selectionné'
                                options={topics}
                                value={topics.find(c => c.value === watch("topic"))}
                                onChange={val => val != null ? setValue("topic", val.value) : setValue("topic", null)}
                            />
                            <small className="text-danger">{errors.topic?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Message</Form.Label>
                            <Form.Control size="sm" as="textarea" rows={10} name='message' id='message' {...register('message')}/>
                            <small className="text-danger">{errors.message?.message}</small>
                        </Form.Group>

                        <div className="d-grid gap-2 mt-3">
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Envoyer'}</Button>
                        </div>
                    </Form>
                }
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseMailModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>
    </>);
};

ContactDev.propTypes = {};

export default ContactDev;