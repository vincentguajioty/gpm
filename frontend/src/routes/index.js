import React from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';
import MainLayout from '../layouts/MainLayout';
import ErrorLayout from '../layouts/ErrorLayout';

import ProtectedRoutes from '../components/connexion/protectedRoutes';
import Error404 from '../pages/errors/Error404';
import Error500 from '../pages/errors/Error500';

const FalconRoutes = () => {
  return (
    <Routes>

      <Route element={<ErrorLayout />}>
        <Route path="errors/404" element={<Error404 />} />
        <Route path="errors/500" element={<Error500 />} />
      </Route>
      <Route path="*" element={<Navigate to="/errors/404" replace />} />

    </Routes>
  );
};

export default FalconRoutes;
