import ProductPageMapper from "../Mapper/ProductPageMapper";
import cartDropDownDom from "../Dom/CartDropDownDom";
import NotificationService from "../../../js/NotificationService";
import orderApiHandler from "./Order/OrderApiHandler";
import loader from "../Dom/LoaderDom";

class ProductPageHandler {
    #orderApiHandler;

    constructor() {
        this.mapper = ProductPageMapper;
        this.cartDom = cartDropDownDom;
        this.notification = NotificationService();
        this.#orderApiHandler = orderApiHandler;
    }

    async save()
    {
        loader.show();

        if (!localStorage.getItem('order')) {
            const order = await this.#orderApiHandler.create();

            localStorage.setItem('order', order.token);
        }

        try {
            const order = await this.#orderApiHandler.manageProduct(
                $('.color-btn.active').data('color'),
                $('.size-btn.active').text(),
                this.mapper.quantity.val()
            )

            this.cartDom.manageDropDown(order);

            $('#scrollUp').click();
            $('#top_cart').click();
        } catch {}

        loader.hide();
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
