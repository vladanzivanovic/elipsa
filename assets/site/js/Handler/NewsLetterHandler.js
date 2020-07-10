import NotificationService from "../../../js/NotificationService";
import loader from "../Dom/LoaderDom";
import coreMapper from "../Mapper/CoreMapper";

class NewsLetterHandler {
    constructor() {
        this.mapper = coreMapper;
        this.notification = NotificationService();
    }

    addUser(form, isModal) {
        let urlRoute = Routing.generate(`site_api.news_letter_add_user.${LOCALE}`);
        let type = 'POST';
        const data = $(form).serializeArray();

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                this.notification.show('success', Translator.trans(`newsletter.success.add.message`, null, 'messages', LOCALE), true);

                if (isModal) {
                    $(this.mapper.newsLetterCloseBtn).click();
                }

                $(form)[0].reset();

                loader.hide();
            },
            error: (error) => {
                const errors = error.responseJSON;
                loader.hide();

                if (errors.hasOwnProperty('message')) {
                    this.notification.show('error', errors.message, true);

                    return;
                }
            }
        })
    }
}

export default NewsLetterHandler;