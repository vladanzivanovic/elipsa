import CartMapper from "../Mapper/CartDropDownMapper";

class CartDropDownDom {
    constructor() {
        if (!CartDropDownDom.instance) {
            this.mapper = CartMapper;

            CartDropDownDom.instance = this;
        }

        return CartDropDownDom.instance;
    }

    addProduct(product) {
        let productDom = this.template();
        let total = parseInt(this.mapper.total.text());

        productDom = productDom.replace(/image_link|product_id|product_name|product_quantity|product_price/gi, search => {
            return product[search];
        });

        let existingProduct = $(`.single-product[data-id="${product['product_id']}"]`);

        if (existingProduct.length > 0) {
            existingProduct.replaceWith(productDom);

            const oldPrice = parseInt($('.quantity-number', existingProduct).text()) * parseInt($('.mcp-pro-price', existingProduct).text());

            total -= oldPrice;
        }

        if (existingProduct.length === 0) {
            this.mapper.productList.append(productDom);

            this.mapper.productLength.text(parseInt(this.mapper.productLength.text()) + 1);
        }

        const price = product.product_price * product.product_quantity;
        total += price;
        this.mapper.total.text(total);
    }

    removeProduct(id) {
        let total = parseInt(this.mapper.total.text());
        const product = $(`.single-product[data-id="${id}"]`);
        const oldPrice = parseInt($('.quantity-number', product).text()) * parseInt($('.mcp-pro-price', product).text());

        product.remove();

        total -= oldPrice;

        this.mapper.total.text(total);
        this.mapper.productLength.text(parseInt(this.mapper.productLength.text()) - 1);
    }

    template() {
        return `<div class="single-mcp clearfix single-product" data-id="product_id">
                    <div class="single-mcp-img">
                        <img src="image_link" alt="">
                    </div>
                    <div class="single-mcp-content">
                        <a class="mcp-product-name" href="#">product_name</a>
                        <span class="mcp-pro-quantity"><span class="quantity-number">product_quantity</span> x <span class="mcp-pro-price">product_price RSD</span></span>
                    </div>
                    <a href="#" class="mcp-pro-delete"><i class="fa fa-times"></i></a>
                </div>`;
    }
}

const instance = new CartDropDownDom();

Object.freeze(instance);

export default instance;