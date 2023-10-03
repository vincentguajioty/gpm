import React from 'react';
import PropTypes from 'prop-types';
import { Nav, Row, Col } from 'react-bootstrap';
import { getFlatRoutes } from 'helpers/utils';
import NavbarNavLink from './NavbarNavLink';

const NavbarDropdownApp = ({ items }) => {
  const routes = getFlatRoutes(items);

  return (
    <Row>
      <Col md={12}>
        <Nav className="flex-column">
          {routes.unTitled.map(route => (
            <NavbarNavLink key={route.name} route={route} />
          ))}
        </Nav>
      </Col>
    </Row>
  );
};

NavbarDropdownApp.propTypes = {
  items: PropTypes.arrayOf(
    PropTypes.shape({
      icon: PropTypes.oneOfType([PropTypes.string, PropTypes.array]),
      name: PropTypes.string.isRequired,
      to: PropTypes.string,
      children: PropTypes.array
    }).isRequired
  ).isRequired
};

export default NavbarDropdownApp;
