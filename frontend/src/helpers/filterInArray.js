const filterArrayInArray = (array, subArray, prop, value) => {
    let filtered = [];
    for (const obj of array) {
        let toBeKept = false;

        for (const subObj of obj[subArray]) {
            subObj[prop] == value ? toBeKept = true : null;
        }

        toBeKept ? (filtered.push(obj)) : null;
    }
    return filtered;
}

export default filterArrayInArray;