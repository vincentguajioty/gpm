import React from 'react';
import { Col, Row } from 'react-bootstrap';
import { version } from 'config';

const Footer = () => (
  <footer className="footer">
    <Row className="justify-content-between text-center fs--1 mt-4 mb-3">
      <Col sm="auto">
        <p className="mb-0 text-600">v{version}</p>
      </Col>
    </Row>
  </footer>
);

export default Footer;
