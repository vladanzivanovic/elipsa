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

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                this.cartDom.addProduct(response);
                $('#top_cart').click();
            },
            error: (error) => {
                console.log(error);
            }
        })
    }

    validateBeforeSave(formData) {
        for (let i in formData) {
            const value = formData[i].value;
            const name = formData[i].name;

            if (!value) {
                this.notification.show('error', Translator.trans(`product.${name}`, null, 'validators', LOCALE), true);

                return ;
            }
        }
    }
}

export default ProductPageHandler;