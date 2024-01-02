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
import { frequencesAttachedForm } from 'helpers/yupValidationSchema';

const FrequencesAttached = ({vhfCanal}) => {
    const[tableReady, setTableReady] = useState(false);
    const[documents, setDocuments] = useState([]);
    const[typesDocuments, setTypesDocuments] = useState([]);

    const getDocuments = async () => {
        try {
            const response = await Axios.post('/vhf/getAllDocumentsOneCanal',{
                idVhfCanal: vhfCanal.idVhfCanal,
            });
            setDocuments(response.data);

            const types = await Axios.get('/select/getTypesDocuments');
            setTypesDocuments(types.data);

            setTableReady(true);
        } catch (error) {
            console.log(error);
        }
    }

    const colonnes = [
        {
            accessor: 'nomDocCanalVHF',
            Header: 'Nom du document',
        },
        {
            accessor: 'libelleTypeDocument',
            Header: 'Type',
        },
        {
            accessor: 'formatDocCanalVHF',
            Header: 'Format',
        },
        {
            accessor: 'dateDocCanalVHF',
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
                            {row.original.formatDocCanalVHF == 'pdf' ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePDF(row.original.urlFichierDocCanalVHF)}}>Afficher le PDF</Dropdown.Item>) : null}
                            {["png", "jpg", "jpeg"].includes(row.original.formatDocCanalVHF) ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePicture(row.original.urlFichierDocCanalVHF)}}>Afficher l'image</Dropdown.Item>) : null}
                            <Dropdown.Item className='text-success' onClick={() => {downloadDocument(row.original.urlFichierDocCanalVHF, row.original.nomDocCanalVHF, row.original.formatDocCanalVHF)}}>Télécharger</Dropdown.Item>
                            <Dropdown.Divider />
                            <Dropdown.Item className='text-danger' onClick={() => (supprimerEntree(row.original.idDocCanalVHF))}>Supprimer (attention pas de confirmation)</Dropdown.Item>
                        </div>
                    </CardDropdown>
                );
			},
        },
    ];

    //partie formulaire
    const [isLoading, setIsLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(frequencesAttachedForm),
    });
    const [file, setFile] = useState(null);

    const onUploadDocument = (e) => {
        setFile(e.target.files[0]);
        setValue("nomDocCanalVHF", e.target.files[0].name);
    }

    const ajouterModifier = async (data) => {
        try {
            setIsLoading(true);

            if(file != null)
            {
                let formData = new FormData();
                formData.append('file', file);

                const uploadDocumentFormation = await AxiosUpload.post('/vhf/uploadCanalAttached?idVhfCanal='+vhfCanal.idVhfCanal, formData);
                let idDocCanalVHF = uploadDocumentFormation.data.idDocCanalVHF;
                setFile(null);

                const updateMetaData = await Axios.post('/vhf/updateMetaDataCanal',
                {
                    idDocCanalVHF : idDocCanalVHF,
                    nomDocCanalVHF : data.nomDocCanalVHF,
                    idTypeDocument : data.idTypeDocument,
                    dateDocCanalVHF : data.dateDocCanalVHF,
                });

                reset();
                await getDocuments();
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
    const supprimerEntree = async (idDocCanalVHF) => {
        try {
            const response = await Axios.post('/vhf/dropCanalDocument',{
                idDocCanalVHF: idDocCanalVHF
            });

            await getDocuments();
        } catch (error) {
            console.error(error)
        }
    }

    //Ouvrir dans un autre onglet
    const displayOnePDF = async (urlFichierDocCanalVHF) => {
        try {
            let documentData = await Axios.post('getSecureFile/vhfCanaux',
            {
                urlFichierDocCanalVHF: urlFichierDocCanalVHF,
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

    const displayOnePicture = async (urlFichierDocCanalVHF) => {
        try {
            let documentData = await Axios.post('getSecureFile/vhfCanaux',
            {
                urlFichierDocCanalVHF: urlFichierDocCanalVHF,
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

    const downloadDocument = async (urlFichierDocCanalVHF, libelle, format) => {
        try {
            let documentData = await Axios.post('getSecureFile/vhfCanaux',
            {
                urlFichierDocCanalVHF: urlFichierDocCanalVHF,
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
        vhfCanal.nbDocuments = documents.length;
        setShowAttachementModal(false);
    }
    const handleShowAttachementModal = async () => {
        await getDocuments();
        setShowAttachementModal(true);
    }
    
    return(<>
        <IconButton
            icon='paperclip'
            size = 'sm'
            variant="outline-info"
            className="me-1"
            onClick={handleShowAttachementModal}
        > {vhfCanal.nbDocuments || 0}</IconButton>
        
        <Modal show={showAttachementModal} onHide={handleCloseAttachementModal} backdrop="static" fullscreen={true} keyboard={false}>
            <Modal.Header>
                <Modal.Title>Pièces jointes de {vhfCanal.chName}</Modal.Title>
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
                                    <Form.Control size="sm" type="text" name="nomDocCanalVHF" id="nomDocCanalVHF" value={watch("nomDocCanalVHF")} {...register("nomDocCanalVHF")}/>
                                    <small className="text-danger">{errors.nomDocCanalVHF?.message}</small>
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

FrequencesAttached.propTypes = {};

export default FrequencesAttached;