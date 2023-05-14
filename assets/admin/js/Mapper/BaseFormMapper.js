class BaseFormMapper {
    constructor() {
        if (!BaseFormMapper.instance) {
            this.form = '#edit_form';
            this.submitBtn = '#submit_btn';

            BaseFormMapper.instance = this;
        }

        return BaseFormMapper.instance;
    }
}

const baseFormMapper = new BaseFormMapper();

Object.freeze(baseFormMapper);
export default baseFormMapper;
