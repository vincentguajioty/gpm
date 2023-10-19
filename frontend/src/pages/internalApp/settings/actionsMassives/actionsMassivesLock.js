import React from 'react';
import IconButton from 'components/common/IconButton';


const ActionsMassivesLock = ({setPageNeedsRefresh}) => {
    const lockAccess = async () => {
        try {
            localStorage.removeItem('amToken');
            localStorage.removeItem('amTokenValidUntil');
            setPageNeedsRefresh(true);
        } catch (e) {
            console.log(e);
        }
    }


    return (<>
        <IconButton
            variant="outline-success"
            icon="lock"
            onClick={lockAccess}
            className='me-1'
        >
            Quitter la fonctionnalité d'actions massives
        </IconButton>
    </>);
};

ActionsMassivesLock.propTypes = {};

export default ActionsMassivesLock;
