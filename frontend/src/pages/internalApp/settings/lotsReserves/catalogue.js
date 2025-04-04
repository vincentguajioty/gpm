import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Modal, Table, Row, Col, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';
import { catalogueDelete } from 'helpers/deleteModalWarningContent';
import GPMtable from 'components/gpmTable/gpmTable';
import SoftBadge from 'components/common/SoftBadge';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { catalogueForm, catalogueMergeForm } from 'helpers/yupValidationSchema';

const CatalogueExport = ({}) => {
    const [isLoading, setLoading] = useState(false);

    const requestExport = async () => {
        try {
            setLoading(true);

            let fileRequest = await Axios.get('/settingsMetiers/genererExportCatalogueMateriel');

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

    return(
            <IconButton
                icon='download'
                size = 'sm'
                variant="outline-info"
                onClick={requestExport}
                className='me-1'
                disabled={isLoading}
            >{isLoading ? "Génération en cours" : "Télécharger un état des lieux complet"}</IconButton>
    );
}

const ComparaisonDeuxItems = ({
    idMaterielCatalogueDELETE = null,
    idMaterielCatalogueKEEP = null,
    catalogueEnrichi = [],
}) => {
    const [elementDelete, setElementDelete] = useState();
    const [elementKeep, setElementKeep] = useState();

    useEffect(() => {
        let tempElement = catalogueEnrichi.filter(item => item.idMaterielCatalogue == idMaterielCatalogueDELETE);
		setElementDelete(tempElement[0]);
        tempElement = catalogueEnrichi.filter(item => item.idMaterielCatalogue == idMaterielCatalogueKEEP);
		setElementKeep(tempElement[0]);

	}, [idMaterielCatalogueDELETE, idMaterielCatalogueKEEP])

    const nl2br = require('react-nl2br');

    if(idMaterielCatalogueDELETE != null && elementDelete && idMaterielCatalogueKEEP != null && elementKeep)
    {
        return(
            <Table className="fs--1 mt-3" size='sm' bordered responsive>
                <tr bgcolor="yellow">
                    <th colSpan={3}><center>Les éléments en jaune nécessitent une vérification manuelle.</center></th>
                </tr>
                <tr>
                    <th></th>
                    <th>Element à supprimer</th>
                    <th>Element à garder</th>
                </tr>
                <tr>
                    <th>Référence technique</th>
                    <td>{elementDelete.idMaterielCatalogue}</td>
                    <td>{elementKeep.idMaterielCatalogue}</td>
                </tr>
                <tr>
                    <th>Catégorie</th>
                    <td>{elementDelete.libelleCategorie}</td>
                    <td>{elementKeep.libelleCategorie}</td>
                </tr>
                <tr>
                    <th>Module</th>
                    <td>
                        <SoftBadge className='me-1 mb-1' bg={elementDelete.modules_ope ? 'success' : 'secondary'}>Opérationnel (lots et réserves)</SoftBadge><br/>
                        <SoftBadge className='me-1 mb-1' bg={elementDelete.modules_vehicules ? 'success' : 'secondary'}>Véhicules</SoftBadge><br/>
                        <SoftBadge className='me-1 mb-1' bg={elementDelete.modules_tenues ? 'success' : 'secondary'}>Tenues</SoftBadge><br/>
                        <SoftBadge className='me-1 mb-1' bg={elementDelete.modules_vhf ? 'success' : 'secondary'}>Transmissions</SoftBadge>
                    </td>
                    <td>
                        <SoftBadge className='me-1 mb-1' bg={elementKeep.modules_ope ? 'success' : 'secondary'}>Opérationnel (lots et réserves)</SoftBadge><br/>
                        <SoftBadge className='me-1 mb-1' bg={elementKeep.modules_vehicules ? 'success' : 'secondary'}>Véhicules</SoftBadge><br/>
                        <SoftBadge className='me-1 mb-1' bg={elementKeep.modules_tenues ? 'success' : 'secondary'}>Tenues</SoftBadge><br/>
                        <SoftBadge className='me-1 mb-1' bg={elementKeep.modules_vhf ? 'success' : 'secondary'}>Transmissions</SoftBadge>
                    </td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Disponible bénévoles</th>
                    <td >{elementDelete.disponibleBenevolesConso ? "Oui" : "Non"}</td>
                    <td>{elementKeep.disponibleBenevolesConso ? "Oui" : "Non"}</td>
                </tr>
                <tr>
                    <th>Commentaires</th>
                    <td>{nl2br(elementDelete.commentairesMateriel)}</td>
                    <td>{nl2br(elementKeep.commentairesMateriel)}</td>
                </tr>
                <tr>
                    <th>Fournisseur</th>
                    <td>{elementDelete.nomFournisseur}</td>
                    <td>{elementKeep.nomFournisseur}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Sterilite</th>
                    <td>{elementDelete.sterilite ? "Oui" : "Non"}</td>
                    <td>{elementKeep.sterilite ? "Oui" : "Non"}</td>
                </tr>
                <tr>
                    <th>Taille</th>
                    <td>{elementDelete.taille}</td>
                    <td>{elementKeep.taille}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Anticipation forcée de la péremption dans les lots</th>
                    <td>{elementDelete.peremptionAnticipationOpe} j</td>
                    <td>{elementKeep.peremptionAnticipationOpe} j</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Anticipation forcée de la péremption dans les réserves</th>
                    <td>{elementDelete.peremptionAnticipationRes} j</td>
                    <td>{elementKeep.peremptionAnticipationRes} j</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Anticipation forcée de la péremption dans le stock tenues</th>
                    <td>{elementDelete.peremptionAnticipationTenues} j</td>
                    <td>{elementKeep.peremptionAnticipationTenues} j</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Anticipation forcée de la péremption dans le stock transmissions</th>
                    <td>{elementDelete.peremptionAnticipationVHF} j</td>
                    <td>{elementKeep.peremptionAnticipationVHF} j</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Anticipation forcée de la péremption dans le stock véhicules</th>
                    <td>{elementDelete.peremptionAnticipationVehicule} j</td>
                    <td>{elementKeep.peremptionAnticipationVehicule} j</td>
                </tr>
                <tr>
                    <th>Nombre de codes barres liés</th>
                    <td>{elementDelete.nbCodesBarres}</td>
                    <td>{elementKeep.nbCodesBarres}</td>
                </tr>
                <tr>
                    <th>Nombre d'apparitions dans les commandes</th>
                    <td>{elementDelete.nbCommandes}</td>
                    <td>{elementKeep.nbCommandes}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Nombre d'apparitions dans les lots opérationnels</th>
                    <td>{elementDelete.nbLotsOpe}</td>
                    <td>{elementKeep.nbLotsOpe}</td>
                </tr>
                <tr>
                    <th>Nombre d'apparitions dans les inventaires de lots</th>
                    <td>{elementDelete.nbInventairesLots}</td>
                    <td>{elementKeep.nbInventairesLots}</td>
                </tr>
                <tr>
                    <th>Nombre d'apparitions dans les consommations bénévoles</th>
                    <td>{elementDelete.nbConsommationLots}</td>
                    <td>{elementKeep.nbConsommationLots}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Nombre d'apparitions dans les référentiels</th>
                    <td>{elementDelete.nbReferentiels}</td>
                    <td>{elementKeep.nbReferentiels}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Nombre d'apparitions dans les réserves</th>
                    <td>{elementDelete.nbReserves}</td>
                    <td>{elementKeep.nbReserves}</td>
                </tr>
                <tr>
                    <th>Nombre d'apparitions dans les inventaires de réserve</th>
                    <td>{elementDelete.nbInventairesReserves}</td>
                    <td>{elementKeep.nbInventairesReserves}</td>
                </tr>
                <tr>
                    <th>Nombre d'apparitions dans les affectations de tenues</th>
                    <td>{elementDelete.nbTenuesAffectees}</td>
                    <td>{elementKeep.nbTenuesAffectees}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Nombre d'apparitions dans le stock de tenues</th>
                    <td>{elementDelete.nbTenuesStock}</td>
                    <td>{elementKeep.nbTenuesStock}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Nombre d'apparitions dans le stock de véhicules</th>
                    <td>{elementDelete.nbVehiculesStock}</td>
                    <td>{elementKeep.nbVehiculesStock}</td>
                </tr>
                <tr bgcolor="yellow">
                    <th>Nombre d'apparitions dans le stock de transmissions</th>
                    <td>{elementDelete.nbVHFStock}</td>
                    <td>{elementKeep.nbVHFStock}</td>
                </tr>
            </Table>
        )
    }else{
        return(<><i className='mb-3'>Selectionnez deux éléments dans les listes ci-dessus<br/></i></>)
    }
}

const FilterCatalogue = ({
    categories = [],
    modules = [
        {value: 'modules_ope', label:'Opérationnel'},
        {value: 'modules_tenues', label:'Tenues'},
        {value: 'modules_vhf', label:'Transmissions'},
        {value: 'modules_vehicules', label:'Véhicules'},
    ],
    catalogue = [],
    setCatalogueFiltered,
}) => {
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(),
    });

    useEffect(() => {
		for(const module of modules)
        {
            setValue(`mod-`+module.value, true)
        }

        for(const cat of categories)
        {
            setValue(`cat-`+cat.value, false)
        }
	}, [])

    useEffect(() => {
		let tempArray = [];

        for(const module of modules)
        {
            if(watch(`mod-`+module.value) == true)
            {
                for(const item of catalogue.filter(item => item[module.value] == true))
                {
                    tempArray.push(item)
                }
            }
        }

        for(const cat of categories)
        {
            if(watch(`cat-`+cat.value) == true)
            {
                for(const item of catalogue.filter(item => item.idCategorie == cat.value))
                {
                    tempArray.push(item)
                }
            }
        }

        setCatalogueFiltered(tempArray);
	}, [watch()])

    return(<>
        <p><u>Filtrer par module ou par catégorie:</u></p>

        {modules.map((module, i)=>{return(
            <Form.Check 
                id={`mod-`+module.value}
                name={module.label}
                checked={watch(`mod-`+module.value)}
                onClick={()=>{setValue(`mod-`+module.value, !watch(`mod-`+module.value))}}
                type='switch'
                label={module.label}
            />
        )})}
        <hr/>
        {categories.map((cat, i)=>{return(
            <Form.Check 
                id={`cat-`+cat.value}
                name={cat.label}
                checked={watch(`cat-`+cat.value)}
                onClick={()=>{setValue(`cat-`+cat.value, !watch(`cat-`+cat.value))}}
                type='switch'
                label={cat.label}
            />
        )})}
    </>)
}

