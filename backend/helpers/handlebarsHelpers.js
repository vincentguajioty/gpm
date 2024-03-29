const ifIsNull = (a, options) => {
    if(a == null)
    {return options.fn(this);}
    else
    {return options.inverse(this);}
}

const ifIsNotNull = (a, options) => {
    if(a != null)
    {return options.fn(this);}
    else
    {return options.inverse(this);}
}

const ifCond = (v1, operator, v2, options) => {
    switch (operator) {
        case '==':
            return (v1 == v2) ? options.fn(this) : options.inverse(this);
        case '===':
            return (v1 === v2) ? options.fn(this) : options.inverse(this);
        case '!=':
            return (v1 != v2) ? options.fn(this) : options.inverse(this);
        case '!==':
            return (v1 !== v2) ? options.fn(this) : options.inverse(this);
        case '<':
            return (v1 < v2) ? options.fn(this) : options.inverse(this);
        case '<=':
            return (v1 <= v2) ? options.fn(this) : options.inverse(this);
        case '>':
            return (v1 > v2) ? options.fn(this) : options.inverse(this);
        case '>=':
            return (v1 >= v2) ? options.fn(this) : options.inverse(this);
        case '&&':
            return (v1 && v2) ? options.fn(this) : options.inverse(this);
        case '||':
            return (v1 || v2) ? options.fn(this) : options.inverse(this);
        default:
            return options.inverse(this);
    }
}

const nl2br = (text) => {
    if(text && text != null && text != "")
    {
        return text.replace(/(\r\n|\n|\r)/gm, '<br/>');
    }else{
        return null;
    }
}

module.exports = {
    ifIsNull,
    ifIsNotNull,
    ifCond,
    nl2br,
};