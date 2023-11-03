import React, { useState, useEffect } from 'react';
import { Row, Col } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import PageHeader from 'components/common/PageHeader';

import { Axios } from 'helpers/axios';

import ToDoListTable from './todolistTable';

const ToDoList = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [componentsHaveToReload, setComponentsHaveToReload] = useState(false);
    const [personnes, setPersonnes] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/todolist/getPersonsForTDL');
            setPersonnes(getData.data);  
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    useEffect(() => {
        if(componentsHaveToReload)
        {
            setComponentsHaveToReload(false);
        }
    }, [componentsHaveToReload])

    return (<>
        <PageHeader
            preTitle="Gestion d'équipe"
            title="Listes de taches - ToDoList"
            className="mb-3"
        />

        <Row>
            <Col md={6}>
                {readyToDisplay ?
                    personnes.map((personne, i)=>{return(
                        <ToDoListTable
                            titreBox={personne.identifiant}
                            filtre='individual'
                            idPersonne={personne.idPersonne}
                            componentsHaveToReload={componentsHaveToReload}
                            setComponentsHaveToReload={setComponentsHaveToReload}
                        />
                    )})
                : <LoaderInfiniteLoop/>}
            </Col>
            <Col md={6}>
                <ToDoListTable
                    titreBox='Taches non-affectées'
                    filtre='unaffected'
                    componentsHaveToReload={componentsHaveToReload}
                    setComponentsHaveToReload={setComponentsHaveToReload}
                />
                <ToDoListTable
                    titreBox='Taches terminées'
                    filtre='finished'
                    componentsHaveToReload={componentsHaveToReload}
                    setComponentsHaveToReload={setComponentsHaveToReload}
                />
            </Col>
        </Row>
    </>);
};

ToDoList.propTypes = {};

export default ToDoList;
