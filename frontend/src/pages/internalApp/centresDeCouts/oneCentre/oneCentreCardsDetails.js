import React from 'react';
import PropTypes from 'prop-types';
import { Col, Row } from 'react-bootstrap';

const OneCentreCardsDetails = ({
    data
}) => {
    return (
        <Row className="g-3 font-sans-serif">
            {data.map((section, i)=>{
                return(
                    <div className="rounded-3 border p-3">
                        <div className="d-flex align-items-center mb-4">
                            <span className={`dot bg-${section.color} bg-opacity-${section.colorOpacity}`} />
                            <h6 className="mb-0 fw-bold">{section.sectionName}</h6>
                        </div>
                        <ul className="list-unstyled mb-0">
                            {section.sousSections.map((sousSection, index) => (
                                <li
                                    key={sousSection.sousSectionID}
                                    className={`d-flex align-items-center fs--2 fw-medium pt-1 ${
                                        index !== section.sousSections - 1 && 'mb-3'
                                    }`}
                                >
                                    <span
                                        className={`dot bg-${sousSection.color} bg-opacity-${sousSection.colorOpacity}`}
                                    />
                                    <p className="lh-sm mb-0 text-700">
                                        {sousSection.sousSectionName}
                                        <span className="text-900 ps-2">{sousSection.montant} €</span>
                                    </p>
                                </li>
                            ))}
                        </ul>
                    </div>
                )
            })}
        </Row>
    );
};

OneCentreCardsDetails.propTypes = {};

export default OneCentreCardsDetails;
