class RegistrationMapper {
    constructor() {
        if (!RegistrationMapper.instance) {
            this.form = '#registration_form';
            this.submitBtn = '#submit';

            this.fields = {
                mobilePhone: '#mobile_phone',
                zipCode: '#zip-code',
            };

            RegistrationMapper.instance = this;
        }

        return  RegistrationMapper.instance;
    }
}

const registrationMapper = new RegistrationMapper();

Object.freeze(registrationMapper);

export default registrationMapper;
