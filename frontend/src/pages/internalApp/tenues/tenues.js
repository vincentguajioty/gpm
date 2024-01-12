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
import Select from 'react-select';
import { tenuesCatalogueDelete } from 'helpers/deleteModalWarningContent';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { catalogueTenuesForm } from 'helpers/yupValidationSchema';

const Tenues = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [catalogue, setCatalogue] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/tenues/getCatalogue');
            setCatalogue(getData.data);
            
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
            accessor: 'libelleCatalogueTenue',
            Header: 'Libellé',
        },
        {
            accessor: 'tailleCatalogueTenue',
            Header: 'Taille',
        },
        {
            accessor: 'stockCatalogueTenue',
            Header: 'Stock',
            Cell: ({ value, row }) => {
				return(
                    value < row.original.stockAlerteCatalogueTenue ?
                        <SoftBadge bg='danger'>{value}</SoftBadge>
                    :
                        value == row.original.stockAlerteCatalogueTenue ?
                            <SoftBadge bg='warning'>{value}</SoftBadge>
                        :
                            <SoftBadge bg='success'>{value}</SoftBadge>
                );
			},
        },
        {
            accessor: 'affectations',
            Header: 'Affectations',
            Cell: ({ value, row }) => {
				return(
                    <>{value.map((affect, i)=>{return(
                        <SoftBadge bg='info' className='me-1'>{affect.personneNonGPM}{affect.identifiant}</SoftBadge>
                    )})}</>
                );
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {HabilitationService.habilitations['tenuesCatalogue_modification'] ? 
                            <IconButton
                                icon='pen'
                                size = 'sm'
                                variant="outline-warning"
                                className="me-1"
                                onClick={()=>{handleShowOffCanevas(row.original.idCatalogueTenue)}}
                            />
                        : null}
                        {HabilitationService.habilitations['tenuesCatalogue_suppression'] ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idCatalogueTenue)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];


    //formulaire d'ajout
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdCatalogueTenue, setOffCanevasIdCatalogueTenue] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(catalogueTenuesForm),
    });
    const [fournisseurs, setFournisseurs] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdCatalogueTenue();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (id) => {
        setOffCanevasIdCatalogueTenue(id);

        if(id > 0)
        {
            let oneItemFromArray = catalogue.filter(ligne => ligne.idCatalogueTenue == id)[0];
            setValue("libelleCatalogueTenue", oneItemFromArray.libelleCatalogueTenue);
            setValue("tailleCatalogueTenue", oneItemFromArray.tailleCatalogueTenue);
            setValue("serigraphieCatalogueTenue", oneItemFromArray.serigraphieCatalogueTenue);
            setValue("idFournisseur", oneItemFromArray.idFournisseur);
            setValue("stockCatalogueTenue", oneItemFromArray.stockCatalogueTenue);
            setValue("stockAlerteCatalogueTenue", oneItemFromArray.stockAlerteCatalogueTenue);
        }

        const getData = await Axios.get('/select/getFournisseurs');
        setFournisseurs(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdCatalogueTenue > 0)    
            {
                const response = await Axios.post('/tenues/updateCatalogue',{
                    idCatalogueTenue: offCanevasIdCatalogueTenue,
                    libelleCatalogueTenue: data.libelleCatalogueTenue,
                    tailleCatalogueTenue: data.tailleCatalogueTenue,
                    serigraphieCatalogueTenue: data.serigraphieCatalogueTenue,
                    idFournisseur: data.idFournisseur,
                    stockCatalogueTenue: data.stockCatalogueTenue,
                    stockAlerteCatalogueTenue: data.stockAlerteCatalogueTenue,
                });
            }
            else
            {
                const response = await Axios.post('/tenues/addCatalogue',{
                    libelleCatalogueTenue: data.libelleCatalogueTenue,
                    tailleCatalogueTenue: data.tailleCatalogueTenue,
                    serigraphieCatalogueTenue: data.serigraphieCatalogueTenue,
                    idFournisseur: data.idFournisseur,
                    stockCatalogueTenue: data.stockCatalogueTenue,
                    stockAlerteCatalogueTenue: data.stockAlerteCatalogueTenue,
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
    const [deleteModalIdCatalogueTenue, setDeleteModalIdCatalogueTenue] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdCatalogueTenue();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdCatalogueTenue(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/tenues/deleteCatalogue',{
                idCatalogueTenue: deleteModalIdCatalogueTenue,
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
            preTitle="Gestion des tenues"
            title="Catalogue des tenues et stock"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdCatalogueTenue > 0 ? "Modification" : "Ajout"} d'une entrée dans le catalogue des tenues</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Libellé</Form.Label>
                        <Form.Control size="sm" type="text" name='libelleCatalogueTenue' id='libelleCatalogueTenue' {...register('libelleCatalogueTenue')}/>
                        <small className="text-danger">{errors.libelleCatalogueTenue?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Taille</Form.Label>
                        <Form.Control size="sm" type="text" name='tailleCatalogueTenue' id='tailleCatalogueTenue' {...register('tailleCatalogueTenue')}/>
                        <small className="text-danger">{errors.tailleCatalogueTenue?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Sérigraphie</Form.Label>
                        <Form.Control size="sm" type="text" name='serigraphieCatalogueTenue' id='serigraphieCatalogueTenue' {...register('serigraphieCatalogueTenue')}/>
                        <small className="text-danger">{errors.serigraphieCatalogueTenue?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Stock actuel</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='stockCatalogueTenue' id='stockCatalogueTenue' {...register('stockCatalogueTenue')}/>
                        <small className="text-danger">{errors.stockCatalogueTenue?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Stock d'alerte</Form.Label>
                        <Form.Control size="sm" type="number" min="0" step="1" name='stockAlerteCatalogueTenue' id='stockAlerteCatalogueTenue' {...register('stockAlerteCatalogueTenue')}/>
                        <small className="text-danger">{errors.stockAlerteCatalogueTenue?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Fournisseur de prédilection</Form.Label>
                        <Select
                            id="idFournisseur"
                            name="idFournisseur"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun fournisseur sélectionné'
                            options={fournisseurs}
                            value={fournisseurs.find(c => c.value === watch("idFournisseur"))}
                            onChange={val => val != null ? setValue("idFournisseur", val.value) : setValue("idFournisseur", null)}
                        />
                        <small className="text-danger">{errors.idFournisseur?.message}</small>
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
            <Modal.Body>{tenuesCatalogueDelete}</Modal.Body>
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
                        data={catalogue}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['tenuesCatalogue_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={()=>{handleShowOffCanevas(0)}}
                                >Nouvel élément</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Tenues.propTypes = {};

export default Tenues;
