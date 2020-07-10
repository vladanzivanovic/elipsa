import NotificationService from "../../../js/NotificationService";
import AppHelperService from "../../../js/Helper/AppHelperService";
import DropZoneService from "../../../js/Services/DropZoneService";
import HomeBannersDataTables from "../Services/DataTables/HomeBannersDataTables";
import couponsEditMapper from "../Mapper/CouponsEditMapper";
import CouponsDataTables from "../Services/DataTables/CouponsDataTables";

class CouponHandler {
    constructor() {
        this.mapper = couponsEditMapper;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = AppHelperService.generateLocalizedUrl('admin.add_coupon_api');
        let type = 'POST';
        const data = this.mapper.form.serializeArray();

        if (! this.mapper.form.valid()) {
            return false;
        }

        if (IS_EDIT) {
            urlRoute = AppHelperService.generateLocalizedUrl('admin.edit_coupon_api', {id: ID});
            type = 'PUT';
        }

        this.notification.showLoadingMessage();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.coupons'));
            },
            error: error => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }

    remove(id) {
        this.notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_coupon_api', {id}),
            dataType: 'json',
            success: () => {
                CouponsDataTables().reload();
                this.notification.remove();
            },
            error: jxHR => {
                this.notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default CouponHandler;