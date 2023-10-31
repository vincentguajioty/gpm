import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import { Card, Col, Row } from 'react-bootstrap';
import Background from 'components/common/Background';
import Flex from 'components/common/Flex';
import Section from 'components/common/Section';

import bgShape from 'assets/img/illustrations/bg-shape.png';
import shape1 from 'assets/img/illustrations/shape-1.png';
import halfCircle from 'assets/img/illustrations/half-circle.png';

const AuthPwdChangeLayout = ({ leftSideContent, children, footer = true }) => {
  return (
    <Section fluid className="py-0">
      <Row className="g-0 min-vh-100 flex-center">
        <Col lg={8} xxl={5} className="py-3 position-relative">
          <img
            className="bg-auth-circle-shape"
            src={bgShape}
            alt=""
            width="250"
          />
          <img
            className="bg-auth-circle-shape-2"
            src={shape1}
            alt=""
            width="150"
          />
          <Card className="overflow-hidden z-index-1">
            <Card.Body className="p-0">
              <Row className="h-100 g-0">
                <Col md={5} className="text-white text-center bg-card-gradient">
                  <div className="position-relative p-4 pt-md-5 pb-md-7">
                    <Background
                      image={halfCircle}
                      className="bg-auth-card-shape"
                    />
                    <div className="z-index-1 position-relative light">
                      <Link
                        className="link-light mb-4 font-sans-serif fw-bolder fs-4 d-inline-block"
                        to="/"
                      >
                        {window.__ENV__.APP_NAME}
                      </Link>
                      <p className="opacity-75 text-white">
                        Il s'agit de votre première connexion ? Ou votre mot de passe a été réinitialisé ? Merci d'en définir un nouveau !
                      </p>
                    </div>
                  </div>
                  <div className="mt-3 mb-4 mt-md-4 mb-md-5 light">
                    {leftSideContent}
                  </div>
                </Col>
                <Col
                  md={7}
                  as={Flex}
                  alignItems="center"
                  justifyContent="center"
                >
                  <div className="p-4 p-md-5 flex-grow-1">{children}</div>
                </Col>
              </Row>
            </Card.Body>
          </Card>
        </Col>
      </Row>
    </Section>
  );
};
AuthPwdChangeLayout.propTypes = {
  leftSideContent: PropTypes.node,
  children: PropTypes.node.isRequired,
  footer: PropTypes.bool
};

export default AuthPwdChangeLayout;
