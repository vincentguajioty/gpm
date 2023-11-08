import React from 'react';
import IconButton from 'components/common/IconButton';

const AesFournisseursLock = ({setPageNeedsRefresh}) => {
    const lockAccess = async () => {
        try {
            localStorage.removeItem('aesToken');
            localStorage.removeItem('aesTokenValidUntil');
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
            Quitter le mode de déchiffrement des données
        </IconButton>
    </>);
};

AesFournisseursLock.propTypes = {};

export default AesFournisseursLock;
