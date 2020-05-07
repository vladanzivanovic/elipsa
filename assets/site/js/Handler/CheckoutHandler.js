import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";
import FormHelperService from "../../../js/Helper/FormHelperService";
import loader from "../Dom/LoaderDom";

class CheckoutHandler {
    constructor() {
        this.mapper = checkoutPageMapper;
    }

    save() {
        const data = this.mapper.form.serializeArray();

        if (! this.mapper.form.valid()) {
            return false;
        }

        loader.show();

        $.ajax({
            type: 'PUT',
            url: AppHelperService.generateLocalizedUrl('site_api.complete_order'),
            data: FormHelperService.sanitize(data),
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('site.checkout_completed_successful'));

                loader.hide();
            },
            error: error => {
                loader.hide();
            }
        })
    }
}

export default CheckoutHandler;