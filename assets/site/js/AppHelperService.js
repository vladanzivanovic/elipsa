// import DateTimePicker from "../Inputs/DateTimePicker";

class AppHelperService {

    static isArray(data){
        return Object.prototype.toString.call(data) === '[object Array]';
    };

    static isObject(data){
        return Object.prototype.toString.call(data) === '[object Object]';
    };

    static isBoolean(data){
        return Object.prototype.toString.call(data) === '[object Boolean]';
    };

    static isString(data){
        return Object.prototype.toString.call(data) === '[object String]';
    };

    static isJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    };

    static redirect(href) {
        if(href == 'reload') {
            window.location.reload();
        } else{
            window.location.href = href;
        }
    };

    static generateLocalizedUrl(url, data) {
        if (LOCALE !== 'rs') {
            data = $.extend({}, data, {_locale: LOCALE});

            return Routing.generate(`site_locale_${url}`, data);
        }

        return Routing.generate(url, data);
    }

};

export default AppHelperService;