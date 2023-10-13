import React, {useEffect, useState} from 'react';
import { Row, Col, Table } from 'react-bootstrap';
import PageHeader from 'components/common/PageHeader';

import HabilitationService from 'services/habilitationsService';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';
import SoftBadge from 'components/common/SoftBadge';

const ReferentielContenuTableau = ({contenu}) => {

    return (
        <Table responsive size='sm'>
            <thead>
                <tr>
                    <th scope="col">Matériel</th>
                    <th scope="col">Quantité</th>
                    <th scope="col">Obligation</th>
                    <th scope="col">Catégorie</th>
                    <th scope="col">Commentaires</th>
                </tr>
            </thead>
            <tbody>
                {contenu.map((content, i) => {return(
                    <tr>
                        <td>{content.libelleMateriel}</td>
                        <td>{content.quantiteReferentiel}</td>
                        <td>{content.obligatoire ? <SoftBadge bg="info">Obligatoire</SoftBadge> : <SoftBadge bg="success">Facultatif</SoftBadge>}</td>
                        <td>{content.libelleCategorie}</td>
                        <td>{content.commentairesReferentiel}</td>
                    </tr>
                )})}
            </tbody>
        </Table>
    );
};

ReferentielContenuTableau.propTypes = {};

export default ReferentielContenuTableau;
