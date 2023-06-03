class FooterMapper {
    constructor() {
        if (!FooterMapper.instance) {
            this.loginBtn = '#footer-login-btn';
            this.registrationBtn = '#footer-registration-btn';

            FooterMapper.instance = this;
        }

        return FooterMapper.instance;
    }

}

const footerMapper = new FooterMapper();

Object.freeze(footerMapper);

export default footerMapper;
