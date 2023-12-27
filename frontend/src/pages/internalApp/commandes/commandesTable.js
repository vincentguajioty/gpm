import React, {useState, useEffect} from 'react';
import { Card } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';

const CommandesTable = ({
    commandesArrayFiltered
}) => {

    const colonnes = [
        {accessor: 'dateCreation'       , Header: 'Date création'},
        {accessor: 'nomCommande'        , Header: 'Nom de la commande'},
        {accessor: 'nomFournisseur'     , Header: 'Fournisseur'},
        {accessor: 'montantTotal'       , Header: 'Montant'},
        {accessor: 'libelleEtat'        , Header: 'Etat'},
        {accessor: 'libelleCentreDecout', Header: 'Centre de couts'},
        {accessor: 'actions'            , Header: 'Actions'},
    ];

    const [lignes, setLignes] = useState([]);
    const initTable = () => {
        try {
            let tempTable  = [];
            for(const item of commandesArrayFiltered)
            {
                tempTable.push({
                    dateCreation: moment(item.dateCreation).format('DD/MM/YYYY HH:mm'),
                    nomCommande: item.nomCommande,
                    nomFournisseur: item.nomFournisseur,
                    montantTotal: item.montantTotal+' €',
                    libelleEtat: item.libelleEtat,
                    libelleCentreDecout: item.libelleCentreDecout,
                    actions: item.actions,
                });
            }
            setLignes(tempTable);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(()=>{
        initTable();
    },[commandesArrayFiltered])

    return (
        <GPMtable
            columns={colonnes}
            data={lignes}
            topButtonShow={false}
        />
    );
};

CommandesTable.propTypes = {};

export default CommandesTable;
