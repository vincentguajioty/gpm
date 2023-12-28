import React, {useState, useEffect} from 'react';
import PageHeader from 'components/common/PageHeader';
import { Card, Tabs, Tab, Alert } from 'react-bootstrap';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import ConfigGeneraleTabAES from './tabAes';
import ConfigGeneraleTabAlertes from './tabAlertes';
import ConfigGeneraleTabCNIL from './tabCnil';
import ConfigGeneraleTabGeneral from './tabGeneral';
import ConfigGeneraleTabNotifs from './tabNotif';

import { Axios } from 'helpers/axios';

const ConfigGenerale = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    const [config, setConfig] = useState([]);

    const initPage = async () => {
        try {
            const getConfig = await Axios.get('/settingsTechniques/getConfigForAdmin');
            setConfig(getConfig.data);

            setReadyToDisplay(true);
            setIsLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=> {
        initPage();
    }, [])
    useEffect(()=> {
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            location.reload();
        }
    }, [pageNeedsRefresh])

    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Paramétrage technique"
            className="mb-3"
        />

        {readyToDisplay ? <>
            <Card>
                <Tabs defaultActiveKey="general" id="uncontrolled-tab-example">
                    <Tab eventKey="general" title="Générale" className='border-bottom border-x p-3'>
                        <ConfigGeneraleTabGeneral
                            config={config.general}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            isLoading={isLoading}
                            setIsLoading={setIsLoading}
                        />
                    </Tab>
                    <Tab eventKey="cnil" title="CNIL" className='border-bottom border-x p-3'>
                        <ConfigGeneraleTabCNIL
                            config={config.cnil}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            isLoading={isLoading}
                            setIsLoading={setIsLoading}
                        />
                    </Tab>
                    <Tab eventKey="aes" title="Cryptage AES" className='border-bottom border-x p-3'>
                        <ConfigGeneraleTabAES
                            config={config.aes}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            isLoading={isLoading}
                            setIsLoading={setIsLoading}
                        />
                    </Tab>
                    <Tab eventKey="notifcmd" title="Notifications des commandes" className='border-bottom border-x p-3'>
                        <ConfigGeneraleTabNotifs
                            config={config.notifcmd}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            isLoading={isLoading}
                            setIsLoading={setIsLoading}
                        />
                    </Tab>
                    <Tab eventKey="alertesbenevoles" title="Alertes bénévoles" className='border-bottom border-x p-3'>
                        <ConfigGeneraleTabAlertes
                            config={config.alertesbenevoles}
                            setPageNeedsRefresh={setPageNeedsRefresh}
                            isLoading={isLoading}
                            setIsLoading={setIsLoading}
                        />
                    </Tab>
                </Tabs>
            </Card>

        </>:<LoaderInfiniteLoop/>}
        
    </>);
};

ConfigGenerale.propTypes = {};

export default ConfigGenerale;
