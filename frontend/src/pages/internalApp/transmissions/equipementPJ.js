import React, {useState} from 'react';
import { Card, Form , Offcanvas, Button, Dropdown } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';
import moment from 'moment-timezone';
import CardDropdown from 'components/common/CardDropdown';
import Select from 'react-select';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';
import { AxiosUpload } from 'helpers/axiosFileUpload';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vhfEquipementsAttachedForm } from 'helpers/yupValidationSchema';

const EquipementVhfPJ = ({equipement, setPageNeedsRefresh}) => {
    const colonnes = [
        {
            accessor: 'nomDocVHF',
            Header: 'Nom du document',
        },
        {
            accessor: 'libelleTypeDocument',
            Header: 'Type',
        },
        {
            accessor: 'formatDocVHF',
            Header: 'Format',
        },
        {
            accessor: 'dateDocVHF',
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
                            {row.original.formatDocVHF == 'pdf' ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePDF(row.original.urlFichierDocVHF)}}>Afficher le PDF</Dropdown.Item>) : null}
                            {["png", "jpg", "jpeg"].includes(row.original.formatDocVHF) ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePicture(row.original.urlFichierDocVHF)}}>Afficher l'image</Dropdown.Item>) : null}
                            <Dropdown.Item className='text-success' onClick={() => {downloadDocument(row.original.urlFichierDocVHF, row.original.nomDocVHF, row.original.formatDocVHF)}}>Télécharger</Dropdown.Item>
                            <Dropdown.Divider />
                            <Dropdown.Item className='text-danger' onClick={() => (supprimerEntree(row.original.idDocVHF))}>Supprimer (attention pas de confirmation)</Dropdown.Item>
                        </div>
                    </CardDropdown>
                );
			},
        },
    ];

    /* FORM */
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const [file, setFile] = useState(null);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vhfEquipementsAttachedForm),
    });

    const[typesDocuments, setTypesDocuments] = useState([]);
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async () => {
        const types = await Axios.get('/select/getTypesDocuments');
        setTypesDocuments(types.data);

        setShowOffCanevas(true);
    }
    

    const onUploadDocument = (e) => {
        setFile(e.target.files[0]);
        setValue("nomDocVHF", e.target.files[0].name);
    }

    const ajouterModifier = async (data) => {
        try {
            setLoading(true);

            if(file != null)
            {
                let formData = new FormData();
                formData.append('file', file);

                const uploadDocumentFormation = await AxiosUpload.post('/vhf/uploadEquipementsAttached?idVhfEquipement='+equipement.idVhfEquipement, formData);
                let idDocVHF = uploadDocumentFormation.data.idDocVHF;
                setFile(null);

                const updateMetaData = await Axios.post('/vhf/updateMetaDataEquipements',
                {
                    idDocVHF : idDocVHF,
                    nomDocVHF : data.nomDocVHF,
                    idTypeDocument : data.idTypeDocument,
                    dateDocVHF : data.dateDocVHF,
                });

                setPageNeedsRefresh(true);
                handleCloseOffCanevas();
                reset();
            }
            else
            {
                console.log('Aucun fichier chargé');
            }
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    //Ouvrir dans un autre onglet
    const displayOnePDF = async (urlFichierDocVHF) => {
        try {
            let documentData = await Axios.post('getSecureFile/vhfEquipements',
            {
                urlFichierDocVHF: urlFichierDocVHF,
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

    const displayOnePicture = async (urlFichierDocVHF) => {
        try {
            let documentData = await Axios.post('getSecureFile/vhfEquipements',
            {
                urlFichierDocVHF: urlFichierDocVHF,
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

    const downloadDocument = async (urlFichierDocVHF, libelle, format) => {
        try {
            let documentData = await Axios.post('getSecureFile/vhfEquipements',
            {
                urlFichierDocVHF: urlFichierDocVHF,
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

    /* DELTE */
    const supprimerEntree = async (idDocVHF) => {
        try {
            const response = await Axios.post('/vhf/dropEquipementsDocument',{
                idDocVHF: idDocVHF
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.error(error)
        }
    }

    return(<>
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Document lié à un équipement radio</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifier)}>
                    <Form.Group className="mb-3">
                        <Form.Control size="sm" type="file" name="file" onChange={onUploadDocument}/>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Nom du document</Form.Label>
                        <Form.Control size="sm" type="text" name="nomDocVHF" id="nomDocVHF" value={watch("nomDocVHF")} {...register("nomDocVHF")}/>
                        <small className="text-danger">{errors.nomDocVHF?.message}</small>
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
            </Offcanvas.Body>
        </Offcanvas>
        
        <Card className="mb-3">
            <Card.Header className="p-2 border-bottom">
                <Flex>
                    <div className="p-2 flex-grow-1">
                        Pièces jointes
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                <GPMtable
                    columns={colonnes}
                    data={equipement.documents}
                    topButtonShow={true}
                    topButton={
                        HabilitationService.habilitations['vhf_equipement_ajout'] ?
                            <IconButton
                                icon='plus'
                                size = 'sm'
                                variant="outline-success"
                                onClick={()=>{handleShowOffCanevas(0)}}
                            >Nouveau document</IconButton>
                        : null
                    }
                />
            </Card.Body>
        </Card>
    </>);
};

EquipementVhfPJ.propTypes = {};

export default EquipementVhfPJ;