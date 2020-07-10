import NotificationService from "../../../js/NotificationService";
import loyaltyPageMapper from "../Mapper/LoyaltyPageMapper";
import loader from "../Dom/LoaderDom";

class LoyaltyPageHandler {
    constructor() {
        this.mapper = loyaltyPageMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = Routing.generate('site_api.add_loyalty');
        let type = 'POST';
        const data = $(this.mapper.form).serializeArray();

        data.push({
            name: 'birth_date',
            value: `${$(this.mapper.monthField).val()}/${$(this.mapper.dayField).val()}/${$(this.mapper.yearField).val()}`,
        });

        if (! $(this.mapper.form).valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                this.notification.show('success', Translator.trans(`loyalty.message.success`, null, 'messages', LOCALE), true);
                loader.hide();
            },
            error: (error) => {
                loader.hide();
            }
        })
    }

    validateBeforeSave(formData) {
        for (let i in formData) {
            const value = formData[i].value;
            const name = formData[i].name;

            if (!value || value < 1) {
                this.notification.show('error', Translator.trans(`product.${name}`, null, 'validators', LOCALE), true);

                throw 'Validation failed.';
            }
        }
    }
}

export default LoyaltyPageHandler;