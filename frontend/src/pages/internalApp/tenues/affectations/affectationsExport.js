import React, { useState } from 'react';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';

const AffectationExport = ({}) => {
    const [isLoading, setLoading] = useState(false);

    const requestExport = async () => {
        try {
            setLoading(true);

            let fileRequest = await Axios.get('/tenues/exporterAffectations');

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

    return(
        <center>
            <IconButton
                icon='download'
                size = 'sm'
                variant="outline-info"
                onClick={requestExport}
                className='ms-1'
                disabled={isLoading}
            >{isLoading ? "Génération en cours" : "Télécharger un état des lieux complet"}</IconButton>
        </center>
    );
}

AffectationExport.propTypes = {};

export default AffectationExport;