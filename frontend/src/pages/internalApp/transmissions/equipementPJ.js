import React, {useEffect, useState} from 'react';
import { Card, Form , Offcanvas, Button, Dropdown } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';
import moment from 'moment-timezone';
import CardDropdown from 'components/common/CardDropdown';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';
import { AxiosUpload } from 'helpers/axiosFileUpload';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vhfEquipementsAttachedForm } from 'helpers/yupValidationSchema';

const EquipementVhfPJ = ({equipement, setPageNeedsRefresh}) => {
    const colonnes = [
        {accessor: 'nomDocVHF',       Header: 'Nom du document'},
        {accessor: 'libelleTypeDocument' , Header: 'Type'},
        {accessor: 'formatDocVHF' ,   Header: 'Format'},
        {accessor: 'dateDocVHF' ,     Header: 'Date'},
        {accessor: 'actions'       ,       Header: 'Actions'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = () => {
        let tempTable  = [];
        for(const item of equipement.documents)
        {
            tempTable.push({
                nomDocVHF: item.nomDocVHF,
                libelleTypeDocument: item.libelleTypeDocument,
                formatDocVHF: item.formatDocVHF,
                dateDocVHF: moment(item.dateDocVHF).format('DD/MM/YYYY HH:mm'),
                actions:
                    <CardDropdown>
                        <div className="py-2">
                            {item.formatDocVHF == 'pdf' ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePDF(item.urlFichierDocVHF)}}>Afficher le PDF</Dropdown.Item>) : null}
                            {["png", "jpg", "jpeg"].includes(item.formatDocVHF) ? (<Dropdown.Item className='text-info' onClick={() => {displayOnePicture(item.urlFichierDocVHF)}}>Afficher l'image</Dropdown.Item>) : null}
                            <Dropdown.Item className='text-success' onClick={() => {downloadDocument(item.urlFichierDocVHF, item.nomDocVHF, item.formatDocVHF)}}>Télécharger</Dropdown.Item>
                            <Dropdown.Divider />
                            <Dropdown.Item className='text-danger' onClick={() => (supprimerEntree(item.idDocVHF))}>Supprimer (attention pas de confirmation)</Dropdown.Item>
                        </div>
                    </CardDropdown>
                ,
            })
        }
        setLignes(tempTable);
    }
    useEffect(() => {
        initTableau();
    }, [equipement.documents])

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
        const types = await Axios.get('/settingsMetiers/getTypesDocuments');
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
                        <Form.Select size="sm" name="idTypeDocument" id="idTypeDocument" {...register("idTypeDocument")}>
                            <option key="0" value="">--- Merci de selectionner un type ---</option>
                            {typesDocuments.map((type, i) => {
                                return (<option key={type.idTypeDocument} value={type.idTypeDocument}>{type.libelleTypeDocument}</option>);
                            })}
                        </Form.Select>
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
                    data={lignes}
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