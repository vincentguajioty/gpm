import React from 'react';
import { Link } from 'react-router-dom';
import { Dropdown } from 'react-bootstrap';
import Avatar from 'components/common/Avatar';
import HabilitationService from 'services/habilitationsService';

const ProfileDropdown = () => {
  return (
    <Dropdown navbar={true} as="li">
      <Dropdown.Toggle
        bsPrefix="toggle"
        as={Link}
        to="#!"
        className="pe-0 ps-2 nav-link"
      >
        <Avatar
          size="l" 
          name={HabilitationService.habilitations.prenomPersonne + " " + HabilitationService.habilitations.nomPersonne}
        />
      </Dropdown.Toggle>

      <Dropdown.Menu className="dropdown-menu-card  dropdown-menu-end">
        <div className="bg-white rounded-2 py-2 dark__bg-1000">
          <Dropdown.Item as={Link} to="/settings/monCompte">
            Mon compte
          </Dropdown.Item>
          <Dropdown.Item as={Link} to="/logout">
            Se déconnecter
          </Dropdown.Item>
        </div>
      </Dropdown.Menu>
    </Dropdown>
  );
};

export default ProfileDropdown;
