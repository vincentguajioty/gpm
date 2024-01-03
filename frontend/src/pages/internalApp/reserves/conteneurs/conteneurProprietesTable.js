import React from 'react';
import { Table } from 'react-bootstrap';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

const ConteneurProprietesTable = ({conteneur, setPageNeedsRefresh}) => {
    return (
        <Table className="fs--1 mt-3" size='sm' responsive>
            <tbody>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Libellé</td>
                    <td>{conteneur.conteneur.libelleConteneur}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Quantiés de matériels</td>
                    <td>
                        {conteneur.conteneur.materielsOK > 0 ? <SoftBadge className='me-1' bg='success'>{conteneur.conteneur.materielsOK}</SoftBadge> : null }
                        {conteneur.conteneur.materielsLimites > 0 ? <SoftBadge className='me-1' bg='warning'>{conteneur.conteneur.materielsLimites}</SoftBadge> : null }
                        {conteneur.conteneur.materielsAlerte > 0 ? <SoftBadge className='me-1' bg='danger'>{conteneur.conteneur.materielsAlerte}</SoftBadge> : null }
                    </td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Lieu de stockage</td>
                    <td>{conteneur.conteneur.libelleLieu}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Dernier inventaire</td>
                    <td>{conteneur.conteneur.dateDernierInventaire != null ? moment(conteneur.conteneur.dateDernierInventaire).format('DD/MM/YYYY') : null}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Fréquence d'inventaire</td>
                    <td>{conteneur.conteneur.frequenceInventaire} jours</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Prochain inventaire</td>
                    <td><SoftBadge bg={conteneur.conteneur.prochainInventaire != null ? (new Date(conteneur.conteneur.prochainInventaire) < new Date() ? 'danger' : 'success') : 'secondary'}>{conteneur.conteneur.prochainInventaire != null ? moment(conteneur.conteneur.prochainInventaire).format('DD/MM/YYYY') : 'N/A'}</SoftBadge></td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Accessible aux bénévoles pour la consommation</td>
                    <td><SoftBadge bg={conteneur.conteneur.dispoBenevoles ? 'warning' : 'success'}>{conteneur.conteneur.dispoBenevoles ? 'Accessible' : 'Verrouillé'}</SoftBadge></td>
                </tr>
            </tbody>
        </Table>
    );
};

ConteneurProprietesTable.propTypes = {};

export default ConteneurProprietesTable;
