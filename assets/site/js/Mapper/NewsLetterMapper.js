class NewsLetterMapper {
    constructor() {
        if(!NewsLetterMapper.instance) {
            this.form = '.news-letter-form';
            this.popUpForm = `.newsletter-pop-up-body ${this.form}`;
            this.footerForm = `.footer-newsletter-area ${this.form}`;
            // this.email = '#email';
            // this.gender = '#gender';
            // this.language = '#language';
            this.btn = '.news_letter_btn';
            this.newsLetterCloseBtn = '.newsletter-close';

            NewsLetterMapper.instance = this;
        }

        return NewsLetterMapper.instance;
    }
}

const newsLetterMapper = new NewsLetterMapper();

Object.freeze(newsLetterMapper);

export default newsLetterMapper;
