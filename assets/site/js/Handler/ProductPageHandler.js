import AppHelperService from "../../../js/Helper/AppHelperService";
import ProductPageMapper from "../Mapper/ProductPageMapper";
import CartDom from "../Dom/CartDropDownDom";
import NotificationService from "../../../js/NotificationService";

class ProductPageHandler {
    constructor() {
        this.mapper = ProductPageMapper;
        this.cartDom = CartDom;
        this.notification = NotificationService();
    }

    save() {
        let urlRoute = AppHelperService.generateLocalizedUrl('site_api.set_order', {slug: SLUG});
        let type = 'POST';
        const data = [
            {
                name: 'color',
                value: $('.color-btn.active').data('color'),
            },
            {
                name: 'size',
                value: $('.size-btn.active').text(),
            },
            {
                name: 'quantity',
                value: this.mapper.quantity.val(),
            },
        ];

        this.validateBeforeSave(data);

        $('#page-loader').fadeOut('slow', function() { $(this).removeClass('hide'); });


        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                this.cartDom.addProduct(response);
                $('#scrollUp').click();
                $('#top_cart').click();
                $('#page-loader').addClass('hide');
            },
            error: (error) => {
                $('#page-loader').addClass('hide');
            }
        })
    }

    validateBeforeSave(formData) {
        for (let i in formData) {
            const value = formData[i].value;
            const name = formData[i].name;

            if (!value || value < 1) {
                this.notification.show('error', Translator.trans(`product.${name}`, null, 'validators', LOCALE), true);

                throw 'Validation failed.';
            }
        }
    }
}

export default ProductPageHandler;