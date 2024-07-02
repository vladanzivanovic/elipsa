import AppHelperService from "./AppHelperService";

require('jquery-serializejson');

class FormHelperService {
    static sanitize(data) {
        return data.filter(obj => obj.value && obj.value.length > 0 && obj.value != '<p><br></p>');
    }

    static sanitizeJson(data) {
        const sanitizedObject = {};

        for (const itemKey in data) {
            if (true === AppHelperService.isObject(data[itemKey])) {
                sanitizedObject[itemKey] = this.sanitizeJson(data[itemKey]);
            }

            let value = new DOMParser()
                .parseFromString(data[itemKey], "text/html")
                .documentElement.textContent;

            if (data[itemKey] && data[itemKey].length > 0 && value.trim() !== '') {
                sanitizedObject[itemKey] = data[itemKey];
            }
        }

        return sanitizedObject;
    }

    /**
     *
     * @param {Object} formElement
     * @returns {Object}
     */
    static formToJson(formElement)
    {
        const serializedForm = formElement.serializeJSON();

        return this.sanitizeJson(serializedForm);
    }
}

export default FormHelperService;
