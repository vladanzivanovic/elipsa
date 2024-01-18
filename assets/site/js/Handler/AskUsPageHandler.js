import NotificationService from "../../../js/NotificationService";
import loyaltyPageMapper from "../Mapper/LoyaltyPageMapper";
import loader from "../Dom/LoaderDom";
import askUsPageMapper from "../Mapper/AskUsFormMapper";

class AskUsPageHandler {
    constructor() {
        this.mapper = askUsPageMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = Routing.generate(`site_api.ask_us.${LOCALE}`);
        let type = 'POST';
        const data = $(this.mapper.form).serializeArray();

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
                this.notification.show('success', Translator.trans(`ask_us.message.success`, null, 'messages', LOCALE), true);
                $(this.mapper.form)[0].reset();
                loader.hide();
            },
            error: (error) => {
                loader.hide();
            }
        })
    }
}

export default AskUsPageHandler;
