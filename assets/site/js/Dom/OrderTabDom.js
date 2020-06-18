import AppHelperService from "../../../js/Helper/AppHelperService";
import myAccountPageMapper from "../Mapper/MyAccountPageMapper";

class OrderTabDom {
    constructor() {
        if (!OrderTabDom.instance) {
            this.mapper = myAccountPageMapper;

            OrderTabDom.instance = this;
        }

        return OrderTabDom.instance;
    }

    generateOrderList(orders) {
        let html = '';

        for(let i in orders) {
            let order = orders[i];
            let price = order.discount > 0 ? order.discount : order.price;

            html += `<tr class="table-row1">
                        <td class="cart_product_image_value">
                            <div class="wishlist_img">
                                <img src="${order.image_link}" alt="${order.title}" />
                            </div>
                        </td>
                        <td class="cart_product_name_value">
                            <p class="wishlist_product_name">
                                <a href="${order.product_link}">${order.title}</a>
                            </p>
                        </td>
                        <td class="wishlist_description">
                            <span class="wishlist-desc">${order.quantity}</span>
                        </td>
                        <td class="wishlist_price">
                            <span class="product_price">${price} RSD</span>
                        </td>
                        <td class="wishlist_color">
                            <span class="product_color_box" style="background: ${order.hex};"></span>
                        </td>
                        <td class="wishlist_price">
                            <span class="product_price">${order.size}</span>
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

const orderTabDom = new OrderTabDom();
Object.freeze(orderTabDom);

export default orderTabDom;