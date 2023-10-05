import ConfigurationService from 'services/configurationService';
import { Axios } from './axios';

const getConfigAndSave = async () => {
    try {
        if(!ConfigurationService.config)
        {
            const response = await Axios.get('getConfig');

            ConfigurationService.setConfig(response.data[0]);
            
        }
    } catch (error) {
        console.log(error)
    }
}

export default getConfigAndSave;