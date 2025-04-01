import React, {useState, useEffect} from 'react';
import { Form, Row, Col, Button, Container, Alert } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import moment from 'moment-timezone';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { tenuesPublicFormDemande, tenuesPublicFormAuth } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';
import TenuesPublicService from 'services/tenuesPublicService';

const TenuesDemandeToken = () => {
    const [isLoading, setLoading] = useState(false);

    //Formulaire demande de code
    const [afficherDemandePatienter, setAfficherDemandePatienter] = useState(false);
    const { register: registerDemande, handleSubmit: handleSubmitDemande, formState: { errors: errorsDemande }, setValue: setValueDemande, reset: resetDemande, watch: watchDemande } = useForm({
        resolver: yupResolver(tenuesPublicFormDemande),
    });

    const demanderAuth = async (data) => {
        try {
            setLoading(true);
            
            const response = await Axios.post('/tenuesPublic/requestAuthentification',{
                mailDemandeur: data.mailDemandeur,
            });

            setLoading(false);
            setAfficherDemandePatienter(true);

            setValueAuth("mailDemandeur", watchDemande("mailDemandeur"));

        } catch (error) {
            console.log(error)
        }
    }
    
    //Formulaire d'authent
    const [afficherErreur, setAfficherErreur] = useState(false);
    const { register: registerAuth, handleSubmit: handleSubmitAuth, formState: { errors: errorsAuth }, setValue: setValueAuth, reset: resetAuth, watch: watchAuth } = useForm({
        resolver: yupResolver(tenuesPublicFormAuth),
    });

    const authentifierDemande = async (data) => {
        try {
            setLoading(true);
            
            const response = await Axios.post('/tenuesPublic/authenticateWithCode',{
                mailDemandeur: data.mailDemandeur,
                codeUnique: data.codeUnique,
            });

            if(response.data.auth && response.data.auth === true)
            {
                TenuesPublicService.setTenuesPublicToken(response.data.token);
                TenuesPublicService.setTenuesPublicTokenValidUntil(response.data.tokenValidUntil);
                location.reload();
            }else{
                setAfficherErreur(true)
            }
        } catch (error) {
            console.log(error)
        }
    }
    
    return (<>
        <Container>
            <Row className="justify-content-md-center">
                <Col>
                    <FalconComponentCard noGuttersBottom className="mb-3">
                        <FalconComponentCard.Header
                            title="1 - Je demande un code pour accéder à mes éléments de tenue"
                        />
                        <FalconComponentCard.Body
                            scope={{ ActionButton }}
                            noLight
                        >
                            {afficherDemandePatienter ?
                                <Alert variant='success' className='mt-3'>Merci de vérifier votre boite de réception dans l'attente de la réception du code. Si vous ne recevez pas de code, c'est que votre adresse mail est incorrect ou inconnue du système.</Alert>
                                :
                                <Form onSubmit={handleSubmitDemande(demanderAuth)}>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Saisissez ci-dessous l'adresse email liée à votre affectation de tenues. Si celle-ci est correcte, vous recevrez un email avec un code à usage unique à entrer dans l'encart suivant pour vous authentifier</Form.Label>
                                        <Form.Control size="sm" type="text" name={"mailDemandeur"} id={"mailDemandeur"} {...registerDemande("mailDemandeur")} placeholder='Votre adresse email'/>
                                        <small className="text-danger">{errorsDemande.mailDemandeur?.message}</small>
                                    </Form.Group>
                                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Envoyez le code'}</Button>
                                </Form>
                            }
                        </FalconComponentCard.Body>
                    </FalconComponentCard>
                </Col>
                <Col>
                    <FalconComponentCard noGuttersBottom className="mb-3">
                        <FalconComponentCard.Header
                            title="2 - J'ai reçu mon code"
                        />
                        <FalconComponentCard.Body
                            scope={{ ActionButton }}
                            noLight
                        >
                            {afficherErreur ?
                                <Alert variant='warning' className='mt-3'>Une erreur s'est produite, merci de rafraichir la page, vérifier les informations saisie, et ré-essayer.</Alert>
                                :
                                <Form onSubmit={handleSubmitAuth(authentifierDemande)}>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Saisissez le code à usage unique ci-dessous:</Form.Label>

                                        <Form.Control size="sm" type="text" name={"mailDemandeur"} id={"mailDemandeur"} {...registerAuth("mailDemandeur")} placeholder='Votre adresse email'/>
                                        <small className="text-danger">{errorsAuth.mailDemandeur?.message}</small>

                                        <Form.Control size="sm" type="text" name={"codeUnique"} id={"codeUnique"} {...registerAuth("codeUnique")} placeholder='Code unique reçu par email'/>
                                        <small className="text-danger">{errorsAuth.codeUnique?.message}</small>
                                    </Form.Group>
                                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Se connecter'}</Button>
                                </Form>
                            }
                        </FalconComponentCard.Body>
                    </FalconComponentCard>
                </Col>
            </Row>
        </Container>
    </>);
};

TenuesDemandeToken.propTypes = {};

export default TenuesDemandeToken;
