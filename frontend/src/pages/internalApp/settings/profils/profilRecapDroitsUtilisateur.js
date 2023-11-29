import React, {useEffect, useState} from 'react';
import { Table } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import HabilitationService from 'services/habilitationsService';
import { Axios } from 'helpers/axios';

const ProfilRecapDroitsUtilisateur = ({idPersonne, pageNeedsRefresh}) => {
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

    useEffect(()=>{
        if(pageNeedsRefresh){initComponent();}
    },[pageNeedsRefresh])

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
                    <FontAwesomeIcon icon={profil['connexion_connexion'] ? 'check' : 'ban'}/> Autorisé à se connecter
                </p>
                
                <hr/>

                <h6>Administration de l'application:</h6>
                <p>
                    <FontAwesomeIcon icon={profil['appli_conf'] ? 'check' : 'ban'}/> Modifier la configuration générale de GPM VG<br/>
                    <FontAwesomeIcon icon={profil['annuaire_mdp'] ? 'check' : 'ban'}/> Réinitialiser les mots de passe des autres utilisateurs<br/>
                    <FontAwesomeIcon icon={profil['delegation'] ? 'check' : 'ban'}/> Se connecter entant qu'autre utilisateur<br/>
                    <FontAwesomeIcon icon={profil['maintenance'] ? 'check' : 'ban'}/> Se connecter même en mode maitenance<br/>
                    <FontAwesomeIcon icon={profil['actionsMassives'] ? 'check' : 'ban'}/> Mener des actions massives directement en base<br/>
                </p>
                
                <hr/>

                <h6>Notifications journalières par mail:</h6>
                <p>
                    <FontAwesomeIcon icon={profil['notifications'] ? 'check' : 'ban'}/> Autorisé à recevoir les notifications journalières par mail<br/>
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
                            <td><FontAwesomeIcon icon={profil['lots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['lots_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['lots_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['lots_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Sacs et emplacements</td>
                            <td><FontAwesomeIcon icon={profil['sac_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['sac_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['sac_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['sac_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Matériels/Consommables</td>
                            <td><FontAwesomeIcon icon={profil['materiel_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['materiel_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['materiel_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['materiel_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['vhf_canal_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_canal_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_canal_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_canal_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Plans de fréquences</td>
                            <td><FontAwesomeIcon icon={profil['vhf_plan_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_plan_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_plan_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_plan_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Equipements de transmission</td>
                            <td><FontAwesomeIcon icon={profil['vhf_equipement_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_equipement_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_equipement_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vhf_equipement_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['vehicules_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehicules_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehicules_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehicules_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Désinfections</td>
                            <td><FontAwesomeIcon icon={profil['desinfections_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['desinfections_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['desinfections_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['desinfections_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Taches de maintenance</td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealth_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealth_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealth_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealth_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['tenues_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['tenues_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['tenues_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['tenues_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Catalogue des tenues</td>
                            <td><FontAwesomeIcon icon={profil['tenuesCatalogue_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['tenuesCatalogue_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['tenuesCatalogue_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['tenuesCatalogue_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Cautions</td>
                            <td><FontAwesomeIcon icon={profil['cautions_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['cautions_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['cautions_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['cautions_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['categories_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['categories_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['categories_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['categories_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Codes Barre</td>
                            <td><FontAwesomeIcon icon={profil['codeBarre_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['codeBarre_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['codeBarre_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['codeBarre_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Référentiels</td>
                            <td><FontAwesomeIcon icon={profil['typesLots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['typesLots_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['typesLots_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['typesLots_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Lieux</td>
                            <td><FontAwesomeIcon icon={profil['lieux_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['lieux_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['lieux_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['lieux_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Catalogue</td>
                            <td><FontAwesomeIcon icon={profil['catalogue_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['catalogue_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['catalogue_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['catalogue_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Types de véhicules</td>
                            <td><FontAwesomeIcon icon={profil['vehicules_types_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehicules_types_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehicules_types_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehicules_types_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Types de désinfections</td>
                            <td><FontAwesomeIcon icon={profil['typesDesinfections_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['typesDesinfections_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['typesDesinfections_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['typesDesinfections_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Types de taches de maintenance</td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealthType_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealthType_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealthType_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['vehiculeHealthType_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Carburants</td>
                            <td><FontAwesomeIcon icon={profil['carburants_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['carburants_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['carburants_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['carburants_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Etats</td>
                            <td><FontAwesomeIcon icon={profil['etats_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['etats_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['etats_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['etats_suppression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['alertesBenevolesLots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['alertesBenevolesLots_affectation'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['alertesBenevolesLots_affectationTier'] ? 'check' : 'ban'}/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Véhicules</td>
                            <td><FontAwesomeIcon icon={profil['alertesBenevolesVehicules_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['alertesBenevolesVehicules_affectation'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['alertesBenevolesVehicules_affectationTier'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['consommationLots_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['consommationLots_affectation'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['consommationLots_supression'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['commande_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['commande_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['commande_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['commande_valider_delegate'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['commande_etreEnCharge'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['commande_abandonner'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Fournisseurs</td>
                            <td><FontAwesomeIcon icon={profil['fournisseurs_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['fournisseurs_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['fournisseurs_modification'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['fournisseurs_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Centres de coûts</td>
                            <td><FontAwesomeIcon icon={profil['cout_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['cout_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['cout_ajout'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['cout_etreEnCharge'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['cout_supprimer'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['reserve_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['reserve_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['reserve_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['reserve_suppression'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['reserve_cmdVersReserve'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['reserve_ReserveVersLot'] ? 'check' : 'ban'}/></td>
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
                            <td><FontAwesomeIcon icon={profil['annuaire_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['annuaire_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['annuaire_modification'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['annuaire_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Profils</td>
                            <td><FontAwesomeIcon icon={profil['profils_lecture'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['profils_ajout'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['profils_modification'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['profils_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Messages généraux</td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['messages_ajout'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['messages_suppression'] ? 'check' : 'ban'}/></td>
                        </tr>
                        <tr>
                            <td>Messages mails</td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['contactMailGroupe'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>ToDoList</td>
                            <td><FontAwesomeIcon icon={profil['todolist_lecture'] ? 'check' : 'ban'}/></td>
                            <td></td>
                            <td><FontAwesomeIcon icon={profil['todolist_modification'] ? 'check' : 'ban'}/></td>
                            <td><FontAwesomeIcon icon={profil['todolist_perso'] ? 'check' : 'ban'}/></td>
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
