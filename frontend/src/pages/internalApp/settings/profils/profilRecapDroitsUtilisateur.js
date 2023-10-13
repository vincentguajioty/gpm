import React, {useEffect, useState} from 'react';
import { Table } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';
import { faHome } from '@fortawesome/free-solid-svg-icons';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

const ProfilRecapDroitsUtilisateur = ({idPersonne}) => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [displayForbiddenMessage, setDisplayForbiddenMessage] = useState(false);

    const [profil, setProfil] = useState([]);

    const initComponent = async () => {
        try {
            if(HabilitationService.habilitations['idPersonne'] != idPersonne && !HabilitationService.habilitations['annuaire_lecture'])
            {
                setDisplayForbiddenMessage(true);
            }
            else
            {
                const getProfil = await Axios.post('/settingsUtilisateurs/getOneUser',{
                    idPersonne: idPersonne
                })
                setProfil(getProfil.data[0]);
                setReadyToDisplay(true);
            }
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initComponent();
    },[])

    if(displayForbiddenMessage)
    {
        return "Vous n'êtes pas habilité à consulter le profil d'une autre personne";
    }
    else
    {
        if(readyToDisplay)
        {
            return(<>
                <h6>Connexion à l'application:</h6>
                <p>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Autorisé à se connecter
                </p>
                
                <hr/>

                <h6>Administration de l'application:</h6>
                <p>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Modifier la configuration générale de GPM VG<br/>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Réinitialiser les mots de passe des autres utilisateurs<br/>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Se connecter entant qu'autre utilisateur<br/>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Se connecter même en mode maitenance<br/>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Gérer les adresses IP bloquées<br/>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Mener des actions massives directement en base<br/>
                </p>
                
                <hr/>

                <h6>Notifications journalières par mail:</h6>
                <p>
                    <FontAwesomeIcon icon={HabilitationService.habilitations['connexion_connexion'] ? 'check' : 'ban'} /> Autorisé à recevoir les notifications journalières par mail<br/>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lots_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lots_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lots_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Sacs</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Emplacements</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac2_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac2_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac2_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['sac2_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Matériels/Consommables</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['materiel_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['materiel_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['materiel_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['materiel_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_canal_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_canal_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_canal_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_canal_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Plans de fréquences</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_plan_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_plan_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_plan_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_plan_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Equipements de transmission</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_equipement_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_equipement_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_equipement_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vhf_equipement_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Désinfections</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['desinfections_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['desinfections_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['desinfections_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['desinfections_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Taches de maintenance</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealth_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealth_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealth_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealth_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenues_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenues_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenues_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenues_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Catalogue des tenues</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenuesCatalogue_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenuesCatalogue_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenuesCatalogue_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['tenuesCatalogue_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Cautions</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cautions_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cautions_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cautions_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cautions_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['categories_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['categories_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['categories_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['categories_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Codes Barre</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['codeBarre_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['codeBarre_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['codeBarre_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['codeBarre_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Référentiels</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesLots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesLots_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesLots_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesLots_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Lieux</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lieux_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lieux_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lieux_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['lieux_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Catalogue</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['catalogue_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['catalogue_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['catalogue_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['catalogue_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Types de véhicules</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_types_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_types_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_types_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehicules_types_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Types de désinfections</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesDesinfections_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesDesinfections_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesDesinfections_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['typesDesinfections_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Types de taches de maintenance</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealthType_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealthType_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealthType_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['vehiculeHealthType_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Carburants</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['carburants_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['carburants_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['carburants_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['carburants_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Etats</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['etats_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['etats_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['etats_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['etats_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['alertesBenevolesLots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['alertesBenevolesLots_affectation'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['alertesBenevolesLots_affectationTier'] ? 'check' : 'ban'}/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Véhicules</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['alertesBenevolesVehicules_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['alertesBenevolesVehicules_affectation'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['alertesBenevolesVehicules_affectationTier'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['consommationLots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['consommationLots_affectation'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['consommationLots_supression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['commande_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['commande_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['commande_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['commande_valider_delegate'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['commande_etreEnCharge'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['commande_abandonner'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Fournisseurs</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['fournisseurs_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['fournisseurs_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['fournisseurs_modification'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['fournisseurs_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Centres de coûts</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cout_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cout_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cout_ajout'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cout_etreEnCharge'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['cout_supprimer'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['reserve_lecture'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['reserve_ajout'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['reserve_modification'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['reserve_suppression'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['reserve_cmdVersReserve'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['reserve_ReserveVersLot'] ? 'check' : 'ban'} /></td>
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
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['annuaire_lecture'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['annuaire_ajout'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['annuaire_modification'] ? 'check' : 'ban'} /></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['annuaire_suppression'] ? 'check' : 'ban'} /></td>
                        </tr>
                        <tr>
                            <td>Profils</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['profils_lecture'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['profils_ajout'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['profils_modification'] ? 'check' : 'ban'} /></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['profils_suppression'] ? 'check' : 'ban'} /></td>
                        </tr>
                        <tr>
                            <td>Messages généraux</td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['messages_ajout'] ? 'check' : 'ban'} /></td>
                            <td></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['messages_suppression'] ? 'check' : 'ban'} /></td>
                        </tr>
                        <tr>
                            <td>Messages mails</td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['contactMailGroupe'] ? 'check' : 'ban'} /></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>ToDoList</td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['todolist_lecture'] ? 'check' : 'ban'} /></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['todolist_modification'] ? 'check' : 'ban'} /></td>
                            <td><FontAwesomeIcon icon={HabilitationService.habilitations['todolist_perso'] ? 'check' : 'ban'} /></td>
                            <td></td>
                        </tr>
                    </tbody>
                </Table>
            </>);
        }
        else
        {
            return <LoaderInfiniteLoop />
        }
    }
};

ProfilRecapDroitsUtilisateur.propTypes = {};

export default ProfilRecapDroitsUtilisateur;