const Catalogue = () => {
    /*PAGE BASICS*/
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [catalogue, setCatalogue] = useState([]);
    const [catalogueFiltered, setCatalogueFiltered] = useState([]);
    const [categories, setCategories] = useState([]);
    const [fournisseurs, setFournisseurs] = useState([]);

    const initTable = async () => {
        try {
            let getFromDb = await Axios.get('/settingsMetiers/getCatalogueMateriel');
            setCatalogue(getFromDb.data);
            setCatalogueFiltered(getFromDb.data);

            getFromDb = await Axios.get('/select/getCategoriesMateriels');
            setCategories(getFromDb.data);

            getFromDb = await Axios.get('/select/getFournisseurs');
            setFournisseurs(getFromDb.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
		initTable();
	}, [])

    /* TABLE FOR DISPLAY */
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'idMaterielCatalogue',
            Header: '#',
        },
        {
            accessor: 'libelleMateriel',
            Header: 'Libellé et dispo bénévole',
            Cell: ({ value, row }) => {
				return(<>
                    <SoftBadge className='mt-1 me-1' bg={row.original.disponibleBenevolesConso ? 'warning' : 'success'}>
                        <FontAwesomeIcon icon={row.original.disponibleBenevolesConso ? 'check' : 'lock'} />
                    </SoftBadge>
                    {value} {row.original.taille ? ' (taille '+row.original.taille+')' : null}
                </>);
			},
        },
        {
            accessor: 'libelleCategorie',
            Header: 'Catégorie',
            Cell: ({ value, row }) => {
				return(<>
                    {value}
                    <br/>
                    {row.original.modules_ope ? <SoftBadge className='me-1 mb-1' bg={'success'}>Opérationnel (lots et réserves)</SoftBadge> : null}
                    {row.original.modules_vehicules ? <SoftBadge className='me-1 mb-1' bg={'success'}>Véhicules</SoftBadge> : null}
                    {row.original.modules_tenues ? <SoftBadge className='me-1 mb-1' bg={'success'}>Tenues</SoftBadge> : null}
                    {row.original.modules_vhf ? <SoftBadge className='me-1 mb-1' bg={'success'}>Transmissions</SoftBadge> : null}
                </>);
			},
        },
        {
            accessor: 'peremptionAnticipationOpe',
            Header: 'Anticipation de péremption',
            Cell: ({ value, row }) => {
				return(<>
                    {row.original.peremptionAnticipationOpe != null ? <>Lots: {row.original.peremptionAnticipationOpe}j<br/></> : null}
                    {row.original.peremptionAnticipationRes != null ? <>Réserves: {row.original.peremptionAnticipationRes}j<br/></> : null}
                    {row.original.peremptionAnticipationVehicule != null ? <>Véhicules: {row.original.peremptionAnticipationVehicule}j<br/></> : null}
                    {row.original.peremptionAnticipationTenues != null ? <>Tenues: {row.original.peremptionAnticipationTenues}j<br/></> : null}
                    {row.original.peremptionAnticipationVHF != null ? <>Transmissions: {row.original.peremptionAnticipationVHF}j<br/></> : null}
                </>);
			},
        },
        {
            accessor: 'commentairesMateriel',
            Header: 'Commentaires',
            Cell: ({ value, row }) => {
				return(nl2br(value));
			},
        },
        {
            accessor: 'nbCodesBarre',
            Header: 'Codes barre liés',
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        {HabilitationService.habilitations['catalogue_modification'] ? 
                            <ActionButton onClick={() => handleShowOffCanevas(row.original.idMaterielCatalogue)} icon="pen" title="Modifier" variant="action" className="p-0 me-2" />
                        : null }
                        {HabilitationService.habilitations['catalogue_suppression'] ? 
                            <ActionButton onClick={() => handleShowDeleteModal(row.original.idMaterielCatalogue)} icon="trash" title="Supprimer" variant="action" className="p-0" />
                        : null }
                    </>
                );
			},
        },
    ];

    /* FORM */
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [offCanevasIdMaterielCatalogue, setOffCanevasIdMaterielCatalogue] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(catalogueForm),
    });
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        setOffCanevasIdMaterielCatalogue();
        reset();
        setLoading(false);
    }
    const handleShowOffCanevas = async (idMaterielCatalogue) => {
        try {
            setOffCanevasIdMaterielCatalogue(idMaterielCatalogue);

            if(idMaterielCatalogue > 0)
            {
                let oneItemFromArray = catalogue.filter(ligne => ligne.idMaterielCatalogue == idMaterielCatalogue)[0];
                setValue("libelleMateriel", oneItemFromArray.libelleMateriel);
                setValue("idCategorie", oneItemFromArray.idCategorie);
                setValue("taille", oneItemFromArray.taille);
                setValue("sterilite", oneItemFromArray.sterilite);
                setValue("conditionnementMultiple", oneItemFromArray.conditionnementMultiple);
                setValue("commentairesMateriel", oneItemFromArray.commentairesMateriel);
                setValue("idFournisseur", oneItemFromArray.idFournisseur);
                setValue("peremptionAnticipationOpe", oneItemFromArray.peremptionAnticipationOpe);
                setValue("peremptionAnticipationRes", oneItemFromArray.peremptionAnticipationRes);
                setValue("peremptionAnticipationVehicule", oneItemFromArray.peremptionAnticipationVehicule);
                setValue("peremptionAnticipationTenues", oneItemFromArray.peremptionAnticipationTenues);
                setValue("peremptionAnticipationVHF", oneItemFromArray.peremptionAnticipationVHF);
                setValue("disponibleBenevolesConso", oneItemFromArray.disponibleBenevolesConso ? true : false)

                if(oneItemFromArray.modules_ope){setValue("module", 'modules_ope')}
                if(oneItemFromArray.modules_vehicules){setValue("module", 'modules_vehicules')}
                if(oneItemFromArray.modules_tenues){setValue("module", 'modules_tenues')}
                if(oneItemFromArray.modules_vhf){setValue("module", 'modules_vhf')}
            }else{
                setValue("module", 'modules_ope')
            }

            setShowOffCanevas(true);
        } catch (error) {
            console.error(error)
        }
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(offCanevasIdMaterielCatalogue > 0)    
            {
                const response = await Axios.post('/settingsMetiers/updateCatalogueMateriel',{
                    idMaterielCatalogue: offCanevasIdMaterielCatalogue,
                    libelleMateriel: data.libelleMateriel,
                    idCategorie: data.idCategorie,
                    taille: data.taille,
                    sterilite: data.sterilite,
                    conditionnementMultiple: data.conditionnementMultiple,
                    commentairesMateriel: data.commentairesMateriel,
                    idFournisseur: data.idFournisseur,
                    peremptionAnticipationOpe: data.module == 'modules_ope' ? data.peremptionAnticipationOpe : null,
                    peremptionAnticipationRes: data.module == 'modules_ope' ? data.peremptionAnticipationRes : null,
                    peremptionAnticipationVehicule: data.module == 'modules_vehicules' ? data.peremptionAnticipationVehicule : null,
                    peremptionAnticipationTenues: data.module == 'modules_tenues' ? data.peremptionAnticipationTenues : null,
                    peremptionAnticipationVHF: data.module == 'modules_vhf' ? data.peremptionAnticipationVHF : null,
                    disponibleBenevolesConso: data.disponibleBenevolesConso  || 0,
                });
            }
            else
            {
                const response = await Axios.post('/settingsMetiers/addCatalogueMateriel',{
                    libelleMateriel: data.libelleMateriel,
                    idCategorie: data.idCategorie,
                    taille: data.taille,
                    sterilite: data.sterilite,
                    conditionnementMultiple: data.conditionnementMultiple,
                    commentairesMateriel: data.commentairesMateriel,
                    idFournisseur: data.idFournisseur,
                    peremptionAnticipationOpe: data.module == 'modules_ope' ? data.peremptionAnticipationOpe : null,
                    peremptionAnticipationRes: data.module == 'modules_ope' ? data.peremptionAnticipationRes : null,
                    peremptionAnticipationVehicule: data.module == 'modules_vehicules' ? data.peremptionAnticipationVehicule : null,
                    peremptionAnticipationTenues: data.module == 'modules_tenues' ? data.peremptionAnticipationTenues : null,
                    peremptionAnticipationVHF: data.module == 'modules_vhf' ? data.peremptionAnticipationVHF : null,
                    disponibleBenevolesConso: data.disponibleBenevolesConso || 0,
                    modules_ope: data.module == 'modules_ope' ? true : false,
                    modules_vehicules: data.module == 'modules_vehicules' ? true : false,
                    modules_tenues: data.module == 'modules_tenues' ? true : false,
                    modules_vhf: data.module == 'modules_vhf' ? true : false,
                });
            }

            handleCloseOffCanevas();
            initTable();
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdMaterielCatalogue, setDeleteModalIdMaterielCatalogue] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdMaterielCatalogue();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdMaterielCatalogue(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/settingsMetiers/deleteCatalogueMateriel',{
                idMaterielCatalogue: deleteModalIdMaterielCatalogue,
            });
            
            initTable();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    /* MERGE MODAL */
    const [warningMergeAccepted, setWarningMergeAccepted] = useState(false);
    const [showMergeModal, setShowMergeModal] = useState(false);
    const [catalogueEnrichi, setCatalogueEnrichi] = useState([]);
    const { register: registerMerge, handleSubmit: handleSubmitMerge, formState: { errors: errorsMerge }, setValue: setValueMerge, reset: resetMerge, watch: watchMerge } = useForm({
        resolver: yupResolver(catalogueMergeForm),
    });

    const handleCloseMergeModal = () => {
        setShowMergeModal(false);
        setLoading(false);
        resetMerge();
        setCatalogueEnrichi([]);
        setWarningMergeAccepted(false);
    };
    const handleShowMergeModal = async () => {
        try {
            setShowMergeModal(true);
            setLoading(true);

            let getFromDb = await Axios.get('/settingsMetiers/getCatalogueEnrichi');
            setCatalogueEnrichi(getFromDb.data);

            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    };

    const mergeCatalogue = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/settingsMetiers/mergeCatalogueItems',{
                idMaterielCatalogueDELETE: data.idMaterielCatalogueDELETE,
                idMaterielCatalogueKEEP: data.idMaterielCatalogueKEEP,
            });
            
            initTable();
            handleCloseMergeModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    useEffect(() => {
		if(watchMerge("idMaterielCatalogueDELETE") == watchMerge("idMaterielCatalogueKEEP"))
        {
            setValueMerge("alerteISO", true);
        }else{
            setValueMerge("alerteISO", false);
        }
	}, [
        watchMerge("idMaterielCatalogueDELETE"),
        watchMerge("idMaterielCatalogueKEEP"),
    ])

    /* RENDER */
    return (
        readyToDisplay ?
            <>
                <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
                    <Modal.Header>
                        <Modal.Title>Suppression</Modal.Title>
                        <FalconCloseButton onClick={handleCloseDeleteModal}/>
                    </Modal.Header>
                    <Modal.Body>{catalogueDelete}</Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={handleCloseDeleteModal}>
                            Annuler
                        </Button>
                        <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
                    </Modal.Footer>
                </Modal>

                <Modal show={showMergeModal} onHide={handleCloseMergeModal} backdrop="static" keyboard={false} size='lg'>
                    <Modal.Header>
                        <Modal.Title>Fusionner deux éléments du catalogue</Modal.Title>
                        <FalconCloseButton onClick={handleCloseMergeModal}/>
                    </Modal.Header>
                    <Modal.Body>
                        {!warningMergeAccepted ?
                            <>
                                <p><center><b>PREREQUIS</b></center></p>
                                <p>
                                    Ce formulaire permet de fusionner deux éléments du catalogue. Il va vous falloir sélectionner un élément à garder, et un à supprimer. Ces éléments sont utilisés dans différents modules. Le comportement de la fusion est le suivant:
                                    <br/>
                                    <br/>
                                    Les éléments suivants seront automatiquement migrés, aucun prérequis nécessaire:
                                    <ul>
                                        <li>Les codes barre: tous les codes barre de l'élément à supprimer seront rattachés à l'élément à garder.</li>
                                        <li>Les éléments commandés: toutes les itérations de commande de l'élément à supprimer seront portées par l'élément à garder.</li>
                                        <li>Contenus d'inventaires de lots opérationnels et réserves: les quantités des deux éléments seront sommées et la date de péremption la moins grande sera gardée. L'inventaire n'affichera donc qu'un seul item (celui à garder) qui correspondra à l'agrégat précédemment expliqué de cet item à garder et de celui à supprimer.</li>
                                        <li>Rapports de consommation: toutes les itérations dans les rapports bénévoles de l'élément à supprimer seront portées par l'élément à garder. Si l'élément à garder figure déjà dans le rapport, aucun agrégat ne sera fait et la ligne apparaitra donc deux fois.</li>
                                        <li>Les affectations de tenues: toutes les itérations de tenues affectées liées à l'élément à supprimer seront portées par l'élément à garder.</li>
                                    </ul>
                                    Les éléments suivants doivent être gérés <u>manuellement avant d'effectuer la fusion</u>:
                                    <ul>
                                        <li>Elements présents dans les lots opérationnels et réserves: un même lot/sac/emplacement ne peut pas contenir plusieurs item identiques. Tous les éléments sélectionnés comme étant à supprimer seront migrés vers l'élément à garder: il convient donc de s'assurer qu'il n'y ait pas déjà, dans le même lot/sac/emplacement, les éléments à supprimer et à garder qui coexistent. Si c'est le cas, il faut gérer la transition manuellement. La réflexion est la même pour les réserves et les conteneurs.</li>
                                        <li>Les référentiels: tout comme pour les lots et réserves, les référentiels contiennent des éléments du catalogue, et ne peuvent contenir plusieurs fois le même élément. Il faut donc s'assurer en amont que l'élément à supprimer et l'élément à garder n'apparaissent pas tous les deux dans le même référentiel afin d'éviter leur colision lors de la fusion.</li>
                                        <li>Elements présents dans le stock des tenues, véhicules, transmissions: tout comme pour les lots et réserves, les stocks ne peuvent contenir plusieurs fois le même élément. Il faut donc s'assurer en amont que l'élément à supprimer et l'élément à garder n'apparaissent pas tous les deux dans les stocks afin d'éviter leur colision lors de la fusion.</li>
                                    </ul>
                                    Si ces éléments ne sont pas gérés manuellement, ils mettront la fusion en échec. Il vous faudra alors refaire une passe sur ces prérequis et ré-executer la fusion.
                                    <br/>
                                    <br/>
                                    Avant de valider la fusion de deux éléments un tableau comparatif des deux éléments est affiché. Vérifiez bien les points d'attention surlignés en jaune avant de valider.
                                </p>

                                <IconButton
                                    icon='check'
                                    size = 'sm'
                                    variant="success"
                                    className="me-1"
                                    onClick={()=>{setWarningMergeAccepted(true)}}
                                >J'ai compris, continuer vers le formulaire</IconButton>
                            </>
                        :
                            <>                                
                                <p><small className="text-danger">{errorsMerge.alerteISO?.message}</small></p>
                                
                                {isLoading ?
                                    <LoaderInfiniteLoop/>
                                :
                                    <Form onSubmit={handleSubmitMerge(mergeCatalogue)}>
                                        <Row>
                                            <Col md={6}>
                                                <Form.Group className="mb-3">
                                                    <Form.Label>Item à <b><u>supprimer</u></b>:</Form.Label>
                                                    <Select
                                                        id="idMaterielCatalogueDELETE"
                                                        name="idMaterielCatalogueDELETE"
                                                        size="sm"
                                                        classNamePrefix="react-select"
                                                        closeMenuOnSelect={true}
                                                        isClearable={true}
                                                        isSearchable={true}
                                                        isDisabled={isLoading}
                                                        placeholder='Selectionner un matériel'
                                                        options={catalogueEnrichi}
                                                        value={catalogueEnrichi.find(c => c.value === watchMerge("idMaterielCatalogueDELETE"))}
                                                        onChange={val => val != null ? setValueMerge("idMaterielCatalogueDELETE", val.value) : setValueMerge("idMaterielCatalogueDELETE", null)}
                                                    />
                                                    <small className="text-danger">{errorsMerge.idMaterielCatalogueDELETE?.message}</small>
                                                </Form.Group>
                                            </Col>
                                            <Col md={6}>
                                                <Form.Group className="mb-3">
                                                    <Form.Label>Item à <b><u>garder</u></b>:</Form.Label>
                                                    <Select
                                                        id="idMaterielCatalogueKEEP"
                                                        name="idMaterielCatalogueKEEP"
                                                        size="sm"
                                                        classNamePrefix="react-select"
                                                        closeMenuOnSelect={true}
                                                        isClearable={true}
                                                        isSearchable={true}
                                                        isDisabled={isLoading}
                                                        placeholder='Selectionner un matériel'
                                                        options={catalogueEnrichi}
                                                        value={catalogueEnrichi.find(c => c.value === watchMerge("idMaterielCatalogueKEEP"))}
                                                        onChange={val => val != null ? setValueMerge("idMaterielCatalogueKEEP", val.value) : setValueMerge("idMaterielCatalogueKEEP", null)}
                                                    />
                                                    <small className="text-danger">{errorsMerge.idMaterielCatalogueKEEP?.message}</small>
                                                </Form.Group>
                                            </Col>
                                        </Row>

                                        <Form.Label>Comparaison:</Form.Label>

                                        <ComparaisonDeuxItems
                                            idMaterielCatalogueDELETE={watchMerge("idMaterielCatalogueDELETE")}
                                            idMaterielCatalogueKEEP={watchMerge("idMaterielCatalogueKEEP")}
                                            catalogueEnrichi={catalogueEnrichi}
                                        />

                                        <p><small className="text-danger">{errorsMerge.alerteISO?.message}</small></p>

                                        <Button variant='warning' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Fusionner'}</Button>
                                    </Form>
                                }
                            </>
                        }
                    </Modal.Body>
                    <Modal.Footer>
                        <Button variant="secondary" onClick={handleCloseMergeModal}>
                            Annuler
                        </Button>
                    </Modal.Footer>
                </Modal>

                <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
                    <Offcanvas.Header closeButton >
                        <Offcanvas.Title>{offCanevasIdMaterielCatalogue > 0 ? "Modification" : "Ajout"} d'un item dans le catalogue commun</Offcanvas.Title>
                    </Offcanvas.Header>
                    <Offcanvas.Body>
                        <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                            {offCanevasIdMaterielCatalogue == 0 ?
                                <Form.Group className="mb-3">
                                    <Form.Label>Modules concernés par ce matériel:</Form.Label>
                                    <Form.Check 
                                        key="modules_ope"
                                        type='radio'
                                        id={`radio-modules_ope`}
                                        label='Opérationnel'
                                        name='radio'
                                        checked={watch("module") == 'modules_ope'}
                                        onClick={(e) => setValue('module', 'modules_ope')}
                                    />
                                    <Form.Check 
                                        key="modules_vehicules"
                                        type='radio'
                                        id={`radio-modules_vehicules`}
                                        label='Véhicules'
                                        name='radio'
                                        checked={watch("module") == 'modules_vehicules'}
                                        onClick={(e) => setValue('module', 'modules_vehicules')}
                                    />
                                    <Form.Check 
                                        key="modules_tenues"
                                        type='radio'
                                        id={`radio-modules_tenues`}
                                        label='Tenues'
                                        name='radio'
                                        checked={watch("module") == 'modules_tenues'}
                                        onClick={(e) => setValue('module', 'modules_tenues')}
                                    />
                                    <Form.Check 
                                        key="modules_vhf"
                                        type='radio'
                                        id={`radio-modules_vhf`}
                                        label='Transmissions'
                                        name='radio'
                                        checked={watch("module") == 'modules_vhf'}
                                        onClick={(e) => setValue('module', 'modules_vhf')}
                                    />
                                    <small className="text-danger">{errors.module?.message}</small>
                                </Form.Group>
                            : null}

                            <Form.Group className="mb-3">
                                <Form.Label>Libellé</Form.Label>
                                <Form.Control type="text" id="libelleMateriel" name="libelleMateriel" {...register("libelleMateriel")}/>
                                <small className="text-danger">{errors.libelleMateriel?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Catégorie</Form.Label>
                                <Select
                                    id="idCategorie"
                                    name="idCategorie"
                                    size="sm"
                                    classNamePrefix="react-select"
                                    closeMenuOnSelect={true}
                                    isClearable={true}
                                    isSearchable={true}
                                    isDisabled={isLoading}
                                    placeholder='Aucune catégorie selectionnée'
                                    options={categories}
                                    value={categories.find(c => c.value === watch("idCategorie"))}
                                    onChange={val => val != null ? setValue("idCategorie", val.value) : setValue("idCategorie", null)}
                                />
                                <small className="text-danger">{errors.idCategorie?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Taille</Form.Label>
                                <Form.Control type="text" id="taille" name="taille" {...register("taille")}/>
                                <small className="text-danger">{errors.taille?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Conditionnement</Form.Label>
                                <Form.Control type="text" id="conditionnementMultiple" name="conditionnementMultiple" {...register("conditionnementMultiple")}/>
                                <small className="text-danger">{errors.conditionnementMultiple?.message}</small>
                            </Form.Group>

                            <Form.Group className="mb-3">
                                <Form.Label>Stérilité</Form.Label>
                                <Form.Check
                                    type='switch'
                                    id="sterilite"
                                    name="sterilite"
                                    label='Une date de péremption est obligatoire pour ce matériel'
                                    checked={watch("sterilite")}
                                    onClick={(e)=>{setValue("sterilite", !watch("sterilite"))}}
                                />
                                <small className="text-danger">{errors.sterilite?.message}</small>
                                {watch("sterilite") ?
                                    <>
                                        {watch("module") == 'modules_ope' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption dans les lots (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationOpe" name="peremptionAnticipationOpe" {...register("peremptionAnticipationOpe")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationOpe?.message}</small>
                                                <br/>
                                                <Form.Label>Anticipation de la péremption dans les réserves (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationRes" name="peremptionAnticipationRes" {...register("peremptionAnticipationRes")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationRes?.message}</small>
                                            </>
                                        : null}

                                        {watch("module") == 'modules_vehicules' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption pour les véhicules (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationVehicule" name="peremptionAnticipationVehicule" {...register("peremptionAnticipationVehicule")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationVehicule?.message}</small>
                                            </>
                                        : null}

                                        {watch("module") == 'modules_tenues' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption pour les tenues (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationTenues" name="peremptionAnticipationTenues" {...register("peremptionAnticipationTenues")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationTenues?.message}</small>
                                            </>
                                        : null}

                                        {watch("module") == 'modules_vhf' ?
                                            <>
                                                <Form.Label>Anticipation de la péremption pour les transmissions (jours)</Form.Label>
                                                <Form.Control type="number" min="0" step="1" id="peremptionAnticipationVHF" name="peremptionAnticipationVHF" {...register("peremptionAnticipationVHF")}/>
                                                <small className="text-danger">{errors.peremptionAnticipationVHF?.message}</small>
                                            </>
                                        : null}
                                    </>
                                :null}
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
                                    isSearchable={false}
                                    isDisabled={isLoading}
                                    placeholder='Aucun fournisseur selectionné'
                                    options={fournisseurs}
                                    value={fournisseurs.find(c => c.value === watch("idFournisseur"))}
                                    onChange={val => val != null ? setValue("idFournisseur", val.value) : setValue("idFournisseur", null)}
                                />
                                <small className="text-danger">{errors.idFournisseur?.message}</small>
                            </Form.Group>

                            {watch("module") == 'modules_ope' ?
                                <Form.Group className="mb-3">
                                    <Form.Label>Accessible hors connexion</Form.Label>
                                    <Form.Check
                                        type='switch'
                                        id="disponibleBenevolesConso"
                                        name="disponibleBenevolesConso"
                                        label='Matériel disponible pour les rapports de consommation'
                                        checked={watch("disponibleBenevolesConso")}
                                        onClick={(e)=>{setValue("disponibleBenevolesConso", !watch("disponibleBenevolesConso"))}}
                                    />
                                    <small className="text-danger">{errors.disponibleBenevolesConso?.message}</small>
                                </Form.Group>
                            : null}

                            {watch("module") == 'modules_tenues' ?
                                <Form.Group className="mb-3">
                                    <Form.Label>Accessible hors connexion</Form.Label>
                                    <Form.Check
                                        type='switch'
                                        id="disponibleBenevolesConso"
                                        name="disponibleBenevolesConso"
                                        label='Matériel disponible pour les demandes de prêts'
                                        checked={watch("disponibleBenevolesConso")}
                                        onClick={(e)=>{setValue("disponibleBenevolesConso", !watch("disponibleBenevolesConso"))}}
                                    />
                                    <small className="text-danger">{errors.disponibleBenevolesConso?.message}</small>
                                </Form.Group>
                            : null}

                            <Form.Group className="mb-3">
                                <Form.Label>Commentaires</Form.Label>
                                <Form.Control size="sm" as="textarea" rows={3} name={"commentairesMateriel"} id={"commentairesMateriel"} {...register("commentairesMateriel")}/>
                                <small className="text-danger">{errors.commentairesMateriel?.message}</small>
                            </Form.Group>

                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                        </Form>
                    </Offcanvas.Body>
                </Offcanvas>

                <FalconComponentCard noGuttersBottom className="mb-3">
                    <FalconComponentCard.Header
                        title="Catalogue du matériel"
                    >
                    </FalconComponentCard.Header>
                    <FalconComponentCard.Body
                        scope={{ ActionButton }}
                        noLight
                    >
                        <Row>
                            <Col md={2}>
                                <FilterCatalogue
                                    categories={categories}
                                    catalogue={catalogue}
                                    setCatalogueFiltered={setCatalogueFiltered}
                                />
                            </Col>
                            <Col md={10}>
                                <GPMtable
                                    columns={colonnes}
                                    data={catalogueFiltered}
                                    topButtonShow={true}
                                    topButton={<>
                                        {HabilitationService.habilitations['catalogue_ajout'] ? 
                                            <IconButton
                                                icon='plus'
                                                size = 'sm'
                                                variant="outline-success"
                                                className="me-1"
                                                onClick={() => handleShowOffCanevas(0)}
                                            >Nouvel élément</IconButton>
                                        : null}
                                        {HabilitationService.habilitations['catalogue_modification'] ? 
                                            <IconButton
                                                icon='code-branch'
                                                size = 'sm'
                                                variant="outline-info"
                                                className="me-1"
                                                onClick={handleShowMergeModal}
                                            >Fusionner</IconButton>
                                        : null}
                                        <CatalogueExport />
                                    </>}
                                />
                            </Col>
                        </Row>
                    </FalconComponentCard.Body>
                </FalconComponentCard>
            </>
        :
            <FalconComponentCard noGuttersBottom className="mb-3">
                <FalconComponentCard.Header
                    title="Catalogue du matériel"
                >
                </FalconComponentCard.Header>
                <FalconComponentCard.Body
                    scope={{ ActionButton }}
                    noLight
                    className="p-0"
                >
                    <LoaderInfiniteLoop />
                </FalconComponentCard.Body>
            </FalconComponentCard>
    );

};

Catalogue.propTypes = {};

export default Catalogue;