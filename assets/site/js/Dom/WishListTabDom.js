import AppHelperService from "../../../js/Helper/AppHelperService";
import myAccountPageMapper from "../Mapper/MyAccountPageMapper";

class WishListTabDom {
    constructor() {
        if (!WishListTabDom.instance) {
            this.mapper = myAccountPageMapper;

            WishListTabDom.instance = this;
        }

        return WishListTabDom.instance;
    }

    generateWishList(wishes) {
        let html = '';

        for(let i in wishes) {
            let wish = wishes[i];
            let price = wish.discount > 0 ? wish.discount : wish.price;

            html += `<tr class="table-row1">
                        <td class="cart_product_image_value">
                            <div class="wishlist_img">
                                <img src="${wish.image_link}" alt="${wish.title}" />
                            </div>
                        </td>
                        <td class="cart_product_name_value">
                            <p class="wishlist_product_name">
                                <a href="${wish.product_link}">${wish.title}</a>
                            </p>
                        </td>
                        <td class="wishlist_description">
                            <span class="wishlist-desc">${wish.quantity}</span>
                        </td>
                        <td class="wishlist_price">
                            <span class="product_price">${price} RSD</span>
                        </td>
                        <td class="wishlist_color">
                            <span class="product_color_box" style="background: ${wish.hex};"></span>
                        </td>
                        <td class="wishlist_price">
                            <span class="product_price">${wish.size}</span>
                        </td>
                        <td class="wishlist_remove">
                            <a href="#">
                                <i class="fa fa-times"></i>
                            </a>
                        </td>
                    </tr>`;
        }

        return html;
    };

    listData(data) {
        let html = '';

        for(let i in data) {
            html += `<li><a href="#">${data[i]}</a></li>`;
        }

        return html;
    }
}

const wishTabDom = new wishTabDom();
Object.freeze(wishTabDom);

export default wishTabDom;