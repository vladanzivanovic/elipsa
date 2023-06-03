class CoreMapper {
    constructor() {
        if(!CoreMapper.instance) {
            this.toggleWishListBtn = '.toggle-wish-list';
            this.newsLetterForm = '#newsLetter';
            this.newsLetterFormFooter = '#newsletter_form_footer';
            this.newsLetterSubmitBtn = '#news_letter_btn';
            this.newsLetterSubmitBtnFooter = '#newsletter_submit';
            this.newsLetterCloseBtn = '#wd1_nlpopup_close';
            this.searchOpener = '.search-opener';
            this.searchClose = '.search-close';
            this.searchArea = '.search-area';
            this.searchInput = '#search-input';
            this.searchForm = '.search-form';

            CoreMapper.instance = this;
        }

        return CoreMapper.instance;
    }
}

const coreMapper = new CoreMapper();

Object.freeze(coreMapper);

export default coreMapper;
