import React, {useState, useEffect} from 'react';
import { Link, useNavigate, } from 'react-router-dom';
import { Card, Offcanvas, Button, Form, Tab, Nav, Row, Col } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Lottie from 'lottie-react';
import lottieClos from 'components/widgets/lottie-commandeClose';
import lottieAnnule from 'components/widgets/lottie-commandeAbandonnee';


import { Axios } from 'helpers/axios';
import HabilitationService from 'services/habilitationsService';

const OneCommandeStep8Cloture = ({
    idCommande,
    commande,
    forceReadOnly,
    setPageNeedsRefresh,
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
