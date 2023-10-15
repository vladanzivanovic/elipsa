class CoreMapper {
    constructor() {
        if(!CoreMapper.instance) {
            this.toggleWishListBtn = '.toggle-wish-list';
            this.newsLetterForm = '#news_letter';
            this.newsLetterFormFooter = '#newsletter_form_footer';
            // this.newsLetterSubmitBtn = '#news_letter_btn';
            this.newsLetterSubmitBtnFooter = '#newsletter_submit';
            // this.newsLetterCloseBtn = '#wd1_nlpopup_close';

            CoreMapper.instance = this;
        }

        return CoreMapper.instance;
    }
}

const coreMapper = new CoreMapper();

Object.freeze(coreMapper);

export default coreMapper;
