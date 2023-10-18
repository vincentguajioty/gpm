const AesFournisseursService = {
    
    aesToken: localStorage.getItem("aesToken"),
    aesTokenValidUntil: localStorage.getItem("aesTokenValidUntil"),

    setAesToken: function (tok) {
        this.aesToken = tok;
        localStorage.setItem("aesToken", tok);
    },
    setAesTokenValidUntil: function (timeLimit) {
        this.aesTokenValidUntil = timeLimit;
        localStorage.setItem("aesTokenValidUntil", timeLimit);
    },
};

export default AesFournisseursService;