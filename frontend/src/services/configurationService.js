const ConfigurationService = {

    appname: localStorage.getItem("appname"),
    urlsite: localStorage.getItem("urlsite"),
    maintenance: localStorage.getItem("maintenance"),
    resetPassword: localStorage.getItem("resetPassword"),
    alertes_benevoles_lots: localStorage.getItem("alertes_benevoles_lots"),
    alertes_benevoles_vehicules: localStorage.getItem("alertes_benevoles_vehicules"),
    consommation_benevoles: localStorage.getItem("consommation_benevoles"),

    setAppname: function (value) {
        this.appname = value;
        localStorage.setItem("appname", value);
    },
    setUrlsite: function (value) {
        this.urlsite = value;
        localStorage.setItem("urlsite", value);
    },
    setMaintenance: function (value) {
        this.maintenance = value;
        localStorage.setItem("maintenance", value);
    },
    setResetPassword: function (value) {
        this.resetPassword = value;
        localStorage.setItem("resetPassword", value);
    },
    setAlertes_benevoles_lots: function (value) {
        this.alertes_benevoles_lots = value;
        localStorage.setItem("alertes_benevoles_lots", value);
    },
    setAlertes_benevoles_vehicules: function (value) {
        this.alertes_benevoles_vehicules = value;
        localStorage.setItem("alertes_benevoles_vehicules", value);
    },
    setConsommation_benevoles: function (value) {
        this.consommation_benevoles = value;
        localStorage.setItem("consommation_benevoles", value);
    },
};

export default ConfigurationService;