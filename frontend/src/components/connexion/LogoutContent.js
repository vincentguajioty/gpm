import React from 'react';
import PropTypes from 'prop-types';
import { Button } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import logoutImg from 'assets/img/icons/spot-illustrations/45.png';

const LogoutContent = ({ titleTag: TitleTag }) => {
  return (
    <>
      <img
        className="d-block mx-auto mb-4"
        src={logoutImg}
        alt="shield"
        width={100}
      />
      <TitleTag>A bientôt !</TitleTag>
      <Button
        as={Link}
        color="primary"
        size="sm"
        className="mt-3"
        to={`/`}
      >
        <FontAwesomeIcon
          icon="chevron-left"
          transform="shrink-4 down-1"
          className="me-1"
        />
        Espace public
      </Button>
    </>
  );
};

LogoutContent.propTypes = {
  titleTag: PropTypes.string
};

LogoutContent.defaultProps = {
  titleTag: 'h4'
};

export default LogoutContent;
