import ConfigurationService from 'services/configurationService';
import HabilitationService from 'services/habilitationsService';

export const dashboardRoutes = {
    label: 'Accueil',
    labelDisable: true,
    children: [
        {
            name: 'Accueil',
            to: '/home',
            icon: 'home',
            exact: false,
            active: true
        },
    ]
};

export const modulesRoutes = {
    label: 'Modules',
    labelDisable: false,
    children: []
};

export const parametresRoutes = {
    label: 'Paramètres',
    labelDisable: false,
    children: []
};

if(ConfigurationService.config)
{
    if(
        ConfigurationService.config['alertes_benevoles_lots']
        ||
        ConfigurationService.config['alertes_benevoles_vehicules']
        ||
        ConfigurationService.config['consommation_benevoles']
    )
    {
        dashboardRoutes.children.push({
            name: 'Espace public',
            to: '/',
            icon: 'arrow-left',
            exact: false,
            active: true
        })
    }
}

if(HabilitationService.habilitations)
{
    //LOTS OPERATIONNELS
    if(
        HabilitationService.habilitations['lots_lecture']
        ||
        HabilitationService.habilitations['sac_lecture']
        ||
        HabilitationService.habilitations['sac2_lecture']
        ||
        HabilitationService.habilitations['materiel_lecture']
        ||
        HabilitationService.habilitations['consommationLots_lecture']
        ||
        HabilitationService.habilitations['alertesBenevolesLots_lecture']
    )
    {
        let tempChildrens=[];
        if(HabilitationService.habilitations['lots_lecture'])
        {
            tempChildrens.push({
                name: 'Lots',
                to: '/lots',
                icon: 'hospital',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['sac_lecture'])
        {
            tempChildrens.push({
                name: 'Sacs',
                to: '/sacs',
                icon: 'briefcase-medical',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['sac2_lecture'])
        {
            tempChildrens.push({
                name: 'Emplacements',
                to: '/emplacements',
                icon: 'cubes',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['materiel_lecture'])
        {
            tempChildrens.push({
                name: 'Matériel',
                to: '/lotsMateriel',
                icon: 'stethoscope',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['consommationLots_lecture'])
        {
            tempChildrens.push({
                name: 'Rapports de consommation',
                to: '/rapportsConso',
                icon: 'heartbeat',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['alertesBenevolesLots_lecture'])
        {
            tempChildrens.push({
                name: 'Alertes des bénévoles',
                to: '/lotsAlertesBenevoles',
                icon: 'comment',
                exact: false,
                active: true
            })
        }

        modulesRoutes.children.push({
            name: 'Lots opérationnels',
            icon: 'briefcase-medical',
            active: true,
            children: tempChildrens
        })
    }

    //RESERVES
    if(HabilitationService.habilitations['reserve_lecture'])
    {
        modulesRoutes.children.push({
            name: 'Reserves',
            icon: 'archive',
            active: true,
            children: [
                {
                    name: 'Conteneurs',
                    to: '/reservesConteneurs',
                    icon: 'cube',
                    exact: false,
                    active: true
                },
                {
                    name: 'Matériels',
                    to: '/reservesMateriels',
                    icon: 'stethoscope',
                    exact: false,
                    active: true
                }
            ]
        })
    }

    //COMMANDES
    if(
        HabilitationService.habilitations['commande_lecture']
        ||
        HabilitationService.habilitations['fournisseurs_lecture']
        ||
        HabilitationService.habilitations['cout_lecture']
    )
    {
        let tempChildrens=[];
        if(HabilitationService.habilitations['commande_lecture'])
        {
            tempChildrens.push({
                name: 'Commandes',
                to: '/commandes',
                icon: 'shopping-cart',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['cout_lecture'])
        {
            tempChildrens.push({
                name: 'Centres de couts',
                to: '/couts',
                icon: 'euro-sign',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['fournisseurs_lecture'])
        {
            tempChildrens.push({
                name: 'Fournisseurs',
                to: '/fournisseurs',
                icon: 'store',
                exact: false,
                active: true
            })
        }

        modulesRoutes.children.push({
            name: 'Commandes',
            icon: 'shopping-cart',
            active: true,
            children: tempChildrens
        })
    }

    //TRANSMISSIONS
    if(
        HabilitationService.habilitations['vhf_canal_lecture']
        ||
        HabilitationService.habilitations['vhf_plan_lecture']
        ||
        HabilitationService.habilitations['vhf_equipement_lecture']
    )
    {
        let tempChildrens=[];
        if(HabilitationService.habilitations['vhf_canal_lecture'])
        {
            tempChildrens.push({
                name: 'Fréquences',
                to: '/vhfFrequences',
                icon: 'tty',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['vhf_plan_lecture'])
        {
            tempChildrens.push({
                name: 'Plans de fréquences',
                to: '/vhfPlans',
                icon: 'sort-numeric-down',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['vhf_equipement_lecture'])
        {
            tempChildrens.push({
                name: 'Equipements radio',
                to: '/vhfEquipements',
                icon: 'mobile',
                exact: false,
                active: true
            })
        }

        modulesRoutes.children.push({
            name: 'Transmissions',
            icon: 'wifi',
            active: true,
            children: tempChildrens
        })
    }
    
    //VEHICULES
    if(
        HabilitationService.habilitations['vehicules_lecture']
        ||
        HabilitationService.habilitations['desinfections_lecture']
        ||
        HabilitationService.habilitations['vehiculeHealth_lecture']
        ||
        HabilitationService.habilitations['alertesBenevolesVehicules_lecture']
    )
    {
        let tempChildrens=[];
        if(HabilitationService.habilitations['vehicules_lecture'])
        {
            tempChildrens.push({
                name: 'Véhicules',
                to: '/vehicules',
                icon: 'ambulance',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['desinfections_lecture'])
        {
            tempChildrens.push({
                name: 'Suivi des désinfections',
                to: '/vehiculesDesinfections',
                icon: 'recycle',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['vehiculeHealth_lecture'])
        {
            tempChildrens.push({
                name: 'Suivi des maintenances',
                to: '/vehiculesMaintenances',
                icon: 'wrench',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['alertesBenevolesVehicules_lecture'])
        {
            tempChildrens.push({
                name: 'Alertes des bénévoles',
                to: '/vehiculesAlertesBenevoles',
                icon: 'comment',
                exact: false,
                active: true
            })
        }

        modulesRoutes.children.push({
            name: 'Véhicules',
            icon: 'ambulance',
            active: true,
            children: tempChildrens
        })
    }

    //TENUES
    if(
        HabilitationService.habilitations['tenues_lecture']
        ||
        HabilitationService.habilitations['tenuesCatalogue_lecture']
        ||
        HabilitationService.habilitations['cautions_lecture']
    )
    {
        let tempChildrens=[];
        if(HabilitationService.habilitations['tenuesCatalogue_lecture'])
        {
            tempChildrens.push({
                name: 'Catalogue des tenues',
                to: '/tenuesCatalogue',
                icon: 'tshirt',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['tenues_lecture'])
        {
            tempChildrens.push({
                name: 'Affectations des tenues',
                to: '/tenuesAffectation',
                icon: 'street-view',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['cautions_lecture'])
        {
            tempChildrens.push({
                name: 'Cautions',
                to: '/tenuesCautions',
                icon: 'money-bill',
                exact: false,
                active: true
            })
        }

        modulesRoutes.children.push({
            name: 'Tenues',
            icon: 'tshirt',
            active: true,
            children: tempChildrens
        })
    }


    //REFERENTIELS
    if(
        HabilitationService.habilitations['typesLots_lecture']
    )
    {
        parametresRoutes.children.push({
            name: 'Référentiels',
            icon: 'landmark',
            active: true,
            children: [
                {
                    name: 'Référentiels',
                    to: '/settingsReferentiels',
                    icon: 'landmark',
                    exact: false,
                    active: true
                }
            ]
        })
    }

    //CHAMPS ET LIBELLES
    if(
        HabilitationService.habilitations['catalogue_lecture']
        ||
        HabilitationService.habilitations['codeBarre_lecture']
        ||
        HabilitationService.habilitations['categories_lecture']
        ||
        HabilitationService.habilitations['lieux_lecture']
        ||
        HabilitationService.habilitations['vehicules_types_lecture']
        ||
        HabilitationService.habilitations['etats_lecture']
        ||
        HabilitationService.habilitations['appli_conf']
    )
    {
        let tempChildrens=[];
        if(
            HabilitationService.habilitations['catalogue_lecture']
            ||
            HabilitationService.habilitations['codeBarre_lecture']
            ||
            HabilitationService.habilitations['categories_lecture']
        )
        {
            tempChildrens.push({
                name: 'Lots et réserves',
                to: '/settingsLotsReserves',
                icon: 'briefcase-medical',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['lieux_lecture'])
        {
            tempChildrens.push({
                name: 'Lieux',
                to: '/settingsLieux',
                icon: 'street-view',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['vehicules_types_lecture'])
        {
            tempChildrens.push({
                name: 'Véhicules',
                to: '/settingsVehicules',
                icon: 'ambulance',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['appli_conf'])
        {
            tempChildrens.push({
                name: 'Transmissions',
                to: '/settingsTransmissions',
                icon: 'wifi',
                exact: false,
                active: true
            })
        }

        parametresRoutes.children.push({
            name: 'Champs et libelles',
            icon: 'table',
            active: true,
            children: tempChildrens
        })
    }

    //GESTION EQUIPE
    if(
        HabilitationService.habilitations['annuaire_lecture']
        ||
        HabilitationService.habilitations['profils_lecture']
        ||
        HabilitationService.habilitations['messages_ajout']
        ||
        HabilitationService.habilitations['messages_suppression']
        ||
        HabilitationService.habilitations['todolist_lecture']
        ||
        HabilitationService.habilitations['contactMailGroupe']
    )
    {
        let tempChildrens=[];
        if(HabilitationService.habilitations['annuaire_lecture'])
        {
            tempChildrens.push({
                name: 'Utilisateurs',
                to: '/teamUtilisateurs',
                icon: 'user',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['profils_lecture'])
        {
            tempChildrens.push({
                name: 'Profils',
                to: '/teamProfils',
                icon: 'users',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['messages_ajout'] || HabilitationService.habilitations['messages_suppression'])
        {
            tempChildrens.push({
                name: 'Messages généraux',
                to: '/teamMessages',
                icon: 'bullhorn',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['contactMailGroupe'])
        {
            tempChildrens.push({
                name: 'Envoyer un mail',
                to: '/teamMail',
                icon: 'mail-bulk',
                exact: false,
                active: true
            })
        }
        if(HabilitationService.habilitations['todolist_lecture'])
        {
            tempChildrens.push({
                name: 'ToDoList',
                to: '/teamToDoList',
                icon: 'check-square',
                exact: false,
                active: true
            })
        }

        parametresRoutes.children.push({
            name: 'Gestion d\'équipe',
            icon: 'users',
            active: true,
            children: tempChildrens
        })
    }

    //AUTRES
    let tempChildrens=[];
    if(HabilitationService.habilitations['appli_conf'])
    {
        tempChildrens.push({
            name: 'Configuration générale',
            to: '/settingsGeneraux',
            icon: 'wrench',
            exact: false,
            active: true
        })
    }
    if(HabilitationService.habilitations['actionsMassives'])
    {
        tempChildrens.push({
            name: 'Actions massives',
            to: '/settingsActionsMassives',
            icon: 'minus-circle',
            exact: false,
            active: true
        })
    }
    tempChildrens.push({
        name: 'Manuel et aide',
        to: '/settingsAide',
        icon: 'question-circle',
        exact: false,
        active: true
    })
    tempChildrens.push({
        name: 'A propos',
        to: '/settingsFAQ',
        icon: 'info',
        exact: false,
        active: true
    })

    parametresRoutes.children.push({
        name: 'Autres',
        icon: 'wrench',
        active: true,
        children: tempChildrens
    })
}

export default [
    dashboardRoutes,
    modulesRoutes,
    parametresRoutes,
];
