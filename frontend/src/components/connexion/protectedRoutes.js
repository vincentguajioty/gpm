import React, { useEffect, useState } from 'react';
import { Navigate, Outlet } from 'react-router-dom';

const ProtectedRoutes = () => {
    const [isAuth, setIsAuth] = useState(true);

    useEffect(() => {
        if(!localStorage.getItem("token"))
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