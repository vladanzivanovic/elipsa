require('jquery-serializejson');

class FormHelperService {
    static sanitize(data) {
        return data.filter(obj => obj.value && obj.value.length > 0);
    }

    /**
     *
     * @param {Object} formElement
     * @returns {Object}
     */
    static formToJson(formElement)
    {
        const serializedForm = formElement.serializeJSON();

        return serializedForm;
    }
}

export default FormHelperService;
