import axios, { AxiosRequestTransformer } from 'axios';
import { toast } from 'react-toastify';
import moment from 'moment';
import HabilitationService from 'services/habilitationsService';

const dateTransformer: AxiosRequestTransformer = data => {
    if (data instanceof Date) {
      // do your specific formatting here
      return moment(data).format("YYYY-MM-DD HH:mm:ss")
    }
    if (Array.isArray(data)) {
      return data.map(val => dateTransformer(val))
    }
    if (typeof data === "object" && data !== null) {
      return Object.fromEntries(Object.entries(data).map(([ key, val ]) =>
        [ key, dateTransformer(val) ]))
    }
    return data
}

const instance = axios.create({
    withCredentials: true,
    baseURL: window.__ENV__.APP_BACKEND_URL,
    transformRequest: [ dateTransformer ].concat(axios.defaults.transformRequest)
});


// Request interceptor to add useful headers
const onRequestFulfilled = async (config) => {
    if(HabilitationService.token)
    {
        if(moment(HabilitationService.tokenValidUntil).subtract(1, 'second') <= moment(new Date()))
        {
            const response = await axios.post(window.__ENV__.APP_BACKEND_URL+'/refreshToken', {
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


export { instance as Axios };