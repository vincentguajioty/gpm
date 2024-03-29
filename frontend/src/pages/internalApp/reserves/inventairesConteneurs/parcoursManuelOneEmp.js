import React, {useEffect, useState} from 'react';
import { Form, FloatingLabel, Table, InputGroup, Dropdown, DropdownButton } from 'react-bootstrap';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import DatePicker from 'react-datepicker';
import { registerLocale, setDefaultLocale } from  "react-datepicker";
import fr from 'date-fns/locale/fr';
registerLocale('fr', fr);
setDefaultLocale('fr');

import HabilitationService from 'services/habilitationsService';

const InventaireParcoursManuelOneEmplacement = ({
    idReserveInventaire,
    socket,
    idConteneur,
    inventaireElements,
    catalogueCodesBarres,
}) => {
    const [alerteScanLabel, setAlerteScanLabel] = useState();
    const [successScanLabel, setSuccessScanLabel] = useState();

    const [champScannette, setChampScannette] = useState("");
    const validerScannette = async (e) => {
        try {
            e.preventDefault();

            if(champScannette.substring(0,6) == 'GPMEMP')
            {
                setAlerteScanLabel('Ceci est un emplacement, merci de scanner uniquement des matériels.')
                setSuccessScanLabel();
            }else{

                let elementScanne = catalogueCodesBarres.filter(code => code.codeBarre == champScannette);
                if(elementScanne.length != 1)
                {
                    setAlerteScanLabel("Code barre inconnu");
                    setSuccessScanLabel();
                }else{
                    elementScanne = elementScanne[0];

                    let trouve = false;
                    for(const elem of inventaireElements)
                    {
                        if(elem.idConteneur == idConteneur && elem.idMaterielCatalogue == elementScanne.idMaterielCatalogue)
                        {
                            trouve = true;

                            let peremptionInventorieeAGarder = elem.peremptionInventoriee;
                            if(elementScanne.peremptionConsommable != null && elementScanne.peremptionConsommable != "")
                            {
                                if(elem.peremptionInventoriee == null)
                                {
                                    peremptionInventorieeAGarder = elementScanne.peremptionConsommable;
                                }else{
                                    let peremptionInventoriee = new Date(elem.peremptionInventoriee);
                                    let peremptionConsommable = new Date(elementScanne.peremptionConsommable);
                                    peremptionInventorieeAGarder = peremptionInventoriee < peremptionConsommable ? peremptionInventoriee : peremptionConsommable;
                                }
                            }

                            let newElement = {
                                idReserveElement: elem.idReserveElement,
                                idConteneur: elem.idConteneur,
                                idReserveInventaire: elem.idReserveInventaire,
                                idMaterielCatalogue: elem.idMaterielCatalogue,
                                libelleMateriel: elem.libelleMateriel,
                                peremptionAvantInventaire: elem.peremptionAvantInventaire,
                                peremptionInventoriee: peremptionInventorieeAGarder ? moment(peremptionInventorieeAGarder).format('YYYY-MM-DD') : null,
                                quantiteAvantInventaire: elem.quantiteAvantInventaire,
                                quantiteInventoriee: Number(elem.quantiteInventoriee) + 1,
                                quantiteAlerte: elem.quantiteAlerte,
                            };

                            await socket.emit("reserve_inventaire_update", newElement);
                        }
                    }

                    if(trouve)
                    {
                        setAlerteScanLabel();
                        setSuccessScanLabel(elementScanne.libelleMateriel);
                    }else{
                        setAlerteScanLabel(<>{elementScanne.libelleMateriel}<br/>Materiel pas à sa place, non-enregistré</>);
                        setSuccessScanLabel();
                    }
                }
            }

            setChampScannette("");
        } catch (error) {
            console.log(error)
        }
    }

    const updateQuantite = async (idReserveElement, quantiteInventoriee, updateOldPeremption) => {
        let oneElement = inventaireElements.filter(elem => elem.idReserveElement == idReserveElement)[0];

        let newElement = {
            idReserveElement: oneElement.idReserveElement,
            idConteneur: oneElement.idConteneur,
            idReserveInventaire: oneElement.idReserveInventaire,
            idMaterielCatalogue: oneElement.idMaterielCatalogue,
            libelleMateriel: oneElement.libelleMateriel,
            peremptionAvantInventaire: oneElement.peremptionAvantInventaire,
            peremptionInventoriee: updateOldPeremption ? oneElement.peremptionAvantInventaire : oneElement.peremptionInventoriee,
            quantiteAvantInventaire: oneElement.quantiteAvantInventaire,
            quantiteInventoriee: Number(quantiteInventoriee),
            quantiteAlerte: oneElement.quantiteAlerte,
        };

        await socket.emit("reserve_inventaire_update", newElement);
    }

    const updatePeremption = async (idReserveElement, peremptionInventoriee) => {
        let oneElement = inventaireElements.filter(elem => elem.idReserveElement == idReserveElement)[0];

        let newElement = {
            idReserveElement: oneElement.idReserveElement,
            idConteneur: oneElement.idConteneur,
            idReserveInventaire: oneElement.idReserveInventaire,
            idMaterielCatalogue: oneElement.idMaterielCatalogue,
            libelleMateriel: oneElement.libelleMateriel,
            peremptionAvantInventaire: oneElement.peremptionAvantInventaire,
            peremptionInventoriee: peremptionInventoriee ? moment(peremptionInventoriee).format('YYYY-MM-DD') : null,
            quantiteAvantInventaire: oneElement.quantiteAvantInventaire,
            quantiteInventoriee: Number(oneElement.quantiteInventoriee),
            quantiteAlerte: oneElement.quantiteAlerte,
        };

        await socket.emit("reserve_inventaire_update", newElement);
    }

    return (<>
        <Form className='mb-3' onSubmit={validerScannette}>
            <FloatingLabel
                controlId="floatingInput"
                label="Scanner ici les matériels de ce conteneur"
                className="mb-3"
            >
                <Form.Control
                    size="sm"
                    type="text"
                    name='champScannette'
                    id='champScannette'
                    value={champScannette}
                    onChange={(e) => setChampScannette(e.target.value)}
                    disabled={!HabilitationService.habilitations['reserve_modification']}
                />
            </FloatingLabel>
        </Form>

        <center className='mb-3'>
            {alerteScanLabel != null && alerteScanLabel != "" ?
                <SoftBadge bg='danger'>{alerteScanLabel}</SoftBadge>
            : null}

            {successScanLabel != null && successScanLabel != "" ?
                <SoftBadge bg='success'>{successScanLabel}</SoftBadge>
            : null}
        </center>

        <hr/>

        <Table size='sm' responsive>
            <thead>
                <tr>
                    <th></th>
                    <th>Matériel</th>
                    <th>Quantité</th>
                    <th>Péremption</th>
                </tr>
            </thead>
            <tbody>
                {inventaireElements.map((elem, i)=>{return(
                    <tr>
                        <td>
                            <FontAwesomeIcon
                                icon={elem.quantiteInventoriee > elem.quantiteAlerte ? 'check-circle' : 'exclamation-circle'}
                                className={elem.quantiteInventoriee > elem.quantiteAlerte ? 'text-success' : 'text-warning'}
                            />
                        </td>
                        <td>{elem.libelleMateriel}</td>
                        <td>
                            <InputGroup className="mb-3">
                                <Form.Control
                                    size="sm"
                                    type="number"
                                    min="0"
                                    step="1"
                                    name='quantiteInventoriee'
                                    id='quantiteInventoriee'
                                    value={elem.quantiteInventoriee}
                                    onChange={(e) => updateQuantite(elem.idReserveElement, e.target.value, false)}
                                    disabled={!HabilitationService.habilitations['reserve_modification']}
                                />
                                {HabilitationService.habilitations['reserve_modification'] ?
                                    <DropdownButton
                                        size='sm'
                                        variant="outline-secondary"
                                        title="Charg."
                                        id="input-group-dropdown-2"
                                        align="end"
                                    >
                                        <Dropdown.Item onClick={()=>{updateQuantite(elem.idReserveElement, elem.quantiteAvantInventaire, true)}}>Qtt précédente</Dropdown.Item>
                                        <Dropdown.Item onClick={()=>{updateQuantite(elem.idReserveElement, elem.quantiteAlerte, false)}}>Stock d'alerte</Dropdown.Item>
                                    </DropdownButton>
                                : null}
                            </InputGroup>
                        </td>
                        <td>
                            <DatePicker
                                selected={elem.peremptionInventoriee ? new Date(elem.peremptionInventoriee) : null}
                                onChange={(date)=>updatePeremption(elem.idReserveElement, date)}
                                formatWeekDay={day => day.slice(0, 3)}
                                className='form-control'
                                placeholderText="Choisir une date"
                                dateFormat="dd/MM/yyyy"
                                fixedHeight
                                locale="fr"
                                disabled={!HabilitationService.habilitations['reserve_modification']}
                            />
                        </td>
                    </tr>
                )})}
            </tbody>
        </Table>
    </>);
};

InventaireParcoursManuelOneEmplacement.propTypes = {};

export default InventaireParcoursManuelOneEmplacement;
