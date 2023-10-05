const ConfigurationService = {
    config: localStorage.getItem("config") ? JSON.parse(localStorage.getItem("config")) : undefined,

    setConfig: function (config) {
        this.config = config;
        localStorage.setItem("config", JSON.stringify(config));
    },
};

export default ConfigurationService;