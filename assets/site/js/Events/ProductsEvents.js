import WishListHandler from "../Handler/WishListHandler";
import productsMapper from "../Mapper/ProductsMapper";
import shopPageDom from "../Dom/ShopPageDom";
import toastrService from "../../../js/Services/ToastrService";

class ProductsEvents {
    #productsMapper;
    #wishListHandler;
    #shopPageDom;
    #toastr;

    constructor() {
        this.#productsMapper = productsMapper;
        this.#wishListHandler = new WishListHandler();
        this.#shopPageDom = shopPageDom;
        this.#toastr = toastrService;

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
                .catch(e => {
                    let message = e.message;

                    if (e.responseJSON.error) {
                        message = e.responseJSON.error.message;
                    }

                    this.#toastr.error(message);
                })
        });
    }
}

export default ProductsEvents;
