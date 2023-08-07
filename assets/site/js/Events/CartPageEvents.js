import cartPageMapper from "../Mapper/CartPageMapper";
import CartHandler from "../Handler/CartHandler";

class CartPageEvents {
    #pageMapper;
    #cartHandler;

    constructor() {
        if (!CartPageEvents.instance) {
            this.#pageMapper = cartPageMapper;
            this.#cartHandler = new CartHandler();

            CartPageEvents.instance = this;
        }

        return CartPageEvents.instance;
    }

    registerEvents() {
        $(this.#pageMapper.promoCouponAddBtn).on('click touchend', async e => {
            e.preventDefault();
            e.stopPropagation();

            await this.#cartHandler.manageCoupon();
        });

        $(this.#pageMapper.promoCouponRemoveBtn).on('click touchend', async e => {
            e.preventDefault();
            e.stopPropagation();

            await this.#cartHandler.manageCoupon(true);
        });

        $(this.#pageMapper.updateBtn).on('click touchend', async e => {
            e.preventDefault();
            e.stopPropagation();

            await this.#cartHandler.updateProducts();
        });

        $(document).on('click touchend', this.#pageMapper.removeProduct, async e => {
            e.preventDefault();
            e.stopPropagation();

            console.log(e);

            await this.#cartHandler.removeProduct(e);
        });
    }
}

const cartPageEvents = new CartPageEvents();

Object.freeze(cartPageEvents);

export default cartPageEvents;
