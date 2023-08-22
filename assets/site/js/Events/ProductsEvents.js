import WishListHandler from "../Handler/WishListHandler";
import productsMapper from "../Mapper/ProductsMapper";
import shopPageDom from "../Dom/ShopPageDom";

class ProductsEvents {
    #productsMapper;
    #wishListHandler;
    #shopPageDom;

    constructor() {
        this.#productsMapper = productsMapper;
        this.#wishListHandler = new WishListHandler();
        this.#shopPageDom = shopPageDom;

        this.#registerEvents();
    }

    #registerEvents() {
        $(document).on('click touchend', this.#productsMapper.toggleWishListBtn, e => {
            e.preventDefault();
            e.stopPropagation();

            this.#wishListHandler.toggle($(e.currentTarget))
                .then((response, textStatus, xhr) => {
                    this.#shopPageDom.toggleWish($(e.currentTarget), xhr.status === 201);
                })
        });
    }
}

export default ProductsEvents;
