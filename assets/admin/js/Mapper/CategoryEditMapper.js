import baseFormMapper from "./BaseFormMapper";

class CategoryEditMapper {
    constructor() {
        if (!CategoryEditMapper.instance) {
            this.parent = '#parent_category';

            this.fields = {};

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.fields[`title_${locale}`] = '#title_'+locale;
            }

            CategoryEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return CategoryEditMapper.instance;
    }
}

const categoryEditMapper = new CategoryEditMapper();

Object.freeze(categoryEditMapper);

export default categoryEditMapper;
