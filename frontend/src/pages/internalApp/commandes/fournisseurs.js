import React, {useEffect, useState} from 'react';
import { Card, Table, } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Link } from 'react-router-dom';
import { faHome } from '@fortawesome/free-solid-svg-icons';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

const Fournisseurs = () => {
    const [readyToDisplay, setReadyToDisplay] = useState(false);
    const [fournisseurs, setFournisseurs] = useState([]);

    const initPage = async () => {
        try {
            const getConfig = await Axios.get('/fournisseurs/getFournisseurs');
            setFournisseurs(getConfig.data);
            
            setReadyToDisplay(true);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    return (<>
        <PageHeader
            title="Fournisseurs"
            className="mb-3"
        />

        <Card>
            {readyToDisplay ?
                <Table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Site</th>
                            <th>Téléphone</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {fournisseurs.map((four, i) => {return(
                            <tr>
                                <td>{four.nomFournisseur}</td>
                                <td>{four.nomFournisseur}</td>
                                <td>{four.nomFournisseur}</td>
                                <td></td>
                            </tr>
                        )})}
                    </tbody>
                </Table>
            : <LoaderInfiniteLoop />}
        </Card>
    </>);
};

Fournisseurs.propTypes = {};

export default Fournisseurs;
