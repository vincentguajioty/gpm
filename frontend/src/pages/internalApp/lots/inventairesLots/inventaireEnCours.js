import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, Alert, } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import WidgetSectionTitle from 'components/widgets/WidgetSectionTitle';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';
import socketIO from 'socket.io-client';

import InventaireScanVolee from './scanVolee';
import InventaireParcoursManuel from './parcoursManuel';
import { Link } from 'react-router-dom';

const socket = socketIO.connect(window.__ENV__.APP_BACKEND_URL,{withCredentials: true, extraHeaders: {
    "token": HabilitationService.token
}});

const LotInventaireEnCours = () => {
    let {idInventaire} = useParams();
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [isClosed, setIsClosed] = useState(false);
    const [displaySocketError, setDisplaySocketError] = useState(false);

    const [detailsInventaire, setDetailsInventaire] = useState([]);
    const [inventaireElements, setInventaireElements] = useState([]);
    const [arborescenceSacs, setArborescenceSacs] = useState([]);
    const [catalogueCodesBarres, setCatalogueCodesBarres] = useState([]);

    const initPageFirstCharge = async () => {
        try {
            const getInventaireDetails = await Axios.post('/lots/getOneInventaireForDisplay',{
                idInventaire: idInventaire,
            });
            setDetailsInventaire(getInventaireDetails.data.inventaire)
            setIsClosed(!getInventaireDetails.data.inventaire.inventaireEnCours);
            
            const getArbo = await Axios.post('/lots/getArborescenceSacs',{
                idInventaire: idInventaire,
            });
            setArborescenceSacs(getArbo.data)

            const getInventaireElements = await Axios.post('/lots/getAllElementsInventaireEnCours',{
                idInventaire: idInventaire,
            });
            setInventaireElements(getInventaireElements.data);

            const getCatalogueCodeBarres = await Axios.get('/select/getCodesBarreCatalogue');
            setCatalogueCodesBarres(getCatalogueCodeBarres.data);
            
            setReadyToDisplay(true);

            await socket.emit("lot_inventaire_join", 'lot-'+idInventaire);
        } catch (error) {
            console.log(error)
        }
    }
    useEffect(()=>{
        initPageFirstCharge();
    },[])

    useEffect(() => {
        socket.removeAllListeners();
        
        socket.on("lot_inventaire_updateYourElement", (data)=>{
            let tempArray = [];
            for(const elem of inventaireElements)
            {
                if(elem.idElement == data.idElement)
                {
                    tempArray.push(data);
                }else{
                    tempArray.push(elem)
                }
            }
            setInventaireElements(tempArray);
        })

        socket.on("lot_inventaire_reloadPage", (data)=>{
            location.reload();
        })

        socket.on("lot_inventaire_validate", (data)=>{
            setIsClosed(true);
        })

        socket.on("connect_error", (error)=>{
            console.log(error);
            setDisplaySocketError(!socket.connected)
        })
	}, [socket, inventaireElements])

    const manageDemandePopullationPrecedente = async () => {
        try {
            await socket.emit("lot_inventaire_demandePopullationPrecedente", {idInventaire: idInventaire});
        } catch (error) {
            console.log(error)
        }
    }

    const validerInventaire = async (commentaire) => {
        try {
            await socket.emit("lot_inventaire_validate", {idInventaire: idInventaire, commentaire: commentaire||null});
            setIsClosed(true);
        } catch (e) {
            console.log(e);
        }
    }

    if(readyToDisplay)
    {
        return (<>
            <PageHeader
                preTitle="Lots opérationnels"
                title={"Inventaire en cours sur "+detailsInventaire.libelleLot}
                description={moment(detailsInventaire.dateInventaire).format('DD/MM/YYYY') + " par " + detailsInventaire.prenomPersonne + " " + detailsInventaire.nomPersonne}
                className="mb-3"
            />

            {displaySocketError ?
                <Alert variant='danger'>La connexion au serveur est perdue.</Alert>
            : null}

            {!HabilitationService.habilitations['lots_modification'] ?
                <Alert variant='warning'>De part vos habilitations, vous ne pouvez pas participer à l'inventaire, mais pouvez le consulter.</Alert>
            : null}

            {isClosed ?
                <Alert variant='success'>
                    Inventaire clos. Vous pouvez revenir au lot <Link to={'/lots/'+detailsInventaire.idLot}>{detailsInventaire.libelleLot}</Link>
                </Alert>
            :
                <Row>
                    <Col md={4}>
                        <WidgetSectionTitle
                            icon="barcode"
                            title="Méthode 1"
                            subtitle="Scan à la volée"
                            transform="shrink-2"
                            className="mb-4 mt-3"
                        />
                        <InventaireScanVolee
                            idInventaire={idInventaire}
                            socket={socket}
                            inventaireElements={inventaireElements}
                            arborescenceSacs={arborescenceSacs}
                            catalogueCodesBarres={catalogueCodesBarres}
                        />
                    </Col>
                    <Col md={8}>
                        <WidgetSectionTitle
                            icon="eye"
                            title="Méthode 2"
                            subtitle="Emplacement par Emplacement"
                            transform="shrink-2"
                            className="mb-4 mt-3"
                        />
                        <InventaireParcoursManuel
                            idInventaire={idInventaire}
                            socket={socket}
                            inventaireElements={inventaireElements}
                            arborescenceSacs={arborescenceSacs}
                            catalogueCodesBarres={catalogueCodesBarres}
                            manageDemandePopullationPrecedente={manageDemandePopullationPrecedente}
                            validerInventaire={validerInventaire}
                        />
                    </Col>
                </Row>
            }
        </>);
    }
    else
    {
        return(<LoaderInfiniteLoop/>)
    }
};

LotInventaireEnCours.propTypes = {};

export default LotInventaireEnCours;
