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
            this.sizes = '.size-table';
            this.sizeAddBtn = '.size-add-btn';
            this.sizeRemoveBtn = '.size-remove-btn';
            this.price = $('#price', baseFormMapper.form);
            this.discount = $('#discount', baseFormMapper.form);

            ProductEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return ProductEditMapper.instance;
    }
}

const productEditMapper = new ProductEditMapper();

Object.freeze(productEditMapper);

export default productEditMapper;
