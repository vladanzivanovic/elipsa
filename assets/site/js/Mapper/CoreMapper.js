class CoreMapper {
    constructor() {
        if(!CoreMapper.instance) {
            this.registrationForm = '#registration-form';
            this.registrationBtn = '#registration-btn';
            this.loginForm = '#login-form';
            this.loginBtn = '#login-btn';
            this.loginEmail = '#login_email';
            this.loginPassword = '#login_password';
            this.toggleWishListBtn = '.toggle-wish-list';
            this.newsLetterForm = '#wd1_nlpopup';
            this.newsLetterFormFooter = '#newsletter_form_footer';
            this.newsLetterSubmitBtn = '#news_letter_btn';
            this.newsLetterSubmitBtnFooter = '#newsletter_submit';
            this.newsLetterCloseBtn = '#wd1_nlpopup_close';

            CoreMapper.instance = this;
        }

        return CoreMapper.instance;
    }
}

const coreMapper = new CoreMapper();

Object.freeze(coreMapper);

export default coreMapper;