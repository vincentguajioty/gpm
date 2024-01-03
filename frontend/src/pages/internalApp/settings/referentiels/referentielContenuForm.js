import React, {useEffect, useState} from 'react';
import { Row, Col, Table, Form, Button} from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { referentielUpdateForm } from 'helpers/yupValidationSchema';

const ReferentielContenuForm = ({referentiel, setPageNeedsRefresh}) => {
    const [formReady, setFormReady] = useState(false);
    const [catalogue, setCatalogue] = useState([]);

    const initForm = async () => {
        try {
            const getData = await Axios.post('/referentiels/getCatalogueForReferentielForm',{
                idTypeLot: referentiel.idTypeLot
            });
            setCatalogue(getData.data);

            setValue("libelleTypeLot", referentiel.libelleTypeLot);

            setFormReady(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initForm()
    }, [])
    useEffect(() => {
        initForm()
    }, [referentiel])

    //form
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(referentielUpdateForm),
    });

    const updateValueForQtt = (i, newValue) => {
        let tempArray = catalogue.map((item, j) =>
            i==j ?
                { ...item, quantiteReferentiel: newValue }
                :
                item
        )
        setCatalogue(tempArray);
    }
    const updateValueForObligation = (i, newValue) => {
        let tempArray = catalogue.map((item, j) =>
            i==j ?
                { ...item, obligatoire: newValue }
                :
                item
        )
        setCatalogue(tempArray);
    }
    const updateValueForComments = (i, newValue) => {
        let tempArray = catalogue.map((item, j) =>
            i==j ?
                { ...item, commentairesReferentiel: newValue }
                :
                item
        )
        setCatalogue(tempArray);
    }

    const enregistrerModifs = async (data) => {
        try {
            setLoading(true);
            setFormReady(false);

            const response = await Axios.post('/referentiels/updateReferentiel',{
                idTypeLot: referentiel.idTypeLot,
                libelleTypeLot: data.libelleTypeLot,
                catalogue: catalogue.filter(item => item.quantiteReferentiel > 0),
            });

            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.error(error)
        }
    }

    if(formReady)
    {
        return (
            <Form onSubmit={handleSubmit(enregistrerModifs)}>
                <Row>
                    <Col md={12} className='mb-2'>
                        <Form.Group className="mb-3">
                            <Form.Label>Libellé du référentiel</Form.Label>
                            <Form.Control size="sm" type="text" name='libelleTypeLot' id='libelleTypeLot' {...register('libelleTypeLot')}/>
                            <small className="text-danger">{errors.libelleTypeLot?.message}</small>
                        </Form.Group>
                    </Col>
                    
                    <Col md={12} className='mb-2'>
                        <Table responsive size='sm'>
                            <thead>
                                <tr>
                                    <th scope="col">Catégorie</th>
                                    <th scope="col">Matériel</th>
                                    <th scope="col">Quantité</th>
                                    <th scope="col">Obligation</th>
                                    <th scope="col">Commentaires</th>
                                </tr>
                            </thead>
                            <tbody>
                                {catalogue.map((item, i) => {return(
                                    <tr>
                                        <td>{item.libelleCategorie}</td>
                                        <td>{item.libelleMateriel}</td>
                                        <td>
                                            <Form.Group className="mb-3">
                                                <Form.Control
                                                    size="sm"
                                                    type="number"
                                                    min="0"
                                                    step="1"
                                                    name={item.idMaterielCatalogue+'quantiteReferentiel'}
                                                    id={item.idMaterielCatalogue+'quantiteReferentiel'}
                                                    value={item.quantiteReferentiel}
                                                    onChange={(e) => {updateValueForQtt(i, e.target.value)}}
                                                />
                                            </Form.Group>
                                        </td>
                                        <td>
                                            <Form.Group className="mb-3">
                                                <Form.Select
                                                    size="sm"
                                                    id={item.idMaterielCatalogue+'obligatoire'}
                                                    name={item.idMaterielCatalogue+'obligatoire'}
                                                    value={item.obligatoire}
                                                    onChange={(e) => {updateValueForObligation(i, e.target.value)}}
                                                >
                                                    <option value="0" key="0">Facultatif</option>
                                                    <option value="1" key="1">Obligatoire</option>
                                                </Form.Select>
                                            </Form.Group>
                                        </td>
                                        <td>
                                            <Form.Group className="mb-3">
                                                <Form.Control
                                                    size="sm"
                                                    type="text"
                                                    name={item.idMaterielCatalogue+'Commentaires'}
                                                    id={item.idMaterielCatalogue+'Commentaires'}
                                                    value={item.commentairesReferentiel}
                                                    onChange={(e) => {updateValueForComments(i, e.target.value)}}
                                                />
                                            </Form.Group>
                                        </td>
                                    </tr>
                                )})}
                            </tbody>
                        </Table>
                    </Col>
                    <Col md={12} className='mb-2'>
                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                    </Col>
                </Row>
            </Form>
        );
    }
    else
    {
        return <LoaderInfiniteLoop />
    }
    
};

ReferentielContenuForm.propTypes = {};

export default ReferentielContenuForm;
