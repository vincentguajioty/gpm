import React, {useState, useEffect} from 'react';
import { useParams } from 'react-router-dom';
import { Row, Col, } from 'react-bootstrap';
import FalconComponentCard from 'components/common/FalconComponentCard';
import ActionButton from 'components/common/ActionButton';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

const OneCentre = () => {
    let {idCentreDeCout} = useParams();
    
    return (
        'TODO'
    );
};

OneCentre.propTypes = {};

export default OneCentre;
