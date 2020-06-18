import NotificationService from "../../../js/NotificationService";
import loader from "../Dom/LoaderDom";
import coreMapper from "../Mapper/CoreMapper";

class NewsLetterHandler {
    constructor() {
        this.mapper = coreMapper;
        this.notification = NotificationService();
    }

    addUser() {
        let urlRoute = Routing.generate(`site_api.news_letter_add_user.${LOCALE}`);
        let type = 'POST';
        const data = $(this.mapper.newsLetterForm).serializeArray();

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                this.notification.show('success', Translator.trans(`newsletter.success.add.message`, null, 'messages', LOCALE), true);

                $(this.mapper.newsLetterCloseBtn).click();

                loader.hide();
            },
            error: (error) => {
                loader.hide();
            }
        })
    }
}

export default NewsLetterHandler;