class LoyaltyPageMapper {
    constructor() {
        if (!LoyaltyPageMapper.instance) {
            this.form = '#loyalty-form';
            this.dayField = '.bd-date';
            this.monthField = '.bd-month';
            this.yearField = '.bd-year';
            this.submitBtn = '#loyalty_submit';

            LoyaltyPageMapper.instance = this;
        }

        return  LoyaltyPageMapper.instance;
    }
}

const loyaltyPageMapper = new LoyaltyPageMapper();

Object.freeze(loyaltyPageMapper);

export default loyaltyPageMapper;