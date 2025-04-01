import React, {useState, useEffect} from 'react';
import { Button, Modal, Form } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import TenuesPublicService from 'services/tenuesPublicService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { tenuesPublicReplace } from 'helpers/yupValidationSchema';

const TenuesEchangePublic = ({
    setPageNeedsRefresh,
    externe,
}) => {
    const [isLoading, setIsLoading] = useState(false);
    const [showReplaceModal, setShowReplaceModal] = useState(false);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(tenuesPublicReplace),
    });

    const handleCloseReplaceModal = () => {
        setShowReplaceModal(false);
        setIsLoading(false);
    };
    const handleShowReplaceModal = () => {
        setShowReplaceModal(true);
    };

    const demanderRemplacement = async (data) => {
        try {
            setIsLoading(true);

            const response = await Axios.post('/tenuesPublic/demandeRemplacement',{
                publicToken: TenuesPublicService.tenuesPublicToken,
                idTenue: data.idTenue,
                motif: data.motif,
            });

            setPageNeedsRefresh(true);
            handleCloseReplaceModal();
            reset();
            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    
    return(<>
        <IconButton
            icon='exchange-alt'
            size = 'sm'
            variant="warning"
            className="me-1 mb-2"
            onClick={handleShowReplaceModal}
        >
        Remplacer un élément</IconButton>

        <Modal show={showReplaceModal} onHide={handleCloseReplaceModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Demander le remplacement</Modal.Title>
                <FalconCloseButton onClick={handleCloseReplaceModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmit(demanderRemplacement)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Element de tenue:</Form.Label>
                        <Select
                            id="idTenue"
                            name="idTenue"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            isDisabled={isLoading}
                            placeholder='Aucun élément selectionné'
                            options={externe.tenues}
                            value={externe.tenues.find(c => c.value === watch("idTenue"))}
                            onChange={val => val != null ? setValue("idTenue", val.value) : setValue("idTenue", null)}
                        />
                        <small className="text-danger">{errors.idTenue?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Motif de la demande:</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"motif"} id={"motif"} {...register("motif")}/>
                        <small className="text-danger">{errors.motif?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' className='me-2 mb-1' type='submit' disabled={isLoading}>{isLoading ? 'Patientez...' : 'Demander le remplacement'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseReplaceModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>
    </>);
};

TenuesEchangePublic.propTypes = {};

export default TenuesEchangePublic;
