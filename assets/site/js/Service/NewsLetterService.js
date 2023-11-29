import newsLetterValidator from "../Validators/NewsLetterValidator";
import newsLetterMapper from "../Mapper/NewsLetterMapper";

require ('select2/dist/js/select2.full.min');

class NewsLetterService {
    #mapper;
    #validator;

    constructor() {
        if(!NewsLetterService.instance) {
            this.#mapper = newsLetterMapper;
            this.#validator = newsLetterValidator;

            NewsLetterService.instance = this;
        }

        return NewsLetterService.instance;
    }

    init()
    {
        $(`${this.#mapper.form} select`).select2({
            minimumResultsForSearch: -1
        });

        this.#validator.validate(this.#mapper.popUpForm);
        this.#validator.validate(this.#mapper.footerForm);
    }
}

const newsLetterService = new NewsLetterService();

Object.freeze(newsLetterService);

export default newsLetterService;
