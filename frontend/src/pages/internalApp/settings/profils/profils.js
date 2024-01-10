import React, { useState, useEffect } from 'react';
import { Offcanvas, Button, Form, Table, Modal } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import SimpleBarReact from 'simplebar-react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import PageHeader from 'components/common/PageHeader';
import SoftBadge from 'components/common/SoftBadge';
import IconButton from 'components/common/IconButton';
import GPMtable from 'components/gpmTable/gpmTable';

import HabilitationService from 'services/habilitationsService';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { profilForm } from 'helpers/yupValidationSchema';

const Profils = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [profils, setProfils] = useState([]);

    const initPage = async () => {
        try {
            const getData = await Axios.get('/profils/getProfils');
            setProfils(getData.data);  
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])
    const nl2br = require('react-nl2br');
    const colonnes = [
        {
            accessor: 'libelleProfil',
            Header: 'Libellé',
        },
        {
            accessor: 'LDAP_BINDDN',
            Header: 'Local/AD',
            Cell: ({ value, row }) => {
				return(<SoftBadge bg={row.original.LDAP_BINDDN == null ? 'secondary' : 'info'} className='me-1'>{row.original.LDAP_BINDDN == null ? 'Groupe local' : 'Lié au groupe AD: '+row.original.LDAP_BINDDN}</SoftBadge>);
			},
        },
        {
            accessor: 'descriptifProfil',
            Header: 'Description',
            Cell: ({ value, row }) => {
				return(nl2br(value));
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
				return(
                    <>
                        <IconButton
                            icon='eye'
                            size = 'sm'
                            variant="outline-primary"
                            className="me-1"
                            onClick={()=>{handleShowEditModal(row.original.idProfil)}}
                        />
                        {HabilitationService?.habilitations?.profils_suppression ? 
                            <IconButton
                                icon='trash'
                                size = 'sm'
                                variant="outline-danger"
                                className="me-1"
                                onClick={()=>{handleShowDeleteModal(row.original.idProfil)}}
                            />
                        : null}
                    </>
                );
			},
        },
    ];

    //formulaire d'ajout et modification
    const [showEditModal, setShowEditModal] = useState(false);
    const [editModalIdProfil, setEditModalIdProfil] = useState();
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(profilForm),
    });
    const handleCloseEditModal = () => {
        setShowEditModal(false);
        reset();
        setEditModalIdProfil();
        setModeEdition(false);
        setLoading(false);
    }
    const handleShowEditModal = (idProfil) => {
        setEditModalIdProfil(idProfil);
        if(idProfil > 0)
        {
            let selected = profils.filter(item => item.idProfil == idProfil);

            setValue('libelleProfil', selected[0].libelleProfil);
            setValue('LDAP_BINDDN', selected[0].LDAP_BINDDN);
            setValue('descriptifProfil', selected[0].descriptifProfil);
            setValue('connexion_connexion', selected[0].connexion_connexion);
            setValue('annuaire_lecture', selected[0].annuaire_lecture);
            setValue('annuaire_ajout', selected[0].annuaire_ajout);
            setValue('annuaire_modification', selected[0].annuaire_modification);
            setValue('annuaire_mdp', selected[0].annuaire_mdp);
            setValue('annuaire_suppression', selected[0].annuaire_suppression);
            setValue('profils_lecture', selected[0].profils_lecture);
            setValue('profils_ajout', selected[0].profils_ajout);
            setValue('profils_modification', selected[0].profils_modification);
            setValue('profils_suppression', selected[0].profils_suppression);
            setValue('categories_lecture', selected[0].categories_lecture);
            setValue('categories_ajout', selected[0].categories_ajout);
            setValue('categories_modification', selected[0].categories_modification);
            setValue('categories_suppression', selected[0].categories_suppression);
            setValue('fournisseurs_lecture', selected[0].fournisseurs_lecture);
            setValue('fournisseurs_ajout', selected[0].fournisseurs_ajout);
            setValue('fournisseurs_modification', selected[0].fournisseurs_modification);
            setValue('fournisseurs_suppression', selected[0].fournisseurs_suppression);
            setValue('typesLots_lecture', selected[0].typesLots_lecture);
            setValue('typesLots_ajout', selected[0].typesLots_ajout);
            setValue('typesLots_modification', selected[0].typesLots_modification);
            setValue('typesLots_suppression', selected[0].typesLots_suppression);
            setValue('lieux_lecture', selected[0].lieux_lecture);
            setValue('lieux_ajout', selected[0].lieux_ajout);
            setValue('lieux_modification', selected[0].lieux_modification);
            setValue('lieux_suppression', selected[0].lieux_suppression);
            setValue('lots_lecture', selected[0].lots_lecture);
            setValue('lots_ajout', selected[0].lots_ajout);
            setValue('lots_modification', selected[0].lots_modification);
            setValue('lots_suppression', selected[0].lots_suppression);
            setValue('sac_lecture', selected[0].sac_lecture);
            setValue('sac_ajout', selected[0].sac_ajout);
            setValue('sac_modification', selected[0].sac_modification);
            setValue('sac_suppression', selected[0].sac_suppression);
            setValue('catalogue_lecture', selected[0].catalogue_lecture);
            setValue('catalogue_ajout', selected[0].catalogue_ajout);
            setValue('catalogue_modification', selected[0].catalogue_modification);
            setValue('catalogue_suppression', selected[0].catalogue_suppression);
            setValue('materiel_lecture', selected[0].materiel_lecture);
            setValue('materiel_ajout', selected[0].materiel_ajout);
            setValue('materiel_modification', selected[0].materiel_modification);
            setValue('materiel_suppression', selected[0].materiel_suppression);
            setValue('messages_ajout', selected[0].messages_ajout);
            setValue('messages_suppression', selected[0].messages_suppression);
            setValue('commande_lecture', selected[0].commande_lecture);
            setValue('commande_ajout', selected[0].commande_ajout);
            setValue('commande_etreEnCharge', selected[0].commande_etreEnCharge);
            setValue('commande_abandonner', selected[0].commande_abandonner);
            setValue('cout_lecture', selected[0].cout_lecture);
            setValue('cout_ajout', selected[0].cout_ajout);
            setValue('cout_etreEnCharge', selected[0].cout_etreEnCharge);
            setValue('cout_supprimer', selected[0].cout_supprimer);
            setValue('appli_conf', selected[0].appli_conf);
            setValue('reserve_lecture', selected[0].reserve_lecture);
            setValue('reserve_ajout', selected[0].reserve_ajout);
            setValue('reserve_modification', selected[0].reserve_modification);
            setValue('reserve_suppression', selected[0].reserve_suppression);
            setValue('reserve_cmdVersReserve', selected[0].reserve_cmdVersReserve);
            setValue('reserve_ReserveVersLot', selected[0].reserve_ReserveVersLot);
            setValue('vhf_canal_lecture', selected[0].vhf_canal_lecture);
            setValue('vhf_canal_ajout', selected[0].vhf_canal_ajout);
            setValue('vhf_canal_modification', selected[0].vhf_canal_modification);
            setValue('vhf_canal_suppression', selected[0].vhf_canal_suppression);
            setValue('vhf_plan_lecture', selected[0].vhf_plan_lecture);
            setValue('vhf_plan_ajout', selected[0].vhf_plan_ajout);
            setValue('vhf_plan_modification', selected[0].vhf_plan_modification);
            setValue('vhf_plan_suppression', selected[0].vhf_plan_suppression);
            setValue('vhf_equipement_lecture', selected[0].vhf_equipement_lecture);
            setValue('vhf_equipement_ajout', selected[0].vhf_equipement_ajout);
            setValue('vhf_equipement_modification', selected[0].vhf_equipement_modification);
            setValue('vhf_equipement_suppression', selected[0].vhf_equipement_suppression);
            setValue('vehicules_lecture', selected[0].vehicules_lecture);
            setValue('vehicules_ajout', selected[0].vehicules_ajout);
            setValue('vehicules_modification', selected[0].vehicules_modification);
            setValue('vehicules_suppression', selected[0].vehicules_suppression);
            setValue('vehicules_types_lecture', selected[0].vehicules_types_lecture);
            setValue('vehicules_types_ajout', selected[0].vehicules_types_ajout);
            setValue('vehicules_types_modification', selected[0].vehicules_types_modification);
            setValue('vehicules_types_suppression', selected[0].vehicules_types_suppression);
            setValue('maintenance', selected[0].maintenance);
            setValue('todolist_perso', selected[0].todolist_perso);
            setValue('todolist_lecture', selected[0].todolist_lecture);
            setValue('todolist_modification', selected[0].todolist_modification);
            setValue('contactMailGroupe', selected[0].contactMailGroupe);
            setValue('tenues_lecture', selected[0].tenues_lecture);
            setValue('tenues_ajout', selected[0].tenues_ajout);
            setValue('tenues_modification', selected[0].tenues_modification);
            setValue('tenues_suppression', selected[0].tenues_suppression);
            setValue('tenuesCatalogue_lecture', selected[0].tenuesCatalogue_lecture);
            setValue('tenuesCatalogue_ajout', selected[0].tenuesCatalogue_ajout);
            setValue('tenuesCatalogue_modification', selected[0].tenuesCatalogue_modification);
            setValue('tenuesCatalogue_suppression', selected[0].tenuesCatalogue_suppression);
            setValue('cautions_lecture', selected[0].cautions_lecture);
            setValue('cautions_ajout', selected[0].cautions_ajout);
            setValue('cautions_modification', selected[0].cautions_modification);
            setValue('cautions_suppression', selected[0].cautions_suppression);
            setValue('etats_lecture', selected[0].etats_lecture);
            setValue('etats_ajout', selected[0].etats_ajout);
            setValue('etats_modification', selected[0].etats_modification);
            setValue('etats_suppression', selected[0].etats_suppression);
            setValue('notifications', selected[0].notifications);
            setValue('actionsMassives', selected[0].actionsMassives);
            setValue('delegation', selected[0].delegation);
            setValue('commande_valider_delegate', selected[0].commande_valider_delegate);
            setValue('desinfections_lecture', selected[0].desinfections_lecture);
            setValue('desinfections_ajout', selected[0].desinfections_ajout);
            setValue('desinfections_modification', selected[0].desinfections_modification);
            setValue('desinfections_suppression', selected[0].desinfections_suppression);
            setValue('typesDesinfections_lecture', selected[0].typesDesinfections_lecture);
            setValue('typesDesinfections_ajout', selected[0].typesDesinfections_ajout);
            setValue('typesDesinfections_modification', selected[0].typesDesinfections_modification);
            setValue('typesDesinfections_suppression', selected[0].typesDesinfections_suppression);
            setValue('carburants_lecture', selected[0].carburants_lecture);
            setValue('carburants_ajout', selected[0].carburants_ajout);
            setValue('carburants_modification', selected[0].carburants_modification);
            setValue('carburants_suppression', selected[0].carburants_suppression);
            setValue('vehiculeHealthType_lecture', selected[0].vehiculeHealthType_lecture);
            setValue('vehiculeHealthType_ajout', selected[0].vehiculeHealthType_ajout);
            setValue('vehiculeHealthType_modification', selected[0].vehiculeHealthType_modification);
            setValue('vehiculeHealthType_suppression', selected[0].vehiculeHealthType_suppression);
            setValue('vehiculeHealth_lecture', selected[0].vehiculeHealth_lecture);
            setValue('vehiculeHealth_ajout', selected[0].vehiculeHealth_ajout);
            setValue('vehiculeHealth_modification', selected[0].vehiculeHealth_modification);
            setValue('vehiculeHealth_suppression', selected[0].vehiculeHealth_suppression);
            setValue('alertesBenevolesLots_lecture', selected[0].alertesBenevolesLots_lecture);
            setValue('alertesBenevolesLots_affectation', selected[0].alertesBenevolesLots_affectation);
            setValue('alertesBenevolesLots_affectationTier', selected[0].alertesBenevolesLots_affectationTier);
            setValue('alertesBenevolesVehicules_lecture', selected[0].alertesBenevolesVehicules_lecture);
            setValue('alertesBenevolesVehicules_affectation', selected[0].alertesBenevolesVehicules_affectation);
            setValue('alertesBenevolesVehicules_affectationTier', selected[0].alertesBenevolesVehicules_affectationTier);
            setValue('codeBarre_lecture', selected[0].codeBarre_lecture);
            setValue('codeBarre_ajout', selected[0].codeBarre_ajout);
            setValue('codeBarre_modification', selected[0].codeBarre_modification);
            setValue('codeBarre_suppression', selected[0].codeBarre_suppression);
            setValue('consommationLots_lecture', selected[0].consommationLots_lecture);
            setValue('consommationLots_affectation', selected[0].consommationLots_affectation);
            setValue('consommationLots_supression', selected[0].consommationLots_supression);
        }
        else
        {
            setModeEdition(true);
        }

        setShowEditModal(true);
    }
    const ajouterModifierEntree = async (data) => {
        try {
            setLoading(true);

            if(editModalIdProfil > 0)
            {
                const response = await Axios.post('/profils/updateProfil',{
                    idProfil: editModalIdProfil,
                    libelleProfil: data.libelleProfil,
                    LDAP_BINDDN: data.LDAP_BINDDN,
                    descriptifProfil: data.descriptifProfil,
                    connexion_connexion: data.connexion_connexion,
                    annuaire_lecture: data.annuaire_lecture,
                    annuaire_ajout: data.annuaire_ajout,
                    annuaire_modification: data.annuaire_modification,
                    annuaire_mdp: data.annuaire_mdp,
                    annuaire_suppression: data.annuaire_suppression,
                    profils_lecture: data.profils_lecture,
                    profils_ajout: data.profils_ajout,
                    profils_modification: data.profils_modification,
                    profils_suppression: data.profils_suppression,
                    categories_lecture: data.categories_lecture,
                    categories_ajout: data.categories_ajout,
                    categories_modification: data.categories_modification,
                    categories_suppression: data.categories_suppression,
                    fournisseurs_lecture: data.fournisseurs_lecture,
                    fournisseurs_ajout: data.fournisseurs_ajout,
                    fournisseurs_modification: data.fournisseurs_modification,
                    fournisseurs_suppression: data.fournisseurs_suppression,
                    typesLots_lecture: data.typesLots_lecture,
                    typesLots_ajout: data.typesLots_ajout,
                    typesLots_modification: data.typesLots_modification,
                    typesLots_suppression: data.typesLots_suppression,
                    lieux_lecture: data.lieux_lecture,
                    lieux_ajout: data.lieux_ajout,
                    lieux_modification: data.lieux_modification,
                    lieux_suppression: data.lieux_suppression,
                    lots_lecture: data.lots_lecture,
                    lots_ajout: data.lots_ajout,
                    lots_modification: data.lots_modification,
                    lots_suppression: data.lots_suppression,
                    sac_lecture: data.sac_lecture,
                    sac_ajout: data.sac_ajout,
                    sac_modification: data.sac_modification,
                    sac_suppression: data.sac_suppression,
                    catalogue_lecture: data.catalogue_lecture,
                    catalogue_ajout: data.catalogue_ajout,
                    catalogue_modification: data.catalogue_modification,
                    catalogue_suppression: data.catalogue_suppression,
                    materiel_lecture: data.materiel_lecture,
                    materiel_ajout: data.materiel_ajout,
                    materiel_modification: data.materiel_modification,
                    materiel_suppression: data.materiel_suppression,
                    messages_ajout: data.messages_ajout,
                    messages_suppression: data.messages_suppression,
                    commande_lecture: data.commande_lecture,
                    commande_ajout: data.commande_ajout,
                    commande_etreEnCharge: data.commande_etreEnCharge,
                    commande_abandonner: data.commande_abandonner,
                    cout_lecture: data.cout_lecture,
                    cout_ajout: data.cout_ajout,
                    cout_etreEnCharge: data.cout_etreEnCharge,
                    cout_supprimer: data.cout_supprimer,
                    appli_conf: data.appli_conf,
                    reserve_lecture: data.reserve_lecture,
                    reserve_ajout: data.reserve_ajout,
                    reserve_modification: data.reserve_modification,
                    reserve_suppression: data.reserve_suppression,
                    reserve_cmdVersReserve: data.reserve_cmdVersReserve,
                    reserve_ReserveVersLot: data.reserve_ReserveVersLot,
                    vhf_canal_lecture: data.vhf_canal_lecture,
                    vhf_canal_ajout: data.vhf_canal_ajout,
                    vhf_canal_modification: data.vhf_canal_modification,
                    vhf_canal_suppression: data.vhf_canal_suppression,
                    vhf_plan_lecture: data.vhf_plan_lecture,
                    vhf_plan_ajout: data.vhf_plan_ajout,
                    vhf_plan_modification: data.vhf_plan_modification,
                    vhf_plan_suppression: data.vhf_plan_suppression,
                    vhf_equipement_lecture: data.vhf_equipement_lecture,
                    vhf_equipement_ajout: data.vhf_equipement_ajout,
                    vhf_equipement_modification: data.vhf_equipement_modification,
                    vhf_equipement_suppression: data.vhf_equipement_suppression,
                    vehicules_lecture: data.vehicules_lecture,
                    vehicules_ajout: data.vehicules_ajout,
                    vehicules_modification: data.vehicules_modification,
                    vehicules_suppression: data.vehicules_suppression,
                    vehicules_types_lecture: data.vehicules_types_lecture,
                    vehicules_types_ajout: data.vehicules_types_ajout,
                    vehicules_types_modification: data.vehicules_types_modification,
                    vehicules_types_suppression: data.vehicules_types_suppression,
                    maintenance: data.maintenance,
                    todolist_perso: data.todolist_perso,
                    todolist_lecture: data.todolist_lecture,
                    todolist_modification: data.todolist_modification,
                    contactMailGroupe: data.contactMailGroupe,
                    tenues_lecture: data.tenues_lecture,
                    tenues_ajout: data.tenues_ajout,
                    tenues_modification: data.tenues_modification,
                    tenues_suppression: data.tenues_suppression,
                    tenuesCatalogue_lecture: data.tenuesCatalogue_lecture,
                    tenuesCatalogue_ajout: data.tenuesCatalogue_ajout,
                    tenuesCatalogue_modification: data.tenuesCatalogue_modification,
                    tenuesCatalogue_suppression: data.tenuesCatalogue_suppression,
                    cautions_lecture: data.cautions_lecture,
                    cautions_ajout: data.cautions_ajout,
                    cautions_modification: data.cautions_modification,
                    cautions_suppression: data.cautions_suppression,
                    etats_lecture: data.etats_lecture,
                    etats_ajout: data.etats_ajout,
                    etats_modification: data.etats_modification,
                    etats_suppression: data.etats_suppression,
                    notifications: data.notifications,
                    actionsMassives: data.actionsMassives,
                    delegation: data.delegation,
                    commande_valider_delegate: data.commande_valider_delegate,
                    desinfections_lecture: data.desinfections_lecture,
                    desinfections_ajout: data.desinfections_ajout,
                    desinfections_modification: data.desinfections_modification,
                    desinfections_suppression: data.desinfections_suppression,
                    typesDesinfections_lecture: data.typesDesinfections_lecture,
                    typesDesinfections_ajout: data.typesDesinfections_ajout,
                    typesDesinfections_modification: data.typesDesinfections_modification,
                    typesDesinfections_suppression: data.typesDesinfections_suppression,
                    carburants_lecture: data.carburants_lecture,
                    carburants_ajout: data.carburants_ajout,
                    carburants_modification: data.carburants_modification,
                    carburants_suppression: data.carburants_suppression,
                    vehiculeHealthType_lecture: data.vehiculeHealthType_lecture,
                    vehiculeHealthType_ajout: data.vehiculeHealthType_ajout,
                    vehiculeHealthType_modification: data.vehiculeHealthType_modification,
                    vehiculeHealthType_suppression: data.vehiculeHealthType_suppression,
                    vehiculeHealth_lecture: data.vehiculeHealth_lecture,
                    vehiculeHealth_ajout: data.vehiculeHealth_ajout,
                    vehiculeHealth_modification: data.vehiculeHealth_modification,
                    vehiculeHealth_suppression: data.vehiculeHealth_suppression,
                    alertesBenevolesLots_lecture: data.alertesBenevolesLots_lecture,
                    alertesBenevolesLots_affectation: data.alertesBenevolesLots_affectation,
                    alertesBenevolesLots_affectationTier: data.alertesBenevolesLots_affectationTier,
                    alertesBenevolesVehicules_lecture: data.alertesBenevolesVehicules_lecture,
                    alertesBenevolesVehicules_affectation: data.alertesBenevolesVehicules_affectation,
                    alertesBenevolesVehicules_affectationTier: data.alertesBenevolesVehicules_affectationTier,
                    codeBarre_lecture: data.codeBarre_lecture,
                    codeBarre_ajout: data.codeBarre_ajout,
                    codeBarre_modification: data.codeBarre_modification,
                    codeBarre_suppression: data.codeBarre_suppression,
                    consommationLots_lecture: data.consommationLots_lecture,
                    consommationLots_affectation: data.consommationLots_affectation,
                    consommationLots_supression: data.consommationLots_supression,
                });
            }else{
                const response = await Axios.post('/profils/addProfil',{
                    libelleProfil: data.libelleProfil,
                    LDAP_BINDDN: data.LDAP_BINDDN,
                    descriptifProfil: data.descriptifProfil,
                    connexion_connexion: data.connexion_connexion,
                    annuaire_lecture: data.annuaire_lecture,
                    annuaire_ajout: data.annuaire_ajout,
                    annuaire_modification: data.annuaire_modification,
                    annuaire_mdp: data.annuaire_mdp,
                    annuaire_suppression: data.annuaire_suppression,
                    profils_lecture: data.profils_lecture,
                    profils_ajout: data.profils_ajout,
                    profils_modification: data.profils_modification,
                    profils_suppression: data.profils_suppression,
                    categories_lecture: data.categories_lecture,
                    categories_ajout: data.categories_ajout,
                    categories_modification: data.categories_modification,
                    categories_suppression: data.categories_suppression,
                    fournisseurs_lecture: data.fournisseurs_lecture,
                    fournisseurs_ajout: data.fournisseurs_ajout,
                    fournisseurs_modification: data.fournisseurs_modification,
                    fournisseurs_suppression: data.fournisseurs_suppression,
                    typesLots_lecture: data.typesLots_lecture,
                    typesLots_ajout: data.typesLots_ajout,
                    typesLots_modification: data.typesLots_modification,
                    typesLots_suppression: data.typesLots_suppression,
                    lieux_lecture: data.lieux_lecture,
                    lieux_ajout: data.lieux_ajout,
                    lieux_modification: data.lieux_modification,
                    lieux_suppression: data.lieux_suppression,
                    lots_lecture: data.lots_lecture,
                    lots_ajout: data.lots_ajout,
                    lots_modification: data.lots_modification,
                    lots_suppression: data.lots_suppression,
                    sac_lecture: data.sac_lecture,
                    sac_ajout: data.sac_ajout,
                    sac_modification: data.sac_modification,
                    sac_suppression: data.sac_suppression,
                    catalogue_lecture: data.catalogue_lecture,
                    catalogue_ajout: data.catalogue_ajout,
                    catalogue_modification: data.catalogue_modification,
                    catalogue_suppression: data.catalogue_suppression,
                    materiel_lecture: data.materiel_lecture,
                    materiel_ajout: data.materiel_ajout,
                    materiel_modification: data.materiel_modification,
                    materiel_suppression: data.materiel_suppression,
                    messages_ajout: data.messages_ajout,
                    messages_suppression: data.messages_suppression,
                    commande_lecture: data.commande_lecture,
                    commande_ajout: data.commande_ajout,
                    commande_etreEnCharge: data.commande_etreEnCharge,
                    commande_abandonner: data.commande_abandonner,
                    cout_lecture: data.cout_lecture,
                    cout_ajout: data.cout_ajout,
                    cout_etreEnCharge: data.cout_etreEnCharge,
                    cout_supprimer: data.cout_supprimer,
                    appli_conf: data.appli_conf,
                    reserve_lecture: data.reserve_lecture,
                    reserve_ajout: data.reserve_ajout,
                    reserve_modification: data.reserve_modification,
                    reserve_suppression: data.reserve_suppression,
                    reserve_cmdVersReserve: data.reserve_cmdVersReserve,
                    reserve_ReserveVersLot: data.reserve_ReserveVersLot,
                    vhf_canal_lecture: data.vhf_canal_lecture,
                    vhf_canal_ajout: data.vhf_canal_ajout,
                    vhf_canal_modification: data.vhf_canal_modification,
                    vhf_canal_suppression: data.vhf_canal_suppression,
                    vhf_plan_lecture: data.vhf_plan_lecture,
                    vhf_plan_ajout: data.vhf_plan_ajout,
                    vhf_plan_modification: data.vhf_plan_modification,
                    vhf_plan_suppression: data.vhf_plan_suppression,
                    vhf_equipement_lecture: data.vhf_equipement_lecture,
                    vhf_equipement_ajout: data.vhf_equipement_ajout,
                    vhf_equipement_modification: data.vhf_equipement_modification,
                    vhf_equipement_suppression: data.vhf_equipement_suppression,
                    vehicules_lecture: data.vehicules_lecture,
                    vehicules_ajout: data.vehicules_ajout,
                    vehicules_modification: data.vehicules_modification,
                    vehicules_suppression: data.vehicules_suppression,
                    vehicules_types_lecture: data.vehicules_types_lecture,
                    vehicules_types_ajout: data.vehicules_types_ajout,
                    vehicules_types_modification: data.vehicules_types_modification,
                    vehicules_types_suppression: data.vehicules_types_suppression,
                    maintenance: data.maintenance,
                    todolist_perso: data.todolist_perso,
                    todolist_lecture: data.todolist_lecture,
                    todolist_modification: data.todolist_modification,
                    contactMailGroupe: data.contactMailGroupe,
                    tenues_lecture: data.tenues_lecture,
                    tenues_ajout: data.tenues_ajout,
                    tenues_modification: data.tenues_modification,
                    tenues_suppression: data.tenues_suppression,
                    tenuesCatalogue_lecture: data.tenuesCatalogue_lecture,
                    tenuesCatalogue_ajout: data.tenuesCatalogue_ajout,
                    tenuesCatalogue_modification: data.tenuesCatalogue_modification,
                    tenuesCatalogue_suppression: data.tenuesCatalogue_suppression,
                    cautions_lecture: data.cautions_lecture,
                    cautions_ajout: data.cautions_ajout,
                    cautions_modification: data.cautions_modification,
                    cautions_suppression: data.cautions_suppression,
                    etats_lecture: data.etats_lecture,
                    etats_ajout: data.etats_ajout,
                    etats_modification: data.etats_modification,
                    etats_suppression: data.etats_suppression,
                    notifications: data.notifications,
                    actionsMassives: data.actionsMassives,
                    delegation: data.delegation,
                    commande_valider_delegate: data.commande_valider_delegate,
                    desinfections_lecture: data.desinfections_lecture,
                    desinfections_ajout: data.desinfections_ajout,
                    desinfections_modification: data.desinfections_modification,
                    desinfections_suppression: data.desinfections_suppression,
                    typesDesinfections_lecture: data.typesDesinfections_lecture,
                    typesDesinfections_ajout: data.typesDesinfections_ajout,
                    typesDesinfections_modification: data.typesDesinfections_modification,
                    typesDesinfections_suppression: data.typesDesinfections_suppression,
                    carburants_lecture: data.carburants_lecture,
                    carburants_ajout: data.carburants_ajout,
                    carburants_modification: data.carburants_modification,
                    carburants_suppression: data.carburants_suppression,
                    vehiculeHealthType_lecture: data.vehiculeHealthType_lecture,
                    vehiculeHealthType_ajout: data.vehiculeHealthType_ajout,
                    vehiculeHealthType_modification: data.vehiculeHealthType_modification,
                    vehiculeHealthType_suppression: data.vehiculeHealthType_suppression,
                    vehiculeHealth_lecture: data.vehiculeHealth_lecture,
                    vehiculeHealth_ajout: data.vehiculeHealth_ajout,
                    vehiculeHealth_modification: data.vehiculeHealth_modification,
                    vehiculeHealth_suppression: data.vehiculeHealth_suppression,
                    alertesBenevolesLots_lecture: data.alertesBenevolesLots_lecture,
                    alertesBenevolesLots_affectation: data.alertesBenevolesLots_affectation,
                    alertesBenevolesLots_affectationTier: data.alertesBenevolesLots_affectationTier,
                    alertesBenevolesVehicules_lecture: data.alertesBenevolesVehicules_lecture,
                    alertesBenevolesVehicules_affectation: data.alertesBenevolesVehicules_affectation,
                    alertesBenevolesVehicules_affectationTier: data.alertesBenevolesVehicules_affectationTier,
                    codeBarre_lecture: data.codeBarre_lecture,
                    codeBarre_ajout: data.codeBarre_ajout,
                    codeBarre_modification: data.codeBarre_modification,
                    codeBarre_suppression: data.codeBarre_suppression,
                    consommationLots_lecture: data.consommationLots_lecture,
                    consommationLots_affectation: data.consommationLots_affectation,
                    consommationLots_supression: data.consommationLots_supression,
                });
            }

            handleCloseEditModal();
            setLoading(false);
            initPage();
        } catch (error) {
            console.error(error)
        }
    }

    const [modeEdition, setModeEdition] = useState(false);
    const handleEdition = () => {
        setModeEdition(!modeEdition);
    }

    /* DELETE */
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteModalIdProfil, setDeleteModalIdProfil] = useState();

    const handleCloseDeleteModal = () => {
        setDeleteModalIdProfil();
        setShowDeleteModal(false);
        setLoading(false);
    };
    const handleShowDeleteModal = (id) => {
        setDeleteModalIdProfil(id);
        setShowDeleteModal(true);
    };

    const supprimerEntree = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/profils/deleteProfil',{
                idProfil: deleteModalIdProfil,
            });
            
            initPage();
            handleCloseDeleteModal();
            setLoading(false);
        } catch (e) {
            console.log(e);
        }
    }

    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Gestion des profils et habilitations"
            className="mb-3"
        />

        <Modal show={showDeleteModal} onHide={handleCloseDeleteModal} backdrop="static" keyboard={false}>
            <Modal.Header>
                <Modal.Title>Suppression</Modal.Title>
                <FalconCloseButton onClick={handleCloseDeleteModal}/>
            </Modal.Header>
            <Modal.Body>
                Attention, vous allez supprimer un profil (id: {deleteModalIdProfil}). Etes-vous certain de vouloir continuer ?
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseDeleteModal}>
                    Annuler
                </Button>
                <Button variant='danger' onClick={supprimerEntree} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Supprimer'}</Button>
            </Modal.Footer>
        </Modal>

        <Modal show={showEditModal} onHide={handleCloseEditModal} backdrop="static" keyboard={false} fullscreen>
            <Modal.Header>
                <Modal.Title>{editModalIdProfil > 0 ? 'Modification de '+watch("libelleProfil") : "Création d'un profil"}</Modal.Title>
                <FalconCloseButton onClick={handleCloseEditModal}/>
            </Modal.Header>
            <Modal.Body>
                <Form onSubmit={handleSubmit(ajouterModifierEntree)}>
                    <Form.Check
                        type='switch'
                        id='defaultSwitch'
                        label='Accéder au mode édition'
                        onClick={handleEdition}
                        checked={modeEdition}
                        disabled={!HabilitationService.habilitations['profils_modification']}
                    />

                    <Form.Group className="mb-3">
                        <Form.Label>Libellé du profil</Form.Label>
                        <Form.Control type="text" name="libelleProfil" id="libelleProfil" {...register("libelleProfil")} disabled={!modeEdition}/>
                        <small className="text-danger">{errors.libelleProfil?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Bind DN (lien avec l'AD)</Form.Label>
                        <Form.Control type="text" name="LDAP_BINDDN" id="LDAP_BINDDN" {...register("LDAP_BINDDN")} disabled={!modeEdition}/>
                        <small className="text-danger">{errors.LDAP_BINDDN?.message}</small>
                    </Form.Group>

                    <Form.Group className="mb-3">
                        <Form.Label>Description</Form.Label>
                        <Form.Control size="sm" as="textarea" rows={3} name={"descriptifProfil"} id={"descriptifProfil"} {...register("descriptifProfil")} disabled={!modeEdition}/>
                        <small className="text-danger">{errors.descriptifProfil?.message}</small>
                    </Form.Group>

                    <h6>Connexion à l'application:</h6>
                    <p>
                        {modeEdition ?
                            <Form.Check
                                id='connexion_connexion'
                                name='connexion_connexion'
                                label="Autorisé à se connecter"
                                type='switch'
                                checked={watch("connexion_connexion")}
                                onClick={(e)=>{
                                    setValue("connexion_connexion", !watch("connexion_connexion"))
                                }}
                            />
                        :
                            <><FontAwesomeIcon icon={watch('connexion_connexion') ? 'check' : 'ban'}/> Autorisé à se connecter</>
                        }
                    </p>
                    
                    <hr/>

                    <h6>Administration de l'application:</h6>
                    <p>
                        {modeEdition ?
                            <Form.Check
                                id='appli_conf'
                                name='appli_conf'
                                label={"Modifier la configuration générale de " + window.__ENV__.APP_NAME}
                                type='switch'
                                checked={watch("appli_conf")}
                                onClick={(e)=>{
                                    setValue("appli_conf", !watch("appli_conf"))
                                }}
                            />
                        :
                            <><FontAwesomeIcon icon={watch('appli_conf') ? 'check' : 'ban'}/> Modifier la configuration générale de {window.__ENV__.APP_NAME}</>
                        }<br/>
                        {modeEdition ?
                            <Form.Check
                                id='annuaire_mdp'
                                name='annuaire_mdp'
                                label="Réinitialiser les mots de passe des autres utilisateurs"
                                type='switch'
                                checked={watch("annuaire_mdp")}
                                onClick={(e)=>{
                                    setValue("annuaire_mdp", !watch("annuaire_mdp"))
                                }}
                            />
                        :
                            <><FontAwesomeIcon icon={watch('annuaire_mdp') ? 'check' : 'ban'}/> Réinitialiser les mots de passe des autres utilisateurs</>
                        }<br/>
                        {modeEdition ?
                            <Form.Check
                                id='delegation'
                                name='delegation'
                                label="Se connecter entant qu'autre utilisateur"
                                type='switch'
                                checked={watch("delegation")}
                                onClick={(e)=>{
                                    setValue("delegation", !watch("delegation"))
                                }}
                            />
                        :
                            <><FontAwesomeIcon icon={watch('delegation') ? 'check' : 'ban'}/> Se connecter entant qu'autre utilisateur</>
                        }<br/>
                        {modeEdition ?
                            <Form.Check
                                id='maintenance'
                                name='maintenance'
                                label="Se connecter même en mode maintenance"
                                type='switch'
                                checked={watch("maintenance")}
                                onClick={(e)=>{
                                    setValue("maintenance", !watch("maintenance"))
                                }}
                            />
                        :
                            <><FontAwesomeIcon icon={watch('maintenance') ? 'check' : 'ban'}/> Se connecter même en mode maintenance</>
                        }<br/>
                        {modeEdition ?
                            <Form.Check
                                id='actionsMassives'
                                name='actionsMassives'
                                label="Mener des actions massives directement en base"
                                type='switch'
                                checked={watch("actionsMassives")}
                                onClick={(e)=>{
                                    setValue("actionsMassives", !watch("actionsMassives"))
                                }}
                            />
                        :
                            <><FontAwesomeIcon icon={watch('actionsMassives') ? 'check' : 'ban'}/> Mener des actions massives directement en base</>
                        }<br/>
                    </p>
                    
                    <hr/>

                    <h6>Notifications journalières par mail:</h6>
                    <p>
                        {modeEdition ?
                            <Form.Check
                                id='notifications'
                                name='notifications'
                                label="Autorisé à recevoir les notifications journalières par mail"
                                type='switch'
                                checked={watch("notifications")}
                                onClick={(e)=>{
                                    setValue("notifications", !watch("notifications"))
                                }}
                            />
                        :
                            <><FontAwesomeIcon icon={watch('notifications') ? 'check' : 'ban'}/> Autorisé à recevoir les notifications journalières par mail</>
                        }<br/>
                    </p>

                    <hr/>

                    <h6>Droits par modules:</h6>
                    <Table responsive size='sm'>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Lecture</th>
                                <th>Ajout</th>
                                <th>Modification</th>
                                <th>Suppression</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>LOTS OPERATIONNELS</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Lots</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lots_lecture'
                                            name='lots_lecture'
                                            type='switch'
                                            checked={watch("lots_lecture")}
                                            onClick={(e)=>{
                                                setValue("lots_lecture", !watch("lots_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('lots_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lots_ajout'
                                            name='lots_ajout'
                                            type='switch'
                                            checked={watch("lots_ajout")}
                                            onClick={(e)=>{
                                                setValue("lots_ajout", !watch("lots_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('lots_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lots_modification'
                                            name='lots_modification'
                                            type='switch'
                                            checked={watch("lots_modification")}
                                            onClick={(e)=>{
                                                setValue("lots_modification", !watch("lots_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('lots_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lots_suppression'
                                            name='lots_suppression'
                                            type='switch'
                                            checked={watch("lots_suppression")}
                                            onClick={(e)=>{
                                                setValue("lots_suppression", !watch("lots_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('lots_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td>Sacs et emplacements</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='sac_lecture'
                                            name='sac_lecture'
                                            type='switch'
                                            checked={watch("sac_lecture")}
                                            onClick={(e)=>{
                                                setValue("sac_lecture", !watch("sac_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('sac_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='sac_ajout'
                                            name='sac_ajout'
                                            type='switch'
                                            checked={watch("sac_ajout")}
                                            onClick={(e)=>{
                                                setValue("sac_ajout", !watch("sac_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('sac_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='sac_modification'
                                            name='sac_modification'
                                            type='switch'
                                            checked={watch("sac_modification")}
                                            onClick={(e)=>{
                                                setValue("sac_modification", !watch("sac_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('sac_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='sac_suppression'
                                            name='sac_suppression'
                                            type='switch'
                                            checked={watch("sac_suppression")}
                                            onClick={(e)=>{
                                                setValue("sac_suppression", !watch("sac_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('sac_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Matériels/Consommables</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='materiel_lecture'
                                            name='materiel_lecture'
                                            type='switch'
                                            checked={watch("materiel_lecture")}
                                            onClick={(e)=>{
                                                setValue("materiel_lecture", !watch("materiel_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('materiel_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='materiel_ajout'
                                            name='materiel_ajout'
                                            type='switch'
                                            checked={watch("materiel_ajout")}
                                            onClick={(e)=>{
                                                setValue("materiel_ajout", !watch("materiel_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('materiel_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='materiel_modification'
                                            name='materiel_modification'
                                            type='switch'
                                            checked={watch("materiel_modification")}
                                            onClick={(e)=>{
                                                setValue("materiel_modification", !watch("materiel_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('materiel_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='materiel_suppression'
                                            name='materiel_suppression'
                                            type='switch'
                                            checked={watch("materiel_suppression")}
                                            onClick={(e)=>{
                                                setValue("materiel_suppression", !watch("materiel_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('materiel_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th>TRANSMISSIONS</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Canaux</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_canal_lecture'
                                            name='vhf_canal_lecture'
                                            type='switch'
                                            checked={watch("vhf_canal_lecture")}
                                            onClick={(e)=>{
                                                setValue("vhf_canal_lecture", !watch("vhf_canal_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_canal_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_canal_ajout'
                                            name='vhf_canal_ajout'
                                            type='switch'
                                            checked={watch("vhf_canal_ajout")}
                                            onClick={(e)=>{
                                                setValue("vhf_canal_ajout", !watch("vhf_canal_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_canal_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_canal_modification'
                                            name='vhf_canal_modification'
                                            type='switch'
                                            checked={watch("vhf_canal_modification")}
                                            onClick={(e)=>{
                                                setValue("vhf_canal_modification", !watch("vhf_canal_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_canal_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_canal_suppression'
                                            name='vhf_canal_suppression'
                                            type='switch'
                                            checked={watch("vhf_canal_suppression")}
                                            onClick={(e)=>{
                                                setValue("vhf_canal_suppression", !watch("vhf_canal_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_canal_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td>Plans de fréquences</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_plan_lecture'
                                            name='vhf_plan_lecture'
                                            type='switch'
                                            checked={watch("vhf_plan_lecture")}
                                            onClick={(e)=>{
                                                setValue("vhf_plan_lecture", !watch("vhf_plan_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_plan_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_plan_ajout'
                                            name='vhf_plan_ajout'
                                            type='switch'
                                            checked={watch("vhf_plan_ajout")}
                                            onClick={(e)=>{
                                                setValue("vhf_plan_ajout", !watch("vhf_plan_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_plan_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_plan_modification'
                                            name='vhf_plan_modification'
                                            type='switch'
                                            checked={watch("vhf_plan_modification")}
                                            onClick={(e)=>{
                                                setValue("vhf_plan_modification", !watch("vhf_plan_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_plan_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_plan_suppression'
                                            name='vhf_plan_suppression'
                                            type='switch'
                                            checked={watch("vhf_plan_suppression")}
                                            onClick={(e)=>{
                                                setValue("vhf_plan_suppression", !watch("vhf_plan_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_plan_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td>Equipements de transmission</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_equipement_lecture'
                                            name='vhf_equipement_lecture'
                                            type='switch'
                                            checked={watch("vhf_equipement_lecture")}
                                            onClick={(e)=>{
                                                setValue("vhf_equipement_lecture", !watch("vhf_equipement_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_equipement_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_equipement_ajout'
                                            name='vhf_equipement_ajout'
                                            type='switch'
                                            checked={watch("vhf_equipement_ajout")}
                                            onClick={(e)=>{
                                                setValue("vhf_equipement_ajout", !watch("vhf_equipement_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_equipement_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_equipement_modification'
                                            name='vhf_equipement_modification'
                                            type='switch'
                                            checked={watch("vhf_equipement_modification")}
                                            onClick={(e)=>{
                                                setValue("vhf_equipement_modification", !watch("vhf_equipement_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_equipement_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vhf_equipement_suppression'
                                            name='vhf_equipement_suppression'
                                            type='switch'
                                            checked={watch("vhf_equipement_suppression")}
                                            onClick={(e)=>{
                                                setValue("vhf_equipement_suppression", !watch("vhf_equipement_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vhf_equipement_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th>VEHICULES</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Véhicules</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_lecture'
                                            name='vehicules_lecture'
                                            type='switch'
                                            checked={watch("vehicules_lecture")}
                                            onClick={(e)=>{
                                                setValue("vehicules_lecture", !watch("vehicules_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehicules_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_ajout'
                                            name='vehicules_ajout'
                                            type='switch'
                                            checked={watch("vehicules_ajout")}
                                            onClick={(e)=>{
                                                setValue("vehicules_ajout", !watch("vehicules_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehicules_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_modification'
                                            name='vehicules_modification'
                                            type='switch'
                                            checked={watch("vehicules_modification")}
                                            onClick={(e)=>{
                                                setValue("vehicules_modification", !watch("vehicules_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehicules_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_suppression'
                                            name='vehicules_suppression'
                                            type='switch'
                                            checked={watch("vehicules_suppression")}
                                            onClick={(e)=>{
                                                setValue("vehicules_suppression", !watch("vehicules_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehicules_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td>Désinfections</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='desinfections_lecture'
                                            name='desinfections_lecture'
                                            type='switch'
                                            checked={watch("desinfections_lecture")}
                                            onClick={(e)=>{
                                                setValue("desinfections_lecture", !watch("desinfections_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('desinfections_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='desinfections_ajout'
                                            name='desinfections_ajout'
                                            type='switch'
                                            checked={watch("desinfections_ajout")}
                                            onClick={(e)=>{
                                                setValue("desinfections_ajout", !watch("desinfections_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('desinfections_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='desinfections_modification'
                                            name='desinfections_modification'
                                            type='switch'
                                            checked={watch("desinfections_modification")}
                                            onClick={(e)=>{
                                                setValue("desinfections_modification", !watch("desinfections_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('desinfections_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='desinfections_suppression'
                                            name='desinfections_suppression'
                                            type='switch'
                                            checked={watch("desinfections_suppression")}
                                            onClick={(e)=>{
                                                setValue("desinfections_suppression", !watch("desinfections_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('desinfections_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td>Taches de maintenance</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealth_lecture'
                                            name='vehiculeHealth_lecture'
                                            type='switch'
                                            checked={watch("vehiculeHealth_lecture")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealth_lecture", !watch("vehiculeHealth_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehiculeHealth_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealth_ajout'
                                            name='vehiculeHealth_ajout'
                                            type='switch'
                                            checked={watch("vehiculeHealth_ajout")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealth_ajout", !watch("vehiculeHealth_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehiculeHealth_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealth_modification'
                                            name='vehiculeHealth_modification'
                                            type='switch'
                                            checked={watch("vehiculeHealth_modification")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealth_modification", !watch("vehiculeHealth_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehiculeHealth_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealth_suppression'
                                            name='vehiculeHealth_suppression'
                                            type='switch'
                                            checked={watch("vehiculeHealth_suppression")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealth_suppression", !watch("vehiculeHealth_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('vehiculeHealth_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th>TENUES</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Tenues</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenues_lecture'
                                            name='tenues_lecture'
                                            type='switch'
                                            checked={watch("tenues_lecture")}
                                            onClick={(e)=>{
                                                setValue("tenues_lecture", !watch("tenues_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('tenues_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenues_ajout'
                                            name='tenues_ajout'
                                            type='switch'
                                            checked={watch("tenues_ajout")}
                                            onClick={(e)=>{
                                                setValue("tenues_ajout", !watch("tenues_ajout"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('tenues_ajout') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenues_modification'
                                            name='tenues_modification'
                                            type='switch'
                                            checked={watch("tenues_modification")}
                                            onClick={(e)=>{
                                                setValue("tenues_modification", !watch("tenues_modification"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('tenues_modification') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenues_suppression'
                                            name='tenues_suppression'
                                            type='switch'
                                            checked={watch("tenues_suppression")}
                                            onClick={(e)=>{
                                                setValue("tenues_suppression", !watch("tenues_suppression"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('tenues_suppression') ? 'check' : 'ban'}/>
                                    }
                                </td>
                            </tr>
                            <tr>
                                <td>Catalogue des tenues</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenuesCatalogue_lecture'
                                            name='tenuesCatalogue_lecture'
                                            type='switch'
                                            checked={watch("tenuesCatalogue_lecture")}
                                            onClick={(e)=>{
                                                setValue("tenuesCatalogue_lecture", !watch("tenuesCatalogue_lecture"))
                                            }}
                                        />
                                    :
                                        <FontAwesomeIcon icon={watch('tenuesCatalogue_lecture') ? 'check' : 'ban'}/>
                                    }
                                </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenuesCatalogue_ajout'
                                            name='tenuesCatalogue_ajout'
                                            type='switch'
                                            checked={watch("tenuesCatalogue_ajout")}
                                            onClick={(e)=>{
                                                setValue("tenuesCatalogue_ajout", !watch("tenuesCatalogue_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('tenuesCatalogue_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenuesCatalogue_modification'
                                            name='tenuesCatalogue_modification'
                                            type='switch'
                                            checked={watch("tenuesCatalogue_modification")}
                                            onClick={(e)=>{
                                                setValue("tenuesCatalogue_modification", !watch("tenuesCatalogue_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('tenuesCatalogue_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='tenuesCatalogue_suppression'
                                            name='tenuesCatalogue_suppression'
                                            type='switch'
                                            checked={watch("tenuesCatalogue_suppression")}
                                            onClick={(e)=>{
                                                setValue("tenuesCatalogue_suppression", !watch("tenuesCatalogue_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('tenuesCatalogue_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Cautions</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cautions_lecture'
                                            name='cautions_lecture'
                                            type='switch'
                                            checked={watch("cautions_lecture")}
                                            onClick={(e)=>{
                                                setValue("cautions_lecture", !watch("cautions_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cautions_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cautions_ajout'
                                            name='cautions_ajout'
                                            type='switch'
                                            checked={watch("cautions_ajout")}
                                            onClick={(e)=>{
                                                setValue("cautions_ajout", !watch("cautions_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cautions_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cautions_modification'
                                            name='cautions_modification'
                                            type='switch'
                                            checked={watch("cautions_modification")}
                                            onClick={(e)=>{
                                                setValue("cautions_modification", !watch("cautions_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cautions_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cautions_suppression'
                                            name='cautions_suppression'
                                            type='switch'
                                            checked={watch("cautions_suppression")}
                                            onClick={(e)=>{
                                                setValue("cautions_suppression", !watch("cautions_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cautions_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <th>PARAMETRES</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Catégories</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='categories_lecture'
                                            name='categories_lecture'
                                            type='switch'
                                            checked={watch("categories_lecture")}
                                            onClick={(e)=>{
                                                setValue("categories_lecture", !watch("categories_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('categories_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='categories_ajout'
                                            name='categories_ajout'
                                            type='switch'
                                            checked={watch("categories_ajout")}
                                            onClick={(e)=>{
                                                setValue("categories_ajout", !watch("categories_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('categories_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='categories_modification'
                                            name='categories_modification'
                                            type='switch'
                                            checked={watch("categories_modification")}
                                            onClick={(e)=>{
                                                setValue("categories_modification", !watch("categories_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('categories_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='categories_suppression'
                                            name='categories_suppression'
                                            type='switch'
                                            checked={watch("categories_suppression")}
                                            onClick={(e)=>{
                                                setValue("categories_suppression", !watch("categories_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('categories_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Codes Barre</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='codeBarre_lecture'
                                            name='codeBarre_lecture'
                                            type='switch'
                                            checked={watch("codeBarre_lecture")}
                                            onClick={(e)=>{
                                                setValue("codeBarre_lecture", !watch("codeBarre_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('codeBarre_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='codeBarre_ajout'
                                            name='codeBarre_ajout'
                                            type='switch'
                                            checked={watch("codeBarre_ajout")}
                                            onClick={(e)=>{
                                                setValue("codeBarre_ajout", !watch("codeBarre_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('codeBarre_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='codeBarre_modification'
                                            name='codeBarre_modification'
                                            type='switch'
                                            checked={watch("codeBarre_modification")}
                                            onClick={(e)=>{
                                                setValue("codeBarre_modification", !watch("codeBarre_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('codeBarre_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='codeBarre_suppression'
                                            name='codeBarre_suppression'
                                            type='switch'
                                            checked={watch("codeBarre_suppression")}
                                            onClick={(e)=>{
                                                setValue("codeBarre_suppression", !watch("codeBarre_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('codeBarre_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Référentiels</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesLots_lecture'
                                            name='typesLots_lecture'
                                            type='switch'
                                            checked={watch("typesLots_lecture")}
                                            onClick={(e)=>{
                                                setValue("typesLots_lecture", !watch("typesLots_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesLots_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesLots_ajout'
                                            name='typesLots_ajout'
                                            type='switch'
                                            checked={watch("typesLots_ajout")}
                                            onClick={(e)=>{
                                                setValue("typesLots_ajout", !watch("typesLots_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesLots_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesLots_modification'
                                            name='typesLots_modification'
                                            type='switch'
                                            checked={watch("typesLots_modification")}
                                            onClick={(e)=>{
                                                setValue("typesLots_modification", !watch("typesLots_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesLots_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesLots_suppression'
                                            name='typesLots_suppression'
                                            type='switch'
                                            checked={watch("typesLots_suppression")}
                                            onClick={(e)=>{
                                                setValue("typesLots_suppression", !watch("typesLots_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesLots_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Lieux</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lieux_lecture'
                                            name='lieux_lecture'
                                            type='switch'
                                            checked={watch("lieux_lecture")}
                                            onClick={(e)=>{
                                                setValue("lieux_lecture", !watch("lieux_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('lieux_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lieux_ajout'
                                            name='lieux_ajout'
                                            type='switch'
                                            checked={watch("lieux_ajout")}
                                            onClick={(e)=>{
                                                setValue("lieux_ajout", !watch("lieux_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('lieux_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lieux_modification'
                                            name='lieux_modification'
                                            type='switch'
                                            checked={watch("lieux_modification")}
                                            onClick={(e)=>{
                                                setValue("lieux_modification", !watch("lieux_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('lieux_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='lieux_suppression'
                                            name='lieux_suppression'
                                            type='switch'
                                            checked={watch("lieux_suppression")}
                                            onClick={(e)=>{
                                                setValue("lieux_suppression", !watch("lieux_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('lieux_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Catalogue</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='catalogue_lecture'
                                            name='catalogue_lecture'
                                            type='switch'
                                            checked={watch("catalogue_lecture")}
                                            onClick={(e)=>{
                                                setValue("catalogue_lecture", !watch("catalogue_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('catalogue_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='catalogue_ajout'
                                            name='catalogue_ajout'
                                            type='switch'
                                            checked={watch("catalogue_ajout")}
                                            onClick={(e)=>{
                                                setValue("catalogue_ajout", !watch("catalogue_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('catalogue_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='catalogue_modification'
                                            name='catalogue_modification'
                                            type='switch'
                                            checked={watch("catalogue_modification")}
                                            onClick={(e)=>{
                                                setValue("catalogue_modification", !watch("catalogue_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('catalogue_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='catalogue_suppression'
                                            name='catalogue_suppression'
                                            type='switch'
                                            checked={watch("catalogue_suppression")}
                                            onClick={(e)=>{
                                                setValue("catalogue_suppression", !watch("catalogue_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('catalogue_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Types de véhicules</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_types_lecture'
                                            name='vehicules_types_lecture'
                                            type='switch'
                                            checked={watch("vehicules_types_lecture")}
                                            onClick={(e)=>{
                                                setValue("vehicules_types_lecture", !watch("vehicules_types_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehicules_types_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_types_ajout'
                                            name='vehicules_types_ajout'
                                            type='switch'
                                            checked={watch("vehicules_types_ajout")}
                                            onClick={(e)=>{
                                                setValue("vehicules_types_ajout", !watch("vehicules_types_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehicules_types_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_types_modification'
                                            name='vehicules_types_modification'
                                            type='switch'
                                            checked={watch("vehicules_types_modification")}
                                            onClick={(e)=>{
                                                setValue("vehicules_types_modification", !watch("vehicules_types_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehicules_types_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehicules_types_suppression'
                                            name='vehicules_types_suppression'
                                            type='switch'
                                            checked={watch("vehicules_types_suppression")}
                                            onClick={(e)=>{
                                                setValue("vehicules_types_suppression", !watch("vehicules_types_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehicules_types_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Types de désinfections</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesDesinfections_lecture'
                                            name='typesDesinfections_lecture'
                                            type='switch'
                                            checked={watch("typesDesinfections_lecture")}
                                            onClick={(e)=>{
                                                setValue("typesDesinfections_lecture", !watch("typesDesinfections_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesDesinfections_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesDesinfections_ajout'
                                            name='typesDesinfections_ajout'
                                            type='switch'
                                            checked={watch("typesDesinfections_ajout")}
                                            onClick={(e)=>{
                                                setValue("typesDesinfections_ajout", !watch("typesDesinfections_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesDesinfections_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesDesinfections_modification'
                                            name='typesDesinfections_modification'
                                            type='switch'
                                            checked={watch("typesDesinfections_modification")}
                                            onClick={(e)=>{
                                                setValue("typesDesinfections_modification", !watch("typesDesinfections_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesDesinfections_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='typesDesinfections_suppression'
                                            name='typesDesinfections_suppression'
                                            type='switch'
                                            checked={watch("typesDesinfections_suppression")}
                                            onClick={(e)=>{
                                                setValue("typesDesinfections_suppression", !watch("typesDesinfections_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('typesDesinfections_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Types de taches de maintenance</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealthType_lecture'
                                            name='vehiculeHealthType_lecture'
                                            type='switch'
                                            checked={watch("vehiculeHealthType_lecture")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealthType_lecture", !watch("vehiculeHealthType_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehiculeHealthType_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealthType_ajout'
                                            name='vehiculeHealthType_ajout'
                                            type='switch'
                                            checked={watch("vehiculeHealthType_ajout")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealthType_ajout", !watch("vehiculeHealthType_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehiculeHealthType_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealthType_modification'
                                            name='vehiculeHealthType_modification'
                                            type='switch'
                                            checked={watch("vehiculeHealthType_modification")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealthType_modification", !watch("vehiculeHealthType_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehiculeHealthType_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='vehiculeHealthType_suppression'
                                            name='vehiculeHealthType_suppression'
                                            type='switch'
                                            checked={watch("vehiculeHealthType_suppression")}
                                            onClick={(e)=>{
                                                setValue("vehiculeHealthType_suppression", !watch("vehiculeHealthType_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('vehiculeHealthType_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Carburants</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='carburants_lecture'
                                            name='carburants_lecture'
                                            type='switch'
                                            checked={watch("carburants_lecture")}
                                            onClick={(e)=>{
                                                setValue("carburants_lecture", !watch("carburants_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('carburants_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='carburants_ajout'
                                            name='carburants_ajout'
                                            type='switch'
                                            checked={watch("carburants_ajout")}
                                            onClick={(e)=>{
                                                setValue("carburants_ajout", !watch("carburants_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('carburants_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='carburants_modification'
                                            name='carburants_modification'
                                            type='switch'
                                            checked={watch("carburants_modification")}
                                            onClick={(e)=>{
                                                setValue("carburants_modification", !watch("carburants_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('carburants_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='carburants_suppression'
                                            name='carburants_suppression'
                                            type='switch'
                                            checked={watch("carburants_suppression")}
                                            onClick={(e)=>{
                                                setValue("carburants_suppression", !watch("carburants_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('carburants_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Etats</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='etats_lecture'
                                            name='etats_lecture'
                                            type='switch'
                                            checked={watch("etats_lecture")}
                                            onClick={(e)=>{
                                                setValue("etats_lecture", !watch("etats_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('etats_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='etats_ajout'
                                            name='etats_ajout'
                                            type='switch'
                                            checked={watch("etats_ajout")}
                                            onClick={(e)=>{
                                                setValue("etats_ajout", !watch("etats_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('etats_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='etats_modification'
                                            name='etats_modification'
                                            type='switch'
                                            checked={watch("etats_modification")}
                                            onClick={(e)=>{
                                                setValue("etats_modification", !watch("etats_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('etats_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='etats_suppression'
                                            name='etats_suppression'
                                            type='switch'
                                            checked={watch("etats_suppression")}
                                            onClick={(e)=>{
                                                setValue("etats_suppression", !watch("etats_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('etats_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                        </tbody>
                    </Table>

                    <hr/>

                    <h6>Droits sur les modules publiques:</h6>
                    <Table responsive size='sm'>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Lecture</th>
                                <th>Traitement</th>
                                <th>Affecter à un tier</th>
                                <th>Suppression</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>ALERTES BENEVOLES</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Lots</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='alertesBenevolesLots_lecture'
                                            name='alertesBenevolesLots_lecture'
                                            type='switch'
                                            checked={watch("alertesBenevolesLots_lecture")}
                                            onClick={(e)=>{
                                                setValue("alertesBenevolesLots_lecture", !watch("alertesBenevolesLots_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('alertesBenevolesLots_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='alertesBenevolesLots_affectation'
                                            name='alertesBenevolesLots_affectation'
                                            type='switch'
                                            checked={watch("alertesBenevolesLots_affectation")}
                                            onClick={(e)=>{
                                                setValue("alertesBenevolesLots_affectation", !watch("alertesBenevolesLots_affectation"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('alertesBenevolesLots_affectation') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='alertesBenevolesLots_affectationTier'
                                            name='alertesBenevolesLots_affectationTier'
                                            type='switch'
                                            checked={watch("alertesBenevolesLots_affectationTier")}
                                            onClick={(e)=>{
                                                setValue("alertesBenevolesLots_affectationTier", !watch("alertesBenevolesLots_affectationTier"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('alertesBenevolesLots_affectationTier') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Véhicules</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='alertesBenevolesVehicules_lecture'
                                            name='alertesBenevolesVehicules_lecture'
                                            type='switch'
                                            checked={watch("alertesBenevolesVehicules_lecture")}
                                            onClick={(e)=>{
                                                setValue("alertesBenevolesVehicules_lecture", !watch("alertesBenevolesVehicules_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('alertesBenevolesVehicules_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='alertesBenevolesVehicules_affectation'
                                            name='alertesBenevolesVehicules_affectation'
                                            type='switch'
                                            checked={watch("alertesBenevolesVehicules_affectation")}
                                            onClick={(e)=>{
                                                setValue("alertesBenevolesVehicules_affectation", !watch("alertesBenevolesVehicules_affectation"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('alertesBenevolesVehicules_affectation') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='alertesBenevolesVehicules_affectationTier'
                                            name='alertesBenevolesVehicules_affectationTier'
                                            type='switch'
                                            checked={watch("alertesBenevolesVehicules_affectationTier")}
                                            onClick={(e)=>{
                                                setValue("alertesBenevolesVehicules_affectationTier", !watch("alertesBenevolesVehicules_affectationTier"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('alertesBenevolesVehicules_affectationTier') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>CONSOMMATION DES BENEVOLES</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>Rapports de consommation</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='consommationLots_lecture'
                                            name='consommationLots_lecture'
                                            type='switch'
                                            checked={watch("consommationLots_lecture")}
                                            onClick={(e)=>{
                                                setValue("consommationLots_lecture", !watch("consommationLots_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('consommationLots_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='consommationLots_affectation'
                                            name='consommationLots_affectation'
                                            type='switch'
                                            checked={watch("consommationLots_affectation")}
                                            onClick={(e)=>{
                                                setValue("consommationLots_affectation", !watch("consommationLots_affectation"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('consommationLots_affectation') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='consommationLots_supression'
                                            name='consommationLots_supression'
                                            type='switch'
                                            checked={watch("consommationLots_supression")}
                                            onClick={(e)=>{
                                                setValue("consommationLots_supression", !watch("consommationLots_supression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('consommationLots_supression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                        </tbody>
                    </Table>

                    <hr/>

                    <h6>Commandes et finances:</h6>
                    <Table responsive size='sm'>
                        <thead>
                            <tr>
                                <th>COMMANDES</th>
                                <th>Lecture</th>
                                <th>Ajout</th>
                                <th>Modification</th>
                                <th>Valideur universel</th>
                                <th>Etre en charge</th>
                                <th>Abandonner Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Commandes</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='commande_lecture'
                                            name='commande_lecture'
                                            type='switch'
                                            checked={watch("commande_lecture")}
                                            onClick={(e)=>{
                                                setValue("commande_lecture", !watch("commande_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('commande_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='commande_ajout'
                                            name='commande_ajout'
                                            type='switch'
                                            checked={watch("commande_ajout")}
                                            onClick={(e)=>{
                                                setValue("commande_ajout", !watch("commande_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('commande_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='commande_ajout'
                                            name='commande_ajout'
                                            type='switch'
                                            checked={watch("commande_ajout")}
                                            onClick={(e)=>{
                                                setValue("commande_ajout", !watch("commande_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('commande_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='commande_valider_delegate'
                                            name='commande_valider_delegate'
                                            type='switch'
                                            checked={watch("commande_valider_delegate")}
                                            onClick={(e)=>{
                                                setValue("commande_valider_delegate", !watch("commande_valider_delegate"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('commande_valider_delegate') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='commande_etreEnCharge'
                                            name='commande_etreEnCharge'
                                            type='switch'
                                            checked={watch("commande_etreEnCharge")}
                                            onClick={(e)=>{
                                                setValue("commande_etreEnCharge", !watch("commande_etreEnCharge"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('commande_etreEnCharge') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='commande_abandonner'
                                            name='commande_abandonner'
                                            type='switch'
                                            checked={watch("commande_abandonner")}
                                            onClick={(e)=>{
                                                setValue("commande_abandonner", !watch("commande_abandonner"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('commande_abandonner') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Fournisseurs</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='fournisseurs_lecture'
                                            name='fournisseurs_lecture'
                                            type='switch'
                                            checked={watch("fournisseurs_lecture")}
                                            onClick={(e)=>{
                                                setValue("fournisseurs_lecture", !watch("fournisseurs_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('fournisseurs_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='fournisseurs_ajout'
                                            name='fournisseurs_ajout'
                                            type='switch'
                                            checked={watch("fournisseurs_ajout")}
                                            onClick={(e)=>{
                                                setValue("fournisseurs_ajout", !watch("fournisseurs_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('fournisseurs_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='fournisseurs_modification'
                                            name='fournisseurs_modification'
                                            type='switch'
                                            checked={watch("fournisseurs_modification")}
                                            onClick={(e)=>{
                                                setValue("fournisseurs_modification", !watch("fournisseurs_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('fournisseurs_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='fournisseurs_suppression'
                                            name='fournisseurs_suppression'
                                            type='switch'
                                            checked={watch("fournisseurs_suppression")}
                                            onClick={(e)=>{
                                                setValue("fournisseurs_suppression", !watch("fournisseurs_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('fournisseurs_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Centres de coûts</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cout_lecture'
                                            name='cout_lecture'
                                            type='switch'
                                            checked={watch("cout_lecture")}
                                            onClick={(e)=>{
                                                setValue("cout_lecture", !watch("cout_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cout_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cout_ajout'
                                            name='cout_ajout'
                                            type='switch'
                                            checked={watch("cout_ajout")}
                                            onClick={(e)=>{
                                                setValue("cout_ajout", !watch("cout_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cout_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cout_ajout'
                                            name='cout_ajout'
                                            type='switch'
                                            checked={watch("cout_ajout")}
                                            onClick={(e)=>{
                                                setValue("cout_ajout", !watch("cout_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cout_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cout_etreEnCharge'
                                            name='cout_etreEnCharge'
                                            type='switch'
                                            checked={watch("cout_etreEnCharge")}
                                            onClick={(e)=>{
                                                setValue("cout_etreEnCharge", !watch("cout_etreEnCharge"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cout_etreEnCharge') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='cout_supprimer'
                                            name='cout_supprimer'
                                            type='switch'
                                            checked={watch("cout_supprimer")}
                                            onClick={(e)=>{
                                                setValue("cout_supprimer", !watch("cout_supprimer"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('cout_supprimer') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                        </tbody>
                    </Table>

                    <hr/>

                    <h6>Réserves:</h6>
                    <Table responsive size='sm'>
                        <thead>
                            <tr>
                                <th>RESERVE</th>
                                <th>Lecture</th>
                                <th>Ajout</th>
                                <th>Modification</th>
                                <th>Supprimer</th>
                                <th>Intégrer du matériel dans la réserve suite à une commande</th>
                                <th>Sortir du matériel de la réserve pour l'intégrer à un lot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Réserve:</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='reserve_lecture'
                                            name='reserve_lecture'
                                            type='switch'
                                            checked={watch("reserve_lecture")}
                                            onClick={(e)=>{
                                                setValue("reserve_lecture", !watch("reserve_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('reserve_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='reserve_ajout'
                                            name='reserve_ajout'
                                            type='switch'
                                            checked={watch("reserve_ajout")}
                                            onClick={(e)=>{
                                                setValue("reserve_ajout", !watch("reserve_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('reserve_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='reserve_modification'
                                            name='reserve_modification'
                                            type='switch'
                                            checked={watch("reserve_modification")}
                                            onClick={(e)=>{
                                                setValue("reserve_modification", !watch("reserve_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('reserve_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='reserve_suppression'
                                            name='reserve_suppression'
                                            type='switch'
                                            checked={watch("reserve_suppression")}
                                            onClick={(e)=>{
                                                setValue("reserve_suppression", !watch("reserve_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('reserve_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='reserve_cmdVersReserve'
                                            name='reserve_cmdVersReserve'
                                            type='switch'
                                            checked={watch("reserve_cmdVersReserve")}
                                            onClick={(e)=>{
                                                setValue("reserve_cmdVersReserve", !watch("reserve_cmdVersReserve"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('reserve_cmdVersReserve') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='reserve_ReserveVersLot'
                                            name='reserve_ReserveVersLot'
                                            type='switch'
                                            checked={watch("reserve_ReserveVersLot")}
                                            onClick={(e)=>{
                                                setValue("reserve_ReserveVersLot", !watch("reserve_ReserveVersLot"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('reserve_ReserveVersLot') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                        </tbody>
                    </Table>

                    <hr/>

                    <h6>Gestion d'équipe:</h6>
                    <Table responsive size='sm'>
                        <thead>
                            <tr>
                                <th>GESTION EQUIPE</th>
                                <th>Lecture</th>
                                <th>Ajout</th>
                                <th>Modification</th>
                                <th>Modification de sa propre liste</th>
                                <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Annuaire</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='annuaire_lecture'
                                            name='annuaire_lecture'
                                            type='switch'
                                            checked={watch("annuaire_lecture")}
                                            onClick={(e)=>{
                                                setValue("annuaire_lecture", !watch("annuaire_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('annuaire_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='annuaire_ajout'
                                            name='annuaire_ajout'
                                            type='switch'
                                            checked={watch("annuaire_ajout")}
                                            onClick={(e)=>{
                                                setValue("annuaire_ajout", !watch("annuaire_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('annuaire_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='annuaire_modification'
                                            name='annuaire_modification'
                                            type='switch'
                                            checked={watch("annuaire_modification")}
                                            onClick={(e)=>{
                                                setValue("annuaire_modification", !watch("annuaire_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('annuaire_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='annuaire_suppression'
                                            name='annuaire_suppression'
                                            type='switch'
                                            checked={watch("annuaire_suppression")}
                                            onClick={(e)=>{
                                                setValue("annuaire_suppression", !watch("annuaire_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('annuaire_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Profils</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='profils_lecture'
                                            name='profils_lecture'
                                            type='switch'
                                            checked={watch("profils_lecture")}
                                            onClick={(e)=>{
                                                setValue("profils_lecture", !watch("profils_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('profils_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='profils_ajout'
                                            name='profils_ajout'
                                            type='switch'
                                            checked={watch("profils_ajout")}
                                            onClick={(e)=>{
                                                setValue("profils_ajout", !watch("profils_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('profils_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='profils_modification'
                                            name='profils_modification'
                                            type='switch'
                                            checked={watch("profils_modification")}
                                            onClick={(e)=>{
                                                setValue("profils_modification", !watch("profils_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('profils_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='profils_suppression'
                                            name='profils_suppression'
                                            type='switch'
                                            checked={watch("profils_suppression")}
                                            onClick={(e)=>{
                                                setValue("profils_suppression", !watch("profils_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('profils_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Messages généraux</td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='messages_ajout'
                                            name='messages_ajout'
                                            type='switch'
                                            checked={watch("messages_ajout")}
                                            onClick={(e)=>{
                                                setValue("messages_ajout", !watch("messages_ajout"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('messages_ajout') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='messages_suppression'
                                            name='messages_suppression'
                                            type='switch'
                                            checked={watch("messages_suppression")}
                                            onClick={(e)=>{
                                                setValue("messages_suppression", !watch("messages_suppression"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('messages_suppression') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                            </tr>
                            <tr>
                                <td>Messages mails</td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='contactMailGroupe'
                                            name='contactMailGroupe'
                                            type='switch'
                                            checked={watch("contactMailGroupe")}
                                            onClick={(e)=>{
                                                setValue("contactMailGroupe", !watch("contactMailGroupe"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('contactMailGroupe') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>ToDoList</td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='todolist_lecture'
                                            name='todolist_lecture'
                                            type='switch'
                                            checked={watch("todolist_lecture")}
                                            onClick={(e)=>{
                                                setValue("todolist_lecture", !watch("todolist_lecture"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('todolist_lecture') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='todolist_modification'
                                            name='todolist_modification'
                                            type='switch'
                                            checked={watch("todolist_modification")}
                                            onClick={(e)=>{
                                                setValue("todolist_modification", !watch("todolist_modification"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('todolist_modification') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td>
                                    {modeEdition ?
                                        <Form.Check
                                            id='todolist_perso'
                                            name='todolist_perso'
                                            type='switch'
                                            checked={watch("todolist_perso")}
                                            onClick={(e)=>{
                                                setValue("todolist_perso", !watch("todolist_perso"))
                                            }}
                                        />
                                        :
                                            <FontAwesomeIcon icon={watch('todolist_perso') ? 'check' : 'ban'}/>
                                        }
                                    </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </Table>

                    {modeEdition ? <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Enregistrer'}</Button> : null}
                </Form>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseEditModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>

        <FalconComponentCard noGuttersBottom className="mb-3">
            <FalconComponentCard.Body
                scope={{ ActionButton }}
                noLight
            >
                {readyToDisplay ?
                    <GPMtable
                        columns={colonnes}
                        data={profils}
                        topButtonShow={true}
                        topButton={
                            HabilitationService.habilitations['profils_ajout'] ?
                                <IconButton
                                    icon='plus'
                                    size = 'sm'
                                    variant="outline-success"
                                    onClick={()=>{handleShowEditModal(0)}}
                                >Nouveau profil</IconButton>
                            : null
                        }
                    />
                : <LoaderInfiniteLoop />}
            </FalconComponentCard.Body>
        </FalconComponentCard>

    </>);
};

Profils.propTypes = {};

export default Profils;
