import React from 'react';
import PropTypes from 'prop-types';
import { Button, Col, Row } from 'react-bootstrap';
import Lottie from 'lottie-react';
import celebration from 'components/widgets/lottie-celebration';

const Success = ({ handleCloseModal }) => {
    const emptyData = () => {
        handleCloseModal()
    };

    return (
        <>
            <Row>
                <Col className="text-center">
                <div className="wizard-lottie-wrapper">
                    <div className="wizard-lottie mx-auto">
                    <Lottie animationData={celebration} loop={true} />
                    </div>
                </div>
                <h4 className="mb-1">Transfert effectué !</h4>
                <Button color="primary" className="px-5 my-3" onClick={emptyData}>
                    Fermer
                </Button>
                </Col>
            </Row>
        </>
    );
};

Success.propTypes = {
    handleCloseModal: PropTypes.func.isRequired
};

export default Success;
