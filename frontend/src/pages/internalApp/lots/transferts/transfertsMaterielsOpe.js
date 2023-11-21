import React, { useState } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { Card, Form, Nav, Modal } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import IconButton from 'components/common/IconButton';
import LoaderInfiniteLoop from 'components/loaderInfiniteLoop';

import TransfertsMaterielsOpeNonRetour from './transfertsMaterielsOpeNonRetour';

import TransfertsMaterielsOpeStep1 from './formulaires/formStep1';
import TransfertsMaterielsOpeStep2 from './formulaires/formStep2';
import TransfertsMaterielsOpeRecap from './formulaires/recapStep3';
import Success from './formulaires/Success';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { transfertReservesLotsStep } from 'helpers/yupValidationSchema';

const navItems = [
    {
        icon: 'archive',
        label: 'Réserve utilisée'
    },
    {
        icon: 'list-ul',
        label: 'Quantité'
    },
    {
        icon: 'eye',
        label: 'Récapitulatif'
    },
    {
        icon: 'thumbs-up',
        label: 'Terminé'
    },
];

const TransfertsMaterielsOpe = ({
    idElement,
    setPageNeedsRefresh,
}) => {
    const [showModal, setShowModal] = useState(false);
    const [showNoRollbackModal, setShowNoRollbackModal] = useState(false);
    const [isLoading, setLoading] = useState(false);
    const [elementMateriel, setElementMateriel] = useState([]);
    const [reserves, setReserves] = useState([]);
    const [step, setStep] = useState(1);

    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(transfertReservesLotsStep[step - 1]),
    });
    
    const handleShowModal = async () => {
        try {
            setLoading(true);
            setShowModal(true);

            setStep(1);
            setValue("idElement", idElement);

            let elem = await Axios.post('/materiels/getOneMateriel',{
                idElement: idElement
            });
            setElementMateriel(elem.data[0]);

            let getData = await Axios.post('/transferts/getReservesForOneTransfert',{
                idMaterielCatalogue: elem.data[0].idMaterielCatalogue
            });
            setReserves(getData.data);

            setLoading(false);
        } catch (error) {
            console.log(error)
        }
    }
    const handleCloseModal = () => {
        setShowModal(false);
        reset();
        setStep(1);
        setPageNeedsRefresh(true);
    }

    const onSubmitData = async (data) => {
        try {
            if(step === 3)
            {
                setLoading(true);
                let effectuerTransfert = await Axios.post('transferts/opererTransfertReserveLot',{
                    idElement: idElement,
                    idReserveElement: data.idReserveElement,
                    qttTransfert: data.qttTransfert,
                });
                setLoading(false);
            }
            setStep(step + 1);
        } catch (error) {
            console.log(error)
        }
    };

    const handleNavs = targetStep => {
        if(targetStep < step)
        {
            setShowNoRollbackModal(true);
        }
        else
        {
            if (step !== 4)
            {
                handleSubmit(onSubmitData);
            }
        }
    };

    return (<>
      
        <TransfertsMaterielsOpeNonRetour modal={showNoRollbackModal} setModal={setShowNoRollbackModal} />

        <IconButton
            icon='arrows-alt-h'
            size = 'sm'
            variant="outline-success"
            className="me-1"
            onClick={handleShowModal}
        />

        <Modal show={showModal} onHide={handleCloseModal} size="lg" backdrop="static" keyboard={false}>
            <Modal.Header closeButton >
                <Modal.Title>{isLoading ? 'Chargement...' : 'Réappro de '+elementMateriel.libelleMateriel}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <Card
                    as={Form}
                    noValidate
                    onSubmit={handleSubmit(onSubmitData)}
                    className="theme-wizard mb-5"
                >
                    <Card.Header
                        className={classNames('bg-light', 'pb-2')}
                    >
                        <Nav className="justify-content-center">
                            {navItems.map((item, index) => (
                                <NavItemCustom
                                    key={item.label}
                                    index={index + 1}
                                    step={step}
                                    handleNavs={handleNavs}
                                    icon={item.icon}
                                    label={item.label}
                                />
                            ))}
                        </Nav>
                    </Card.Header>
                  
                    <Card.Body className="fw-normal px-md-6 py-4">
                        {isLoading ?
                            <LoaderInfiniteLoop/>
                        : <>
                            
                            {step === 1 ? 
                                <TransfertsMaterielsOpeStep1
                                    register={register}
                                    setValue={setValue}
                                    errors={errors}
                                    watch={watch}
                                    reserves={reserves}
                                />
                            : null}

                            {step === 2 ?
                                <TransfertsMaterielsOpeStep2
                                    register={register}
                                    setValue={setValue}
                                    errors={errors}
                                    watch={watch}
                                    reserves={reserves}
                                />
                            : null}

                            {step === 3 ?
                                <TransfertsMaterielsOpeRecap
                                    watch={watch}    
                                    elementMateriel={elementMateriel}
                                    reserves={reserves}
                                />
                            : null}

                            {step === 4 ?
                                <Success
                                    handleCloseModal={handleCloseModal}
                                />
                            : null}

                        </>}
                    </Card.Body>

                    <Card.Footer
                        className={classNames('px-md-6 bg-light', {
                            'd-none': step === 4,
                            ' d-flex': step < 4
                        })}
                    >
                        <IconButton
                            variant="link"
                            iconAlign="left"
                            transform="down-1 shrink-4"
                            className={classNames('px-0 fw-semi-bold', {
                                'd-none': step === 1
                            })}
                            onClick={handleCloseModal}
                        >
                                Annuler
                        </IconButton>

                        <IconButton
                            variant="primary"
                            className="ms-auto"
                            type="submit"
                            icon={'chevron-right'}
                            iconAlign="right"
                            transform="down-1 shrink-4"
                        >
                            Suivant
                        </IconButton>
                    </Card.Footer>
                </Card>    
            </Modal.Body>
        </Modal>
    </>);
};

const NavItemCustom = ({ index, step, handleNavs, icon, label }) => {
  return (
    <Nav.Item>
      <Nav.Link
        className={classNames('fw-semi-bold', {
          done: index < 4 ? step > index : step > 3,
          active: step === index
        })}
        onClick={() => handleNavs(index)}
      >
        <span className="nav-item-circle-parent">
          <span className="nav-item-circle">
            <FontAwesomeIcon icon={icon} />
          </span>
        </span>
        <span className="d-none d-md-block mt-1 fs--1">{label}</span>
      </Nav.Link>
    </Nav.Item>
  );
};

NavItemCustom.propTypes = {
  index: PropTypes.number.isRequired,
  step: PropTypes.number.isRequired,
  handleNavs: PropTypes.func.isRequired,
  icon: PropTypes.string.isRequired,
  label: PropTypes.string.isRequired
};

TransfertsMaterielsOpe.propTypes = {};

export default TransfertsMaterielsOpe;
