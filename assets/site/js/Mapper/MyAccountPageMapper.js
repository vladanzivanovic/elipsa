class MyAccountPageMapper {
    constructor() {
        if(!MyAccountPageMapper.instance) {
            this.personTab = '#person-info-tab';
            this.personalForm = '#personal-form';
            this.personalSaveBtn = '#personal-save-btn';
            this.personalCancelBtn = '#personal-cancel-btn';
            this.personalChangeBtn = '#personal-info-btn';
            this.orderTab = '#order-list-tab';
            this.wishTab = '#wish-list-tab';
            this.orderTable = '#order-table';

            this.personalInfoBoard = '.personal-info';
            this.personalFormBoard = '.personal-form';

            this.personalFormFields = {
                firstName: '#first_name',
                lastName: '#last_name',
                email: '#email',
                mobilePhone: '#phone',
                street: '#address',
                city: '#city',
                country: '#country',
                zipCode: '#zipCode',
                password: '#profile_password',
            };

            this.personalInfoFields = {
                mobilePhone: '.profile-info-phone',
            }

            MyAccountPageMapper.instance = this;
        }

        return MyAccountPageMapper.instance;
    }
}

const myAccountPageMapper = new MyAccountPageMapper();

Object.freeze(myAccountPageMapper);

export default myAccountPageMapper;
