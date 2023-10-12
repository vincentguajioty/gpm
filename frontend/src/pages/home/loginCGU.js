import React, {useState, useEffect} from 'react';
import AuthCGULayout from 'layouts/AuthCGULayout';
import Password from 'pages/internalApp/settings/utilisateurs/password';
import IconButton from 'components/common/IconButton';
import { useNavigate } from 'react-router-dom';

import { Axios } from 'helpers/axios';

const LoginCGU = () => {
    const navigate = useNavigate();
    
    const[cgu, setCgu] = useState("");
    const getCGU = async () => {
        try {
            let dbQuery = await Axios.get('/getCGU')
            setCgu(dbQuery.data[0].cnilDisclaimer)
        } catch (error) {
            console.log(error)
        }
    }
    useEffect(() => {
        getCGU();
    },[]);

    const acceptCGU = async () => {
        try {
            const accept = await Axios.post('/acceptCGU');
            if(accept.status == 201)
            {
                navigate('/home');
            }
        } catch (error) {
            console.log(error)
        }
    }
    
    const rejectCGU = () => {
        navigate('/logout');
    }

    const nl2br = require('react-nl2br');
    return (
		<AuthCGULayout>
            <p>{nl2br(cgu)}</p>
            <p>
                <IconButton
                    icon='check'
                    variant="success"
                    className="mb-1"
                    onClick={acceptCGU}
                >
                    J'accepte les CGU
                </IconButton>
                <IconButton
                    icon='eject'
                    variant="danger"
                    onClick={rejectCGU}
                >
                    Je refuse les CGU
                </IconButton>
            </p>
		</AuthCGULayout>
  );
};

export default LoginCGU;
