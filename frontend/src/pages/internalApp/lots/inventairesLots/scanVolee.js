import React, {useEffect, useState} from 'react';
import { Card, Form, FloatingLabel, } from 'react-bootstrap';
import SoftBadge from 'components/common/SoftBadge';
import moment from 'moment-timezone';

import HabilitationService from 'services/habilitationsService';
import filterArrayInArray from 'helpers/filterInArray';

const InventaireScanVolee = ({
    idInventaire,
    socket,
    inventaireElements,
    arborescenceSacs,
    catalogueCodesBarres,
}) => {
    const [emplacementActuelLabel, setEmplacementActuelLabel] = useState();
    const [idEmplacementActuel, setIdEmplacementActuel] = useState();
    const [alerteScanLabel, setAlerteScanLabel] = useState();
    const [successScanLabel, setSuccessScanLabel] = useState();

    const [champScannette, setChampScannette] = useState("");
    const validerScannette = async (e) => {
        try {
            e.preventDefault();

            if(champScannette.substring(0,6) == 'GPMEMP')
            {
                let id = champScannette.substring(6);
                setIdEmplacementActuel(id);
                let enCours = filterArrayInArray(arborescenceSacs, 'emplacements', 'idEmplacement', id);
                if(enCours.length == 1)
                {
                    let libelleSac = enCours[0].libelleSac;
                    let emplacement = enCours[0].emplacements.filter(emp => emp.idEmplacement == id);
                    let libelleEmplacement = emplacement[0].libelleEmplacement
                    setEmplacementActuelLabel(<>{libelleSac}<br/>{libelleEmplacement}</>);
                    setAlerteScanLabel();
                    setSuccessScanLabel();
                }else{
                    setEmplacementActuelLabel('Emplacement inconnu dans ce lot');
                }
            }else{
                if(idEmplacementActuel != null && idEmplacementActuel > 0)
                {
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
                            if(elem.idEmplacement == idEmplacementActuel && elem.idMaterielCatalogue == elementScanne.idMaterielCatalogue)
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
                                    idElement: elem.idElement,
                                    idEmplacement: elem.idEmplacement,
                                    idInventaire: elem.idInventaire,
                                    idMaterielCatalogue: elem.idMaterielCatalogue,
                                    libelleMateriel: elem.libelleMateriel,
                                    peremptionAvantInventaire: elem.peremptionAvantInventaire,
                                    peremptionInventoriee: peremptionInventorieeAGarder ? moment(peremptionInventorieeAGarder).format('YYYY-MM-DD') : null,
                                    quantiteAvantInventaire: elem.quantiteAvantInventaire,
                                    quantiteInventoriee: elem.quantiteInventoriee += 1,
                                    quantiteAlerte: elem.quantiteAlerte,
                                };

                                await socket.emit("lot_inventaire_update", newElement);
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
                }else{
                    setAlerteScanLabel(<>Scannez un emplacement<br/>avant de scanner du matériel !</>);
                    setSuccessScanLabel();
                }
            }

            setChampScannette("");
        } catch (error) {
            console.log(error)
        }
    }

    return (<>
        <Card className="mb-3">
            <Card.Body>
                <p>Placez votre curseur dans le champ ci-dessous et scannez successivement les emplacements puis leur contenu.</p>

                <center className='mb-3'><SoftBadge>
                    {emplacementActuelLabel != null && emplacementActuelLabel != "" ?
                        emplacementActuelLabel
                    : <i>Commencez par scanner un emplacement</i>}
                </SoftBadge></center>

                <Form className='mb-3' onSubmit={validerScannette}>
                    <FloatingLabel
                        controlId="floatingInput"
                        label="Scanner ici emplacements et matériels"
                        className="mb-3"
                    >
                        <Form.Control
                            size="sm"
                            type="text"
                            name='champScannette'
                            id='champScannette'
                            value={champScannette}
                            onChange={(e) => setChampScannette(e.target.value)}
                            disabled={!HabilitationService.habilitations['lots_modification']}
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

                <p>Une fois votre inventaire terminé, travaillez sur la vue <i>Méthode 2</i> ci-après pour valider emplacement par emplacement ce qui a été scanné et terminer votre inventaire.</p>
            </Card.Body>
        </Card>
    </>);
};

InventaireScanVolee.propTypes = {};

export default InventaireScanVolee;
