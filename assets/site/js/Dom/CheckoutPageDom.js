import checkoutPageMapper from "../Mapper/CheckoutPageMapper";

class CheckoutPageDom {
    #pageMapper;

    constructor() {
        if(!CheckoutPageDom.instance) {
            this.#pageMapper = checkoutPageMapper;

            CheckoutPageDom.instance = this;
        }

        return CheckoutPageDom.instance;
    }

    manageOrderData(order)
    {
        this.#togglePromoCouponHolder(order.promotion);

        this.#setOrderData(order);

        for (let orderProduct of order.products) {
            this.#manageProduct(orderProduct);
        }
    }

    #manageProduct(orderProduct)
    {
        // const productTable = this.#mapper.productTable;
        //
        // const productRow = $(`tr[data-id="${orderProduct.id}"]`);
        //
        // if (0 === productRow.length) {
            const productElement = this.#createProductElement(orderProduct);

            $(`${this.#pageMapper.productList}`).append(productElement);

            // return;
        // }
        //
        // this.#updateProductRow(orderProduct, productRow);
    }

    #createProductElement(orderProduct)
    {
        const isDiscounted = 0 < Object.keys(orderProduct.discount).length;
        const isPromoPrice = 0 < Object.keys(orderProduct.promotion_price).length;
        let price = orderProduct.price;

        if (isPromoPrice) {
            price = orderProduct.promotion_price;
        } else if (isDiscounted) {
            price = orderProduct.discount.price;
        }

        return `
            <li class="clearfix">
                ${ orderProduct.translation.title }  x ${ orderProduct.quantity } 
                <span>${ price.amount } ${ price.currency }</span></li>
        `;
    }

    #setOrderData(order)
    {
        $(`${ this.#pageMapper.productsTotal } span`).text(`${ order.total.amount } ${ order.total.currency }`);
        $(`${ this.#pageMapper.shippingPrice } span`).text(`${ order.shipping.price.amount } ${ order.shipping.price.currency }`);
        $(`${ this.#pageMapper.totalWithShipping } span`).text(`${ order.total_with_shipping.amount } ${ order.total_with_shipping.currency }`);

        if (0 !== order.promotion.length) {
            $(`${ this.#pageMapper.promoPrice } span`).text(`${order.promotion.percentage} %`);
        }
    }

    #togglePromoCouponHolder(promoCoupon)
    {
        $(this.#pageMapper.promoPrice).removeClass('hide');

        if (0 === promoCoupon.length) {
            $(this.#pageMapper.promoPrice).addClass('hide');
        }
    }
}

const checkoutPageDom = new CheckoutPageDom();

Object.freeze(checkoutPageDom);

export default checkoutPageDom;
