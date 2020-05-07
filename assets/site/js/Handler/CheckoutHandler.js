import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";
import FormHelperService from "../../../js/Helper/FormHelperService";

class CheckoutHandler {
    constructor() {
        this.mapper = checkoutPageMapper;
    }

    save() {
        const data = this.mapper.form.serializeArray();
        const paymentType = $('input[name="payment_type"]:checked').val();

        data.push({
            name: 'payment_type',
            value: paymentType,
        });

        if (! this.mapper.form.valid()) {
            return false;
        }

        $.ajax({
            type: 'PUT',
            url: AppHelperService.generateLocalizedUrl('site_api.complet_order'),
            data: FormHelperService.sanitize(data),
            dataType: 'json',
            success: response => {
                AppHelperService.redirect(AppHelperService.generateLocalizedUrl('site.checkout_completed_successful'));
            },
            error: error => {

            }
        })
    }
}

export default CheckoutHandler;