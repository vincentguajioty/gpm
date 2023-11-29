import React, { useEffect, useState } from 'react';
import { ButtonGroup, ToggleButton, Row, Col, Form, Button, Modal } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import MaterielsTable from '../materiels/materielsTable';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { emplacementsFormSchema } from 'helpers/yupValidationSchema';

const SacsContent = ({
    idSac,
    lockIdSac = null,
    inventaireEnCours = false,
}) => {
    const [idEmplacement, setIdEmplacement] = useState();
    const [emplacements, setEmplacements] = useState([]);
    const [sacs, setSacs] = useState([]);

    const [isLoading, setLoading] = useState(true);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(emplacementsFormSchema),
    });

    
    const getEmplacements = async () => {
        try {
            const getData = await Axios.post('/sacs/getEmplacementsOneSac',{
                idSac: idSac
            });
            setEmplacements(getData.data);

            let getForForm = await Axios.get('/select/getSacsFull');
            setSacs(getForForm.data);
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        getEmplacements();
    },[])

    useEffect(()=>{
        if(idEmplacement == 0 || idEmplacement == null)
        {
            reset();
            setValue("idSac", idSac);
        }else{
            let oneItemFromArray = emplacements.filter(emp => emp.idEmplacement == idEmplacement);
            oneItemFromArray = oneItemFromArray[0];
            setValue("libelleEmplacement", oneItemFromArray.libelleEmplacement);
            setValue("idSac", oneItemFromArray.idSac);
        }

        if(lockIdSac > 0)
        {
            setValue("idSac", lockIdSac);
        }
    },[idEmplacement])

    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(idEmplacement > 0)    
            {
                const response = await Axios.post('/sacs/updateEmplacement',{
                    idEmplacement: idEmplacement,
                    libelleEmplacement: data.libelleEmplacement,
                    idSac: data.idSac,
                });
                await getEmplacements();
            }
            else
            {
                const response = await Axios.post('/sacs/addEmplacement',{
                    libelleEmplacement: data.libelleEmplacement,
                    idSac: idSac,
                });

                await getEmplacements();
                setIdEmplacement(response.data.idEmplacement);
            }

            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

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

            const response = await Axios.post('/sacs/emplacementDelete',{
                idEmplacement: idEmplacement,
            });
            
            setIdEmplacement();
            reset();
            getEmplacements();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return (
    <>
        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>Suppression</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un emplacement (id: {idEmplacement}) et détacher tous ses matériels. Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>
        
        <Row>
            <Col md="12" className="mb-2">
                {isLoading ? <LoaderInfiniteLoop/> : 
                    <ButtonGroup>
                        {emplacements.map((empl, idx) => (
                            <ToggleButton
                                key={empl.idEmplacement}
                                id={`radio-${idx+1}`}
                                type="radio"
                                variant='outline-info'
                                name="radio"
                                value={idEmplacement}
                                checked={idEmplacement === empl.idEmplacement}
                                onChange={(e) => {setIdEmplacement(empl.idEmplacement)}}
                                size='sm'
                            >
                                {empl.libelleEmplacement}
                            </ToggleButton>
                        ))}
                        {idEmplacement != 0 ?
                            <ToggleButton
                                key="add"
                                id={`radio-0`}
                                type="radio"
                                variant='outline-info'
                                name="radio"
                                value="0"
                                checked={idEmplacement === 0}
                                onChange={(e) => {setIdEmplacement(0)}}
                                size='sm'
                                disabled={inventaireEnCours || !HabilitationService.habilitations['sac_ajout']}
                            >
                                <FontAwesomeIcon icon="plus" />
                            </ToggleButton>
                        : null }
                    </ButtonGroup>
                }
            </Col>
            <Col md="12" className="mb-2">
                {idEmplacement > 0 ? <h5>Propriétés de l'emplacement:</h5> : null}
                {idEmplacement == 0 ? <h5>Créer un emplacement:</h5> : null}
                {idEmplacement != null && HabilitationService.habilitations['sac_modification'] && !inventaireEnCours ? 
                    <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                        <Row className="align-items-center g-3">
                            <Col xs="auto">
                                <Form.Control size="sm" type="text" name='libelleEmplacement' id='libelleEmplacement' {...register('libelleEmplacement')} placeholder="Libellé de l'emplacement"/>
                                <small className="text-danger">{errors.libelleEmplacement?.message}</small>
                            </Col>
                            <Col xs="auto">
                                <Select
                                    id="idSac"
                                    name="idSac"
                                    size="sm"
                                    classNamePrefix="react-select"
                                    closeMenuOnSelect={true}
                                    isClearable={true}
                                    isSearchable={true}
                                    isDisabled={isLoading || lockIdSac > 0}
                                    placeholder='Aucun sac selectionné'
                                    options={sacs}
                                    isOptionDisabled={(option) => option.inventaireEnCours}
                                    value={sacs.find(c => c.value === watch("idSac"))}
                                    onChange={val => val != null ? setValue("idSac", val.value) : setValue("idSac", null)}
                                />
                                <small className="text-danger">{errors.idSac?.message}</small>
                            </Col>

                            <Col xs="auto">
                                <Button type="submit" className="mb-2 me-1" variant='outline-primary' size='sm'>
                                    {idEmplacement > 0 ? 'Modifier' : 'Créer'}
                                </Button>
                                {emplacements.length > 1 && idEmplacement > 0 ?
                                    <Button className="mb-2 me-1" variant='outline-danger' size='sm' onClick={handleShowDeleteModal}>
                                        Supprimer
                                    </Button>
                                :null}
                            </Col>
                        </Row>
                    </Form>
                : null }
            </Col>
            <Col md={12} className="mb-2">
                {idEmplacement && idEmplacement != null && idEmplacement > 0 ?  <>
                    <h5>Matériels de l'emplacement:</h5>
                    <MaterielsTable
                        filterIdEmplacement={idEmplacement}
                        displayLibelleLot={false}
                        displayLibelleSac={false}
                        displayLibelleEmplacement={false}
                        displayNotif={false}
                        hideAddButton={inventaireEnCours}
                    />
                </>: null }
            </Col>
        </Row>
    </>);
};

SacsContent.propTypes = {};

export default SacsContent;
