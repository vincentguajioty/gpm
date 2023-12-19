import React, {useState, useEffect} from 'react';
import { Card, Form, Row, Col, Button, } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';
import { faHome } from '@fortawesome/free-solid-svg-icons';
import PageHeader from 'components/common/PageHeader';
import FalconComponentCard from 'components/common/FalconComponentCard';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { consommationPubliqueCreation } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
registerLocale('fr', fr);
setDefaultLocale('fr');

import { Axios } from 'helpers/axios';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';

const ChoixConsommation = ({
    setIdConsommation,
}) => {
    const [consommationsEnCours, setConsommationsEnCours] = useState([]);

    const initPage = async () => {
        try {
            let getData = await Axios.get('/select/getConsommationsEnCours');
            setConsommationsEnCours(getData.data);
            setValue("dateConsommation", new Date());
            
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const [isLoading, setLoading] = useState(true);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(consommationPubliqueCreation),
    });

    const createConso = async (data) => {
        try {
            const response = await Axios.post('/consommations/createConso',{
                nomDeclarantConsommation: data.nomDeclarantConsommation,
                dateConsommation: data.dateConsommation,
                evenementConsommation: data.evenementConsommation,
            });

            setIdConsommation(response.data.idConsommation);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    return (<>
        <PageHeader
            preTitle="Espace public"
            title="Tracer la consommation de matériel"
            className="mb-3"
        />

        {isLoading ? 
            <LoaderInfiniteLoop/>
        :
            <>
                {consommationsEnCours.length > 0 ? <>
                    
                    <FalconComponentCard noGuttersBottom className="mb-3">
                        <FalconComponentCard.Header
                            className="p-2 border-bottom"
                            title='Se joindre à'
                        />
                        <FalconComponentCard.Body>
                            {consommationsEnCours.map((conso, i)=>{return(
                                <IconButton
                                size='sm'    
                                icon='notes-medical'
                                className='mb-1 me-1'
                                variant={conso.reapproEnCours ? 'outline-warning' : conso.declarationEnCours ? 'outline-info' : 'outline-success'}
                                onClick={()=>{setIdConsommation(conso.idConsommation)}}
                                >
                                    {conso.reapproEnCours ? 'RECONDITIONNEMENT' : conso.declarationEnCours ? 'EN COURS' : null}
                                    <br/>
                                    {moment(conso.dateConsommation).format('DD/MM/YYYY')}
                                    <br/>
                                    {conso.evenementConsommation}
                                    <br/>
                                    {conso.nomDeclarantConsommation}</IconButton>
                            )})}
                        </FalconComponentCard.Body>
                    </FalconComponentCard>

                    <hr/>
                </> : null}

                <Row>
                    <Col md={{ span: 4, offset: 4 }}>
                        <FalconComponentCard noGuttersBottom className="mb-3">
                            <FalconComponentCard.Header
                                className="p-2 border-bottom"
                                title='Nouvel évènement'
                            />
                            <FalconComponentCard.Body>
                                <Form onSubmit={handleSubmit(createConso)}>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Votre Nom/Prénom</Form.Label>
                                        <Form.Control size="sm" type="text" name='nomDeclarantConsommation' id='nomDeclarantConsommation' {...register('nomDeclarantConsommation')}/>
                                        <small className="text-danger">{errors.nomDeclarantConsommation?.message}</small>
                                    </Form.Group>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Date de l'évènement</Form.Label><br/>
                                        <DatePicker
                                            selected={watch("dateConsommation")}
                                            onChange={(date)=>setValue("dateConsommation", date)}
                                            formatWeekDay={day => day.slice(0, 3)}
                                            className='form-control'
                                            placeholderText="Choisir une date"
                                            dateFormat="dd/MM/yyyy"
                                            fixedHeight
                                            locale="fr"
                                        />
                                        <small className="text-danger">{errors.dateConsommation?.message}</small>
                                    </Form.Group>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Nom de l'évènement</Form.Label>
                                        <Form.Control size="sm" type="text" name='evenementConsommation' id='evenementConsommation' {...register('evenementConsommation')}/>
                                        <small className="text-danger">{errors.evenementConsommation?.message}</small>
                                    </Form.Group>

                                    <div className="d-grid gap-2 mt-3">
                                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Créer'}</Button>
                                    </div>
                                </Form>
                            </FalconComponentCard.Body>
                        </FalconComponentCard>
                    </Col>
                </Row>
            </>
        }
        
    </>);
};

ChoixConsommation.propTypes = {};

export default ChoixConsommation;
