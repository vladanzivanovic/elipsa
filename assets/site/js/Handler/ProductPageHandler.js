import productPageMapper from "../Mapper/ProductPageMapper";
import cartDropDownDom from "../Dom/CartDropDownDom";
import NotificationService from "../../../js/NotificationService";
import orderApiHandler from "./Order/OrderApiHandler";
import loader from "../Dom/LoaderDom";
import orderStorageManipulator from "../Manipulator/OrderStorageManipulator";
import notificationApiHandler from "./NotificationApiHandler";

class ProductPageHandler {
    #orderApiHandler;
    #orderStorageManipulator;
    #productPageMapper;
    #notificationApiHandler;

    constructor() {
        if (!ProductPageHandler.instance) {
            this.#productPageMapper = productPageMapper;
            this.cartDom = cartDropDownDom;
            this.notification = NotificationService();
            this.#orderApiHandler = orderApiHandler;
            this.#orderStorageManipulator = orderStorageManipulator;
            this.#notificationApiHandler = notificationApiHandler;

            ProductPageHandler.instance = this;
        }

        return ProductPageHandler.instance;
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
                $(this.#productPageMapper.colorActive).data('color'),
                $(this.#productPageMapper.sizeActive).data('slug'),
                $(this.#productPageMapper.quantity).val()
            )

            if (order) {
                this.cartDom.manageDropDown(order);

                $('#scrollUp').click();
                $('#top_cart').click();
            }
        } catch (e) {}

        loader.hide();
    }

    async sizeNotification()
    {
        loader.show();

        try {
            const size = $(this.#productPageMapper.sizeActive).text();
            const email = $(this.#productPageMapper.notifyMeInput).val();
            const payload = {product: PRODUCT_ID, size};

            await this.#notificationApiHandler.notifyMe(NOTIFICATION_TYPES.TYPE_SIZE_AVAILABLE, payload, email);

            this.notification.show('success', Translator.trans('notification.success', null, 'messages', LOCALE), true);
        } catch (e) {
            let message = Translator.trans('generic_error', null, 'messages', LOCALE);

            if (e.responseJSON.error) {
                message = e.responseJSON.error.message;
            }

            this.notification.show('warning', message, true);

        }

        loader.hide();
    }
}

const productPageHandler = new ProductPageHandler();

Object.freeze(productPageHandler);

export default productPageHandler;
