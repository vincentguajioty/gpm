import React, {useState, useEffect} from 'react';
import { Card, Form, Button, Modal, Alert, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import moment from 'moment-timezone';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';
import FalconComponentCard from 'components/common/FalconComponentCard';
import Select from 'react-select';
import GPMtable from 'components/gpmTable/gpmTable';
import IconButton from 'components/common/IconButton';

import { Axios } from 'helpers/axios';

const GestionReappro = ({
    socket,
    idConsommation,
    consommation,
}) => {
    const [isLoading, setLoading] = useState(true);
    const [conteneurs, setConteneurs] = useState([]);

    const colonnes = [
        {accessor: 'libelleLot'          , Header: 'Lot'},
        {accessor: 'libelleMateriel'     , Header: 'Matériel'},
        {accessor: 'quantiteConsommation', Header: 'Quantité'},
        {accessor: 'reconditionnement'   , Header: 'Reconditionnement'},
    ];
    const [lignes, setLignes] = useState([]);
    const initTableau = async (conteneursFromDB) => {
        let conteneurListe = conteneursFromDB != null && conteneursFromDB.length > 0 ? conteneursFromDB : conteneurs;

        let tempTable  = [];
        for(const item of consommation.elements)
        {
            tempTable.push({
                libelleLot: item.libelleLot,
                libelleMateriel: item.libelleMateriel,
                quantiteConsommation: item.quantiteConsommation,
                reconditionnement:<>
                    <Form.Select size="sm" value={item.idConteneur || 0} onChange={(e) => {updateReconditionnementState(item.idConsommationMateriel, e.target.value || null)}}>
                        <option key="0" value="0">--- Reconditionnement impossible ---</option>
                        {conteneurListe.map((item, i) => {
                            return (<option key={item.value} value={item.value}>{item.label}</option>);
                        })}
                    </Form.Select>
                </>,
            })
        }
        setLignes(tempTable);

        setLoading(false);
    }
    useEffect(() => {
        initTableau();
    }, [consommation])

    const initPage = async () => {
        try {
            let conteneurs = await Axios.get('/select/getConteneursPublics');
            setConteneurs(conteneurs.data);

            await initTableau(conteneurs.data);
        } catch (error) {
            console.log(error)
        }
    }

    useEffect(() => {
        initPage();
    }, [])

    const updateReconditionnementState = async (idConsommationMateriel, idConteneur) => {
        try {
            await socket.emit("consommation_updateRecond",{
                idConsommation: idConsommation,
                idConsommationMateriel: idConsommationMateriel,
                idConteneur: idConteneur || null,
            });
        } catch (error) {
            console.log(error)
        }
    }

    /* FIN CONSOMMATION */
    const [showFinishModal, setShowFinishModal] = useState(false);
    const [commentaire, setCommentaire] = useState();
    const handleCloseFinishModal = () => {
        setShowFinishModal(false);
        setLoading(false);
    };
    const handleShowFinishModal = () => {
        setShowFinishModal(true);
        setCommentaire(consommation.consommation.commentairesConsommation)
    };

    const finirLaConso = async () => {
        try {
            setLoading(true);

            await socket.emit("consommation_terminerReconditionnement",{
                idConsommation: idConsommation,
                commentairesConsommation: commentaire,
            });
        } catch (e) {
            console.log(e);
        }
    }

    return(<>
        <Modal show={showFinishModal} onHide={handleCloseFinishModal} backdrop="static" size='lg' keyboard={false}>
            <Modal.Header>
                <Modal.Title>Valider la saisie</Modal.Title>
                <FalconCloseButton onClick={handleCloseFinishModal}/>
            </Modal.Header>
            <Modal.Body>
                <Alert>Vous êtes sur le point de valider la saisie qui ne pourra plus être modifiée par la suite.</Alert>
                <Form.Control size="sm" as="textarea" placeholder="Zone de commentaires" value={commentaire} rows={5} name={"commentaire"} id={"commentaire"} onChange={(e)=>{setCommentaire(e.target.value)}}/>
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseFinishModal}>
                    Annuler
                </Button>
                <Button variant='success' onClick={finirLaConso} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Valider'}</Button>
            </Modal.Footer>
        </Modal>

        <FalconComponentCard>
            <FalconComponentCard.Header
                title="Je reconditionne"
            />
            <FalconComponentCard.Body>
                <GPMtable
                    columns={colonnes}
                    data={lignes}
                    topButtonShow={false}
                />
                <div className="d-grid gap-2 mt-3">
                    <Button variant='primary' className='me-2 mb-1' onClick={handleShowFinishModal} disabled={isLoading}>{isLoading ? 'Patientez...' : 'Reconditionnement terminé, envoyer à l\'équipe logistique'}</Button>
                </div>
            </FalconComponentCard.Body>
        </FalconComponentCard>
    </>);
};

GestionReappro.propTypes = {};

export default GestionReappro;
