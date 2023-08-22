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
        const modusParam = IS_MOBILE ? 2 : 3;
        const closingRowModus = modusParam - 1;

        for(let i in data.products.data) {

            let product = data.products.data[i];
            const productLink = Routing.generate(`site.product_page.${LOCALE}`, {'slug': product.slug});
            const wishListClass = product.has_wish == 1 ? 'fa-heart' : 'fa-heart-o';

            if ((i % modusParam) === 0) {
                html += '<div class="row m-l-0 m-r-0">'
            }

            html += `<div class="col-md-4 col-sm-6 col-xs-6">
                        <div class="single-featured-item">
                            <div class="sfi-img">
                                <a href="${productLink}"><img src="${product.image.file}" alt="{{ default_alt_tag }}"></a>
                                ${ this.#setBadgeHtml(product) }
                                <div class="sfi-img-content sfi-data-content">
                                    <p class="text-capitalize">${Translator.trans('available_colors', null, 'messages', LOCALE)}:</p>
                                    <ul class="sfi-data-color">${this.productColors(product.colors)}</ul>
                                    <p class="text-capitalize">${Translator.trans('available_sizes', null, 'messages', LOCALE)}:</p>
                                    <ul>${this.listData(product.sizes, 'sizes')}</ul>
                                </div>
                            </div>
                            <div class="sfi-content">
                                <div class="sfi-buttons">
                                    <ul class="clearfix">
                                        <li><a href="#" class="toggle-wish-list" data-product-id="${product.id}"><i class="fa ${wishListClass}"></i></a></li>
                                        <li><a href="${productLink}" ><i class="fa fa-eye"></i></a></li>
                                    </ul>
                                </div>
                                <div class="sfi-name-cat">
                                    <a class="sfi-name" href="${productLink}">${product.title}</a>
                                </div>
                                <div class="sfi-price-rating">
                                    ${ this.#setPriceDom(product) }
                                </div>
                            </div>
                        </div>
                    </div>`;

            if ((i % modusParam) === closingRowModus) {
                html += '</div>'
            }
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
            if (type === 'sizes') {
                html += `<li><a href="#">${data[i].size}</a></li>`;

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

        const criteria = `<a class="btn selected-filter-btn letter-capitalize" data-name="${name}" data-value="${value}">${text}<span class="close"></span></a>`;

        this.mapper.searchView.append(criteria);
    }

    toggleWish(elm, isAdded)
    {
        if (isAdded) {
            $(elm).find('.fa-heart-o').addClass('fa-heart');
            $(elm).find('.fa-heart-o').removeClass('fa-heart-o');
        } else {
            $(elm).find('.fa-heart').addClass('fa-heart-o');
            $(elm).find('.fa-heart').removeClass('fa-heart');
        }
    }

    #setPriceDom(product) {
        if (product.discount !== null) {
            return `
                <p class="sfi-old-price">-${ product.price.amount } ${ product.price.currency }</p>
                <div class="discount-price">
                    <p class="sfi-price text-uppercase">
                        <span>${ product.discount.price.amount } ${ product.price.currency }</span>
                    </p>
                    <p class="price-saving">${ Translator.trans('saving', null, 'messages', LOCALE) } ${ product.discount.saving.amount } ${ product.discount.saving.currency }</p>
                </div>
            `;
        }

        return `
            <p class="sfi-price text-uppercase">
                <span>${ product.price.amount } ${ product.price.currency }</span>
            </p>
        `;
    }

    #setBadgeHtml(product) {
        let html = '';

        if (product.is_sold === true) {
            html += `
                <div class="product-sold">
                    <span>${Translator.trans('sold', null, 'messages', LOCALE)}</span>
                </div>
            `;
        }

        if (product.discount !== null) {
            html += `
                <div class="sfi-img-banner">
                    <span>-${product.discount.percentage} %</span>
                </div>
            `;
        }

        return html;
    }
}

const shopPageDom = new ShopPageDom();

Object.freeze(shopPageDom);

export default shopPageDom;
