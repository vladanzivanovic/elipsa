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

        $.ajax({
            type: 'PUT',
            url: AppHelperService.generateLocalizedUrl('site_api.complet_order'),
            data: FormHelperService.sanitize(data),
            dataType: 'json',
            success: response => {

            },
            error: error => {

            }
        })
    }
}

export default CheckoutHandler;