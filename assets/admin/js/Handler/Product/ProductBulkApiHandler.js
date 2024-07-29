class ProductBulkApiHandler {
    constructor() {
        if (!ProductBulkApiHandler.instance) {
            ProductBulkApiHandler.instance = this;
        }

        return ProductBulkApiHandler.instance;
    }

    async changeProductsHomePositions(productIds, position, country)
    {
        let result;

        try {
            result = await $.ajax({
                type: 'POST',
                'url': Routing.generate('admin.api_bulk_product_home_page_position', {position, country}),
                data: JSON.stringify({'ids': productIds}),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            })
        }catch (error) {
            result = error;
        }

        return result;
    }

    async setProductsDiscount(productIds, discount)
    {
        let result;

        try {
            result = await $.ajax({
                type: 'POST',
                'url': Routing.generate('admin.api_bulk_products_discount'),
                data: JSON.stringify({'ids': productIds, discount}),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            })
        }catch (error) {
            result = error;
        }

        return result;
    }

    async changeHomePagePosition(products) {
       return  $.ajax({
            type: 'POST',
            'url': Routing.generate('admin.api_bulk_products_home_page_position'),
            data: JSON.stringify({ids: products}),
            dataType: 'json',
        })
    }
}
const productBulkApiHandler = new ProductBulkApiHandler();

Object.freeze(productBulkApiHandler);

export default productBulkApiHandler;

