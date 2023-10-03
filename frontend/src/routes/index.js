import React from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';
import AuthSimpleLayout from '../layouts/AuthSimpleLayout';
import PublicLayout from '../layouts/PublicLayout';
import MainLayout from '../layouts/MainLayout';
import ErrorLayout from '../layouts/ErrorLayout';

import ProtectedRoutes from '../components/connexion/protectedRoutes';

import Error404 from 'pages/errors/Error404';
import Error500 from 'pages/errors/Error500';

import Landing from 'pages/landing/landing';
import Login from 'pages/home/login';
import Logout from 'pages/home/logout';

import Home from 'pages/home/home';

const FalconRoutes = () => {
  return (
    <Routes>
      <Route element={<PublicLayout />}>
        <Route path="/" element={<Landing />} />
      </Route>

      <Route element={<AuthSimpleLayout />}>
        <Route path="/login" element={<Login />} />
        <Route path="/logout" element={<Logout />} />
      </Route>

      <Route element={<ProtectedRoutes />}>
        <Route element={<MainLayout />}>
          <Route path="/home" element={<Home />} />
        </Route>
      </Route>

      <Route element={<ErrorLayout />}>
        <Route path="errors/404" element={<Error404 />} />
        <Route path="errors/500" element={<Error500 />} />
      </Route>

      {/* <Navigate to="/errors/404" /> */}
      <Route path="*" element={<Navigate to="/errors/404" replace />} />
    </Routes>
  );
};

export default FalconRoutes;
