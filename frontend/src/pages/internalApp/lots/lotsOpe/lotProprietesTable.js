import React from 'react';
import { Table } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

const LotProprietesTable = ({lot, setPageNeedsRefresh}) => {

    return (
        <Table className="fs--1 mt-3" size='sm' responsive>
            <tbody>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Libellé</td>
                    <td>{lot.lot.libelleLot}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Etat</td>
                    <td>{lot.lot.libelleLotsEtat}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Référentiel</td>
                    <td><SoftBadge bg={lot.lot.alerteConfRef == 1 ? 'danger' : lot.lot.alerteConfRef == 0 ? 'success' : 'secondary'}>{lot.lot.libelleTypeLot != null ? lot.lot.libelleTypeLot : 'N/A'}</SoftBadge></td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Nombre de sacs</td>
                    <td>{lot.sacs.length}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Quantiés de matériels</td>
                    <td>
                        {lot.lot.materielsOK > 0 ? <SoftBadge className='me-1' bg='success'>{lot.lot.materielsOK}</SoftBadge> : null }
                        {lot.lot.materielsLimites > 0 ? <SoftBadge className='me-1' bg='warning'>{lot.lot.materielsLimites}</SoftBadge> : null }
                        {lot.lot.materielsAlerte > 0 ? <SoftBadge className='me-1' bg='danger'>{lot.lot.materielsAlerte}</SoftBadge> : null }
                    </td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Personne référente</td>
                    <td>{lot.lot.identifiant}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Lieu de stockage</td>
                    <td>{lot.lot.libelleLieu}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Véhicule d'affectation</td>
                    <td>{lot.lot.libelleVehicule}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Dernier inventaire</td>
                    <td>{lot.lot.dateDernierInventaire != null ? moment(lot.lot.dateDernierInventaire).format('DD/MM/YYYY') : null}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Fréquence d'inventaire</td>
                    <td>{lot.lot.frequenceInventaire} jours</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Prochain inventaire</td>
                    <td><SoftBadge bg={lot.lot.prochainInventaire != null ? (new Date(lot.lot.prochainInventaire) < new Date() ? 'danger' : 'success') : 'secondary'}>{lot.lot.prochainInventaire != null ? moment(lot.lot.prochainInventaire).format('DD/MM/YYYY') : 'N/A'}</SoftBadge></td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Notifications</td>
                    <td>{lot.lot.notifiationEnabled == true ? <FontAwesomeIcon icon='bell' /> : <FontAwesomeIcon icon='bell-slash'/>}</td>
                </tr>
                <tr>
                    <td className="bg-100" style={{ width: '30%' }}>Alertes de bénévoles en cours</td>
                    <td>{lot.lot.nbAlertesEnCours > 0 ? <SoftBadge>{lot.lot.nbAlertesEnCours}</SoftBadge> : null}</td>
                </tr>
            </tbody>
        </Table>
    );
};

LotProprietesTable.propTypes = {};

export default LotProprietesTable;
