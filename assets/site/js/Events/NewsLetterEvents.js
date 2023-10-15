import footerMapper from "../Mapper/FooterMapper";
import newsLetterMapper from "../Mapper/NewsLetterMapper";
import newsLetterService from "../Service/NewsLetterService";
import NewsLetterHandler from "../Handler/NewsLetterHandler";

class NewsLetterEvents {
    #mapper;
    #handler;
    #newsLetterService;

    constructor() {
        this.#handler = new NewsLetterHandler();
        this.#mapper = newsLetterMapper;
        this.#newsLetterService = newsLetterService;

        this.#registerEvents();
    }

    #registerEvents() {
        this.#newsLetterService.init();

        $(document).on('click touchend', this.#mapper.btn, e => {
            e.preventDefault();
            e.stopPropagation();

            this.#handler.addUser(e.currentTarget.form);
        });
    }
}

export default NewsLetterEvents;
