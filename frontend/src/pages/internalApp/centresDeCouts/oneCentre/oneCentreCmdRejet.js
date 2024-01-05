import React, {useState, useEffect} from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Tabs, Tab, Modal, Button, Offcanvas, Form, FloatingLabel, Row, Col, Accordion } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import GPMtable from 'components/gpmTable/gpmTable';
import moment from 'moment-timezone';
import IconButton from 'components/common/IconButton';
import Select from 'react-select';
import SoftBadge from 'components/common/SoftBadge';

import { Axios } from 'helpers/axios';

const OneCentreCommandesRefusees = ({
    idCentreDeCout,
    commandes,
    setPageNeedsRefresh,
    tabInReadOnly,
}) => {
    const colonnes = [
        {
            accessor: 'nomCommande',
            Header: 'Titre',
        },
        {
            accessor: 'nomFournisseur',
            Header: 'Fournisseur',
        },
        {
            accessor: 'dates',
            Header: 'Dates',
            Cell: ({ value, row }) => {
				return(<>
                    {row.original.dateCreation ? <><SoftBadge className='mb-1'>Crée le {moment(row.original.dateCreation).format('DD/MM/YYYY')}</SoftBadge><br/></> : null}
                    {row.original.dateValidation ? <><SoftBadge className='mb-1'>Validée le {moment(row.original.dateValidation).format('DD/MM/YYYY')}</SoftBadge><br/></> : null}
                    {row.original.datePassage ? <SoftBadge className='mb-1'>Passée le {moment(row.original.datePassage).format('DD/MM/YYYY')}</SoftBadge> : null}
                </>);
			},
        },
        {
            accessor: 'montantTotal',
            Header: 'Montant',
            Cell: ({ value, row }) => {
				return(value + ' €');
			},
        },
        {
            accessor: 'libelleEtat',
            Header: 'Etat',
        },
        {
            accessor: 'affectees',
            Header: 'Gerants',
            Cell: ({ value, row }) => {
                return(<>
                    {value.map((affecte, i)=>{
                        return(<><SoftBadge bg='info' className='me-1 mb-1'>{affecte.label}</SoftBadge><br/></>)
                    })}
                </>);
			},
        },
        {
            accessor: 'actions',
            Header: 'Actions',
            Cell: ({ value, row }) => {
                if(!tabInReadOnly)
                {
                    return(<>
                        <IconButton
                            className='me-1 mb-1'
                            size='sm'
                            variant='outline-warning'
                            icon='recycle'
                            disabled={isLoading}
                            onClick={()=>{recyclerCmd(row.original.idCommande)}}
                        >Recycler la commande</IconButton>
                    </>);
                }
			},
        },
    ];

    const [isLoading, setLoading] = useState(false);

    const recyclerCmd = async (idCommande) => {
        try {
            setLoading(true);
            
            await Axios.post('/centresCouts/recyclerCommande',{
                idCommande: idCommande,
                idCentreDeCout: idCentreDeCout,
            });

            setPageNeedsRefresh(true);
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    return(<>
        <GPMtable
            columns={colonnes}
            data={commandes}
            topButtonShow={false}
        />
    </>);
};

OneCentreCommandesRefusees.propTypes = {};

export default OneCentreCommandesRefusees;