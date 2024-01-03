import React, {useState} from 'react';
import { Button, Form, InputGroup } from 'react-bootstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import Activity from 'components/widgets/Activity';

import { Axios } from 'helpers/axios';

import { useForm } from "react-hook-form";
import { yupResolver } from '@hookform/resolvers/yup';
import { commandeAddComment } from 'helpers/yupValidationSchema';

const OneCommandeTimeLine = ({
    idCommande,
    commande,
    setPageNeedsRefresh,
}) => {
    const [isLoading, setLoading] = useState(false);
    const { register, handleSubmit, formState: { errors }, setValue, reset, watch } = useForm({
        resolver: yupResolver(commandeAddComment),
    });

    const postComment = async (data) => {
        try {
            setLoading(true);

            await Axios.post('/commandes/addComment',{
                idCommande: idCommande,
                comment: data.comment,
            })

            setPageNeedsRefresh(true);
            setLoading(false);
            reset();
        } catch (error) {
            console.log(error);
        }
    }

    return (<>
        <Form className='mb-3' onSubmit={handleSubmit(postComment)}>
            <InputGroup>
                <Form.Control size="sm" type="text" placeholder="Ajouter une note"  name='comment' id='comment' {...register('comment')} />
                <small className="text-danger">{errors.comment?.message}</small>
                <Button variant="outline-primary" type='submit' disabled={isLoading}>
                    <FontAwesomeIcon icon='comment' />
                </Button>
            </InputGroup>
        </Form>
        
        {commande.timeLine.map((activity, index) => (
            <Activity
                key={activity.id}
                activity={activity}
                isLast={index === commande.timeLine.length - 1}
            />
        ))}
    </>);
};

OneCommandeTimeLine.propTypes = {};

export default OneCommandeTimeLine;
