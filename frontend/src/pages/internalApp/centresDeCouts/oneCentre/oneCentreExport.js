import React, {useState} from 'react';
import { Tab, Nav, Row, Col } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Lottie from 'lottie-react';
import lottieClos from 'components/widgets/lottie-commandeClose';
import IconButton from 'components/common/IconButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

const OneCentreExport = ({
    idCentreDeCout,
}) => {
    const [isLoading, setLoading] = useState(false);
    const [downloadGenerated, setDownloadGenerated] = useState(false);

    const requestExport = async () => {
        try {
            setLoading(true);

            let fileRequest = await Axios.post('/centresCouts/genererExport',{
                idCentreDeCout: idCentreDeCout,
            });

            let documentData = await Axios.post('getSecureFile/temp',
            {
                fileName: fileRequest.data.fileName,
            },
            {
                responseType: 'blob'
            });
            
            // create file link in browser's memory
            const href = URL.createObjectURL(documentData.data);
            
            // create "a" HTML element with href to file & click
            const link = document.createElement('a');
            link.href = href;
            link.setAttribute('download', fileRequest.data.fileName); //or any other extension
            document.body.appendChild(link);
            link.click();

            // clean up "a" element & remove ObjectURL
            document.body.removeChild(link);
            URL.revokeObjectURL(href);
            
            setDownloadGenerated(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    if(isLoading)
    {
        return <LoaderInfiniteLoop/>
    }else{
        return(<>
            {downloadGenerated ?
                <Row className='mt-3'>
                    <Col className="text-center">
                        <center>
                            <div className="wizard-lottie-wrapper w-25">
                                <div className="wizard-lottie mx-auto">
                                    <Lottie animationData={lottieClos} loop={true} />
                                </div>
                            </div>
                            <h4 className="mb-1 mt-3">Fichier généré, le téléchargement va commencer</h4>
                        </center>
                    </Col>
                </Row>
            :
                <center>
                    <IconButton
                        onClick={requestExport}
                        icon='download'
                    >Télécharger l'export des opérations</IconButton>
                </center>
            }
        </>);
    }
};

OneCentreExport.propTypes = {};

export default OneCentreExport;