import cartPageMapper from "../Mapper/CartPageMapper";
import CartHandler from "../Handler/CartHandler";

class CartPageController {
    constructor() {
        this.mapper = cartPageMapper;
        this.hander = new CartHandler();

        this.registerEvents();
    }

    registerEvents() {
        this.mapper.updateBtn.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.hander.update();
        });

        this.mapper.removeProduct.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            const id = $(e.currentTarget).data('id');

            this.hander.remove(id, $(e.currentTarget).closest('tr'));
        })

        this.mapper.promoCouponBtn.on('click touchend', e => {
            e.preventDefault();
            e.stopPropagation();

            this.hander.setCoupon();
        });
    }
}

export default CartPageController