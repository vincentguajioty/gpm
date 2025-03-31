const TenuesPublicService = {
    
    tenuesPublicToken: sessionStorage.getItem("tenuesPublicToken"),
    tenuesPublicTokenValidUntil: sessionStorage.getItem("tenuesPublicTokenValidUntil"),

    setTenuesPublicToken: function (tok) {
        this.tenuesPublicToken = tok;
        sessionStorage.setItem("tenuesPublicToken", tok);
    },
    setTenuesPublicTokenValidUntil: function (timeLimit) {
        this.tenuesPublicTokenValidUntil = timeLimit;
        sessionStorage.setItem("tenuesPublicTokenValidUntil", timeLimit);
    },

    disconnect: function () {
        sessionStorage.removeItem("tenuesPublicToken");
        sessionStorage.removeItem("tenuesPublicTokenValidUntil");

        delete this.tenuesPublicToken;
        delete this.tenuesPublicTokenValidUntil;
    }
};

export default TenuesPublicService;