import React from 'react';
import { Row, Col } from 'react-bootstrap';
import Lottie from 'lottie-react';
import lottieClos from 'components/widgets/lottie-commandeClose';
import lottieAnnule from 'components/widgets/lottie-commandeAbandonnee';


import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const OneCommandeStep8Cloture = ({
    commande,
}) => {
    if(commande.detailsCommande.idEtat == 7)
    {
        return (<>
            <Row className='mt-3'>
                <Col className="text-center">
                    <center>
                        <div className="wizard-lottie-wrapper w-25">
                            <div className="wizard-lottie mx-auto">
                                <Lottie animationData={lottieClos} loop={true} />
                            </div>
                        </div>
                        <h4 className="mb-1 mt-3">Cette commande est close</h4>
                    </center>
                </Col>
            </Row>
        </>);
    }

    if(commande.detailsCommande.idEtat == 8)
    {
        return (<>
            <Row className='mt-3'>
                <Col className="text-center">
                    <center>
                        <div className="wizard-lottie-wrapper w-25">
                            <div className="wizard-lottie mx-auto">
                                <Lottie animationData={lottieAnnule} loop={true} />
                            </div>
                        </div>
                        <h4 className="mb-1 mt-3">Cette commande est abandonnée</h4>
                    </center>
                </Col>
            </Row>
        </>);
    }
};

OneCommandeStep8Cloture.propTypes = {};

export default OneCommandeStep8Cloture;
