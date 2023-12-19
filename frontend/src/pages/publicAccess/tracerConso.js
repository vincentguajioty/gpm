import React, {useState} from 'react';

import GererConsommation from './tracerConso/gererConsommation';
import ChoixConsommation from './tracerConso/choixConsommation';

const TracerConso = () => {
    const [idConsommation, setIdConsommation] = useState();
    
    if(idConsommation && idConsommation > 0)
    {
        return(
            <GererConsommation
                idConsommation={idConsommation}
            />
        );
    }
    else
    {
        return(
            <ChoixConsommation
                setIdConsommation={setIdConsommation}
            />
        );
    }
};

TracerConso.propTypes = {};

export default TracerConso;
