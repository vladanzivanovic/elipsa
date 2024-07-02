import baseFormMapper from "./BaseFormMapper";

class TagEditMapper {
    constructor() {
        if (!TagEditMapper.instance) {

            this.productType = '#product_type';

            this.fields = {};

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.fields[`title_${locale}`] = '#title_'+locale;
            }

            TagEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return TagEditMapper.instance;
    }
}

const tagEditMapper = new TagEditMapper();

Object.freeze(tagEditMapper);

export default tagEditMapper;
