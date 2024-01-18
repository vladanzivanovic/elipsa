import baseFormMapper from "./BaseFormMapper";

class BlogEditMapper {
    constructor() {
        if (!BlogEditMapper.instance) {
            this.fields = {};

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.fields[`description_${locale}`] = '#description_'+locale;
                this.fields[`short_description_${locale}`] = '#short_description_'+locale;
                this.fields[`title_${locale}`] = '#title_'+locale;
            }

            this.fields.tags = '#tags';

            BlogEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return BlogEditMapper.instance;
    }
}

const blogEditMapper = new BlogEditMapper();

Object.freeze(blogEditMapper);

export default blogEditMapper;
