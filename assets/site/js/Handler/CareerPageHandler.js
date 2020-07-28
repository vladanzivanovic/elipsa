import NotificationService from "../../../js/NotificationService";
import collaboratorPageMapper from "../Mapper/CollaboratorPageMapper";
import loader from "../Dom/LoaderDom";
import careerPageMapper from "../Mapper/CareerPageMapper";

class CareerPageHandler {
    constructor() {
        this.mapper = careerPageMapper;
        this.notification = NotificationService();
    }

    save(dateTimeService) {
        let urlRoute = Routing.generate('site_api.add_career');
        let type = 'POST';
        const data = new FormData($(this.mapper.form)[0]);

        data.set('birth_date',
            dateTimeService.getDateTimeString('/')
        );

        if (! $(this.mapper.form).valid()) {
            return false;
        }

        this.notification.remove();
        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: (response) => {
                this.notification.show('success', Translator.trans(`career.message.success`, null, 'messages', LOCALE), true);
                $(this.mapper.form)[0].reset();
                loader.hide();
            },
            error: (error) => {
                this.notification.show('error', Translator.trans(`career.message.already_applied`, null, 'messages', LOCALE), true);
                loader.hide();
            }
        })
    }
}

export default CareerPageHandler;