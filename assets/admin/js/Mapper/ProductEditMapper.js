import baseFormMapper from "./BaseFormMapper";

class ProductEditMapper {
    constructor() {
        if (!ProductEditMapper.instance) {
            this.titleRs = $('#title_rs', baseFormMapper.form);
            this.shortDescRs = $('#short_description_rs', baseFormMapper.form);
            this.descRs = $('#description_rs', baseFormMapper.form);
            this.titleEn = $('#title_en', baseFormMapper.form);
            this.shortDescEn = $('#short_description_en', baseFormMapper.form);
            this.descEn = $('#description_en', baseFormMapper.form);
            this.code = $('#code', baseFormMapper.form);
            this.badge = $('#badge', baseFormMapper.form);
            this.category = $('#categories', baseFormMapper.form);
            this.tags = $('#tags', baseFormMapper.form);
            // this.sizes = '.size-table';
            // this.sizeAddBtn = '.size-add-btn';
            // this.sizeRemoveBtn = '.size-remove-btn';
            this.price = $('#price', baseFormMapper.form);
            this.discount = $('#discount', baseFormMapper.form);

            this.sizes = {};
            this.homePagePosition = {};

            for(const countryCode in COUNTRIES) {
                this.sizes[countryCode] = {};

                this.sizes[countryCode].table =  `.size-table-${countryCode}`;
                this.sizes[countryCode].addBtn =  `.size-add-btn-${countryCode}`;
                this.sizes[countryCode].removeBtn =  `.size-remove-btn-${countryCode}`;

                this.homePagePosition[countryCode] = {};

                this.homePagePosition[countryCode].up = `#home_page_checkbox_up_${countryCode}`;
                this.homePagePosition[countryCode].upPosition = `#home_page_slider_position_up_${countryCode}`;
                this.homePagePosition[countryCode].down = `#home_page_checkbox_down_${countryCode}`;
                this.homePagePosition[countryCode].downPosition = `#home_page_slider_position_down_${countryCode}`;
            }

            ProductEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return ProductEditMapper.instance;
    }
}

const productEditMapper = new ProductEditMapper();

Object.freeze(productEditMapper);

export default productEditMapper;
