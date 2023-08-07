import AppHelperService from "../../../js/Helper/AppHelperService";
import CartDom from "../Dom/CartDropDownDom";
import cartPageMapper from "../Mapper/CartPageMapper";
import NotificationService from "../../../js/NotificationService";
import loader from "../Dom/LoaderDom";
import orderApiHandler from "./Order/OrderApiHandler";
import cartPageManipulator from "../Manipulator/CartPageManipulator";
import orderApiChecker from "../Checker/OrderApiChecker";

class CartHandler {
    #orderApiHandler;
    #pageManipulator;
    #pageMapper;
    #orderApiChecker;

    constructor() {
        this.cartDom = CartDom;
        this.#pageMapper = cartPageMapper;
        this.notification = NotificationService();
        this.#orderApiHandler = orderApiHandler;
        this.#pageManipulator = cartPageManipulator;
        this.#orderApiChecker = orderApiChecker;
    }

    async removeProduct(event)
    {
        loader.show();

        try {
            let order;

            const productRow = $(event.currentTarget).parents('tr');

            order = await this.#orderApiHandler.removeProduct(productRow.data('id'));

            if(
                0 === order.products.length ||
                false === this.#orderApiChecker.hasAvailableProducts(order.products)
            ) {
                await this.#orderApiHandler.removeOrder();

                localStorage.removeItem('order');
                localStorage.removeItem('orderData');

                order = null;
            }

            this.#pageManipulator.updatePage(order);

        } catch (e) {
            let message = e.message;

            if (e.responseJSON.error) {
                message = e.responseJSON.error.message;
            }

            this.#pageManipulator.showError(message, 'coupon');
        }

        loader.hide();
    }

    async manageCoupon(removeCoupon = false) {
        loader.show();
        this.#pageMapper.promoCouponErrorText.empty();

        try {
            let order;

            if (true === removeCoupon) {
                const localOrder = JSON.parse(localStorage.getItem('orderData'));

                order = await this.#orderApiHandler.removeCoupon(
                    localOrder.promotion.code
                );
            } else {
                const couponCode = $(this.#pageMapper.promoCouponInput).val();

                if (0 === couponCode.length) {
                    throw new Error('field.not_blank');
                }

                order = await this.#orderApiHandler.setCoupon(couponCode);

                $(this.#pageMapper.promoCouponInput).val('');
            }

            this.#pageManipulator.updatePage(order);

        } catch (e) {
            let message = e.message;

            if (e?.responseJSON?.error) {
                message = e.responseJSON.error.message;
            }

            this.#pageManipulator.showError(message, 'coupon');
        }

        loader.hide();
    }

    async updateProducts()
    {
        loader.show();

        try {
            let order;

            const localOrder = JSON.parse(localStorage.getItem('orderData'));

            for(let orderProduct of localOrder.products) {
                const productRow = $(`tr[data-id="${orderProduct.id}"]`);

                const quantity = productRow.find('input[name="quantity"]').val();

                order = await this.#orderApiHandler.manageProduct(
                    orderProduct.color.id,
                    orderProduct.size.toString(),
                    quantity,
                    orderProduct.translation.slug
                );
            }

            this.#pageManipulator.updatePage(order);

        } catch (e) {
            let message = e.message;

            if (e.responseJSON.error) {
                message = e.responseJSON.error.message;
            }

            this.#pageManipulator.showError(message, 'coupon');
        }

        loader.hide();
    }
}

export default CartHandler;
