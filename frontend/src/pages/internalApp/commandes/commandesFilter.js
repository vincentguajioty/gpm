import React, {useState, useEffect} from 'react';
import { Form, Accordion } from 'react-bootstrap';

const CommandesFilter = ({
    commandesArray,
    setCommandesArrayFiltered,
}) => {

    const [displayEtat1, setDisplayEtat1] = useState(true);
    const [displayEtat2, setDisplayEtat2] = useState(true);
    const [displayEtat3, setDisplayEtat3] = useState(true);
    const [displayEtat4, setDisplayEtat4] = useState(true);
    const [displayEtat5, setDisplayEtat5] = useState(true);
    const [displayEtat6, setDisplayEtat6] = useState(true);
    const [displayEtat7, setDisplayEtat7] = useState(false);
    const [displayEtat8, setDisplayEtat8] = useState(false);

    useEffect(()=>{
        let tempArray = [];
        
        if(displayEtat1)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 1))
            {
                tempArray.push(cmd)
            }
        }

        if(displayEtat2)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 2))
            {
                tempArray.push(cmd)
            }
        }

        if(displayEtat3)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 3))
            {
                tempArray.push(cmd)
            }
        }

        if(displayEtat4)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 4))
            {
                tempArray.push(cmd)
            }
        }

        if(displayEtat5)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 5))
            {
                tempArray.push(cmd)
            }
        }

        if(displayEtat6)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 6))
            {
                tempArray.push(cmd)
            }
        }
        if(displayEtat7)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 7))
            {
                tempArray.push(cmd)
            }
        }

        if(displayEtat8)
        {
            for(const cmd of commandesArray.filter(cmd => cmd.idEtat == 8))
            {
                tempArray.push(cmd)
            }
        }

        setCommandesArrayFiltered(tempArray);
    },[
        commandesArray,
        displayEtat1,
        displayEtat2,
        displayEtat3,
        displayEtat4,
        displayEtat5,
        displayEtat6,
        displayEtat7,
        displayEtat8,
    ])
    
    return (<>
        <Form.Check 
            id="displayEtat1"
            name="displayEtat1"
            checked={displayEtat1}
            onClick={()=>{setDisplayEtat1(!displayEtat1)}}
            type='switch'
            label="Nouveau"
        />
        <Form.Check 
            id="displayEtat2"
            name="displayEtat2"
            checked={displayEtat2}
            onClick={()=>{setDisplayEtat2(!displayEtat2)}}
            type='switch'
            label="Attente de validation"
        />
        <Form.Check 
            id="displayEtat3"
            name="displayEtat3"
            checked={displayEtat3}
            onClick={()=>{setDisplayEtat3(!displayEtat3)}}
            type='switch'
            label="Validation OK"
        />
        <Form.Check 
            id="displayEtat4"
            name="displayEtat4"
            checked={displayEtat4}
            onClick={()=>{setDisplayEtat4(!displayEtat4)}}
            type='switch'
            label="Attente de livraison"
        />
        <Form.Check 
            id="displayEtat5"
            name="displayEtat5"
            checked={displayEtat5}
            onClick={()=>{setDisplayEtat5(!displayEtat5)}}
            type='switch'
            label="Livraison OK"
        />
        <Form.Check 
            id="displayEtat6"
            name="displayEtat6"
            checked={displayEtat6}
            onClick={()=>{setDisplayEtat6(!displayEtat6)}}
            type='switch'
            label="SAV"
        />
        <Form.Check 
            id="displayEtat7"
            name="displayEtat7"
            checked={displayEtat7}
            onClick={()=>{setDisplayEtat7(!displayEtat7)}}
            type='switch'
            label="Clos"
        />
        <Form.Check 
            id="displayEtat8"
            name="displayEtat8"
            checked={displayEtat8}
            onClick={()=>{setDisplayEtat8(!displayEtat8)}}
            type='switch'
            label="Abandonnée"
        />
    </>);
};

CommandesFilter.propTypes = {};

export default CommandesFilter;
