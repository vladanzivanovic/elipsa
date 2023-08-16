import ProductPageMapper from "../Mapper/ProductPageMapper";
import cartDropDownDom from "../Dom/CartDropDownDom";
import NotificationService from "../../../js/NotificationService";
import orderApiHandler from "./Order/OrderApiHandler";
import loader from "../Dom/LoaderDom";
import orderStorageManipulator from "../Manipulator/OrderStorageManipulator";

class ProductPageHandler {
    #orderApiHandler;
    #orderStorageManipulator;

    constructor() {
        this.mapper = ProductPageMapper;
        this.cartDom = cartDropDownDom;
        this.notification = NotificationService();
        this.#orderApiHandler = orderApiHandler;
        this.#orderStorageManipulator = orderStorageManipulator;
    }

    async save()
    {
        const orderToken = this.#orderStorageManipulator.getOrderToken();

        loader.show();

        if (!orderToken) {
            const order = await this.#orderApiHandler.create();

            this.#orderStorageManipulator.setOrder(order.token, order);
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
}

export default ProductPageHandler;
