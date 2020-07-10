class MyAccountPageMapper {
    constructor() {
        if(!MyAccountPageMapper.instance) {
            this.personTab = '#person-info-tab';
            this.personalForm = '#personal-form';
            this.personalBtn = '#personal-btn';
            this.orderTab = '#order-list-tab';
            this.wishTab = '#wish-list-tab';
            this.orderTable = '#order-table'

            MyAccountPageMapper.instance = this;
        }

        return MyAccountPageMapper.instance;
    }
}

const myAccountPageMapper = new MyAccountPageMapper();

Object.freeze(myAccountPageMapper);

export default myAccountPageMapper;