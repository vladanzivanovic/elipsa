class ProductBulkApiHandler {
    constructor() {
        if (!ProductBulkApiHandler.instance) {
            ProductBulkApiHandler.instance = this;
        }

        return ProductBulkApiHandler.instance;
    }

    async changeProductsHomePositions(productIds, position)
    {
        let result;

        try {
            result = await $.ajax({
                type: 'POST',
                'url': Routing.generate('admin.api_bulk_product_home_page_position', {position}),
                data: JSON.stringify({'ids': productIds}),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            })
        }catch (error) {
            console.log(error);
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
            console.log(error);
            result = error;
        }

        return result;
    }
}
const productBulkApiHandler = new ProductBulkApiHandler();

Object.freeze(productBulkApiHandler);

export default productBulkApiHandler;

