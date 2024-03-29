import React, { useState, useEffect } from 'react';
import { Accordion, Form, Row, Col } from 'react-bootstrap';

const UtilisateursFilter = ({
    personnes,
    setPersonnesFiltered,
}) => {
    const [displayFilter, setDisplayFilter] = useState(true);

    const [filtreConnexion, setFiltreConnexion] = useState(true);
    const [filtreAnonymisation, setFiltreAnonymisation] = useState(true);

    useEffect(()=>{
        let tempArray = personnes;

        if(filtreConnexion){ tempArray = tempArray.filter(user => user.connexion_connexion == true) }

        if(filtreAnonymisation){ tempArray = tempArray.filter(user => user.cnil_anonyme != true) }

        setPersonnesFiltered(tempArray);
    },[
        personnes,
        filtreConnexion,
        filtreAnonymisation,
    ])

    return (
        <Accordion className="mb-3">
            <Accordion.Item eventKey="0" flush="true">
                <Accordion.Header>Filtres</Accordion.Header>
                <Accordion.Body>
                    {!displayFilter ? ('Chargement du filtre ...'):(<>
                        <Row>
                            <Col md={3}>
                                <Form.Check 
                                    id="filtreConnexion"
                                    name="filtreConnexion"
                                    checked={filtreConnexion}
                                    onClick={()=>{setFiltreConnexion(!filtreConnexion)}}
                                    type='switch'
                                    label="Masquer les utilisateurs sans droits de connexion"
                                />
                            </Col>
                            <Col md={3}>
                                <Form.Check 
                                    id="filtreAnonymisation"
                                    name="filtreAnonymisation"
                                    checked={filtreAnonymisation}
                                    onClick={()=>{setFiltreAnonymisation(!filtreAnonymisation)}}
                                    type='switch'
                                    label="Masques les utilisateurs anonymisés"
                                />
                            </Col>
                        </Row>
                    </>)}
                </Accordion.Body>
            </Accordion.Item>
        </Accordion>
    );
};

UtilisateursFilter.propTypes = {};

export default UtilisateursFilter;
