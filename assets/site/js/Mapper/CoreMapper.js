class CoreMapper {
    constructor() {
        if(!CoreMapper.instance) {
            this.registrationForm = '#registration-form';
            this.registrationBtn = '#registration-btn';
            this.loginForm = '#login-form';
            this.loginBtn = '#login-btn';
            this.loginEmail = '#login_email';
            this.loginPassword = '#login_password';
            this.loginCsrf = '#_csrf_token_login';
            this.toggleWishListBtn = '.toggle-wish-list';
            this.newsLetterForm = '#newsLetter';
            this.newsLetterFormFooter = '#newsletter_form_footer';
            this.newsLetterSubmitBtn = '#news_letter_btn';
            this.newsLetterSubmitBtnFooter = '#newsletter_submit';
            this.newsLetterCloseBtn = '#wd1_nlpopup_close';
            this.resetPasswordBtn = '#reset_btn';
            this.resetForm = '#reset_form';
            this.searchOpener = '.search-opener';
            this.searchClose = '.search-close';
            this.searchArea = '.search-area';
            this.searchAreaMobile = '.search-area-mobile';
            this.searchInput = '#search-input';
            this.searchSubmit = '.search-submit';
            this.searchForm = '.search-form';

            CoreMapper.instance = this;
        }

        return CoreMapper.instance;
    }
}

const coreMapper = new CoreMapper();

Object.freeze(coreMapper);

export default coreMapper;