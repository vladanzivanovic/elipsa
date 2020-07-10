import DropZoneService from "../../../js/Services/DropZoneService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import NotificationService from "../../../js/NotificationService";
import BlogDataTables from "../Services/DataTables/BlogDataTables";
import aboutUsPageMapper from "../Mapper/AboutUsPageMapper";

class AboutUsHandler {
    constructor() {
        this.mapper = aboutUsPageMapper;
        this.notification = NotificationService();
    }

    save()
    {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.set_about_us_api');
        let type = 'POST';
        const data = $(this.mapper.form).serializeArray();

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
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.about_us_page'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default AboutUsHandler;