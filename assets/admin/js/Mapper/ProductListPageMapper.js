import baseFormMapper from "./BaseFormMapper";

class ProductListPageMapper {
    constructor() {
        if (!ProductListPageMapper.instance) {
            const defaultMapping = Object.assign(this, baseFormMapper);

            this.filter = {
                title: '#title',
                tags: '#tags',
                categories: '#categories',
                sold: '#sold',
                homePageShow: '#home_page_show',
                productStatus: '#product_status',
                country: '#country'
            };

            this.dataTable = {
                'actionBox': '.action-box',
            }

            this.form = '#product_filter_form';
            this.resetBtn = '#reset_btn';

            ProductListPageMapper.instance = defaultMapping;
        }

        return ProductListPageMapper.instance;
    }
}

const productListPageMapper = new ProductListPageMapper();

Object.freeze(productListPageMapper);

export default productListPageMapper;
