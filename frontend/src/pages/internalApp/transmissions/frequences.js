import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, FloatingLabel, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';

import FrequencesAttached from './frequencesAttached';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { frequencesForm } from 'helpers/yupValidationSchema';

const Frequences = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [canaux, setCanaux] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/vhf/getFrequences');
            setCanaux(getData.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const colonnes = [
        {
            accessor: 'chName',
            Header: 'Libellé',
        },
        {
            accessor: 'libelleTechno',
            Header: 'Technologie',
        },
        {
            accessor: 'rxFreq',
            Header: 'RX',
            Cell: ({ value, row }) => {
				return(
                    <>
                        Fréquence: {row.original.rxFreq} MHz
                        <br/>
                        {row.original.rxCtcss ? "CTCSS: "+row.original.rxCtcss+"Hz" : null}
                    </>
                );
			},
        },
        {
            accessor: 'txFreq',
            Header: 'TX',
            Cell: ({ value, row }) => {
				return(
                    <>
                        Fréquence: {row.original.txFreq} MHz
                        {row.original.txCtcss ? <><br/>CTCSS: {row.original.txCtcss}Hz</> : null}
                        {row.original.txPower ? <><br/>Puissance: {row.original.txPower}W</> : null}
                    </>
                );
			},
        },
        {
            accessor: 'porteuse',
            Header: 'Porteuse',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {row.original.appelSelectifPorteuse ? "Fréquence: "+row.original.appelSelectifPorteuse+"Hz" : null}
                        {row.original.appelSelectifCode ? <><br/>Code d'appel: {row.original.appelSelectifCode}</> : null}
                        {row.original.niveauCtcss ? <><br/>CTCSS: {row.original.niveauCtcss}Hz</> : null}
                        {row.original.let ? <><br/>Let: {row.original.let}ms</> : null}
                        {row.original.notone ? <><br/>NoTone: {row.original.notone}ms</> : null}
                    </>
                );
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        <FrequencesAttached vhfCanal={row.original} />
                        
                        {HabilitationService.habilitations['vhf_canal_modification'] ? 
                            <IconButton
                                icon='pen'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowOffCanevas(row.original.idVhfCanal)}}
                            />
                        : null}
                        {HabilitationService.habilitations['vhf_canal_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idVhfCanal)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdVhfCanal, setOffCanevasIdVhfCanal] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(frequencesForm),
    });
    const [technologies, setTechnologies] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdVhfCanal();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdVhfCanal(id);

        if(id > 0)
        {
            let oneItemFromArray = canaux.filter(ligne => ligne.idVhfCanal == id)[0];
            setValue("rxFreq", oneItemFromArray.rxFreq);
            setValue("txFreq", oneItemFromArray.txFreq);
            setValue("rxCtcss", oneItemFromArray.rxCtcss);
            setValue("txCtcss", oneItemFromArray.txCtcss);
            setValue("niveauCtcss", oneItemFromArray.niveauCtcss);
            setValue("txPower", oneItemFromArray.txPower);
            setValue("chName", oneItemFromArray.chName);
            setValue("appelSelectifCode", oneItemFromArray.appelSelectifCode);
            setValue("appelSelectifPorteuse", oneItemFromArray.appelSelectifPorteuse);
            setValue("let", oneItemFromArray.let);
            setValue("notone", oneItemFromArray.notone);
            setValue("idVhfTechno", oneItemFromArray.idVhfTechno);
            setValue("remarquesCanal", oneItemFromArray.remarquesCanal);
        }

        const getData = await Axios.get('/select/getTechnologiesVHF');
        setTechnologies(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdVhfCanal > 0)    
            {
                const response = await Axios.post('/vhf/updatecanal',{
                    idVhfCanal: offCanevasIdVhfCanal,
                    rxFreq: data.rxFreq,
                    txFreq: data.txFreq,
                    rxCtcss: data.rxCtcss,
                    txCtcss: data.txCtcss,
                    niveauCtcss: data.niveauCtcss,
                    txPower: data.txPower,
                    chName: data.chName,
                    appelSelectifCode: data.appelSelectifCode,
                    appelSelectifPorteuse: data.appelSelectifPorteuse,
                    let: data.let,
                    notone: data.notone,
                    idVhfTechno: data.idVhfTechno,
                    remarquesCanal: data.remarquesCanal,
                });
            }
            else
            {
                const response = await Axios.post('/vhf/addCanal',{
                    rxFreq: data.rxFreq,
                    txFreq: data.txFreq,
                    rxCtcss: data.rxCtcss,
                    txCtcss: data.txCtcss,
                    niveauCtcss: data.niveauCtcss,
                    txPower: data.txPower,
                    chName: data.chName,
                    appelSelectifCode: data.appelSelectifCode,
                    appelSelectifPorteuse: data.appelSelectifPorteuse,
                    let: data.let,
                    notone: data.notone,
                    idVhfTechno: data.idVhfTechno,
                    remarquesCanal: data.remarquesCanal,
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
    const [deleteModalIdVhfCanal, setDeleteModalIdVhfCanal] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdVhfCanal();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdVhfCanal(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/vhf/deleteCanal',{
                idVhfCanal: deleteModalIdVhfCanal,
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
            title="Fréquences radio et canaux"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdVhfCanal > 0 ? "Modification" : "Ajout"} d'une fréquence VHF</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé</Form.Label>
                        <Form.Control size="sm" type="text" name='chName' id='chName' {...register('chName')}/>
                        <small className="text-danger">{errors.chName?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Technologie Radio</Form.Label>
                        <Select
                            id="idVhfTechno"
                            name="idVhfTechno"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucune technologie selectionnée'
                            options={technologies}
                            value={technologies.find(c => c.value === watch("idVhfTechno"))}
                            onChange={val => val != null ? setValue("idVhfTechno", val.value) : setValue("idVhfTechno", null)}
                        />
                        <small className="text-danger">{errors.idVhfTechno?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Réception</Form.Label>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Fréquence"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.00001" name='rxFreq' id='rxFreq' {...register('rxFreq')}/>
                            <small className="text-danger">{errors.rxFreq?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="CTCSS"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.00001" name='rxCtcss' id='rxCtcss' {...register('rxCtcss')}/>
                            <small className="text-danger">{errors.rxCtcss?.message}</small>
                        </FloatingLabel>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Emission</Form.Label>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Fréquence"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.00001" name='txFreq' id='txFreq' {...register('txFreq')}/>
                            <small className="text-danger">{errors.txFreq?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="CTCSS"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.00001" name='txCtcss' id='txCtcss' {...register('txCtcss')}/>
                            <small className="text-danger">{errors.txCtcss?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Puissance"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.01" name='txPower' id='txPower' {...register('txPower')}/>
                            <small className="text-danger">{errors.txPower?.message}</small>
                        </FloatingLabel>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Fréquence porteuse</Form.Label>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Fréquence porteuse de l'appel selectif"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.00001" name='appelSelectifPorteuse' id='appelSelectifPorteuse' {...register('appelSelectifPorteuse')}/>
                            <small className="text-danger">{errors.appelSelectifPorteuse?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Code d'appel selectif"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="text" name='appelSelectifCode' id='appelSelectifCode' {...register('appelSelectifCode')}/>
                            <small className="text-danger">{errors.appelSelectifCode?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="CTCSS de la porteuse"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" min="0" step="0.00001" name='niveauCtcss' id='niveauCtcss' {...register('niveauCtcss')}/>
                            <small className="text-danger">{errors.niveauCtcss?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="Let"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" step="1" name='let' id='let' {...register('let')}/>
                            <small className="text-danger">{errors.let?.message}</small>
                        </FloatingLabel>
                        <FloatingLabel
                            controlId="floatingInput"
                            label="NoTone"
                            className="mb-3"
                        >
                            <Form.Control size="sm" type="number" step="1" name='notone' id='notone' {...register('notone')}/>
                            <small className="text-danger">{errors.notone?.message}</small>
                        </FloatingLabel>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Remarques</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name='remarquesCanal' id='remarquesCanal' {...register('remarquesCanal')}/>
                        <small className="text-danger">{errors.remarquesCanal?.message}</small>
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
                Attention, vous allez supprimer un canal VHF (id: {deleteModalIdVhfCanal}). Etes-vous certain de vouloir continuer ?
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
                        data={canaux}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['vhf_canal_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={()=>{handleShowOffCanevas(0)}}
                                >Nouvelle fréquence/canal</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Frequences.propTypes = {};

export default Frequences;
