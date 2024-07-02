import baseFormMapper from "./BaseFormMapper";

class ColorEditMapper {
    constructor() {
        if (!ColorEditMapper.instance) {
            this.color = '#color_field';

            this.fields = {};

            for(const [locale, data] of Object.entries(LANGUAGES)) {
                this.fields[`title_${locale}`] = '#title_'+locale;
            }

            ColorEditMapper.instance = Object.assign(this, baseFormMapper);
        }

        return ColorEditMapper.instance;
    }
}

const colorEditMapper = new ColorEditMapper();

Object.freeze(colorEditMapper);

export default colorEditMapper;
