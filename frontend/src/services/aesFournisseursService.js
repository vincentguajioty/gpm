const AesFournisseursService = {
    
    aesToken: sessionStorage.getItem("aesToken"),
    aesTokenValidUntil: sessionStorage.getItem("aesTokenValidUntil"),

    setAesToken: function (tok) {
        this.aesToken = tok;
        sessionStorage.setItem("aesToken", tok);
    },
    setAesTokenValidUntil: function (timeLimit) {
        this.aesTokenValidUntil = timeLimit;
        sessionStorage.setItem("aesTokenValidUntil", timeLimit);
    },
};

export default AesFournisseursService;