import React, {useState} from 'react';
import { Modal, Button, Row, Col, Form, Dropdown, OverlayTrigger, Tooltip, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';
import Select from 'react-select';
import CardDropdown from 'components/common/CardDropdown';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { vehiculeCarteGriseForm } from 'helpers/yupValidationSchema';

const VehiculeCarteGrise = ({idVehicule, libelleVehicule, carteGrise, setPageNeedsRefresh}) => {

    const[tableReady, setTableReady] = useState(false);
    const[champsCarteGrise, setChampsCarteGrise] = useState([]);

    const initModal = async () => {
        try {
            const types = await Axios.get('/select/getChampsCarteGrise');
            setChampsCarteGrise(types.data);

            setTableReady(true);
        } catch (error) {
            console.log(error);
        }
    }

    const colonnes = [
        {
            accessor: 'codeChamp',
            Header: 'Champ',
            Cell: ({ value, row }) => {
				return(<>
                    {value}
                    <OverlayTrigger
                        placement="top"
                        overlay={
                            <Tooltip style={{ position: 'fixed' }}>
                                {row.original.libelleChamp}
                            </Tooltip>
                        }
                    >
                        <span>
                            <FontAwesomeIcon
                                icon='info-circle'
                                transform="shrink-1"
                                className="ms-1 text-info"
                            />
                        </span>
                    </OverlayTrigger>
                </>);
			},
        },
        {
            accessor: 'detailsChamps',
            Header: 'Contenu du champ',
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    HabilitationService.habilitations['vehicules_modification'] ? 
                        <IconButton
                            icon='trash'
                            size = 'sm'
                            variant="outline-danger"
                            className="me-1"
                            onClick={()=>{supprimerEntree(row.original.idVehiculeChampCG)}}
                        >Supprimer (sans confirmation)</IconButton>
                    : null
                );
			},
        },
    ];
    
    //partie formulaire
    const [isLoading, setIsLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(vehiculeCarteGriseForm),
    });

    const ajouter = async (data) => {
        try {
            setIsLoading(true);

            const addElement = await Axios.post('/vehicules/addCGdetails',
            {
                idVehicule : idVehicule,
                idChamp : data.idChamp,
                detailsChamps : data.detailsChamps,
            });

            reset();
            setPageNeedsRefresh(true);
            
            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    //delete    
    const supprimerEntree = async (idVehiculeChampCG) => {
        try {
            const response = await Axios.post('/vehicules/vehiculesDetailCGDelete',{
                idVehiculeChampCG: idVehiculeChampCG
            });

            setPageNeedsRefresh(true);
        } catch (error) {
            console.error(error)
        }
    }

    //modal
    const[showCGModal, setShowCGModal] = useState(false);
    const handleCloseCGModal = () => {
        setShowCGModal(false);
    }
    const handleShowCGModal = async () => {
        await initModal();
        setShowCGModal(true);
    }
    
    return(<>
        <IconButton
            icon='newspaper'
            size = 'sm'
            variant="outline-info"
            className="ms-2"
            onClick={handleShowCGModal}
        >Carte grise</IconButton>
        
        <Modal show={showCGModal} onHide={handleCloseCGModal} backdrop="static" fullscreen={true} keyboard={false}>
            <Modal.Header>
                <Modal.Title>Carte grise de {libelleVehicule}</Modal.Title>
                <FalconCloseButton onClick={handleCloseCGModal}/>
            </Modal.Header>
            <Modal.Body>
                {tableReady ?
                    <Row>
                        <Col md={8} className='mb-2'>
                            <GPMtable
                                columns={colonnes}
                                data={carteGrise}
                                topButton={<i>L'original de la carte grise doit être chargée dans les pièces jointes</i>}
                                topButtonShow={true}
                            />
                        </Col>
                        <Col md={4}>
                            {HabilitationService.habilitations['vehicules_modification'] ? 
                                <>
                                    <h5 className='mb-2'>Ajouter un champ</h5>
                                    <Form onSubmit={handleSubmit(ajouter)}>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Champ</Form.Label>
                                            <Select
                                                id="idChamp"
                                                name="idChamp"
                                                size="sm"
                                                classNamePrefix="react-select"
                                                closeMenuOnSelect={true}
                                                isClearable={true}
                                                isSearchable={true}
                                                isDisabled={isLoading}
                                                placeholder='Aucun champ selectionné'
                                                options={champsCarteGrise}
                                                value={champsCarteGrise.find(c => c.value === watch("idChamp"))}
                                                onChange={val => val != null ? setValue("idChamp", val.value) : setValue("idChamp", null)}
                                            />
                                            <small className="text-danger">{errors.idChamp?.message}</small>
                                        </Form.Group>
                                        <Form.Group className="mb-3">
                                            <Form.Label>Valeur du champ</Form.Label>
                                            <Form.Control size="sm" type="text" name="detailsChamps" id="detailsChamps" value={watch("detailsChamps")} {...register("detailsChamps")}/>
                                            <small className="text-danger">{errors.detailsChamps?.message}</small>
                                        </Form.Group>
                                        
                                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                                    </Form>
                                </>
                            : null}
                        </Col>
                    </Row>
                :
                'Chargement en cours'
                }
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseCGModal}>Fermer</Button>
            </Modal.Footer>
        </Modal>
    </>);
};

VehiculeCarteGrise.propTypes = {};

export default VehiculeCarteGrise;
