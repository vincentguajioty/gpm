import React, { useContext, useEffect } from 'react';
import { Outlet, useLocation } from 'react-router-dom';
import AppContext from 'context/Context';
import classNames from 'classnames';
import NavbarTop from 'components/navbar/publicTop/NavbarTop';
import NavbarVertical from 'components/navbar/publicNavBar/NavbarVertical';
import Footer from 'components/footer/Footer';

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

  return (
    <div className={isFluid ? 'container-fluid' : 'container'}>
      <NavbarVertical />
      <div className={classNames('content', { 'pb-0': isKanban })}>
        <NavbarTop />
        {/*------ Main Routes ------*/}
        <Outlet />
        {!isKanban && <Footer />}
      </div>
    </div>
  );
};

export default MainLayout;
