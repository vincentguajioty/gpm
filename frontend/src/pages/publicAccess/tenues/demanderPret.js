import React, {useState, useEffect} from 'react';
import { Button, Modal, Form, Row, Col, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';

import { Axios } from 'helpers/axios';
import TenuesPublicService from 'services/tenuesPublicService';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { tenuesPublicPret } from 'helpers/yupValidationSchema';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const TenuesPretPublic = ({
    setPageNeedsRefresh,
    externe,
}) => {
    const [isLoading, setIsLoading] = useState(false);
    const [showPretModal, setShowPretModal] = useState(false);
    const [publicCatalogue, setPublicCatalogue] = useState([]);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(tenuesPublicPret),
    });

    const handleClosePretModal = () => {
        setShowPretModal(false);
        setIsLoading(false);
        reset();
    };
    const handleShowPretModal = async () => {
        try {
            setIsLoading(true);
            setShowPretModal(true);

            let getFromDb = await Axios.get('/select/getPublicCatalogueTenues');
            setPublicCatalogue(getFromDb.data);

            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const demandePret = async (data) => {
        try {
            setIsLoading(true);

            const response = await Axios.post('/tenuesPublic/demandePret',{
                publicToken: TenuesPublicService.tenuesPublicToken,
                idExterne: externe.idExterne,
                motif: data.motif,
                dateAffectation: data.dateAffectation,
                dateRetour: data.dateRetour,
                elementsDemandes: data.elementsDemandes || [],
            });

            setPageNeedsRefresh(true);
            handleClosePretModal();
            reset();
            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    
    return(<>
        <IconButton
            icon='clock'
            size = 'sm'
            variant="success"
            className="me-1 mb-2"
            onClick={handleShowPretModal}
        >
        Demander un prêt</IconButton>

        <Modal show={showPretModal} onHide={handleClosePretModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Demander un prêt</Modal.Title>
                <FalconCloseButton onClick={handleClosePretModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmit(demandePret)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Selectionnez les éléments souhaités:</Form.Label>
                        <Select
                            id="elementsDemandes"
                            name="elementsDemandes"
                            size="sm"
                            classNamePrefix="react-select"
                            closeMenuOnSelect={true}
                            isClearable={true}
                            isSearchable={true}
                            placeholder='Aucun élément actuellement demandé'
                            options={publicCatalogue}
                            value={watch("elementsDemandes")}
                            onChange={selected => setValue("elementsDemandes", selected)}
                            isMulti
                        />
                        <small className="text-danger">{errors.elementsDemandes?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Row>
                            <Col md={6}>
                                <Form.Label>Date souhaitée de début de prêt:</Form.Label>
                                <DatePicker
                                    selected={watch("dateAffectation")}
                                    onChange={(date)=>setValue("dateAffectation", date)}
                                    formatWeekDay={day => day.slice(0, 3)}
                                    className='form-control'
                                    placeholderText="Choisir une date"
                                    dateFormat="dd/MM/yyyy"
                                    fixedHeight
                                    locale="fr"
                                /><br/>
                                <small className="text-danger">{errors.dateAffectation?.message}</small>
                            </Col>
                            <Col md={6}>
                                <Form.Label>Date souhaitée de fin de prêt:</Form.Label>
                                <DatePicker
                                    selected={watch("dateRetour")}
                                    onChange={(date)=>setValue("dateRetour", date)}
                                    formatWeekDay={day => day.slice(0, 3)}
                                    className='form-control'
                                    placeholderText="Choisir une date"
                                    dateFormat="dd/MM/yyyy"
                                    fixedHeight
                                    locale="fr"
                                /><br/>
                                <small className="text-danger">{errors.dateRetour?.message}</small>
                            </Col>
                        </Row>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Motif de la demande:</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"motif"} id={"motif"} {...register("motif")}/>
                        <small className="text-danger">{errors.motif?.message}</small>
                    </Form.Group>
                    <div className="d-grid gap-2 mt-3">
                        <Button variant='primary' className='me-2 mb-1' type='submit' disabled={isLoading}>{isLoading ? 'Patientez...' : 'Demander le prêt'}</Button>
                    </div>
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleClosePretModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>
    </>);
};

TenuesPretPublic.propTypes = {};

export default TenuesPretPublic;
