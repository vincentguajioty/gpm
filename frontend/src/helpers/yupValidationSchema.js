import * as Yup from "yup";

const champObligatoire = "Ce champ est obligatoire";
const champMail = "Adresse email valide requise";
const champURL = "Adresse internet valide requise";

export const utilisateurPasswordChange = Yup.object().shape({
    oldPwd: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    newPwd: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    newPwdConfirmed: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
        .oneOf([Yup.ref('newPwd'), null], 'Les mots de passe correspondent pas'),
});

export const utilisateurPasswordReinit = Yup.object().shape({
    identifiant: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    mailPersonne: Yup
        .string()
        .typeError(champObligatoire)
        .email(champMail)
        .required(champObligatoire),
});

export const utilisateurPasswordChangeWithoutCheck = Yup.object().shape({
    newPwd: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    newPwdConfirmed: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
        .oneOf([Yup.ref('newPwd'), null], 'Les mots de passe correspondent pas'),
});

export const categoriesMateriels = Yup.object().shape({
    libelleCategorie: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const etatsLots = Yup.object().shape({
    libelleLotsEtat: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const etatsMateriels = Yup.object().shape({
    libelleMaterielsEtat: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vhfTypes = Yup.object().shape({
    libelleType: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vhfAccessoiresTypes = Yup.object().shape({
    libelleVhfAccessoireType: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vhfEtats = Yup.object().shape({
    libelleVhfEtat: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vhfTechnologies = Yup.object().shape({
    libelleTechno: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const lieuxSettings = Yup.object().shape({
    libelleLieu: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const etatsVehicules = Yup.object().shape({
    libelleVehiculesEtat: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const typesVehicules = Yup.object().shape({
    libelleType: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const typesDesinfections = Yup.object().shape({
    libelleVehiculesDesinfectionsType: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const typesHealthVehicules = Yup.object().shape({
    libelleHealthType: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const typesMaintenanceVehicules = Yup.object().shape({
    libelleTypeMaintenance: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const carburants = Yup.object().shape({
    libelleCarburant: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const catalogueForm = Yup.object().shape({
    libelleMateriel: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const mfaInit = Yup.object().shape({
    confirmation: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
        .length(6, "6 chiffres sont requis"),
});

export const referentielAddForm = Yup.object().shape({
    libelleTypeLot: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const referentielUpdateForm = Yup.object().shape({
    libelleTypeLot: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const userInfoForm = Yup.object().shape({
    nomPersonne: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    prenomPersonne: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const configGeneraleForm = Yup.object().shape({
    appname: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    urlsite: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    mailserver: Yup
        .string()
        .typeError(champObligatoire)
        .email(champMail)
        .required(champObligatoire),
});

export const configCnilForm = Yup.object().shape({
    mailcnil: Yup
        .string()
        .typeError(champObligatoire)
        .email(champMail),
});

export const configAlertesBenevolesForm = Yup.object().shape({
});

export const configNotifCommandesForm = Yup.object().shape({
});

export const aesFournisseursUnlockModalForm = Yup.object().shape({
    aesKey: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const aesFournisseursKeyUpdateModalForm = Yup.object().shape({
    aesKey: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    aesKeyConfirmed: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
        .oneOf([Yup.ref('aesKey'), null], 'Les clefs ne correspondent pas'),
});

export const aesFournisseursKeyInitModalForm = Yup.object().shape({
    aesKey: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    aesKeyConfirmed: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
        .oneOf([Yup.ref('aesKey'), null], 'Les clefs ne correspondent pas'),
});

export const fournisseurAddForm = Yup.object().shape({
    nomFournisseur: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const fournisseurUpdateForm = Yup.object().shape({
    nomFournisseur: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    mailFournisseur: Yup
        .string()
        .typeError(champObligatoire)
        .email(champMail),
    siteWebFournisseur: Yup
        .string()
        .typeError(champObligatoire)
        .url(champURL),
});

export const fournisseurUpdateAesDataForm = Yup.object().shape({
});

export const actionsMassivesUnlockModalForm = Yup.object().shape({
    motDePasse: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const profilForm = Yup.object().shape({
    libelleProfil: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const userAddForm = Yup.object().shape({
    identifiant: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const messagesGenerauxForm = Yup.object().shape({
    corpsMessage: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idMessageType: Yup
        .number()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const todolistEditForm = Yup.object().shape({
    titre: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const catalogueTenuesForm = Yup.object().shape({
    libelleCatalogueTenue: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const affectationsTenuesForm = Yup.object().shape({
    idCatalogueTenue: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    dateAffectation: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idPersonne: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    personneNonGPM: Yup
        .string()
        .typeError(champObligatoire)
        .nullable(true)
        .test('isExternal', champObligatoire,
            function(value){
                if(idPersonne.value == 0)
                {
                    if(value == null || value == ''){return false;}
                }
                return true;
            }
        ),
});

export const cautionsForm = Yup.object().shape({
    dateEmissionCaution: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
    montantCaution: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    idPersonne: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    personneNonGPM: Yup
        .string()
        .typeError(champObligatoire)
        .nullable(true)
        .test('isExternal', champObligatoire,
            function(value){
                if(idPersonne.value == 0)
                {
                    if(value == null || value == ''){return false;}
                }
                return true;
            }
        ),
});

export const frequencesForm = Yup.object().shape({
    chName: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const frequencesAttachedForm = Yup.object().shape({
    nomDocCanalVHF: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const planVHFForm = Yup.object().shape({
    libellePlan: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const planAttachedForm = Yup.object().shape({
    nomDocPlanVHF: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const planCanauxForm = Yup.object().shape({
});

export const vhfEquipementsAddForm = Yup.object().shape({
    vhfIndicatif: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vhfEquipementsUpdateForm = Yup.object().shape({
    vhfIndicatif: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vhfEquipementsAttachedForm = Yup.object().shape({
    nomDocVHF: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vhfAccessoiresForm = Yup.object().shape({
    libelleVhfAccessoire: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const vehiculeAddForm = Yup.object().shape({
    libelleVehicule: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const vehiculeUpdateForm = Yup.object().shape({
    libelleVehicule: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire)
});

export const vehiculeAttachedForm = Yup.object().shape({
    nomDocVehicule: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const releveKM = Yup.object().shape({
    dateReleve: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
    releveKilometrique: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    idPersonne: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const maintenancePonctuelle = Yup.object().shape({
    dateMaintenance: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
    releveKilometrique: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    idExecutant: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    idTypeMaintenance: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const maintenanceReguliere = Yup.object().shape({
    dateHealth: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idPersonne: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const desinfectionForm = Yup.object().shape({
    dateDesinfection: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idExecutant: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    idVehiculesDesinfectionsType: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const alerteVehiculeAffectation = Yup.object().shape({
    idTraitant: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const materielsForm = Yup.object().shape({
    idMaterielCatalogue: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    idMaterielsEtat: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    quantite: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    quantiteAlerte: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
});

export const transfertReservesLotsStep = [
    Yup.object().shape({
        idReserveElement: Yup
            .number()
            .typeError(champObligatoire)
            .min(1, champObligatoire)
            .required(champObligatoire),
    }),

    Yup.object().shape({
        idReserveElement: Yup
            .number()
            .typeError(champObligatoire)
            .min(1, champObligatoire)
            .required(champObligatoire),
        qttTransfert: Yup
            .number()
            .typeError(champObligatoire)
            .min(1, champObligatoire)
            .test({
                name: 'max',
                exclusive: false,
                params: {},
                message: 'Stock insuffisant',
                test: function (value) {
                    return value <= parseInt(this.parent.quantiteReserve)
                },
            })
            .required(champObligatoire),
    }),

    Yup.object().shape({
    }),
];

export const reservesMaterielsForm = Yup.object().shape({
    idMaterielCatalogue: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    quantiteReserve: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    quantiteAlerteReserve: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
});

export const sacsFormSchema = Yup.object().shape({
    libelleSac: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const emplacementsFormSchema = Yup.object().shape({
    libelleEmplacement: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const lotsAddForm = Yup.object().shape({
    libelleLot: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idNotificationEnabled: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    idLotsEtat: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    frequenceInventaire: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    dateDernierInventaire: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const lotsDuplicateForm = Yup.object().shape({
    libelleLot: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idLot: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const lotsUpdateForm = Yup.object().shape({
    libelleLot: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idNotificationEnabled: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    idLotsEtat: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    frequenceInventaire: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    dateDernierInventaire: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const alerteLotAffectation = Yup.object().shape({
    idTraitant: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const conteneursAddForm = Yup.object().shape({
    libelleConteneur: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    frequenceInventaire: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    dateDernierInventaire: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const conteneursUpdateForm = Yup.object().shape({
    libelleConteneur: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    frequenceInventaire: Yup
        .number()
        .typeError(champObligatoire)
        .min(0, champObligatoire)
        .required(champObligatoire),
    dateDernierInventaire: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const lotsStartInventaireModal = Yup.object().shape({
    idPersonne: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    dateInventaire: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const reservesStartInventaireModal = Yup.object().shape({
    idPersonne: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    dateInventaire: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const consommationPubliqueCreation = Yup.object().shape({
    nomDeclarantConsommation: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    dateConsommation: Yup
        .date()
        .typeError(champObligatoire)
        .required(champObligatoire),
    evenementConsommation: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const consommationPubliqueAjoutMateriel = Yup.object().shape({
    idMaterielCatalogue: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    idLot: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    quantiteConsommation: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const consommationPubliqueModificationMateriel = Yup.object().shape({
    idLot: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    quantiteConsommation: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const codesBarreFormFournisseur = Yup.object().shape({
    codeBarre: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idMaterielCatalogue: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const codesBarreFormInterne = Yup.object().shape({
    idMaterielCatalogue: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
});

export const alerteBenevoleLots = Yup.object().shape({
    nomDeclarant: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idLot: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    messageAlerteLot: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    mailDeclarant: Yup
        .string()
        .typeError(champObligatoire)
        .email(champMail),
});

export const alerteBenevoleVehicules = Yup.object().shape({
    nomDeclarant: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    idVehicule: Yup
        .number()
        .typeError(champObligatoire)
        .min(1, champObligatoire)
        .required(champObligatoire),
    messageAlerteVehicule: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    mailDeclarant: Yup
        .string()
        .typeError(champObligatoire)
        .email(champMail),
});

export const contactDeveloppeur = Yup.object().shape({
    topic: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    message: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const mailEquipeForm = Yup.object().shape({
    sujet: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
    message: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});

export const commandeAddForm = Yup.object().shape({
    nomCommande: Yup
        .string()
        .typeError(champObligatoire)
        .required(champObligatoire),
});