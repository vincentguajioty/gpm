const getItemFromStorage = (itemName) => {
    const local = localStorage.getItem(itemName);
    if(local)
    {
        return local
    }
    
    const session = sessionStorage.getItem(itemName)
    if(session){
        return session;
    }
    
    return undefined;
}

const setItemToStorage = (itemName, itemValue, seSouvenir) => {
    if(seSouvenir == "1")
    {
        localStorage.setItem(itemName, itemValue)
    }else{
        sessionStorage.setItem(itemName, itemValue)
    }
}

const HabilitationService = {
    
    seSouvenir: getItemFromStorage("seSouvenir"),
    
    habilitations: getItemFromStorage("habilitations") ? JSON.parse(getItemFromStorage("habilitations")) : undefined,
    token: getItemFromStorage("token"),
    tokenValidUntil: getItemFromStorage("tokenValidUntil"),
    refreshToken: getItemFromStorage("refreshToken"),

    habilitationsInitial: getItemFromStorage("habilitationsInitial") ? JSON.parse(getItemFromStorage("habilitationsInitial")) : undefined,
    tokenInitial: getItemFromStorage("tokenInitial"),
    tokenValidUntilInitial: getItemFromStorage("tokenValidUntilInitial"),
    refreshTokenInitial: getItemFromStorage("refreshTokenInitial"),

    delegationActive: getItemFromStorage("delegationActive"),

    setSeSouvenir: function (seSouvenir) {
        this.seSouvenir = seSouvenir ? "1" : "0";
        setItemToStorage("seSouvenir", seSouvenir ? "1" : "0", this.seSouvenir);
    },

    setHabilitations: function (hab) {
        this.habilitations = hab;
        setItemToStorage("habilitations", JSON.stringify(hab), this.seSouvenir);
    },
    setToken: function (tok) {
        this.token = tok;
        setItemToStorage("token", tok, this.seSouvenir);
    },
    setTokenValidUntil: function (timeLimit) {
        this.tokenValidUntil = timeLimit;
        setItemToStorage("tokenValidUntil", timeLimit, this.seSouvenir);
    },
    setRefreshToken: function (tok) {
        this.refreshToken = tok;
        setItemToStorage("refreshToken", tok, this.seSouvenir);
    },

    saveBeforeDelegate: function () {
        this.habilitationsInitial = this.habilitations;
        this.tokenInitial = this.token;
        this.tokenValidUntilInitial = this.tokenValidUntil;
        this.refreshTokenInitial = this.refreshToken;

        setItemToStorage("habilitationsInitial", JSON.stringify(this.habilitations), this.seSouvenir);
        setItemToStorage("tokenInitial", this.token, this.seSouvenir);
        setItemToStorage("tokenValidUntilInitial", this.tokenValidUntil, this.seSouvenir);
        setItemToStorage("refreshTokenInitial", this.refreshToken, this.seSouvenir);

        this.delegationActive = 1;
        setItemToStorage("delegationActive", 1, this.seSouvenir);
    },

    backToInitialSession: function () {
        this.habilitations = this.habilitationsInitial;
        this.token = this.tokenInitial;
        this.tokenValidUntil = this.tokenValidUntilInitial;
        this.refreshToken = this.refreshTokenInitial;

        setItemToStorage("habilitations", JSON.stringify(this.habilitationsInitial), this.seSouvenir);
        setItemToStorage("token", this.tokenInitial, this.seSouvenir);
        setItemToStorage("tokenValidUntil", this.tokenValidUntilInitial, this.seSouvenir);
        setItemToStorage("refreshToken", this.refreshTokenInitial, this.seSouvenir);

        localStorage.removeItem("habilitationsInitial");
        localStorage.removeItem("tokenInitial");
        localStorage.removeItem("tokenValidUntilInitial");
        localStorage.removeItem("refreshTokenInitial");
        localStorage.removeItem("delegationActive");

        sessionStorage.removeItem("habilitationsInitial");
        sessionStorage.removeItem("tokenInitial");
        sessionStorage.removeItem("tokenValidUntilInitial");
        sessionStorage.removeItem("refreshTokenInitial");
        sessionStorage.removeItem("delegationActive");
    },

    disconnect: function () {
        localStorage.clear();
        sessionStorage.clear();

        this.habilitations = undefined;
        this.token = undefined;
        this.tokenValidUntil = undefined;
        this.refreshToken = undefined;
        this.habilitationsInitial = undefined;
        this.tokenInitial = undefined;
        this.tokenValidUntilInitial = undefined;
        this.refreshTokenInitial = undefined;
        this.delegationActive = undefined;
    }
};

export default HabilitationService;