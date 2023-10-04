import ConfigurationService from 'services/configurationService';
import { Axios } from './axios';

const getConfigAndSave = async () => {
    try {
        if(!ConfigurationService.appname || ConfigurationService.appname == null || ConfigurationService.appname == "")
        {
            const response = await Axios.get('getConfig');

            ConfigurationService.setAppname(response.data[0].appname);
            ConfigurationService.setUrlsite(response.data[0].urlsite);
            ConfigurationService.setMaintenance(response.data[0].maintenance);
            ConfigurationService.setResetPassword(response.data[0].resetPassword);
            ConfigurationService.setAlertes_benevoles_lots(response.data[0].alertes_benevoles_lots);
            ConfigurationService.setAlertes_benevoles_vehicules(response.data[0].alertes_benevoles_vehicules);
            ConfigurationService.setConsommation_benevoles(response.data[0].consommation_benevoles);
        }
    } catch (error) {
        console.log(error)
    }
}

export default getConfigAndSave;