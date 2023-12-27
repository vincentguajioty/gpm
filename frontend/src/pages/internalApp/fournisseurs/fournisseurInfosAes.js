import React, {useEffect, useState} from 'react';
import { Card, Form, Button } from 'react-bootstrap';
import Flex from 'components/common/Flex';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';
import AesFournisseursService from 'services/aesFournisseursService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { fournisseurUpdateAesDataForm } from 'helpers/yupValidationSchema';

import AesFournisseursUnlock from '../settings/aesFournisseurs/aesFournisseursUnlock';
import AesFournisseursLock from '../settings/aesFournisseurs/aesFournisseursLock';

const FournisseurInfosAes = ({
    fournisseur,
    setPageNeedsRefresh,
}) => {
    const [modeEdition, setModeEdition] = useState(false);
    const handleEdition = () => {
        setModeEdition(!modeEdition);
    }

    const initForm = () => {
        try {
            setValue("aesFournisseur", fournisseur.aesFournisseur);
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
        resolver: yupResolver(fournisseurUpdateAesDataForm),
    });
    const modifierEntree = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/fournisseurs/updateFournisseurAesData',{
                idFournisseur: fournisseur.idFournisseur,
                aesFournisseur: data.aesFournisseur,
                aesToken: AesFournisseursService.aesToken,
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
                        Informations chiffrées
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
                {
                    AesFournisseursService.aesTokenValidUntil && moment(AesFournisseursService.aesTokenValidUntil) > new Date() ?
                        <>
                            {modeEdition ?
                                <Form onSubmit={handleSubmit(modifierEntree)}>
                                    <Form.Group className="mb-3">
                                        <Form.Label>Informations chiffrées</Form.Label>
                                        <Form.Control size="sm" as="textarea" rows={10} name={"aesFournisseur"} id={"aesFournisseur"} {...register("aesFournisseur")}/>
                                        <small className="text-danger">{errors.aesFournisseur?.message}</small>
                                    </Form.Group>
                                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                                </Form>
                            :
                                nl2br(fournisseur.aesFournisseur || 'Aucune information chiffrée')
                            }
                            <AesFournisseursLock
                                setPageNeedsRefresh={setPageNeedsRefresh}
                            />
                        </>
                    :
                        <AesFournisseursUnlock
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            isLoading={isLoading}
                            setIsLoading={setLoading}
                        />
                }
            </Card.Body>
        </Card>
    );
};

FournisseurInfosAes.propTypes = {};

export default FournisseurInfosAes;
