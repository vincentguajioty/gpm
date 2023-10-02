const HabilitationService = {
    
    habilitations: localStorage.getItem("habilitations") ? JSON.parse(localStorage.getItem("habilitations")) : undefined,
    token: localStorage.getItem("token"),
    tokenValidUntil: localStorage.getItem("tokenValidUntil"),
    refreshToken: localStorage.getItem("refreshToken"),

    setHabilitations: function (hab) {
        this.habilitations = hab;
        localStorage.setItem("habilitations", JSON.stringify(hab));
    },
    setToken: function (tok) {
        this.token = tok;
        localStorage.setItem("token", tok);
    },
    setTokenValidUntil: function (timeLimit) {
        this.tokenValidUntil = timeLimit;
        localStorage.setItem("tokenValidUntil", timeLimit);
    },
    setRefreshToken: function (tok) {
        this.refreshToken = tok;
        localStorage.setItem("refreshToken", tok);
    },
};

export default HabilitationService;