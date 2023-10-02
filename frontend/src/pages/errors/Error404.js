import React from 'react';
import { Card } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';
import { faHome } from '@fortawesome/free-solid-svg-icons';

const Error404 = () => {
  return (
    <Card className="text-center">
      <Card.Body className="p-5">
        <div className="display-1 text-300 fs-error">404</div>
        <hr />
        <p className="lead mt-4 text-800 font-sans-serif fw-semi-bold">
          La page demandée n'existe pas
        </p>
        <Link className="btn btn-primary btn-sm mt-3" to="/">
          <FontAwesomeIcon icon={faHome} className="me-2" />
          Retour à l'accueil
        </Link>
      </Card.Body>
    </Card>
  );
};

export default Error404;
