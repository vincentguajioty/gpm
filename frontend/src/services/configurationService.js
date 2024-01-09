const ConfigurationService = {
    config: sessionStorage.getItem("config") ? JSON.parse(sessionStorage.getItem("config")) : undefined,

    setConfig: function (config) {
        this.config = config;
        sessionStorage.setItem("config", JSON.stringify(config));
    },
};

export default ConfigurationService;