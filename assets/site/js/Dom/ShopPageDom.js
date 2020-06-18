import ShopPageMapper from "../Mapper/ShopPageMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";

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
            const productLink = Routing.generate(`site.product_page.${LOCALE}`, {'slug': product.slug});
            const wishListClass = product.has_wish == 1 ? 'fa-heart' : 'fa-heart-o';

            if (product.discount > 0) {
                oldPriceHtml = `<p class="sfi-old-price">-${product.price} RSD</p>`
            }

            html += `<div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="single-featured-item">
                            <div class="sfi-img">
                                <a href="${productLink}"><img src="${product.image_link_list}" alt="{{ default_alt_tag }}"></a>
                                <ul class="sfi-tag-list">${this.listData(data.product_tags[product.id], 'tags')}</ul>
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
                                        <li><a href="#" class="toggle-wish-list"><i class="fa ${wishListClass}"></i></a></li>
                                        <li><a href="${productLink}" ><i class="fa fa-eye"></i></a></li>
                                    </ul>
                                </div>
                                <div class="sfi-name-cat">
                                    <a class="sfi-name" href="${productLink}">${product.title}</a>
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

    listData(data, type) {
        let html = '';

        for(let i in data) {
            if (type === 'tags') {
                const tagKey = Translator.trans('tags', null, 'messages', LOCALE);

                const link = AppHelperService.generateLocalizedUrl('site.trendy_page', {'searchData': `${tagKey}/${data[i].slug}`})

                html += `<li><a href="${link}">${data[i].label}</a></li>`;

                continue;
            }
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

const shopPageDom = new ShopPageDom();
Object.freeze(shopPageDom);

export default shopPageDom;