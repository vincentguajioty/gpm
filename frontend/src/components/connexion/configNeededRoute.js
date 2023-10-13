import React, { useEffect, useState } from 'react';
import { Navigate, Outlet } from 'react-router-dom';

const ConfigNeededRoute = () => {
    const [isReady, setIsReady] = useState(true);

    useEffect(() => {
        if(!localStorage.getItem("config"))
        {
          setIsReady(false);
        }
        else
        {
          setIsReady(true);
        }
    }, []);
    
    return isReady ? <Outlet/> : <Navigate to="/loadingConfig" />;
};

export default ConfigNeededRoute;