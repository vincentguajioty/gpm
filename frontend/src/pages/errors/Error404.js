import React from 'react';
import { Card } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';
import { faHome } from '@fortawesome/free-solid-svg-icons';
import Lottie from 'lottie-react';
import lottie404 from 'components/widgets/lottie-error400';

const Error404 = () => {
  return (
    <Card className="text-center">
      <Card.Body className="p-5">
        <center>
          <div className="display-1 text-300 fs-error w-75">
            <Lottie animationData={lottie404} loop={true} />
          </div>
        </center>
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
