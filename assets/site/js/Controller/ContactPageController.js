import MapsService from "../../../js/Services/MapsService";
import askUsFormMapper from "../Mapper/AskUsFormMapper";
import askUsPageValidation from "../Validators/AskUsFormValidation";
import contactHandler from "../Handler/Contact/ContactHandler";

require('inputmask/dist/jquery.inputmask.bundle');

class ContactPageController {
    #askUsFormMapper;
    #handler;

    constructor() {
        this.#askUsFormMapper = askUsFormMapper;
        this.#handler = contactHandler;
        this.gmapApi = new MapsService();

        $(this.#askUsFormMapper.fields.telephone).inputmask('(999) 99 999-999[9]');

        askUsPageValidation.validate();

        if (IS_MOBILE) {
            $('#map_canvas').addClass('mobile');
        }

        this.gmapApi.load().then(() => {
            this.gmapApi.showMap();

            this.gmapApi.registerEvents();

            this.gmapApi.getMapsDataByAddress(ADDRESS, true);
        });

        this.#registerEvents();
    }

    #registerEvents() {
        $(this.#askUsFormMapper.submitBtn).on('click', async e => {
            e.preventDefault();
            e.stopPropagation();

            await this.#handler.askUs();
        });
    }
}

export default ContactPageController;
