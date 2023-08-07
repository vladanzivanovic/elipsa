import CartMapper from "../Mapper/CartDropDownMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";

class CartDropDownDom {
    constructor() {
        if (!CartDropDownDom.instance) {
            this.mapper = CartMapper;

            CartDropDownDom.instance = this;
        }

        return CartDropDownDom.instance;
    }

    manageDropDown(order)
    {
        localStorage.setItem('orderData', JSON.stringify(order));

        for (let product of order.products) {
            this.manageProduct(product);
        }

        this.setOrderData(order);
    }

    manageProduct(orderProduct)
    {
        let productDom = this.template();
        let existingProduct = $(`.single-product[data-id="${orderProduct.id}"]`);

        const isDiscounted = 0 < Object.keys(orderProduct.discount).length;

        if (orderProduct.is_sold) {
            return;
        }

        orderProduct.amount = isDiscounted ? orderProduct.discount.price.amount : orderProduct.price.amount;
        orderProduct.currency = isDiscounted ? orderProduct.discount.price.currency : orderProduct.price.currency;
        orderProduct.title = orderProduct.translation.title;
        orderProduct.image_link = orderProduct.image.file;
        orderProduct.product_id = orderProduct.id;
        orderProduct.link = Routing.generate(`site.product_page.${LOCALE}`, {'slug': orderProduct.translation.slug});
        orderProduct.slug = orderProduct.translation.slug;

        productDom = productDom.replace(/image_link|product_id|title|quantity|amount|currency|size|link/gi, search => {
            return orderProduct[search];
        });

        existingProduct.replaceWith(productDom);

        if (existingProduct.length === 0) {
            this.mapper.productList.append(productDom);

            // this.mapper.productLength.text(parseInt(this.mapper.productLength.text()) + 1);
        }
    }

    setOrderData(order)
    {
        let productsCounter = 0;

        this.mapper.total.data('cartTotal', order.total.unformatted_amount);
        this.mapper.total.text(order.total.amount);

        for (let product of order.products) {
            if (product.is_sold) {
                continue;
            }

            productsCounter++;
        }

        this.mapper.productLength.text(productsCounter);
    }

    addProduct(product) {
        let productDom = this.template();
        let total = parseInt(this.mapper.total.data('cartTotal'));

        product.product_price_text = AppHelperService.formatPrice(product.product_price);

        productDom = productDom.replace(/image_link|product_id|product_name|product_quantity|product_price_text|product_price/gi, search => {
            return product[search];
        });

        let existingProduct = $(`.single-product[data-id="${product['product_id']}"]`);

        if (existingProduct.length > 0) {
            existingProduct.replaceWith(productDom);

            const oldPrice = parseInt($('.quantity-number', existingProduct).data('cartQuantity')) * parseInt($('.mcp-pro-price', existingProduct).data('cartPrice'));

            total -= oldPrice;
        }

        if (existingProduct.length === 0) {
            this.mapper.productList.append(productDom);

            this.mapper.productLength.text(parseInt(this.mapper.productLength.text()) + 1);
        }

        const price = product.product_price * product.product_quantity;
        total += price;
        this.mapper.total.data('cartTotal', total);
        this.mapper.total.text(AppHelperService.formatPrice(total));
    }

    removeProduct(id) {
        // let total = parseInt(this.mapper.total.data('cartTotal'));
        const product = $(`.single-product[data-id="${id}"]`);
        // const oldPrice = parseInt($('.quantity-number', product).data('cartQuantity')) * parseInt($('.mcp-pro-price', product).data('cartPrice'));

        product.remove();

        // total -= oldPrice;
        //
        // this.mapper.total.data('cartTotal', total);
        // this.mapper.total.text(AppHelperService.formatPrice(total));
        // this.mapper.productLength.text(parseInt(this.mapper.productLength.text()) - 1);
    }

    template() {
        return `<div class="single-mcp clearfix single-product" data-id="product_id">
                    <div class="single-mcp-img">
                        <img src="image_link" alt="">
                    </div>
                    <div class="single-mcp-content">
                        <a class="mcp-product-name" href="link">title (size)</a>
                        <span class="mcp-pro-quantity"><span class="quantity-number" data-cart-quantity="quantity">quantity</span> x <span class="mcp-pro-price" data-cart-price="amount">amount currency</span></span>
                    </div>
                    <a href="javascript:void(0)" class="mcp-pro-delete"><i class="fa fa-times"></i></a>
                </div>`;
    }
}

const cartDropDownDom = new CartDropDownDom();

Object.freeze(cartDropDownDom);

export default cartDropDownDom;
