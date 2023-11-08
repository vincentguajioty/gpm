import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';

import HabilitationService from 'services/habilitationsService';

import PlansAttached from './plansAttached';
import PlansCanaux from './plansCanaux';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { planVHFForm } from 'helpers/yupValidationSchema';

const Plans = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [vhfPlans, setVhfPlans] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vhf/getPlans');
            setVhfPlans(getData.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {accessor: 'libellePlan',         Header: 'Libellé'},
        {accessor: 'canaux' ,      Header: 'Canaux'},
        {accessor: 'actions'       , Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of vhfPlans)
        {
            tempTable.push({
                libellePlan: item.libellePlan,
                canaux: <PlansCanaux vhfPlan={item} />,
                actions:
                    <>
                        <PlansAttached vhfPlan={item} />
                        
                        {HabilitationService.habilitations['vhf_plan_modification'] ? 
                            <IconButton
                                icon='pen'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowOffCanevas(item.idVhfPlan)}}
                            />
                        : null}
                        {HabilitationService.habilitations['vhf_plan_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(item.idVhfPlan)}}
                            />
                        : null}
                    </>,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [vhfPlans])

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdVhfPlan, setOffCanevasIdVhfPlan] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(planVHFForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdVhfPlan();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdVhfPlan(id);

        if(id > 0)
        {
            let oneItemFromArray = vhfPlans.filter(ligne => ligne.idVhfPlan == id)[0];
            setValue("libellePlan", oneItemFromArray.libellePlan);
            setValue("remarquesPlan", oneItemFromArray.remarquesPlan);
        }

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdVhfPlan > 0)    
            {
                const response = await Axios.post('/vhf/updatePlan',{
                    idVhfPlan: offCanevasIdVhfPlan,
                    libellePlan: data.libellePlan,
                    remarquesPlan: data.remarquesPlan,
                });
            }
            else
            {
                const response = await Axios.post('/vhf/addPlan',{
                    libellePlan: data.libellePlan,
                    remarquesPlan: data.remarquesPlan,
                });
            }

            handleCloseOffCanevas();
            initPage();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdVhfPlan, setDeleteModalIdVhfPlan] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdVhfPlan();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVhfPlan(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vhf/deletePlan',{
                idVhfPlan: deleteModalIdVhfPlan,
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
            preTitle="Transmissions"
            title="Plans de fréquences et programmations"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdVhfPlan > 0 ? "Modification" : "Ajout"} d'un plan de programmation VHF</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé</Form.Label>
                        <Form.Control size="sm" type="text" name='libellePlan' id='libellePlan' {...register('libellePlan')}/>
                        <small className="text-danger">{errors.libellePlan?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name='remarquesPlan' id='remarquesPlan' {...register('remarquesPlan')}/>
                        <small className="text-danger">{errors.remarquesPlan?.message}</small>
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
            <Modal.Body>
                Attention, vous allez supprimer un plan VHF (id: {deleteModalIdVhfPlan}). Etes-vous certain de vouloir continuer ?
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
                            HabilitationService.habilitations['vhf_plan_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={()=>{handleShowOffCanevas(0)}}
                                >Nouveau plan</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Plans.propTypes = {};

export default Plans;
