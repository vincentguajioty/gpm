import React from 'react';
import { Card } from 'react-bootstrap';

const Error500 = () => (
  <Card className="text-center h-100">
    <Card.Body className="p-5">
      <div className="display-1 text-300 fs-error">500</div>
      <hr />
      <p className="lead mt-4 text-800 font-sans-serif fw-semi-bold">
        Oups, erreur technique !
      </p>
    </Card.Body>
  </Card>
);

export default Error500;
