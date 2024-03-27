import AppHelperService from "../../../../js/Helper/AppHelperService";
import couponsEditMapper from "../../Mapper/CouponsEditMapper";
import CouponsDataTables from "../../Services/DataTables/PromotionDataTables";
import toastrService from "../../../../js/Services/ToastrService";
import couponApiHandler from "./CouponApiHandler";
import promotionDataTables from "../../Services/DataTables/PromotionDataTables";

class CouponHandler {
    #mapper;
    #apiHandler;
    #notification;
    #dataTable;

    constructor() {
        this.#mapper = couponsEditMapper;
        this.#apiHandler = couponApiHandler;
        this.#notification = toastrService;
        this.#dataTable = promotionDataTables;
    }

    async save() {
        if (! $(this.#mapper.form).valid()) {
            return false;
        }

        try {
            this.#notification.showLoadingMessage();

            await this.#apiHandler.save();

            if (!IS_EDIT) {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('admin.promotions'));

                return ;
            }

            AppHelperService.redirect('reload');
        } catch (error) {
            this.#notification.error(Translator.trans('generic_error', null, 'messages', LOCALE));
        }
    }

    remove(id) {
        this.#notification.showLoadingMessage();

        $.ajax({
            type: 'DELETE',
            url: AppHelperService.generateLocalizedUrl('admin.remove_promotion_api', {id}),
            dataType: 'json',
            success: () => {
                this.#dataTable.reload();
                this.#notification.remove();
            },
            error: jxHR => {
                this.#notification.show('error', Translator.trans('generic_error', null, 'messages', LOCALE));
            }
        })
    }
}

export default CouponHandler;
