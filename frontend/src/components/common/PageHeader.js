import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { Card, Col, Row } from 'react-bootstrap';
import Background from './Background';
import corner4 from 'assets/img/illustrations/corner-4.png';
import createMarkup from 'helpers/createMarkup';
import QRCode from "react-qr-code"; 

const PageHeader = ({
  title,
  preTitle,
  titleTag: TitleTag,
  description,
  image,
  col,
  children,
  qrCodeURL = null,
  ...rest
}) => (
  <Card {...rest}>
    <Background
      image={image}
      className="bg-card d-none d-sm-block"
      style={{
        borderTopRightRadius: '0.375rem',
        borderBottomRightRadius: '0.375rem'
      }}
    />
    <Card.Body className="position-relative">
      <Row>
        <Col {...col}>
          {preTitle && <h6 className="text-600">{preTitle}</h6>}
          <TitleTag className="mb-0">{title}</TitleTag>
          {description && (
            <p
              className={classNames('mt-2', { 'mb-0': !children })}
              dangerouslySetInnerHTML={createMarkup(description)}
            />
          )}
          {children}
        </Col>
        {qrCodeURL != null ?
          <Col>
            <QRCode
              value={qrCodeURL}
              style={{ height: "auto", maxWidth: "20%", width: "20%"}}
            />
          </Col>
        : null}
      </Row>
    </Card.Body>
  </Card>
);

PageHeader.propTypes = {
  title: PropTypes.string.isRequired,
  preTitle: PropTypes.string,
  titleTag: PropTypes.string,
  description: PropTypes.string,
  col: PropTypes.shape(Col.propTypes),
  image: PropTypes.string,
  className: PropTypes.string,
  children: PropTypes.node
};

PageHeader.defaultProps = { col: { lg: 8 }, image: corner4, titleTag: 'h3' };

export default PageHeader;
