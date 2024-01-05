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
import { centreDeCoutsAttached } from 'helpers/yupValidationSchema';
import SoftBadge from 'components/common/SoftBadge';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

const OneCommandePJ = ({
    idCentreDeCout,
    documents,
    setPageNeedsRefresh
}) => {
    const colonnes = [
        {
            accessor: 'nomDocCouts',
            Header: 'Nom du document',
            Cell: ({ value, row }) => {
				return(<>
                    {value}
                    {row.original.idDocCommande != null ? <SoftBadge className='ms-1'><FontAwesomeIcon icon='shopping-cart'/></SoftBadge> : null}
                </>);
			},
        },
        {
            accessor: 'libelleTypeDocument',
            Header: 'Type',
        },
        {
            accessor: 'formatDocCouts',
            Header: 'Format',
        },
        {
            accessor: 'dateDocCouts',
            Header: 'Date',
            Cell: ({ value, row }) => {
				return(moment(value).format('DD/MM/YYYY HH:mm'));
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
                if(row.original.idDocCommande == null && row.original.idDocCouts != null)
                {
                    return(
                        <CardDropdown>
                            <div className="py-2">
                                {row.original.formatDocCouts == 'pdf' ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePDF(row.original.urlFichierDocCouts)}}>Afficher le PDF</Dropdown.Item>) : null}
                                {["png", "jpg", "jpeg"].includes(row.original.formatDocCouts) ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePicture(row.original.urlFichierDocCouts)}}>Afficher l'image</Dropdown.Item>) : null}
                                <Dropdown.Item className='text-success' onClick={() => {downloadDocument(row.original.urlFichierDocCouts, row.original.nomDocCouts, row.original.formatDocCouts)}}>Télécharger</Dropdown.Item>
                                <Dropdown.Divider />
                                <Dropdown.Item className='text-danger' onClick={() => (supprimerEntree(row.original.idDocCouts))}>Supprimer (attention pas de confirmation)</Dropdown.Item>
                            </div>
                        </CardDropdown>
                    );
                }else{
                    return(
                        <CardDropdown>
                            <div className="py-2">
                                {row.original.formatDocCouts == 'pdf' ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePDFCOMMANDE(row.original.urlFichierDocCouts)}}>Afficher le PDF</Dropdown.Item>) : null}
                                {["png", "jpg", "jpeg"].includes(row.original.formatDocCouts) ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePictureCOMMANDE(row.original.urlFichierDocCouts)}}>Afficher l'image</Dropdown.Item>) : null}
                                <Dropdown.Item className='text-success' onClick={() => {downloadDocumentCOMMANDE(row.original.urlFichierDocCouts, row.original.nomDocCouts, row.original.formatDocCouts)}}>Télécharger</Dropdown.Item>
                            </div>
                        </CardDropdown>
                    );
                }
			},
        },
    ];

    /* FORM */
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const [file, setFile] = useState(null);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(centreDeCoutsAttached),
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
        setValue("nomDocCouts", e.target.files[0].name);
    }

    const ajouterModifier = async (data) => {
        try {
            setLoading(true);

            if(file != null)
            {
                let formData = new FormData();
                formData.append('file', file);

                const uploadDocumentFormation = await AxiosUpload.post('/centresCouts/uploadCentreCoutsAttached?idCentreDeCout='+idCentreDeCout, formData);
                let idDocCouts = uploadDocumentFormation.data.idDocCouts;
                setFile(null);

                const updateMetaData = await Axios.post('/centresCouts/updateMetaDataCentreCouts',
                {
                    idDocCouts : idDocCouts,
                    nomDocCouts : data.nomDocCouts,
                    idTypeDocument : data.idTypeDocument,
                    dateDocCouts : data.dateDocCouts,
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
    const displayOnePDF = async (urlFichierDocCouts) => {
        try {
            let documentData = await Axios.post('getSecureFile/centresCouts',
            {
                urlFichierDocCouts: urlFichierDocCouts,
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

    const displayOnePicture = async (urlFichierDocCouts) => {
        try {
            let documentData = await Axios.post('getSecureFile/centresCouts',
            {
                urlFichierDocCouts: urlFichierDocCouts,
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

    const downloadDocument = async (urlFichierDocCouts, libelle, format) => {
        try {
            let documentData = await Axios.post('getSecureFile/centresCouts',
            {
                urlFichierDocCouts: urlFichierDocCouts,
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

    //Ouvrir dans un autre onglet POUR COMMANDE
    const displayOnePDFCOMMANDE = async (urlFichierDocCommande) => {
        try {
            let documentData = await Axios.post('getSecureFile/commandes',
            {
                urlFichierDocCommande: urlFichierDocCommande,
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

    const displayOnePictureCOMMANDE = async (urlFichierDocCommande) => {
        try {
            let documentData = await Axios.post('getSecureFile/commandes',
            {
                urlFichierDocCommande: urlFichierDocCommande,
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

    const downloadDocumentCOMMANDE = async (urlFichierDocCommande, libelle, format) => {
        try {
            let documentData = await Axios.post('getSecureFile/commandes',
            {
                urlFichierDocCommande: urlFichierDocCommande,
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
    const supprimerEntree = async (idDocCouts) => {
        try {
            const response = await Axios.post('/centresCouts/dropCentreCoutsDocument',{
                idDocCouts: idDocCouts
            });
            setPageNeedsRefresh(true);
        } catch (error) {
            console.error(error)
        }
    }

    return(<>
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Document lié à un centre de couts</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterModifier)}>
                    <Form.Group className="mb-3">
                        <Form.Control size="sm" type="file" name="file" onChange={onUploadDocument}/>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Nom du document</Form.Label>
                        <Form.Control size="sm" type="text" name="nomDocCouts" id="nomDocCouts" value={watch("nomDocCouts")} {...register("nomDocCouts")}/>
                        <small className="text-danger">{errors.nomDocCouts?.message}</small>
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
        
        <GPMtable
            columns={colonnes}
            data={documents}
            topButtonShow={true}
            topButton={
                HabilitationService.habilitations['cout_etreEnCharge'] ?
                    <IconButton
                        icon='plus'
                        size = 'sm'
                        variant="outline-success"
                        onClick={()=>{handleShowOffCanevas(0)}}
                    >Nouveau document</IconButton>
                : null
            }
        />
    </>);
};

OneCommandePJ.propTypes = {};

export default OneCommandePJ;