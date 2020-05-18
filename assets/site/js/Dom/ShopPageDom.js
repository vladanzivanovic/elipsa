import ShopPageMapper from "../Mapper/ShopPageMapper";

class ShopPageDom {
    constructor() {
        if (!ShopPageDom.instance) {
            this.mapper = new ShopPageMapper();

            ShopPageDom.instance = this;
        }

        return ShopPageDom.instance;
    }

    generateProducts(data) {
        let html = '';

        for(let i in data.products.data) {
            let product = data.products.data[i];
            let oldPriceHtml = '';

            if (product.discount > 0) {
                oldPriceHtml = `<p class="sfi-old-price">-${product.price} RSD</p>`
            }

            html += `<div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="single-featured-item">
                            <div class="sfi-img">
                                <a href="single-product.html"><img src="${product.image_link_list}" alt="{{ default_alt_tag }}"></a>
                                <ul class="sfi-tag-list">${this.listData(data.product_tags[product.id])}</ul>
                                <div class="sfi-img-content sfi-data-content">
                                    <p class="text-capitalize">${Translator.trans('available_colors', null, 'messages', LOCALE)}:</p>
                                    <ul class="sfi-data-color">${this.productColors(data.product_colors[product.id])}</ul>
                                    <p class="text-capitalize">${Translator.trans('available_sizes', null, 'messages', LOCALE)}:</p>
                                    <ul>${this.listData(data.product_sizes[product.id])}</ul>
                                </div>
                            </div>
                            <div class="sfi-content">
                                <div class="sfi-buttons">
                                    <ul class="clearfix">
                                        <li><a href="#"><i class="fa fa-heart-o"></i></a></li>
                                        <li><a href="#"><i class="fa fa-shopping-bag"></i></a></li>
                                        <li><a href="single-product.html" data-toggle="modal" data-target="#myModal"><i class="fa fa-eye"></i></a></li>
                                    </ul>
                                </div>
                                <div class="sfi-name-cat">
                                    <a class="sfi-name" href="single-product.html">${product.title}</a>
                                </div>
                                <div class="sfi-price-rating">
                                    <p class="sfi-price text-uppercase"><span>${product.discount > 0 ? product.discount : product.price} RSD</span></p>
                                    ${oldPriceHtml}
                                </div>
                            </div>
                        </div>
                    </div>`;
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

    productColors(colors) {
        let html = '';

        for(let i in colors) {
            html += `<li><a style="background-color: ${colors[i]}" href="#"></a></li>`;
        }

        return html;
    }

    addCriteriaOnPage(name, value, text) {

        const criteria = `<a class="btn selected-filter-btn" data-name="${name}" data-value="${value}">${text}<span class="close"></span></a>`;

        this.mapper.searchView.append(criteria);
    }
}

const instance = new ShopPageDom();
Object.freeze(instance);

export default instance;