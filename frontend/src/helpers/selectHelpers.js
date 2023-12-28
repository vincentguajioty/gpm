function findOptionForSelect (selectOpts, selectedValue) {
    for(const optiongroup of selectOpts)
    {
        for(const option of optiongroup.options)
        {
            if(option.value == selectedValue)
            {return option}
        }
    }
    return null;
}

export default findOptionForSelect;