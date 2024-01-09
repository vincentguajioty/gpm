const ActionsMassivesService = {
    
    amToken: sessionStorage.getItem("amToken"),
    amTokenValidUntil: sessionStorage.getItem("amTokenValidUntil"),

    setAmToken: function (tok) {
        this.amToken = tok;
        sessionStorage.setItem("amToken", tok);
    },
    setAmTokenValidUntil: function (timeLimit) {
        this.amTokenValidUntil = timeLimit;
        sessionStorage.setItem("amTokenValidUntil", timeLimit);
    },
};

export default ActionsMassivesService;