import cartPageMapper from "../Mapper/CartPageMapper";
import orderApiChecker from "../Checker/OrderApiChecker";

class CartPageDom {
    #mapper;
    #orderApiChecker;

    constructor() {
        if (!CartPageDom.instance) {
            this.#mapper = cartPageMapper;
            this.#orderApiChecker = orderApiChecker;

            CartPageDom.instance = this;
        }

        return CartPageDom.instance;
    }

    manageOrderData(order)
    {
        const hasAvailableProducts = this.#orderApiChecker.hasAvailableProducts(order.products);

        $(this.#mapper.total).text(`${order.total.amount} ${order.total.currency}`);
        $(this.#mapper.promoCouponPrice).text(`${order.promotion.percentage} %`);
        $(this.#mapper.shippingPrice).text(`${order.shipping.price.amount}  ${order.shipping.price.currency}`);
        $(this.#mapper.totalShipping).text(`${order.total_with_shipping.amount}  ${order.total_with_shipping.currency}`);

        this.#toggleUpdateButton(hasAvailableProducts);
        this.#toggleNextStepButton(hasAvailableProducts);
        this.#togglePromoCouponHolder(order.promotion);
        this.#togglePromoBox(order.promotion, hasAvailableProducts);

        for (let orderProduct of order.products) {
            this.#manageProduct(orderProduct);
        }

        this.#removeDeletedProductsFromTable(order.products);
    }

    resetCartPage()
    {
        $(this.#mapper.total).text(`0`);
        $(this.#mapper.promoCouponPrice).text(`0`);
        $(this.#mapper.shippingPrice).text(`0`);
        $(this.#mapper.totalShipping).text(`0`);

        this.#toggleUpdateButton(false);
        this.#toggleNextStepButton(false);
        this.#togglePromoCouponHolder([]);
        this.#togglePromoBox([], false);

        this.#removeDeletedProductsFromTable([]);

        $(`${this.#mapper.productTable} tbody`).append(`<tr><td colspan="6">${Translator.trans('cart.empty', null, 'messages', LOCALE)}</td></tr>`);
    }

    #manageProduct(orderProduct)
    {
        const productTable = this.#mapper.productTable;

        const productRow = $(`tr[data-id="${orderProduct.id}"]`);

        if (0 === productRow.length) {
            const row = this.#createProductTableRow(orderProduct);

            $(`${productTable} tbody`).append(row);

            return;
        }

        this.#updateProductRow(orderProduct, productRow);
    }

    #createProductTableRow(orderProduct)
    {
        let isSoldHtml = '';
        let discountPriceHtml = '';
        let $sizeHtml = '';

        const isDiscounted = 0 < Object.keys(orderProduct.discount).length;
        const isPromoPrice = 0 < Object.keys(orderProduct.promotion_price).length;

        if (orderProduct.is_sold) {
            isSoldHtml = `<p class="cart_product_name product-name-sold">(${Translator.trans('product.unavailable', null, 'messages', LOCALE)})</p>`;
        }

        if (isPromoPrice) {
            discountPriceHtml = `
                    <span class="product_price discount">
                        ${ orderProduct.promotion_price.amount } ${ orderProduct.promotion_price.currency }
                    </span>`;
        }
        else if (isDiscounted) {
            discountPriceHtml = `
                    <span class="product_price discount">
                        ${ orderProduct.discount.price.amount } ${ orderProduct.discount.price.currency }
                    </span>`;
        }

        if (orderProduct.size !== NO_SIZE) {
            $sizeHtml = `<p className="cart_product_size">
                <span
                    className="text-label">${Translator.trans('email.order.table.size', null, 'messages', LOCALE)}:</span>
                ${orderProduct.size}
            </p>`;
        }

        return `
            <tr class="table-row1 ${orderProduct.is_sold ? 'product-sold' : ''}" data-id="${ orderProduct.id }">
                <td class="cart_product_image_value">
                    <div class="pro-photo-checkout">
                        <img src="${ orderProduct.image.file }" alt="" />
                    </div>
                </td>
                <td class="cart_product_name_value">
                    <p class="cart_product_name">
                        <a href="#" class="product-title">${ orderProduct.translation.title }</a>
                    </p>
                    ${$sizeHtml}
                    ${isSoldHtml}
                </td>
                <td class="cart_product_price_value">
                    <span class="product_price ${ isPromoPrice || isDiscounted ? 'old-price' : '' }">
                        ${ orderProduct.price.amount } ${ orderProduct.price.currency }
                    </span>
                    ${discountPriceHtml}
                </td>
                <td class="cart_product_quantity_value">
                    <div class="product-quantity-t">
                        <input type="number" name="quantity" value="${ orderProduct.quantity }" min="1" />
                    </div>
                </td>
                <td class="cart_product_total_value">
                    <span class="product_price">
                        ${ orderProduct.total.amount } ${ orderProduct.total.currency }
                    </span>
                </td>
                <td class="cart_product_name">
                    <a href="#" class="remove-product">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
        `;
    }

    #updateProductRow(orderProduct, row)
    {
        const isDiscounted = 0 < Object.keys(orderProduct.discount).length;
        const isPromoPrice = 0 < Object.keys(orderProduct.promotion_price).length;

        row.find('.product-title').text(orderProduct.translation.title);
        row.find('.cart_product_total_value span').text(`${orderProduct.total.amount} ${orderProduct.total.currency}`);
        row.find('.product-quantity-t input').val(orderProduct.quantity);
        row.find('.cart_product_total_value span').removeClass('old-price').text(`${orderProduct.total.amount} ${orderProduct.total.currency}`);

        if (isPromoPrice) {
            if (0 === row.find('.cart_product_price_value .old-price').length) {
                row.find('.cart_product_price_value span').addClass('old-price').text(`${orderProduct.price.amount} ${orderProduct.price.currency}`);

                row.find('.cart_product_price_value').append(`
                <span class="product_price discount">
                    ${ isPromoPrice ? orderProduct.promotion_price.amount : orderProduct.discount.price.amount } ${ isPromoPrice ? orderProduct.promotion_price.currency : orderProduct.discount.price.currency }
                </span>`
                );
            } else {
                row.find('.cart_product_price_value .old-price').text(`${orderProduct.price.amount} ${orderProduct.price.currency}`);
                row.find('.cart_product_price_value .discount').text(`
                    ${ isPromoPrice ? orderProduct.promotion_price.amount : orderProduct.discount.price.amount } ${ isPromoPrice ? orderProduct.promotion_price.currency : orderProduct.discount.price.currency }
                `);
            }
        }
        else if (isDiscounted) {
            row.find('.cart_product_price_value .old-price').text(`${orderProduct.price.amount} ${orderProduct.price.currency}`);
            row.find('.cart_product_price_value .discount').text(`${orderProduct.discount.price.amount} ${orderProduct.discount.price.currency}`);
        }
        else {
            row.find('.cart_product_price_value span').removeClass('old-price').text(`${orderProduct.price.amount} ${orderProduct.price.currency}`);
            row.find('.cart_product_price_value .discount').remove();
        }
    }

    #removeDeletedProductsFromTable(orderProducts)
    {
        const tableBody = $(`${this.#mapper.productTable} tbody`);
        const orderProductIds = [];

        for (let orderProduct of orderProducts) {
            orderProductIds.push(orderProduct.id);
        }

        for (let tr of tableBody.children('tr')) {
            const productRowId = $(tr).data('id');

            if (orderProductIds.includes(productRowId)) {
                continue;
            }

            $(tr).remove();
        }
    }

    #toggleUpdateButton(hasAvailableProducts)
    {
        $(this.#mapper.updateBtn).removeClass('disabled');

        if (false === hasAvailableProducts) {
            $(this.#mapper.updateBtn).addClass('disabled');
        }
    }

    #toggleNextStepButton(hasAvailableProducts)
    {
        $(this.#mapper.nextStepBtn).removeClass('disabled');

        if (false === hasAvailableProducts) {
            $(this.#mapper.nextStepBtn).addClass('disabled');
        }
    }

    #togglePromoCouponHolder(promoCoupon)
    {
        $(this.#mapper.promoCouponHolder).removeClass('hide');

        if (0 === promoCoupon.length) {
            $(this.#mapper.promoCouponHolder).addClass('hide');
        }
    }

    #togglePromoBox(orderPromotion, hasAvailableProducts)
    {
        $(this.#mapper.promoCouponAddBtn).removeClass('disabled');
        $(this.#mapper.promoCouponRemoveBtn).addClass('disabled');

        if (false === hasAvailableProducts) {
            $(this.#mapper.promoCouponAddBtn).addClass('disabled');
        }

        if(0 !== orderPromotion.length) {
            $(this.#mapper.promoCouponViewBox).removeClass('hide');
            $(this.#mapper.promoCouponAddBox).addClass('hide');

            $(this.#mapper.promoCouponAddBtn).addClass('disabled');
            $(this.#mapper.promoCouponRemoveBtn).removeClass('disabled');

            $(this.#mapper.promoCouponBoxText).text(orderPromotion.code);

            return;
        }

        $(this.#mapper.promoCouponViewBox).addClass('hide');
        $(this.#mapper.promoCouponAddBox).removeClass('hide');
    }
}

const cartPageDom = new CartPageDom();

Object.freeze(cartPageDom);

export default cartPageDom;
