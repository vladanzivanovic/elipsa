import NotificationService from "../../../js/NotificationService";
import collaboratorPageMapper from "../Mapper/CollaboratorPageMapper";
import loader from "../Dom/LoaderDom";

class CollaboratorPageHandler {
    constructor() {
        this.mapper = collaboratorPageMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = Routing.generate('site_api.add_collaborator');
        let type = 'POST';
        const data = new FormData($(this.mapper.form)[0]);

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
                this.notification.show('success', Translator.trans(`collaborator.message.success`, null, 'messages', LOCALE), true);
                loader.hide();
            },
            error: (error) => {
                this.notification.show('error', Translator.trans(`collaborator.message.already_applied`, null, 'messages', LOCALE), true);
                loader.hide();
            }
        })
    }
}

export default CollaboratorPageHandler;