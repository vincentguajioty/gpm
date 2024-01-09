import React, { useState, useEffect } from 'react';
import { Table, Card } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import IconButton from 'components/common/IconButton';

import ActionsMassivesService from 'services/actionsMassivesAuthService';

import { Axios } from 'helpers/axios';
import moment from 'moment-timezone';

import ActionsMassivesLock from './actionsMassivesLock';
import ActionsMassivesUnlock from './actionsMassivesUnlock';

const ActionsMassives = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [pageNeedsRefresh, setPageNeedsRefresh] = useState(false);

    const [actions, setActions] = useState([]);

    const initPage = async () => {
        try {
            if(ActionsMassivesService.amTokenValidUntil && moment(ActionsMassivesService.amTokenValidUntil) > new Date())
            {
                const getData = await Axios.post('/actionsmassives/getAvailableActions',{
                    amToken: ActionsMassivesService.amToken
                });
                setActions(getData.data);
            }
        } catch (error) {
            console.log(error)
        }
    }

    const lancerUneAction = async (id) =>{
        try {
            if(ActionsMassivesService.amTokenValidUntil && moment(ActionsMassivesService.amTokenValidUntil) > new Date())
            {
                let action = actions.filter(action => action.id == id);
                action = action[0];

                setIsLoading(true);

                const getData = await Axios.post(action.endPointBo,{
                    amToken: ActionsMassivesService.amToken
                });

                setIsLoading(false);
                setPageNeedsRefresh(true);
            }
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initPage();
    },[])

    useEffect(()=>{
        if(pageNeedsRefresh)
        {
            setPageNeedsRefresh(false);
            location.reload();
        }
    },[pageNeedsRefresh])

    return (<>
        <PageHeader
            preTitle="Attention - Zone de paramétrage"
            title="Actions massives en base de données"
            className="mb-3"
        />

        {ActionsMassivesService.amTokenValidUntil && moment(ActionsMassivesService.amTokenValidUntil) > new Date() ?
            <>
                <center><ActionsMassivesLock setPageNeedsRefresh={setPageNeedsRefresh} /></center>
                <Card className='mt-3'>
                    <Card.Body>
                        <Table>
                            <thead>
                                <tr>
            						<th>Catégorie</th>
            						<th>Action</th>
            						<th>Estimation d'impact</th>
            						<th>Lancement</th>
            					</tr>
                            </thead>
                            <tbody>
                                {actions.map((action, i) => {return(
                                    <tr>
                                        <td>{action.categorie}</td>
                                        <td>{action.description}</td>
                                        <td>{action.impact}</td>
                                        <td>
                                            <IconButton
                                                variant='warning'
                                                icon='exclamation-triangle'
                                                size='sm'
                                                onClick={()=>{lancerUneAction(action.id);}}
                                                disabled={isLoading}
                                            >
                                                {isLoading ? 'Chargement' : "Lancer l'action"}
                                            </IconButton>
                                        </td>
                                    </tr>
                                )})}
                            </tbody>
                        </Table>
                    </Card.Body>
                </Card>
            </>
        :
            <center><ActionsMassivesUnlock setPageNeedsRefresh={setPageNeedsRefresh} isLoading={isLoading} setIsLoading={setIsLoading}/></center>
        }
    </>);
};

ActionsMassives.propTypes = {};

export default ActionsMassives;
