
class ProductEditMapper {
    constructor() {
        this.form = $('#edit_form');
        this.titleRs = $('#title_rs', this.form);
        this.shortDescRs = $('#short_description_rs', this.form);
        this.descRs = $('#description_rs', this.form);
        this.titleEn = $('#title_en', this.form);
        this.shortDescEn = $('#short_description_en', this.form);
        this.descEn = $('#description_en', this.form);
        this.code = $('#code', this.form);
        this.badge = $('#badge', this.form);
        this.category = $('#categories', this.form);
        this.tags = $('#tags', this.form);
        this.sizes = $('#sizes', this.form);
        this.price = $('#price', this.form);
        this.discount = $('#discount', this.form);
        this.submitBtn = $('#product_submit');
    }
}

export default ProductEditMapper;