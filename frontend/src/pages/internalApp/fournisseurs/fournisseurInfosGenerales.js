import React, {useEffect, useState} from 'react';
import { Link } from 'react-router-dom';
import { Card, Form, Table, Button } from 'react-bootstrap';
import Flex from 'components/common/Flex';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { fournisseurUpdateForm } from 'helpers/yupValidationSchema';

const FournisseurInfosGenerales = ({
    fournisseur,
    setPageNeedsRefresh,
}) => {
    const [modeEdition, setModeEdition] = useState(false);
    const handleEdition = () => {
        setModeEdition(!modeEdition);
    }

    const initForm = () => {
        try {
            setValue("nomFournisseur", fournisseur.nomFournisseur);
            setValue("adresseFournisseur", fournisseur.adresseFournisseur);
            setValue("telephoneFournisseur", fournisseur.telephoneFournisseur);
            setValue("mailFournisseur", fournisseur.mailFournisseur);  
            setValue("siteWebFournisseur", fournisseur.siteWebFournisseur);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initForm();
    },[])
    useEffect(() => {
        initForm();
    },[fournisseur])

    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(fournisseurUpdateForm),
    });
    const modifierEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/fournisseurs/updateFournisseur',{
                idFournisseur: fournisseur.idFournisseur,
                nomFournisseur: data.nomFournisseur,
                adresseFournisseur: data.adresseFournisseur,
                telephoneFournisseur: data.telephoneFournisseur,
                mailFournisseur: data.mailFournisseur,
                siteWebFournisseur: data.siteWebFournisseur,
            });
            
            setLoading(false);
            setPageNeedsRefresh(true);
            setModeEdition(false);
        } catch (error) {
            console.error(error)
        }
    }

    const nl2br = require('react-nl2br');
    return (
        <Card className="mb-3">
            <Card.Header className="p-2 border-bottom">
                <Flex>
                    <div className="p-2 flex-grow-1">
                        Informations générales
                    </div>
                    <div className="p-2">
                        <Form.Check 
                            type='switch'
                            id='defaultSwitch'
                            label='Modifier'
                            onClick={handleEdition}
                            checked={modeEdition}
                            disabled={!HabilitationService.habilitations['fournisseurs_modification']}
                        />
                    </div>
                </Flex>
            </Card.Header>
            <Card.Body>
                {modeEdition ?
                    <Form onSubmit={handleSubmit(modifierEntree)}>
                        <Form.Group className="mb-3">
                            <Form.Label>Nom</Form.Label>
                            <Form.Control type="text" id="nomFournisseur" name="nomFournisseur" {...register("nomFournisseur")}/>
                            <small className="text-danger">{errors.nomFournisseur?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Adresse</Form.Label>
                            <Form.Control size="sm" as="textarea" rows={3} name={"adresseFournisseur"} id={"adresseFournisseur"} {...register("adresseFournisseur")}/>
                            <small className="text-danger">{errors.adresseFournisseur?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Téléphone</Form.Label>
                            <Form.Control type="text" id="telephoneFournisseur" name="telephoneFournisseur" {...register("telephoneFournisseur")}/>
                            <small className="text-danger">{errors.telephoneFournisseur?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Mail</Form.Label>
                            <Form.Control type="text" id="mailFournisseur" name="mailFournisseur" {...register("mailFournisseur")}/>
                            <small className="text-danger">{errors.mailFournisseur?.message}</small>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Site internet</Form.Label>
                            <Form.Control type="text" id="siteWebFournisseur" name="siteWebFournisseur" {...register("siteWebFournisseur")}/>
                            <small className="text-danger">{errors.siteWebFournisseur?.message}</small>
                        </Form.Group>
                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                    </Form>
                :
                    <Table className="fs--1 mt-3" size='sm' responsive>
                        <tbody>
                            <tr>
                                <th className="bg-100" style={{ width: '20%' }}>Nom</th>
                                <td>{fournisseur.nomFournisseur}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '20%' }}>Adresse</th>
                                <td>{nl2br(fournisseur.adresseFournisseur)}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '20%' }}>Téléphone</th>
                                <td>{fournisseur.telephoneFournisseur}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '20%' }}>Mail</th>
                                <td>{fournisseur.mailFournisseur}</td>
                            </tr>
                            <tr>
                                <th className="bg-100" style={{ width: '20%' }}>Site internet</th>
                                <td><Link to={fournisseur.siteWebFournisseur}>{fournisseur.siteWebFournisseur}</Link></td>
                            </tr>
                        </tbody>
                    </Table>
                }
            </Card.Body>
        </Card>
    );
};

FournisseurInfosGenerales.propTypes = {};

export default FournisseurInfosGenerales;
