import React, { useState, useEffect } from 'react';
import { Button, Form, Modal, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');


const AffectationDateMassive = ({
    displayIdExterne,
    setPageNeedsRefresh,
}) => {
    //Actions massives - Plannifier date de retour
    const [isLoading, setLoading] = useState(false);
    const [showRetourMassifModal, setShowRetourMassifModal] = useState(false);
    const [datePourRetourMassif, setDatePourRetourMassif] = useState(new Date());

    const handleCloseRetourMassifModal = () => {
        setShowRetourMassifModal(false);
        setLoading(false);
    };
    const handleShowRetourMassifModal = () => {
        setShowRetourMassifModal(true);
    };

    const plannifierRetourMassifTenue = async () => {
        try {
            setLoading(true);

            if(datePourRetourMassif && datePourRetourMassif!=null)
            {
                const response = await Axios.post('/tenues/plannifierRetourMassifTenue',{
                    idExterne: displayIdExterne || null,
                    dateRetour: datePourRetourMassif,
                });
            }
            
            setPageNeedsRefresh(true);
            handleCloseRetourMassifModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return(<>
        <IconButton
            icon='calendar-day'
            size = 'sm'
            variant="outline-warning"
            className="me-1"
            onClick={handleShowRetourMassifModal}
        >Prévoir une date de retour générale</IconButton>

        <Modal show={showRetourMassifModal} onHide={handleCloseRetourMassifModal} backdrop="static" keyboard={false} size='lg'>
            <Modal.Header>
                <Modal.Title>Prévoir une date de retour générale</Modal.Title>
                <FalconCloseButton onClick={handleCloseRetourMassifModal}/>
            </Modal.Header>
            <Modal.Body>
                Cette personne vous a communiqué une date à laquelle elle souhaite vous retourner tous les éléments de tenues ? Saisissez la date ci-dessous, elle sera appliquée comme date de retour à tous les éléments de tenue, et l'option de notification par email sera activée pour relancer régulièrement cette personne.
                <center>
                    <Form.Group className="mt-3 mb-3">
                        <Form.Label>Date de retour</Form.Label><br/>
                        <DatePicker
                            selected={datePourRetourMassif}
                            onChange={(date)=>setDatePourRetourMassif(date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                    </Form.Group>
                </center>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseRetourMassifModal}>
                    Annuler
                </Button>
                <Button variant='warning' onClick={plannifierRetourMassifTenue} disabled={isLoading || datePourRetourMassif == null}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
            </Modal.Footer>
        </Modal>
    </>)
}

AffectationDateMassive.propTypes = {};

export default AffectationDateMassive;