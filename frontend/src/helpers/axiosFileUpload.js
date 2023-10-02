import axios, { AxiosRequestTransformer } from 'axios';
import { toast } from 'react-toastify';
import moment from 'moment';
import HabilitationService from 'services/habilitationsService';

const instance = axios.create({
    withCredentials: true,
    baseURL: process.env.REACT_APP_BACKEND_URL,
});

/// Request interceptor to add useful headers
const onRequestFulfilled = async (config) => {
    if(HabilitationService.token)
    {
        if(moment(HabilitationService.tokenValidUntil).subtract(1, 'second') <= moment(new Date()))
        {
            const response = await axios.post(process.env.REACT_APP_BACKEND_URL+'/refreshToken', {
                expiredToken: HabilitationService.token,
                refreshToken: HabilitationService.refreshToken,
            });

            if(response.data.auth === true) {
                HabilitationService.setToken(response.data.token);
                HabilitationService.setTokenValidUntil(response.data.tokenValidUntil);
                HabilitationService.setRefreshToken(response.data.refreshToken);
                HabilitationService.setHabilitations(response.data.habilitations);
                
                config.headers.common['x-access-token'] = response.data.token;
            }
        }
        else
        {
            config.headers.common['x-access-token'] = HabilitationService.token;
        }
    }
    config.headers.common['Content-Type'] = 'multipart/form-data';
    return config;
}
instance.interceptors.request.use(onRequestFulfilled);

// Request interceptor to handle error codes and show Toast
instance.interceptors.response.use(function (response) {
    if(response.status === 201)
    {
        toast.success("Enregistré !");
    }
    
    return response;
}, function (error) {
    switch (error.response.status) {
        case 401:
            localStorage.clear();
            break;
        
        case 403:
            toast.warning("Vous n'êtes pas habilité à effectuer cette action.");
            break;
    
        default:
            toast.error("Une erreur est survenue ("+error+")");
            break;
    }
    return Promise.reject(error);
});


export { instance as AxiosUpload };