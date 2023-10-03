import React, {useState, useEffect} from 'react';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import IconAlert from 'components/common/IconAlert';

import {Axios} from 'helpers/axios';

const LoginPwdForgottenValidate = ({token}) => {
    const [displayOption, setDisplayOption] = useState('loading');

    const checkToken = async () => {
        try {
            const response = await Axios.post('pwdReinitValidate', {
                token: token,
            });

            setDisplayOption(response.data.handleResult);

        } catch (error) {
            console.log(error);
        }
    }

    useEffect(() => {
		checkToken();
	}, [])

    switch (displayOption) {
        case 'loading':
            return <LoaderInfiniteLoop />
        break;

        case 'reussite':
            return <IconAlert variant="success">Votre mot de passe est bien réinitialisé. Un email de confirmation va vous être envoyé. Vous pouvez dors et déjà vous connecter avec vos identifiants par défaut <a href="/login">ici</a>.</IconAlert> ;
        break;

        case 'echec':
            return <IconAlert variant="warning">Votre mot de passe n'a pas pu être réinitialiser. Merci de vous rapprocher de votre administrateur pour plus d'informations.</IconAlert> ;
        break;
    
        default:
            return <IconAlert variant="danger">Erreur dans le processus de réinitialisation (compte non-trouvé, envoi d'email en erreur, ...).</IconAlert> ;
        break;
    }
};

LoginPwdForgottenValidate.propTypes = {};

export default LoginPwdForgottenValidate;