import AppHelperService from "../../../../js/Helper/AppHelperService";
import couponsEditMapper from "../../Mapper/CouponsEditMapper";
import CouponsDataTables from "../../Services/DataTables/CouponsDataTables";
import toastrService from "../../../../js/Services/ToastrService";
import couponApiHandler from "./CouponApiHandler";

class CouponHandler {
    #mapper;
    #apiHandler;
    #notification;

    constructor() {
        this.#mapper = couponsEditMapper;
        this.#apiHandler = couponApiHandler;
        this.#notification = toastrService;
    }

    async save() {
        this.#notification.showLoadingMessage();

        try {
            await this.#apiHandler.save();

            AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.coupons'));
        } catch (error) {
            console.log(error);
            this.#notification.error(Translator.trans('generic_error', null, 'messages', LOCALE), true);
        }
    }

    remove(id) {
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_coupon_api', {id}),
            dataType: 'json',
            success: () => {
                CouponsDataTables().reload();
                this.#notification.remove();
            },
            error: jxHR => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE), true);
            }
        })
    }
}

export default CouponHandler;
