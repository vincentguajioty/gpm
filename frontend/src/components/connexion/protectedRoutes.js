import React, { useEffect, useState } from 'react';
import { Navigate, Outlet } from 'react-router-dom';

import HabilitationService from 'services/habilitationsService';

const ProtectedRoutes = () => {
    const [isAuth, setIsAuth] = useState(true);

    useEffect(() => {
        if(!HabilitationService.token)
        {
          setIsAuth(false);
        }
        else
        {
          setIsAuth(true);
        }
    }, []);
    
    return isAuth ? <Outlet/> : <Navigate to="/" />;
};

export default ProtectedRoutes;