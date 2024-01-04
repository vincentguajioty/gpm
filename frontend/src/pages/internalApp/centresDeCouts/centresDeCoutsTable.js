import React, {useState,} from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Offcanvas, Button, Form } from 'react-bootstrap';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import SoftBadge from 'components/common/SoftBadge';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { centresCoutsAddForm } from 'helpers/yupValidationSchema';

import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const CentresDeCoutsTable = ({
    centresArrayFiltered
}) => {
    const colonnes = [
        {
            accessor: 'libelleCentreDecout',
            Header: 'Libellé',
            Cell: ({ value, row }) => {
				return(<Link to={'/couts/'+row.original.idCentreDeCout}>{value}</Link>);
			},
        },
        {
            accessor: 'statutOuverture',
            Header: 'Etat',
            Cell: ({ value, row }) => {
                switch (value) {
                    case '1 - Ouvert':
                        return <SoftBadge bg='success'>{value}</SoftBadge>
                    break;

                    case '2 - Futur':
                        return <SoftBadge bg='info'>{value}</SoftBadge>
                    break;

                    case '3 - Clos':
                        return <SoftBadge bg='secondary'>{value}</SoftBadge>
                    break;
                
                    default:
                        return <SoftBadge bg='danger'>ERREUR</SoftBadge>
                    break;
                }
			},
        },
        {
            accessor: 'dateOuverture',
            Header: 'Dates',
            Cell: ({ value, row }) => {
				return(<>
                    {row.original.dateOuverture && row.original.dateOuverture != null ? <SoftBadge className='me-1 mb-1'>Ouverture le: {moment(row.original.dateOuverture).format('DD/MM/YYYY')}</SoftBadge> : null}<br/>
                    {row.original.dateFermeture && row.original.dateFermeture != null ? <SoftBadge className='me-1 mb-1'>Fermeture le: {moment(row.original.dateFermeture).format('DD/MM/YYYY')}</SoftBadge> : null}
                </>);
			},
        },
        {
            accessor: 'gestionnaires',
            Header: 'Gestionnaires',
            Cell: ({ value, row }) => {
				return(value.map((personne, i)=>{return(
                    <><SoftBadge className='me-1 mb-1'>{personne.identifiant}</SoftBadge><br/></>
                )}));
			},
        },
        {
            accessor: 'soldeActuel',
            Header: 'Solde actuel',
            Cell: ({ value, row }) => {
				if(value > 0)
                {
                    return <SoftBadge bg='success'>{value} €</SoftBadge>;
                }else if(value == 0)
                {
                    return <SoftBadge bg='warning'>{value} €</SoftBadge>;
                }else{
                    return <SoftBadge bg='danger'>{value} €</SoftBadge>;
                }
			},
        },
        {
            accessor: 'commandesAintegrer',
            Header: 'Commandes à intégrer',
            Cell: ({ value, row }) => {
				if(value > 0)
                {
                    return <SoftBadge bg='warning'>{value}</SoftBadge>;
                }else{
                    return <SoftBadge bg='success'>{value}</SoftBadge>;
                }
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
        },
    ];

    //Add offCanevas
    const [showOffCanevas, setShowOffCanevas] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(centresCoutsAddForm),
    });
    const handleShowOffCanevas = () => {
        setShowOffCanevas(true);
    }
    const handleCloseOffCanevas = () => {
        setShowOffCanevas(false);
        reset();
    }
    
    const navigate = useNavigate();
    const ajouterCentre = async (data) => {
        try {
            setLoading(true);

            const response = await Axios.post('/centresCouts/addCentre',{
                libelleCentreDecout: data.libelleCentreDecout,
                commentairesCentreCout: data.commentairesCentreCout,
                dateOuverture: data.dateOuverture,
                dateFermeture: data.dateFermeture,
            });

            let idTarget = response.data.idCentreDeCout;
            navigate('/couts/'+idTarget);

            handleCloseOffCanevas();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <Offcanvas show={showOffCanevas} onHide={handleCloseOffCanevas} placement='end'>
            <Offcanvas.Header closeButton >
                <Offcanvas.Title>Nouveau centre de cout</Offcanvas.Title>
            </Offcanvas.Header>
            <Offcanvas.Body>
                <Form onSubmit={handleSubmit(ajouterCentre)}>
                    <Form.Group className="mb-3">
                        <Form.Label>Titre du centre</Form.Label>
                        <Form.Control size="sm" type="text" name='libelleCentreDecout' id='libelleCentreDecout' {...register('libelleCentreDecout')}/>
                        <small className="text-danger">{errors.libelleCentreDecout?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Ouverture du centre</Form.Label>
                        <DatePicker
                            selected={watch("dateOuverture")}
                            onChange={(date)=>setValue("dateOuverture", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateOuverture?.message}</small>
                    </Form.Group>
                    <Form.Group className="mb-3">
                        <Form.Label>Fermeture du centre</Form.Label>
                        <DatePicker
                            selected={watch("dateFermeture")}
                            onChange={(date)=>setValue("dateFermeture", date)}
                            formatWeekDay={day => day.slice(0, 3)}
                            className='form-control'
                            placeholderText="Choisir une date"
                            dateFormat="dd/MM/yyyy"
                            fixedHeight
                            locale="fr"
                        />
                        <small className="text-danger">{errors.dateFermeture?.message}</small>
                    </Form.Group>
                    <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Ajouter'}</Button>
                </Form>
            </Offcanvas.Body>
        </Offcanvas>

        <GPMtable
            columns={colonnes}
            data={centresArrayFiltered}
            topButtonShow={true}
            topButton={HabilitationService.habilitations.cout_ajout ?
                <IconButton
                    icon='plus'
                    size = 'sm'
                    variant="outline-success"
                    onClick={handleShowOffCanevas}
                >Nouveau Centre</IconButton>
            : null}
        />
    </>);
};

CentresDeCoutsTable.propTypes = {};

export default CentresDeCoutsTable;
