import React, {useState, useEffect} from 'react';
import { Row, Col, Alert, } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';
import { faHome } from '@fortawesome/free-solid-svg-icons';
import PageHeader from 'components/common/PageHeader';
import moment from 'moment-timezone';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';
import socketIO from 'socket.io-client';
const socket = socketIO.connect(window.__ENV__.APP_BACKEND_URL,{withCredentials: true, extraHeaders: {
    "token": 'PUBLIC_ACCESS'
}});

import AjouterConsommable from './ajouterConsommable';
import AfficherConsommable from './afficherConsommation';
import GestionReappro from './gererReappro';

const GererConsommation = ({
    idConsommation,
}) => {
    const [isLoading, setLoading] = useState(true);
    const [consommation, setConsommation] = useState([]);

    const initPage = async () => {
        try {
            await socket.emit("consommation_join_evenement", 'consommation-'+idConsommation);

            let getData = await Axios.post('/consommations/getOneConso',{
                idConsommation: idConsommation,
            });
            setConsommation(getData.data);
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    useEffect(()=>{
        socket.removeAllListeners();

        socket.on("consommation_addElement", (data)=>{
            let elementExisting = consommation.elements.filter(elem => elem.idConsommationMateriel == data.idConsommationMateriel);

            let tempArray = [];
            if(elementExisting.length == 1)
            {
                for(const elem of consommation.elements)
                {
                    if(elem.idConsommationMateriel == data.idConsommationMateriel)
                    {
                        tempArray.push(data);
                    }else{
                        tempArray.push(elem);
                    }
                }
            }else{
                tempArray = consommation.elements;
                tempArray.push(data);
            }

            setConsommation({
                consommation: consommation.consommation,
                elements: tempArray,
            })
        })

        socket.on("consommation_updateElement", (data)=>{
            console.log(data)
            let tempArray = [];
            for(const elem of consommation.elements)
            {
                if(elem.idConsommationMateriel == data.toUpdate.idConsommationMateriel)
                {
                    tempArray.push(data.toUpdate);
                }else{
                    if(data.toDelete != null)
                    {
                        if(elem.idConsommationMateriel != data.toDelete)
                        {
                            tempArray.push(elem);
                        }
                    }else{
                        tempArray.push(elem);
                    }
                }
            }

            setConsommation({
                consommation: consommation.consommation,
                elements: tempArray,
            })
        })

        socket.on("consommation_deleteElement", (data)=>{
            let tempArray = [];
            for(const elem of consommation.elements)
            {
                if(elem.idConsommationMateriel != data.idConsommationMateriel)
                {
                    tempArray.push(elem);
                }
            }
            setConsommation({
                consommation: consommation.consommation,
                elements: tempArray,
            })
        })

        socket.on("consommation_reloadPage", (data)=>{
            location.reload();
        })

    },[socket, consommation])
    
    if(isLoading)
    {
        return(<LoaderInfiniteLoop/>);
    }else{
        return (<>
            <PageHeader
                preTitle="Espace public"
                title="Tracer la consommation de matériel"
                description={moment(consommation.consommation.dateConsommation).format('DD/MM/YYYY') + ' ' + consommation.consommation.evenementConsommation + ' géré par ' + consommation.consommation.nomDeclarantConsommation}
                className="mb-3"
            />

            {consommation.consommation.reapproEnCours == true ?
                <GestionReappro
                    socket={socket}
                    idConsommation={idConsommation}
                    consommation={consommation}
                />
            :
                consommation.consommation.declarationEnCours == true ?
                    <Row>
                        <Col md={4}>
                            <AjouterConsommable
                                socket={socket}
                                idConsommation={idConsommation}
                            />
                        </Col>
                        <Col md={8}>
                            <AfficherConsommable
                                socket={socket}
                                idConsommation={idConsommation}
                                consommation={consommation}
                            />
                        </Col>
                    </Row>
                :
                    <Alert variant='danger'>ERREUR - Rapport de consommation clos</Alert>
            }
        </>);
    }
};

GererConsommation.propTypes = {};

export default GererConsommation;
