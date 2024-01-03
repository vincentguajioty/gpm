import React, {useState} from 'react';
import { Table, Button, Modal, Form, } from 'react-bootstrap';
import FalconCloseButton from 'components/common/FalconCloseButton';
import SoftBadge from 'components/common/SoftBadge';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import { Axios } from 'helpers/axios';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

const LotReferentielImport = ({
    idLot,
    idTypeLot,
    setPageNeedsRefresh,
}) => {
    const [showImportModal, setShowImportModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const [emplacements, setEmplacements] = useState([]);
    const [referentiel, setReferentiel] = useState([]);
    const [importArray, setImportArray] = useState([]);

    const handleCloseImportModal = () => {
        setShowImportModal(false);
        setLoading(false);

        setEmplacements([]);
        setReferentiel([]);
        setImportArray([]);
    };
    const handleShowImportModal = async () => {
        try {
            setShowImportModal(true);
            setLoading(true);

            let getEmplacements = await Axios.get('/select/getEmplacementsFull');
            setEmplacements(getEmplacements.data.filter(emp => emp.idLot == idLot));

            let getRef = await Axios.post('/referentiels/getOneReferentiel',{
                idTypeLot: idTypeLot,
            });
            setReferentiel(getRef.data[0]);
            
            let tempArray = [];
            for (const item of getRef.data[0].contenu)
            {
                tempArray.push({
                    idMaterielCatalogue: item.idMaterielCatalogue,
                    libelleMateriel: item.libelleMateriel,
                    idEmplacement: null,
                    quantite: item.quantiteReferentiel,
                    quantiteAlerte: item.quantiteReferentiel - 1,
                    obligatoire: item.obligatoire,
                    peremption: null,
                })
            }

            setImportArray(tempArray);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    };

    const lancerImport = async () => {
        try {
            setLoading(true);

            const response = await Axios.post('/lots/importRef',{
                idLot: idLot,
                importArray: importArray,
            });

            setPageNeedsRefresh(true);
            handleCloseImportModal();
            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }

    const updateQuantite = (index, quantite) => {
        const newState = importArray.map((item, i) => {
            if(i === index)
            {
                return {...item, quantite: quantite}
            }
            else
            {
                return item;
            }
        })
        setImportArray(newState);
    }
    const updateQuantiteAlerte = (index, quantiteAlerte) => {
        const newState = importArray.map((item, i) => {
            if(i === index)
            {
                return {...item, quantiteAlerte: quantiteAlerte}
            }
            else
            {
                return item;
            }
        })
        setImportArray(newState);
    }
    const updatePeremption = (index, peremption) => {
        const newState = importArray.map((item, i) => {
            if(i === index)
            {
                return {...item, peremption: peremption}
            }
            else
            {
                return item;
            }
        })
        setImportArray(newState);
    }
    const updateIdEmplacement = (index, idEmplacement) => {
        const newState = importArray.map((item, i) => {
            if(i === index)
            {
                return {...item, idEmplacement: idEmplacement}
            }
            else
            {
                return item;
            }
        })
        setImportArray(newState);
    }

    return (<>
        <Button size='sm' onClick={handleShowImportModal}>important le référentiel</Button>

        <Modal show={showImportModal} onHide={handleCloseImportModal} backdrop="static" keyboard={false} fullscreen>
            <Modal.Header>
                <Modal.Title>Importer le référentiel "{referentiel.libelleTypeLot ? referentiel.libelleTypeLot : null}"</Modal.Title>
                <FalconCloseButton onClick={handleCloseImportModal}/>
            </Modal.Header>
            <Modal.Body>
                {isLoading ? <LoaderInfiniteLoop/> : 
                    <Form onSubmit={lancerImport}>
                        <Table>
                            <thead>
                                <tr>
                                    <th>Obligatoire</th>
                                    <th>Matériel</th>
                                    <th>Quantité présente dans le lot</th>
                                    <th>Quantité d'alerte</th>
                                    <th>Péremption dans le lot</th>
                                    <th>Emplacement</th>
                                </tr>
                            </thead>
                            <tbody>
                                {importArray.map((ligne, i) => {return(
                                    <tr>
                                        <td>{ligne.obligatoire ? <SoftBadge>Obligatoire</SoftBadge> : <SoftBadge bg='secondary'>Facultatif</SoftBadge>}</td>
                                        <td>{ligne.libelleMateriel}</td>
                                        <td>
                                            <Form.Control size="sm" type="number" min="0" name='quantite' id='quantite' value={ligne.quantite} onChange={(e) => {updateQuantite(i, e.target.value)}}/>
                                        </td>
                                        <td>
                                            <Form.Control size="sm" type="number" min="0" name='quantiteAlerte' id='quantiteAlerte' value={ligne.quantiteAlerte} onChange={(e) => {updateQuantiteAlerte(i, e.target.value)}}/>
                                        </td>
                                        <td>
                                            <DatePicker
                                                selected={ligne.peremption}
                                                onChange={(date)=>updatePeremption(i, date)}
                                                formatWeekDay={day => day.slice(0, 3)}
                                                className='form-control'
                                                placeholderText="Choisir une date"
                                                dateFormat="dd/MM/yyyy"
                                                fixedHeight
                                                locale="fr"
                                                isClearable
                                            />
                                        </td>
                                        <td>
                                            <Form.Select size="sm" name="idEmplacement" id="idEmplacement" value={ligne.idEmplacement} onChange={(e) => {updateIdEmplacement(i, e.target.value)}}>
                                                <option key="0" value="">--- Selectionner un emplacement ---</option>
                                                {emplacements.map((item, i) => {
                                                    return (<option key={item.value} value={item.value}>{item.label}</option>);
                                                })}
                                            </Form.Select>
                                        </td>
                                    </tr>
                                )})}
                            </tbody>
                        </Table>
                        <div className="d-grid gap-2 mt-3">
                            <Button variant='primary' className='me-2 mb-1' type="submit" disabled={isLoading}>{isLoading ? 'Patientez...' : 'Importer'}</Button>
                        </div>
                    </Form>
                }
            </Modal.Body>
            <Modal.Footer>
                <Button variant="secondary" onClick={handleCloseImportModal}>
                    Annuler
                </Button>
            </Modal.Footer>
        </Modal>
    </>);
};

LotReferentielImport.propTypes = {};

export default LotReferentielImport;
