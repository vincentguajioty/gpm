import React, {useEffect, useState} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col,} from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';

import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import UserInfo from './userInfo';
import UtilisateurAccess from './utilisateurAccess';
import UtilisateurMFA from './utilisateurMFA';
import UtilisateurCnilDelete from './utilisateurCnilDelete';
import ProfilRecapDroitsUtilisateurBox from '../profils/profilRecapDroitsUtilisateurBox';

import { Axios } from 'helpers/axios';

const UtilisateurDetails = () => {
    let {idPersonne} = useParams();

    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [personne, setPersonne] = useState([]);
    
    const initPage = async () => {
        try {
            const getData = await Axios.post('/settingsUtilisateurs/getOneUser',{
                idPersonne: idPersonne
            });
            setPersonne(getData.data[0]);  
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage()
    }, [])
    useEffect(() => {
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            initPage();
        }
    }, [pageNeedsRefresh])

    if(readyToDisplay)
    {
        return (<>
            <PageHeader
                preTitle="Attention - Zone de paramétrage"
                title={personne.identifiant}
                className="mb-3"
            />

        <Row>
			<Col md={8}>
				<UserInfo idPersonne={idPersonne} pageNeedsRefresh={pageNeedsRefresh} />
			</Col>
			<Col md={4}>
                <UtilisateurAccess personne={personne} setPageNeedsRefresh={setPageNeedsRefresh} />
                <UtilisateurMFA personne={personne} setPageNeedsRefresh={setPageNeedsRefresh} />
                <UtilisateurCnilDelete personne={personne} setPageNeedsRefresh={setPageNeedsRefresh} />
			</Col>
			<Col md={12}>
				<ProfilRecapDroitsUtilisateurBox idPersonne={idPersonne} pageNeedsRefresh={pageNeedsRefresh} />
			</Col>
		</Row>
            
        </>);
    }
    else
    {
        return(<LoaderInfiniteLoop/>)
    }
};

UtilisateurDetails.propTypes = {};

export default UtilisateurDetails;
