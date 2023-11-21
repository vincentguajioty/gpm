import React, { useEffect } from 'react';
import {  Form, } from 'react-bootstrap';

const TransfertsMaterielsOpeStep2 = ({
    register,
    setValue,
    errors,
    watch,
    reserves,
}) => {
    useEffect(()=>{
        setValue("step", 2);
    },[])

    return(<>
        <Form.Group className="mb-3">
            <Form.Label>Quantité à transférer (max:{watch("quantiteReserve")})</Form.Label>
            <Form.Control
                className="mb-1"
                size="sm"
                name='qttTransfert'
                id='qttTransfert'
                type="number"
                min={1}
                max={watch("quantiteReserve")}
                step='1'
                {...register("qttTransfert")}
            />
            <small className="text-danger">{errors.qttTransfert?.message}</small>
        </Form.Group>
    </>);
    
};

TransfertsMaterielsOpeStep2.propTypes = {};

export default TransfertsMaterielsOpeStep2;
