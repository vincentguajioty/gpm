import React, { useContext, useEffect } from 'react';
import { Navigate, Outlet, useLocation } from 'react-router-dom';
import AppContext from 'context/Context';
import classNames from 'classnames';
import NavbarTop from 'components/navbar/top/NavbarTop';
import NavbarVertical from 'components/navbar/vertical/NavbarVertical';
import Footer from 'components/footer/Footer';
import HabilitationService from 'services/habilitationsService';
import { Alert } from 'react-bootstrap';
import ConfigurationService from 'services/configurationService';

const MainLayout = () => {
  const { hash, pathname } = useLocation();
  const isKanban = pathname.includes('kanban');
  // const isChat = pathname.includes('chat');

  const {
    config: { isFluid, navbarPosition }
  } = useContext(AppContext);

  useEffect(() => {
    setTimeout(() => {
      if (hash) {
        const id = hash.replace('#', '');
        const element = document.getElementById(id);
        if (element) {
          element.scrollIntoView({ block: 'start', behavior: 'smooth' });
        }
      }
    }, 0);
  }, []);

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [pathname]);

  if(!localStorage.getItem("token"))
  {
    return <Navigate to="/login" />
  }
  else
  {
    return (
      <div className={isFluid ? 'container-fluid' : 'container'}>
        <NavbarVertical />
        <div className={classNames('content', { 'pb-0': isKanban })}>
          <NavbarTop />
          {ConfigurationService.config['maintenance'] ?
            <Alert variant='danger'>Application en maintenance, seuls les utilisateurs habilités seront en mesure de se connecter.</Alert>
          : null}
          {HabilitationService.delegationActive && HabilitationService.delegationActive == 1 ?
            <Alert variant='warning'>Attention, vous agissez entant que {HabilitationService.habilitations.identifiant}, les actions menées via cette session restent de votre responsabilité et restent tracées en votre nom ({HabilitationService.habilitationsInitial.identifiant}).</Alert>
          : null}
          <Outlet />
          <Footer />
        </div>
      </div>
    );
  }
};

export default MainLayout;
