import React, { useEffect, useState} from 'react';
import { Form, Row, Col } from 'react-bootstrap';
import moment from 'moment-timezone';

const TransfertsMaterielsOpeRecap = ({
    watch,
    elementMateriel,
    reserves,
}) => {
    const [peremptionCible, setPeremptionCible] = useState();

    useEffect(()=>{
        let perMatos = elementMateriel.peremption;
        let perReserve = reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].peremptionReserve;
        
        if(perMatos == null && perReserve == null)
        {
            setPeremptionCible("Aucune");
        }

        if(perMatos == null && perReserve != null)
        {
            setPeremptionCible(moment(perReserve).format("DD/MM/YYYY"));
        }

        if(perMatos != null && perReserve == null)
        {
            setPeremptionCible(moment(perMatos).format("DD/MM/YYYY"));
        }

        if(perMatos != null && perReserve != null)
        {
            if(new Date(perMatos) < new Date(perReserve))
            {
                setPeremptionCible(moment(perMatos).format("DD/MM/YYYY"));
            }
            else
            {
                setPeremptionCible(moment(perReserve).format("DD/MM/YYYY"));
            }
        }

    },[])

    return(<>
        <Row>
            <Col md={6}>
                <Form.Group className="mb-3">
                    <Form.Label>Source</Form.Label>
                    {watch("idReserveElement") > 0 ? <>
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Réserve: ' + reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].libelleConteneur}
                            disabled
                        />
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Stock actuel: ' + reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].quantiteReserve + " éléments"}
                            disabled
                        />
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Transfert de: ' + watch("qttTransfert") + " éléments"}
                            disabled
                        />
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Stock après réapro: ' + (reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].quantiteReserve - watch("qttTransfert")) + " éléments"}
                            disabled
                        />
                    
                    </> : ' Selectionnez un élément'}
                </Form.Group>
            </Col>
            <Col md={6}>
                <Form.Group className="mb-3">
                    <Form.Label>Cible</Form.Label>
                    {watch("idReserveElement") > 0 ? <>
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={elementMateriel.libelleLot + " > " + elementMateriel.libelleSac + " > " + elementMateriel.libelleEmplacement}
                            disabled
                        />
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Quantité actuel: ' + elementMateriel.quantite + " éléments"}
                            disabled
                        />
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Quantité après réappro: ' + ( parseInt(elementMateriel.quantite) + parseInt(watch("qttTransfert"))) + " éléments"}
                            disabled
                        />
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Péremption: ' + (elementMateriel.peremption != null ? moment(elementMateriel.peremption).format("DD/MM/YYYY") : 'Aucune')}
                            disabled
                        />
                        <Form.Control
                            className="mb-1"
                            size="sm"
                            type="text"
                            value={'Péremption après réappro: ' + peremptionCible}
                            disabled
                        />
                    
                    </> : ' Selectionnez un élément'}
                </Form.Group>
            </Col>
        </Row>
    </>);
};

TransfertsMaterielsOpeRecap.propTypes = {};

export default TransfertsMaterielsOpeRecap;
