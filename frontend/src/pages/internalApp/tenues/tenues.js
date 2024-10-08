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
            accessor: 'libelleMateriel',
            Header: 'Libellé',
        },
        {
            accessor: 'taille',
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
    const [catalogueMateriel, setCatalogueMateriel] = useState([]);
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
            setValue("idMaterielCatalogue", oneItemFromArray.idMaterielCatalogue);
            setValue("idFournisseur", oneItemFromArray.idFournisseur);
            setValue("stockCatalogueTenue", oneItemFromArray.stockCatalogueTenue);
            setValue("stockAlerteCatalogueTenue", oneItemFromArray.stockAlerteCatalogueTenue);
        }

        let getData = await Axios.get('/select/getFournisseurs');
        setFournisseurs(getData.data);
        getData = await Axios.get('/select/getCatalogueMaterielTenues');
        setCatalogueMateriel(getData.data);

        setShowOffCanevas(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdCatalogueTenue > 0)    
            {
                const response = await Axios.post('/tenues/updateCatalogue',{
                    idCatalogueTenue: offCanevasIdCatalogueTenue,
                    idMaterielCatalogue: data.idMaterielCatalogue,
                    idFournisseur: data.idFournisseur,
                    stockCatalogueTenue: data.stockCatalogueTenue,
                    stockAlerteCatalogueTenue: data.stockAlerteCatalogueTenue,
                });
            }
            else
            {
                const response = await Axios.post('/tenues/addCatalogue',{
                    idMaterielCatalogue: data.idMaterielCatalogue,
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

    //Export
    const requestExport = async () => {
        try {
            setLoading(true);

            let fileRequest = await Axios.get('/tenues/exporterCatalogue');

            let documentData = await Axios.post('getSecureFile/temp',
            {
                fileName: fileRequest.data.fileName,
            },
            {
                responseType: 'blob'
            });
            
            // create file link in browser's memory
            const href = URL.createObjectURL(documentData.data);
            
            // create "a" HTML element with href to file & click
            const link = document.createElement('a');
            link.href = href;
            link.setAttribute('download', fileRequest.data.fileName); //or any other extension
            document.body.appendChild(link);
            link.click();

            // clean up "a" element & remove ObjectURL
            document.body.removeChild(link);
            URL.revokeObjectURL(href);
            
            setDownloadGenerated(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }


    return (<>
        <PageHeader
            preTitle="Gestion des tenues"
            title="Stock des tenues"
            className="mb-3"
        />

        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>{offCanevasIdCatalogueTenue > 0 ? "Modification" : "Ajout"} d'une entrée dans le stock des tenues</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                <   Form.Group className="mb-3">
                        <Form.Label>Référence du catalogue</Form.Label>
                        <Select
                            id="idMaterielCatalogue"
                            name="idMaterielCatalogue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun élément selectionné'
                            options={catalogueMateriel}
                            value={catalogueMateriel.find(c => c.value === watch("idMaterielCatalogue"))}
                            onChange={val => val != null ? setValue("idMaterielCatalogue", val.value) : setValue("idMaterielCatalogue", null)}
                        />
                        <small className="text-danger">{errors.idMaterielCatalogue?.message}</small>
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
                            <>
                                {HabilitationService.habilitations['tenuesCatalogue_ajout'] ?
                                    <IconButton
                                        icon='plus'
                                        size = 'sm'
                                        variant="outline-success"
                                        onClick={()=>{handleShowOffCanevas(0)}}
                                    >Nouvel élément</IconButton>
                                : ''}

                                {HabilitationService.habilitations['tenuesCatalogue_lecture'] ?
                                    <IconButton
                                        icon='download'
                                        size = 'sm'
                                        variant="outline-info"
                                        onClick={requestExport}
                                        className='ms-1'
                                        disabled={isLoading}
                                    >{isLoading ? "Génération en cours" : "Télécharger un état des lieux complet"}</IconButton>
                                : ''}
                            </>
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

Tenues.propTypes = {};

export default Tenues;
