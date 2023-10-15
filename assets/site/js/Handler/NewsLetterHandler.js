import NotificationService from "../../../js/NotificationService";
import loader from "../Dom/LoaderDom";
import coreMapper from "../Mapper/CoreMapper";
import FormHelperService from "../../../js/Helper/FormHelperService";
import newsLetterMapper from "../Mapper/NewsLetterMapper";
import toastrService from "../../../js/Services/ToastrService";

class NewsLetterHandler {
    #mapper;
    #notification;

    constructor() {
        this.#mapper = newsLetterMapper;
        this.#notification = toastrService;
    }

    addUser(form) {
        let urlRoute = Routing.generate(`site_api.news_letter_add_user`);
        let type = 'POST';
        const data = FormHelperService.formToJson($(form));

        if (! $(form).valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data: JSON.stringify(data),
            dataType: 'json',
            contentType: 'application/json',
            headers: {
                'Content-Language': LOCALE,
            },
            success: (response) => {
                this.#notification.success(Translator.trans(`newsletter.success.add.message`, null, 'messages', LOCALE));

                $(this.#mapper.newsLetterCloseBtn).click();

                $(form)[0].reset();

                loader.hide();

                if (!response.hasLoyalty) {
                    location.href = Routing.generate(`site.loyalty.${LOCALE}`);
                }
            },
            error: (error) => {
                const errors = error.responseJSON;
                loader.hide();

                if (errors.hasOwnProperty('message')) {
                    this.#notification.error(errors.message);

                    return;
                }
            }
        })
    }
}

export default NewsLetterHandler;
