import React, { useContext } from 'react';
import NavbarDropdown from './NavbarDropdown';
// import {
//   dashboardRoutes,
//   appRoutes,
//   pagesRoutes,
//   modulesRoutes,
//   documentationRoutes
// } from 'routes/routes';
import NavbarDropdownApp from './NavbarDropdownApp';
import AppContext from 'context/Context';

const NavbarTopDropDownMenus = () => {
  const {
    config: { navbarCollapsed, showBurgerMenu },
    setConfig
  } = useContext(AppContext);

  const handleDropdownItemClick = () => {
    if (navbarCollapsed) {
      setConfig('navbarCollapsed', !navbarCollapsed);
    }
    if (showBurgerMenu) {
      setConfig('showBurgerMenu', !showBurgerMenu);
    }
  };
  return (
    <>
      
    </>
  );
};

export default NavbarTopDropDownMenus;
