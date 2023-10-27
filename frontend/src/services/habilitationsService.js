const HabilitationService = {
    
    habilitations: localStorage.getItem("habilitations") ? JSON.parse(localStorage.getItem("habilitations")) : undefined,
    token: localStorage.getItem("token"),
    tokenValidUntil: localStorage.getItem("tokenValidUntil"),
    refreshToken: localStorage.getItem("refreshToken"),

    habilitationsInitial: localStorage.getItem("habilitationsInitial") ? JSON.parse(localStorage.getItem("habilitationsInitial")) : undefined,
    tokenInitial: localStorage.getItem("tokenInitial"),
    tokenValidUntilInitial: localStorage.getItem("tokenValidUntilInitial"),
    refreshTokenInitial: localStorage.getItem("refreshTokenInitial"),

    delegationActive: localStorage.getItem("delegationActive"),

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

    saveBeforeDelegate: function () {
        this.habilitationsInitial = this.habilitations;
        this.tokenInitial = this.token;
        this.tokenValidUntilInitial = this.tokenValidUntil;
        this.refreshTokenInitial = this.refreshToken;

        localStorage.setItem("habilitationsInitial", JSON.stringify(this.habilitations));
        localStorage.setItem("tokenInitial", this.token);
        localStorage.setItem("tokenValidUntilInitial", this.tokenValidUntil);
        localStorage.setItem("refreshTokenInitial", this.refreshToken);

        this.delegationActive = 1;
        localStorage.setItem("delegationActive", 1);
    },

    backToInitialSession: function () {
        this.habilitations = this.habilitationsInitial;
        this.token = this.tokenInitial;
        this.tokenValidUntil = this.tokenValidUntilInitial;
        this.refreshToken = this.refreshTokenInitial;

        localStorage.setItem("habilitations", JSON.stringify(this.habilitationsInitial));
        localStorage.setItem("token", this.tokenInitial);
        localStorage.setItem("tokenValidUntil", this.tokenValidUntilInitial);
        localStorage.setItem("refreshToken", this.refreshTokenInitial);

        localStorage.removeItem("habilitationsInitial");
        localStorage.removeItem("tokenInitial");
        localStorage.removeItem("tokenValidUntilInitial");
        localStorage.removeItem("refreshTokenInitial");

        localStorage.removeItem("delegationActive");
    },
};

export default HabilitationService;