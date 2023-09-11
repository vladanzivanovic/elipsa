class ShopPageProvider {
    constructor() {
        if (!ShopPageProvider.instance) {
            ShopPageProvider.instance = this;
        }

        return ShopPageProvider.instance;
    }

    /**
     *
     * @param {Object} params
     * @returns {*|jQuery}
     */
    getProducts(params)
    {
        const postParams = {};

        for (let filter in params) {
            if (params[filter] === null || 0 === params[filter].length) {
                continue;
            }

            postParams[filter] = params[filter];
        }

        return $.ajax({
            type: 'POST',
            url: Routing.generate(`site_api.shop_page.${LOCALE}`),
            data: JSON.stringify(postParams),
            dataType: 'json',
            contentType: 'application/json',
            headers: {
                'Content-Language': LOCALE,
            },
            // success: response => {
            //     $('.grid-items > .row').empty()
            //         .append(this.dom.generateProducts(response));
            //
            //     $('#scroll-to-products').trigger('click');
            //     $('#page-loader').addClass('hide');
            //
            //     this.pagination.generate(response.products.pagination);
            //
            //     $('#locale-dropdown li a').attr('href', response.localized_url);
            // },
            // error: error => {
            //
            // }
        })
    }
}

const shopPageProvider = new ShopPageProvider();

Object.freeze(shopPageProvider);

export default shopPageProvider;
