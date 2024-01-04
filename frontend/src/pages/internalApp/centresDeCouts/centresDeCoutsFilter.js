import React, {useState, useEffect} from 'react';
import { Form, } from 'react-bootstrap';

const CentresDeCoursFilter = ({
    centresArray,
    setCentresArrayFiltered,
}) => {

    const [displayEnCours, setDisplayEnCours] = useState(true);
    const [displayFutur, setDisplayFutur] = useState(false);
    const [displayClos, setDisplayClos] = useState(false);

    useEffect(()=>{
        let tempArray = [];

        if(displayEnCours)
        {
            for(const centre of centresArray.filter(centre => centre.statutOuverture == "1 - Ouvert"))
            {
                tempArray.push(centre)
            }
        }

        if(displayFutur)
        {
            for(const centre of centresArray.filter(centre => centre.statutOuverture == "2 - Futur"))
            {
                tempArray.push(centre)
            }
        }

        if(displayClos)
        {
            for(const centre of centresArray.filter(centre => centre.statutOuverture == "3 - Clos"))
            {
                tempArray.push(centre)
            }
        }

        setCentresArrayFiltered(tempArray);
    },[
        centresArray,
        displayEnCours,
        displayFutur,
        displayClos,
    ])
    
    return (<>
        <Form.Check 
            id="displayEnCours"
            name="displayEnCours"
            checked={displayEnCours}
            onClick={()=>{setDisplayEnCours(!displayEnCours)}}
            type='switch'
            label="1 - Ouvert"
        />
        <Form.Check 
            id="displayFutur"
            name="displayFutur"
            checked={displayFutur}
            onClick={()=>{setDisplayFutur(!displayFutur)}}
            type='switch'
            label="2 - Futur"
        />
        <Form.Check 
            id="displayClos"
            name="displayClos"
            checked={displayClos}
            onClick={()=>{setDisplayClos(!displayClos)}}
            type='switch'
            label="3 - Clos"
        />
    </>);
};

CentresDeCoursFilter.propTypes = {};

export default CentresDeCoursFilter;
