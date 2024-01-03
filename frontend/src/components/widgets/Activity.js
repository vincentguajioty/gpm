import React from 'react';
import { Col, Row } from 'react-bootstrap';
import { Link } from 'react-router-dom';
import classNames from 'classnames';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import moment from 'moment-timezone';

const Activity = ({
  activity: { title, text, icon, iconColor, time, status, link, },
  isLast
}) => {
  const nl2br = require('react-nl2br');
  return (
    <Row
      className={classNames(
        'g-3 recent-activity-timeline recent-activity-timeline-primary',
        {
          'pb-card': !isLast,
          'recent-activity-timeline-past': status === 'completed',
          'recent-activity-timeline-current': status === 'current'
        }
      )}
    >
      <Col xs="auto" className="ps-4 ms-2">
        <div className="ps-2">
          <div className="icon-item icon-item-sm rounded-circle bg-200 shadow-none">
            <FontAwesomeIcon icon={icon} className={iconColor} />
          </div>
        </div>
      </Col>
      <Col>
        <Row
          className={classNames('g-3', { 'border-bottom pb-card': !isLast })}
        >
          <Col>
            {link ? <Link to={link}><h6 className="text-800 mb-1">{title}</h6></Link> : <h6 className="text-800 mb-1">{title}</h6>}
            <p className="fs--1 text-600 mb-0">{nl2br(text)}</p>
          </Col>
          <Col xs="auto">
            <p className="fs--2 text-500 mb-0">{moment(time).format('DD-MM-YYYY HH:mm')}</p>
          </Col>
        </Row>
      </Col>
    </Row>
  );
};

export default Activity;