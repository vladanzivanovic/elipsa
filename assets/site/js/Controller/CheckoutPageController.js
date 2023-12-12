import checkoutPageMapper from "../Mapper/CheckoutPageMapper";
import checkoutValidation from "../Validators/CheckoutValidation";
import RecaptchaLoader from "../../../js/Services/RecaptchaLoader";
import checkoutPageEvents from "../Events/CheckoutPageEvents";
import checkoutPageManipulator from "../Manipulator/CheckoutPageManipulator";

class CheckoutPageController {
    #pageEvents;
    #pageManipulator;
    #validator;
    #mapper;

    constructor() {
        this.#mapper = checkoutPageMapper;
        this.#validator = checkoutValidation;
        this.#pageEvents = checkoutPageEvents;
        this.#pageManipulator = checkoutPageManipulator;

        RecaptchaLoader.loadRecaptcha();

        const cachedResult = {};
        let queryString = '';

        $(this.#mapper.billingFields.address).select2({
            data: [],
            minimumInputLength: 2,
            ajax: {
                transport: (params, success, failure) => {
                    let request;

                    if (cachedResult[params.data.query] === undefined) {
                        request = $.ajax(params);
                        cachedResult[params.data.query] = null;
                        queryString = params.data.query;

                        request.then(success);
                        request.fail(failure);

                        return request;
                    }

                    request = new Promise((resolve, reject) => {
                        resolve(cachedResult[params.data.query]);
                    });

                    request.then(success);

                    return request;
                },
                url: Routing.generate('app_api.place_search'),
                delay: 250,
                data: function (params) {
                    return {query: params.term};
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    const payload = [];

                    for (const place of data) {
                        place.text = place.street;
                        place.id = place.place_id;
                    }

                    if (cachedResult[queryString] === null) {
                        cachedResult[queryString] = data;
                    }

                    return {
                        results: data
                    };
                },
            },
            templateResult: place => {
                if(place.loading) {
                    return place.text;
                }
                return $(`
                        <div>
                            <ul>
                                <li>Adresa: ${place.address_text}</li>
                            </ul>
                        </div>
                    `);
            },
            templateSelection: place => {
                $(this.#mapper.billingFields.city).val(place.city);

                return place.street;
            }
        });

        this.#pageManipulator.setPage();

        this.#validator.validate();

        this.#pageEvents.registerEvents();
    }
}

export default CheckoutPageController;
