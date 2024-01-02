import React, { useState, useEffect } from 'react';
import { Button, Form, Modal, Row, Col, Dropdown } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import CardDropdown from 'components/common/CardDropdown';
import moment from 'moment-timezone';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import { AxiosUpload } from 'helpers/axiosFileUpload';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vehiculeAttachedForm } from 'helpers/yupValidationSchema';

const VehiculeAttached = ({idVehicule, libelleVehicule, documents, setPageNeedsRefresh}) => {
    const[tableReady, setTableReady] = useState(false);
    const[typesDocuments, setTypesDocuments] = useState([]);

    const initModal = async () => {
        try {
            const types = await Axios.get('/select/getTypesDocuments');
            setTypesDocuments(types.data);

            setTableReady(true);
        } catch (error) {
            console.log(error);
        }
    }

    const colonnes = [
        {
            accessor: 'nomDocVehicule',
            Header: 'Nom du document',
        },
        {
            accessor: 'libelleTypeDocument',
            Header: 'Type',
        },
        {
            accessor: 'formatDocVehicule',
            Header: 'Format',
        },
        {
            accessor: 'dateDocVehicule',
            Header: 'Date',
            Cell: ({ value, row }) => {
				return(moment(value).format('DD/MM/YYYY HH:mm'));
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <CardDropdown>
                        <div className="py-2">
                            {row.original.formatDocVehicule == 'pdf' ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePDF(row.original.urlFichierDocVehicule)}}>Afficher le PDF</Dropdown.Item>) : null}
                            {["png", "jpg", "jpeg"].includes(row.original.formatDocVehicule) ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePicture(row.original.urlFichierDocVehicule)}}>Afficher l'image</Dropdown.Item>) : null}
                            <Dropdown.Item className='text-success' onClick={() => {downloadDocument(row.original.urlFichierDocVehicule, row.original.nomDocVehicule, row.original.formatDocVehicule)}}>Télécharger</Dropdown.Item>
                            <Dropdown.Divider />
                            <Dropdown.Item className='text-danger' onClick={() => (supprimerEntree(row.original.idDocVehicules))}>Supprimer (attention pas de confirmation)</Dropdown.Item>
                        </div>
                    </CardDropdown>
                );
			},
        },
    ];
    
    //partie formulaire
    const [isLoading, setIsLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vehiculeAttachedForm),
    });
    const [file, setFile] = useState(null);

    const onUploadDocument = (e) => {
        setFile(e.target.files[0]);
        setValue("nomDocVehicule", e.target.files[0].name);
    }

    const ajouterModifier = async (data) => {
        try {
            setIsLoading(true);

            if(file != null)
            {
                let formData = new FormData();
                formData.append('file', file);

                const uploadDocumentFormation = await AxiosUpload.post('/vehicules/uploadVehiculeAttached?idVehicule='+idVehicule, formData);
                let idDocVehicules = uploadDocumentFormation.data.idDocVehicules;
                setFile(null);

                const updateMetaData = await Axios.post('/vehicules/updateMetaDataVehicule',
                {
                    idDocVehicules : idDocVehicules,
                    nomDocVehicule : data.nomDocVehicule,
                    idTypeDocument : data.idTypeDocument,
                    dateDocVehicule : data.dateDocVehicule,
                });

                reset();
                setPageNeedsRefresh(true);
            }
            else
            {
                console.log('Aucun fichier chargé');
            }
            
            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    //delete    
    const supprimerEntree = async (idDocVehicules) => {
        try {
            const response = await Axios.post('/vehicules/dropVehiculeDocument',{
                idDocVehicules: idDocVehicules
            });

            setPageNeedsRefresh(true);
        } catch (error) {
            console.error(error)
        }
    }

    //Ouvrir dans un autre onglet
    const displayOnePDF = async (urlFichierDocVehicule) => {
        try {
            let documentData = await Axios.post('getSecureFile/vehicules',
            {
                urlFichierDocVehicule: urlFichierDocVehicule,
            },
            {
                responseType: 'blob'
            });
            
            const file = new Blob([documentData.data], { type: "application/pdf" });
            const fileURL = URL.createObjectURL(file);
            const pdfWindow = window.open();
            pdfWindow.location.href = fileURL;

        } catch (error) {
            console.log(error);
        }
    }

    const displayOnePicture = async (urlFichierDocVehicule) => {
        try {
            let documentData = await Axios.post('getSecureFile/vehicules',
            {
                urlFichierDocVehicule: urlFichierDocVehicule,
            },
            {
                responseType: 'blob'
            });
            
            const fileURL = URL.createObjectURL(documentData.data);
            const pdfWindow = window.open();
            pdfWindow.location.href = fileURL;

        } catch (error) {
            console.log(error);
        }
    }

    const downloadDocument = async (urlFichierDocVehicule, libelle, format) => {
        try {
            let documentData = await Axios.post('getSecureFile/vehicules',
            {
                urlFichierDocVehicule: urlFichierDocVehicule,
            },
            {
                responseType: 'blob'
            });
            
            // create file link in browser's memory
            const href = URL.createObjectURL(documentData.data);
            
            // create "a" HTML element with href to file & click
            const link = document.createElement('a');
            link.href = href;
            link.setAttribute('download', libelle+'.'+format); //or any other extension
            document.body.appendChild(link);
            link.click();

            // clean up "a" element & remove ObjectURL
            document.body.removeChild(link);
            URL.revokeObjectURL(href);
        } catch (error) {
            console.log(error);
        }
    }

    //modal
    const[showAttachementModal, setShowAttachementModal] = useState(false);
    const handleCloseAttachementModal = () => {
        setShowAttachementModal(false);
    }
    const handleShowAttachementModal = async () => {
        await initModal();
        setShowAttachementModal(true);
    }
    
    return(<>
        <IconButton
            icon='paperclip'
            size = 'sm'
            variant="outline-info"
            className="me-1"
            onClick={handleShowAttachementModal}
        >Gérer les documents ({documents.length || 0})</IconButton>
        
        <Modal show={showAttachementModal} onHide={handleCloseAttachementModal} backdrop="static" fullscreen={true} keyboard={false}>
            <Modal.Header>
                <Modal.Title>Pièces jointes de {libelleVehicule}</Modal.Title>
                <FalconCloseButton onClick={handleCloseAttachementModal}/>
            </Modal.Header>
            <Modal.Body>
                {tableReady ?
                    <Row>
                        <Col md={8} className='mb-2'>
                            <GPMtable
                                columns={colonnes}
                                data={documents}
                            />
                        </Col>
                        <Col md={4}>
                            <h5 className='mb-2'>Charger un nouveau document</h5>
                            <Form onSubmit={handleSubmit(ajouterModifier)}>
                                <Form.Group className="mb-3">
                                    <Form.Control size="sm" type="file" name="file" onChange={onUploadDocument}/>
                                </Form.Group>
                                <Form.Group className="mb-3">
                                    <Form.Label>Nom du document</Form.Label>
                                    <Form.Control size="sm" type="text" name="nomDocVehicule" id="nomDocVehicule" value={watch("nomDocVehicule")} {...register("nomDocVehicule")}/>
                                    <small className="text-danger">{errors.nomDocVehicule?.message}</small>
                                </Form.Group>
                                <Form.Group className="mb-3">
                                    <Form.Label>Type de document</Form.Label>
                                    <Select
                                        id="idTypeDocument"
                                        name="idTypeDocument"
                                        size="sm"
                                        classNamePrefix="react-select"
                                        closeMenuOnSelect={true}
                                        isClearable={true}
                                        isSearchable={true}
                                        isDisabled={isLoading}
                                        placeholder='Aucun type selectionné'
                                        options={typesDocuments}
                                        value={typesDocuments.find(c => c.value === watch("idTypeDocument"))}
                                        onChange={val => val != null ? setValue("idTypeDocument", val.value) : setValue("idTypeDocument", null)}
                                    />
                                    <small className="text-danger">{errors.idTypeDocument?.message}</small>
                                </Form.Group>
                                
                                <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Charger'}</Button>
                            </Form>
                        </Col>
                    </Row>
                :
                'Chargement en cours'
                }
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseAttachementModal}>Fermer</Button>
            </Modal.Footer>
        </Modal>
    </>);
};

VehiculeAttached.propTypes = {};

export default VehiculeAttached;