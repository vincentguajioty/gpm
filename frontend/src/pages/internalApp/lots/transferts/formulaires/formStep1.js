import React, { useEffect } from 'react';
import { Form, } from 'react-bootstrap';
import Select from 'react-select';
import moment from 'moment-timezone';

const TransfertsMaterielsOpeStep1 = ({
    register,
    setValue,
    errors,
    watch,
    reserves,
}) => {

    useEffect(()=>{
        setValue("step", 1);
    },[])

    useEffect(()=>{
        if(watch("idReserveElement") > 0)
        {
            setValue("quantiteReserve", reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].quantiteReserve)
        }else{
            setValue("quantiteReserve", null)
        }
    },[watch("idReserveElement")])

    return(<>
        <Form.Group className="mb-3">
            <Form.Label>Source du transfert:</Form.Label>
            <Select
                id="idReserveElement"
                name="idReserveElement"
                size="sm"
                classNamePrefix="react-select"
                closeMenuOnSelect={true}
                isClearable={true}
                isSearchable={true}
                placeholder='Aucun élément selectionné'
                options={reserves}
                isOptionDisabled={(option) => option.inventaireEnCours}
                value={reserves.find(c => c.value === watch("idReserveElement"))}
                onChange={val => val != null ? setValue("idReserveElement", val.value) : setValue("idReserveElement", null)}
            />
            <small className="text-danger">{errors.idReserveElement?.message}</small>
        </Form.Group>

        <Form.Group className="mb-3">
            <Form.Label>Détails de l'élément selectionné:</Form.Label>
            {watch("idReserveElement") > 0 ? <>
            
                <Form.Control
                    className="mb-1"
                    size="sm"
                    type="text"
                    value={'Element: ' + reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].libelleMateriel}
                    disabled
                />
                <Form.Control
                    className="mb-1"
                    size="sm"
                    type="text"
                    value={'Conteneur: ' + reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].libelleConteneur}
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
                    value={'Péremption: ' + (reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].peremptionReserve != null ? moment(reserves.filter(res => res.idReserveElement == watch("idReserveElement"))[0].peremptionReserve).format("DD/MM/YYYY") : 'Aucune')}
                    disabled
                />
            
            </> : ' Selectionnez un élément'}
        </Form.Group>
    </>);
    
};

TransfertsMaterielsOpeStep1.propTypes = {};

export default TransfertsMaterielsOpeStep1;
