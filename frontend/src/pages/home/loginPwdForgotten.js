import React from 'react';
import { useParams } from 'react-router-dom';
import AuthPwdForgotten from 'layouts/AuthPwdForgotten';

import LoginPwdForgottenInit from './loginPwdForgottenInit';
import LoginPwdForgottenValidate from './loginPwdForgottenValidate';

const LoginPwdForgotten = () => {
    let {token} = useParams();

    if(token)
    {
        return (
            <>
                <AuthPwdForgotten>
                    <LoginPwdForgottenValidate token={token} />
                </AuthPwdForgotten>
            </>
        );
    }
    else
    {
        return (
            <>
                <AuthPwdForgotten>
                    <LoginPwdForgottenInit />
                </AuthPwdForgotten>
            </>
        );
    }

    
};

export default LoginPwdForgotten;
