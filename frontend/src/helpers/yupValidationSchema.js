import * as Yup from "yup";

const champObligatoire = "Ce champ est obligatoire";
const champMail = "Adresse email valide requise";

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