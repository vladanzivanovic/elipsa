import datePicker from 'bootstrap-datepicker';
import couponsEditMapper from "../Mapper/CouponsEditMapper";
import CouponHandler from "../Handler/Coupon/CouponHandler";
import couponsEditValidator from "../Validators/CouponsEditValidator";

class CouponsEditController {
    #mapper;
    #handler;
    #validator;

    constructor() {
        this.#mapper = couponsEditMapper;
        this.#handler = new CouponHandler();
        this.#validator = couponsEditValidator;

        this.setDatePickerElm($(this.#mapper.fields.validFrom));
        this.setDatePickerElm($(this.#mapper.fields.validTo));

        this.#prepareProductPreselectedData();

        $(`${this.#mapper.form} select`).select2();
        $(this.#mapper.fields.products).select2({
            data: PRODUCTS,
            minimumInputLength: 4,
            ajax: {
                url: Routing.generate('admin.product_search_name_api'),
                data: function (params) {
                    return {query: params.term};
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    const payload = [];

                    for (const product of data.payload) {
                        payload.push({
                            text: product.translations.rs.title,
                            id: product.id,
                        });
                    }

                    return {
                        results: payload
                    };
                }
            }
        });

        this.#validator.validate();

        this.registerEvents();
    }

    setDatePickerElm(elm) {
        const date = new Date();
        const today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        elm.datepicker({
            format: "dd.mm.yyyy",
            todayHighlight: true,
            autoclose: true
        });

        if (!IS_EDIT) {
            elm.datepicker('setDate', today);
        }
    }

    registerEvents() {
        $(this.#mapper.submitBtn).on('click touchend', e => {
            this.#handler.save();
        });
    }

    #prepareProductPreselectedData()
    {
        for (const index in PRODUCTS) {
            PRODUCTS[index].selected = true;
        }
    }
}

export default CouponsEditController;
