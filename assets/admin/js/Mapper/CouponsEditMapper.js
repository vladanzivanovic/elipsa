import baseFormMapper from "./BaseFormMapper";

class CouponsEditMapper {
    constructor() {
        if (!CouponsEditMapper.instance) {
            const defaultMapping = Object.assign(this, baseFormMapper);

            this.fields = {
                validFrom: '#datePicker_valid_from',
                validTo: '#datePicker_valid_to',
                categories: '#categories',
                tags: '#tags',
                products: '#products',
            };

            CouponsEditMapper.instance = defaultMapping;
        }

        return CouponsEditMapper.instance;
    }
}

const couponsEditMapper = new CouponsEditMapper();

Object.freeze(couponsEditMapper);

export default couponsEditMapper;
