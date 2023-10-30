import * as Yup from "yup";

const champObligatoire = "Ce champ est obligatoire";
const champMail = "Adresse email valide requise";
const champURL = "Adresse internet valide requise";

export const utilisateurPasswordChange = Yup.object().shape({
    oldPwd: Yup
        .string()
        .required(champObligatoire),
    newPwd: Yup
        .string()
        .required(champObligatoire),
    newPwdConfirmed: Yup
        .string()
        .required(champObligatoire)
        .oneOf([Yup.ref('newPwd'), null], 'Les mots de passe correspondent pas'),
});

export const utilisateurPasswordReinit = Yup.object().shape({
    identifiant: Yup.string()
        .required(champObligatoire),
    mailPersonne: Yup
        .string()
        .email(champMail)
        .required(champObligatoire),
});

export const utilisateurPasswordChangeWithoutCheck = Yup.object().shape({
    newPwd: Yup
        .string()
        .required(champObligatoire),
    newPwdConfirmed: Yup
        .string()
        .required(champObligatoire)
        .oneOf([Yup.ref('newPwd'), null], 'Les mots de passe correspondent pas'),
});

export const categoriesMateriels = Yup.object().shape({
    libelleCategorie: Yup.string()
        .required(champObligatoire),
});

export const etatsLots = Yup.object().shape({
    libelleLotsEtat: Yup.string()
        .required(champObligatoire),
});

export const etatsMateriels = Yup.object().shape({
    libelleMaterielsEtat: Yup.string()
        .required(champObligatoire),
});

export const vhfTypes = Yup.object().shape({
    libelleType: Yup.string()
        .required(champObligatoire),
});

export const vhfAccessoiresTypes = Yup.object().shape({
    libelleVhfAccessoireType: Yup.string()
        .required(champObligatoire),
});

export const vhfEtats = Yup.object().shape({
    libelleVhfEtat: Yup.string()
        .required(champObligatoire),
});

export const vhfTechnologies = Yup.object().shape({
    libelleTechno: Yup.string()
        .required(champObligatoire),
});

export const lieuxSettings = Yup.object().shape({
    libelleLieu: Yup.string()
        .required(champObligatoire),
});

export const etatsVehicules = Yup.object().shape({
    libelleVehiculesEtat: Yup.string()
        .required(champObligatoire)
});

export const typesVehicules = Yup.object().shape({
    libelleType: Yup.string()
        .required(champObligatoire)
});

export const typesDesinfections = Yup.object().shape({
    libelleVehiculesDesinfectionsType: Yup.string()
        .required(champObligatoire)
});

export const typesHealthVehicules = Yup.object().shape({
    libelleHealthType: Yup.string()
        .required(champObligatoire)
});

export const typesMaintenanceVehicules = Yup.object().shape({
    libelleTypeMaintenance: Yup.string()
        .required(champObligatoire)
});

export const carburants = Yup.object().shape({
    libelleCarburant: Yup.string()
        .required(champObligatoire)
});

export const catalogueForm = Yup.object().shape({
    libelleMateriel: Yup.string()
        .required(champObligatoire)
});

export const mfaInit = Yup.object().shape({
    confirmation: Yup
        .string()
        .required(champObligatoire)
        .length(6, "6 chiffres sont requis"),
});

export const referentielAddForm = Yup.object().shape({
    libelleTypeLot: Yup.string()
        .required(champObligatoire)
});

export const referentielUpdateForm = Yup.object().shape({
    libelleTypeLot: Yup.string()
        .required(champObligatoire)
});

export const userInfoForm = Yup.object().shape({
    nomPersonne: Yup
        .string()
        .required(champObligatoire),
    prenomPersonne: Yup
        .string()
        .required(champObligatoire),
});

export const configGeneraleForm = Yup.object().shape({
    appname: Yup
        .string()
        .required(champObligatoire),
    urlsite: Yup
        .string()
        .required(champObligatoire),
    mailserver: Yup
        .string()
        .email(champMail)
        .required(champObligatoire),
});

export const configCnilForm = Yup.object().shape({
    mailcnil: Yup
        .string()
        .email(champMail),
});

export const configAlertesBenevolesForm = Yup.object().shape({
});

export const configNotifCommandesForm = Yup.object().shape({
});

export const aesFournisseursUnlockModalForm = Yup.object().shape({
    aesKey: Yup
        .string()
        .required(champObligatoire),
});

export const aesFournisseursKeyUpdateModalForm = Yup.object().shape({
    aesKey: Yup
        .string()
        .required(champObligatoire),
    aesKeyConfirmed: Yup
        .string()
        .required(champObligatoire)
        .oneOf([Yup.ref('aesKey'), null], 'Les clefs ne correspondent pas'),
});

export const aesFournisseursKeyInitModalForm = Yup.object().shape({
    aesKey: Yup
        .string()
        .required(champObligatoire),
    aesKeyConfirmed: Yup
        .string()
        .required(champObligatoire)
        .oneOf([Yup.ref('aesKey'), null], 'Les clefs ne correspondent pas'),
});

export const fournisseurAddForm = Yup.object().shape({
    nomFournisseur: Yup.string()
        .required(champObligatoire)
});

export const fournisseurUpdateForm = Yup.object().shape({
    nomFournisseur: Yup.string()
        .required(champObligatoire),
    mailFournisseur: Yup
        .string()
        .email(champMail),
    siteWebFournisseur: Yup
        .string()
        .url(champURL),
});

export const fournisseurUpdateAesDataForm = Yup.object().shape({
});

export const actionsMassivesUnlockModalForm = Yup.object().shape({
    motDePasse: Yup
        .string()
        .required(champObligatoire),
});

export const profilForm = Yup.object().shape({
    libelleProfil: Yup
        .string()
        .required(champObligatoire),
});

export const userAddForm = Yup.object().shape({
    identifiant: Yup
        .string()
        .required(champObligatoire),
});

export const messagesGenerauxForm = Yup.object().shape({
    corpsMessage: Yup
        .string()
        .required(champObligatoire),
    idMessageType: Yup
        .number()
        .required(champObligatoire),
});

export const todolistEditForm = Yup.object().shape({
    titre: Yup
        .string()
        .required(champObligatoire),
});