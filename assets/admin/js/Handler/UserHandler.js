import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DropZoneService from "../../../js/Services/DropZoneService";
import HomeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";
import UsersDataTables from "../Services/DataTables/UsersDataTables";
import userPageMapper from "../Mapper/UserPageMapper";
import FormHelperService from "../../../js/Helper/FormHelperService";

class UserHandler {
    constructor() {
        this.mapper = userPageMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = Routing.generate('admin.add_user_api');
        let type = 'POST';
        const data = FormHelperService.sanitize($(this.mapper.form).serializeArray());

        if (IS_EDIT) {
            urlRoute = Routing.generate('admin.edit_user_api', {id: ID});
            type = 'PUT';
        }

        if (! $(this.mapper.form).valid()) {
            return false;
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(Routing.generate('admin.users'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    changeStatus(checkbox, id, status) {
        $.ajax({
            type: 'PATCH',
            'url': Routing.generate('admin.api_toggle_user_status', {id, status}),
            dataType: 'json',
            success: (response) => {
                checkbox.parentElement.firstElementChild.innerText = Translator.trans(response.text, null, 'messages', LOCALE);
            },
            error: () => {
                this.notification.show('error', Translator.trans('generic_error', null, 'message', LOCALE), true);
            }
        })
    }
}

export default UserHandler;