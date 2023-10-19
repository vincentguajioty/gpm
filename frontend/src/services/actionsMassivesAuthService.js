const ActionsMassivesService = {
    
    amToken: localStorage.getItem("amToken"),
    amTokenValidUntil: localStorage.getItem("amTokenValidUntil"),

    setAmToken: function (tok) {
        this.amToken = tok;
        localStorage.setItem("amToken", tok);
    },
    setAmTokenValidUntil: function (timeLimit) {
        this.amTokenValidUntil = timeLimit;
        localStorage.setItem("amTokenValidUntil", timeLimit);
    },
};

export default ActionsMassivesService;