import AppHelperService from "../../../js/Helper/AppHelperService";
import shopPageMapper from "../Mapper/ShopPageMapper";

class ShopPageDom {
    constructor() {
        if (!ShopPageDom.instance) {
            this.mapper = shopPageMapper;

            ShopPageDom.instance = this;
        }

        return ShopPageDom.instance;
    }

    generateProducts(products, removeItemsBeforeSetNew, noItems) {
        let html = '';
        const modusParam = IS_MOBILE ? 2 : 3;
        const closingRowModus = modusParam - 1;

        for(let i in products) {

            let product = products[i];
            const productLink = products[i]._links[LOCALE];
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
                                    <a class="sfi-name" href="${productLink}">${product.translations[LOCALE].title}</a>
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

        if (true === removeItemsBeforeSetNew) {
            $('.grid-items > .row').empty();
        }

        $('.grid-items > .row').append(html);


        true === noItems ? this.#hideLoadMore() : this.#showLoadMoreButton();
    };


    #hideLoadMore()
    {
        $(this.mapper.loadMore).addClass('hide');
        $(this.mapper.noMoreItemsText).removeClass('hide');
    }

    #showLoadMoreButton()
    {
        $(this.mapper.loadMore).removeClass('hide');
        $(this.mapper.noMoreItemsText).addClass('hide');
    }

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

    setFilterCounter(elm, total)
    {
        let totalText = `(${total})`;

        if (0 === total) {
            totalText = '';
        }

        elm.parents('.sidebar-box').find('.sidebar-accordion-button').find(this.mapper.filterCounter).text(`${totalText}`);
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
                <svg class='svg-stroke product-sold' viewBox='0 -0.2 6.5 15' preserveAspectRatio='none'>
                    <line x1="7" y1="-1" x2="-1" y2="11" stroke="#a70303" stroke-width=".6" stroke-opacity="1"></line>
                    <text x="0" y="8" font-size="0.04em" textLength="9em" transform="translate(-4.25, 1.8) rotate(303)" font-family="'Montserrat', sans-serif" fill="white">${Translator.trans('sold', null, 'messages', LOCALE)}</text>
                </svg>
            `;
        }

        if (product.discount !== null) {
            html += `
                <div class="sfi-img-banner product-discount">
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
