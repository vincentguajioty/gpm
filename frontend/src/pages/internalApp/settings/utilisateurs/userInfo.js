import React, {useState, useEffect} from 'react';
import FalconComponentCard from 'components/common/FalconComponentCard';
import { Form, Button, Row, Col } from 'react-bootstrap'; 
import Select from 'react-select';

import {Axios} from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { userInfoForm } from 'helpers/yupValidationSchema';

const UserInfo = ({idPersonne, pageNeedsRefresh}) => {
	const [isLoading, setLoading] = useState(false);
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [personne, setPersonne] = useState([]);
    const [conditionsNotif, setConditionsNotifs] = useState([]);

    const notificationsDisponibles = [
        {config: 'notif_lots_manquants'         , label:'Matériels manquants (lots)'            , profilNeeded: 'lots_lecture',},
        {config: 'notif_lots_peremptions'       , label:'Matériels périmés (lots)'              , profilNeeded: 'lots_lecture',},
        {config: 'notif_lots_inventaires'       , label:'Inventaires (lots)'                    , profilNeeded: 'lots_lecture',},
        {config: 'notif_lots_conformites'       , label:'Lots non conformes'                    , profilNeeded: 'lots_lecture',},
        {config: 'notif_reserves_manquants'     , label:'Matériels manquants (réserve)'         , profilNeeded: 'reserve_lecture',},
        {config: 'notif_reserves_peremptions'   , label:'Matériels périmés (réserve)'           , profilNeeded: 'reserve_lecture',},
        {config: 'notif_reserves_inventaires'   , label:'Inventaires (réserve)'                 , profilNeeded: 'reserve_lecture',},
        {config: 'notif_vehicules_desinfections', label:'Désinfections véhicules'               , profilNeeded: 'desinfections_lecture',},
        {config: 'notif_vehicules_health'       , label:'Maintenance régulière des véhicules'   , profilNeeded: 'vehiculeHealth_lecture',},
        {config: 'notif_tenues_stock'           , label:'Stock des tenues'                      , profilNeeded: 'tenues_lecture',},
        {config: 'notif_tenues_retours'         , label:'Non retour de tenues'                  , profilNeeded: 'tenues_lecture',},
        {config: 'notif_benevoles_lots'         , label:'Alertes de bénévoles sur les lots'     , profilNeeded: 'alertesBenevolesLots_lecture',},
        {config: 'notif_benevoles_vehicules'    , label:'Alertes de bénévoles sur les véhicules', profilNeeded: 'alertesBenevolesVehicules_lecture',},
        {config: 'notif_consommations_lots'     , label:'Rapports de consommations'             , profilNeeded: 'lots_lecture',},
    ];

    const indicateursDisponibles = [
        {config: 'conf_indicateur1Accueil' , label: "Matériels périmés (lots)",            profilNeeded: 'lots_lecture',},
        {config: 'conf_indicateur2Accueil' , label: "Matériels manquants (lots)",          profilNeeded: 'lots_lecture',},
        {config: 'conf_indicateur3Accueil' , label: "Lots en attente d'inventaire",        profilNeeded: 'lots_lecture',},
        {config: 'conf_indicateur4Accueil' , label: "Lots non conformes",                  profilNeeded: 'lots_lecture',},
        {config: 'conf_indicateur5Accueil' , label: "Matériels périmés (réserve)",         profilNeeded: 'reserve_lecture',},
        {config: 'conf_indicateur6Accueil' , label: "Matériels manquants (réserve)",       profilNeeded: 'reserve_lecture',},
        {config: 'conf_indicateur9Accueil' , label: "Stock des tenues",                    profilNeeded: 'tenues_lecture',},
        {config: 'conf_indicateur10Accueil', label: "Non retour de tenues",                profilNeeded: 'tenues_lecture',},
        {config: 'conf_indicateur11Accueil', label: "Désinfections des véhicules",         profilNeeded: 'desinfections_lecture',},
        {config: 'conf_indicateur12Accueil', label: "Maintenance régulière des véhicules", profilNeeded: 'vehiculeHealth_lecture',},
    ];

	const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(userInfoForm),
    });

    const initValue = async () => {
        try {
            const getNotificationsConditions = await Axios.get('/select/getNotificationsConditions');
            setConditionsNotifs(getNotificationsConditions.data);
            
            const response = await Axios.post('/settingsUtilisateurs/getOneUser',{
                idPersonne: idPersonne,
            });
            setPersonne( response.data[0]);
            
            setValue("nomPersonne", response.data[0].nomPersonne);
            setValue("prenomPersonne", response.data[0].prenomPersonne);
            setValue("mailPersonne", response.data[0].mailPersonne);
            setValue("telPersonne", response.data[0].telPersonne);
            setValue("fonction", response.data[0].fonction);
            setValue("abonnementsNotificationsJournalieres", response.data[0].abonnementsNotificationsJournalieres);
            console.log(response.data[0].abonnementsNotificationsJournalieres);

            for(const notif of notificationsDisponibles)
            {
                setValue(notif.config, response.data[0][notif.config]);
            }

            for(const indicateur of indicateursDisponibles)
            {
                setValue(indicateur.config, response.data[0][indicateur.config]);
            }

            if(response.data[0].mailPersonne == null || response.data[0].mailPersonne == '')
			{
				setValue("desincriptionMail", true);
			}
            

            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

	const save = async (data) => {
		setLoading(true);
		try {
            console.log(data);
            const requeteUpdate = await Axios.post('settingsUtilisateurs/updateMonCompte',{
                idPersonne: idPersonne,
                data: data,
            });

            initValue();

			setLoading(false);
		} catch (e) {
			console.log(e);
		}
	}

    useEffect(() => {
        initValue();
    }, [])

    useEffect(() => {
        if(pageNeedsRefresh){initValue()};
    }, [pageNeedsRefresh])

	return (
		<>
		<FalconComponentCard>
			<FalconComponentCard.Header
				title="Modifier mes informations"
			>
			</FalconComponentCard.Header>
			<FalconComponentCard.Body>
                {readyToDisplay ? (
                    <Form onSubmit={handleSubmit(save)} autoComplete="off">
                        <Form.Group className="mb-3">
							<Form.Label>Nom</Form.Label>
							<Form.Control type="text" name="nomPersonne" id="nomPersonne" {...register("nomPersonne")}/>
							<small className="text-danger">{errors.nomPersonne?.message}</small>
						</Form.Group>
                        <Form.Group className="mb-3">
							<Form.Label>Prénom</Form.Label>
							<Form.Control type="text" name="prenomPersonne" id="prenomPersonne" {...register("prenomPersonne")}/>
							<small className="text-danger">{errors.prenomPersonne?.message}</small>
						</Form.Group>
						<Form.Group className="mb-3">
							<Form.Label>Téléphone</Form.Label>
							<Form.Control type="text" name="telPersonne" id="telPersonne" {...register("telPersonne")}/>
							<small className="text-danger">{errors.telPersonne?.message}</small>
						</Form.Group>
                        <Form.Group className="mb-3">
							<Form.Label>Fonction</Form.Label>
							<Form.Control type="text" name="fonction" id="fonction" {...register("fonction")}/>
							<small className="text-danger">{errors.fonction?.message}</small>
						</Form.Group>

                        <Row>
                            <Col md={6}>
                                <Form.Group className="mb-3">
                                    <Form.Label>Indicateurs sur la page d'accueil</Form.Label>
                                    {indicateursDisponibles.map((indicateur, i) => {return(
                                        <Form.Check
                                            type='switch'
                                            id={indicateur.config}
                                            name={indicateur.config}
                                            label={indicateur.label}
                                            checked={watch(indicateur.config)}
                                            disabled={!personne[indicateur.profilNeeded]}
                                            onClick={(e)=>{
                                                setValue(indicateur.config, !watch(indicateur.config));
                                            }}
                                        />
                                    )})}
                                </Form.Group>
                            </Col>

                            <Col md={6}>
                                <Form.Group className="mb-3">
                                    <Form.Label>Adresse email</Form.Label>
                                    <Form.Check
                                        type='switch'
                                        id='desincriptionMail'
                                        name='desincriptionMail'
                                        label='Je ne souhaite pas recevoir de notifications par email'
                                        checked={watch("desincriptionMail")}
                                        onClick={(e)=>{
                                            setValue("desincriptionMail", !watch("desincriptionMail"));
                                            setValue("mailPersonne", null);
                                        }}
                                    />
                                    {!watch("desincriptionMail") ?
                                        <>
                                            <Form.Control type="email" name="mailPersonne" id="mailPersonne" {...register("mailPersonne")} disabled={watch("desincriptionMail")}/>
                                            <small className="text-danger">{errors.mailPersonne?.message}</small>

                                            <Form.Label className='mt-3'>Abonnement aux notifications journalières:</Form.Label>
                                            <Select
                                                id="abonnementsNotificationsJournalieres"
                                                name="abonnementsNotificationsJournalieres"
                                                size="sm"
                                                className='mb-2'
                                                closeMenuOnSelect={false}
                                                placeholder='Aucune condition selectionnée'
                                                options={conditionsNotif}
                                                isClearable={true}
                                                isSearchable={true}
                                                isMulti
                                                classNamePrefix="react-select"
                                                value={watch("abonnementsNotificationsJournalieres")}
                                                onChange={selected => setValue("abonnementsNotificationsJournalieres", selected)}
                                                isDisabled={isLoading}
                                            />
                                            <small className="text-danger">{errors.abonnementsNotificationsJournalieres?.message}</small>
                                            
                                            {notificationsDisponibles.map((notif, i) => {return(
                                                <Form.Check
                                                    type='switch'
                                                    id={notif.config}
                                                    name={notif.config}
                                                    label={notif.label}
                                                    checked={watch(notif.config)}
                                                    disabled={!personne[notif.profilNeeded]}
                                                    onClick={(e)=>{
                                                        setValue(notif.config, !watch(notif.config));
                                                    }}
                                                />
                                            )})}
                                        </>
                                    : null}
                                </Form.Group>
                            </Col>
                        </Row>
                        
                        <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button>
                    </Form>
                ) : "Chargement en cours"}
			</FalconComponentCard.Body>
		</FalconComponentCard>
		</>
	);
};

UserInfo.propTypes = {};

export default UserInfo;