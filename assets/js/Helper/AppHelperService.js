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

    static isUrl(url) {
        const regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;

        return regex.test(url);
    }

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
        return Routing.generate(url, data);
    }

    static formatPrice(price) {
        return price.toLocaleString(LOCALE, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
};

export default AppHelperService;
