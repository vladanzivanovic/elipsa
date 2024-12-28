import couponsEditMapper from "../Mapper/CouponsEditMapper";

class PromotionEditDom {
    #mapper;

    constructor() {
        if (!PromotionEditDom.instance) {
            this.#mapper = couponsEditMapper;

            PromotionEditDom.instance = this;
        }

        return PromotionEditDom.instance;
    }

    preparePage()
    {
        this.#setDatePickerElm($(this.#mapper.fields.validFrom));
        this.#setDatePickerElm($(this.#mapper.fields.validTo));
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
                        let trans = product.translations.rs;

                        if (trans === undefined) {
                            trans = product.translations.ba;
                        }

                        payload.push({
                            text: trans.title,
                            id: product.id,
                        });
                    }

                    return {
                        results: payload
                    };
                }
            }
        });

        $(this.#mapper.fields.colors).select2({
            templateSelection: this.#optionCallback,
            templateResult: this.#optionCallback,
        });

        this.manageFieldsByPromotionType();
    }

    manageFieldsByPromotionType()
    {
        const selectedType = $(this.#mapper.fields.type).val();

        switch (selectedType) {
            case PROMOTION_TYPES.TYPE_FREE_SHIPPING:
                $(this.#mapper.fields.allProductsChk).prop('checked', true);
                $(this.#mapper.fields.discount).val(0);
                $(this.#mapper.fields.discount).prop('readonly', true);
                break;
            default:
                $(this.#mapper.fields.discount).prop('readonly', false);

                if (!IS_EDIT) {
                    $(this.#mapper.fields.allProductsChk).prop('checked', false);
                    $(this.#mapper.fields.discount).val();
                }
        }
    }

    #prepareProductPreselectedData()
    {
        for (const index in PRODUCTS) {
            PRODUCTS[index].selected = true;
        }
    }

    #setDatePickerElm(elm) {
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

    #optionCallback(state)
    {
        let $state = null;
        if (state.element) {
            $state = $(
                `<div class="d-flex justify-content-start align-items-center">
                    <span style="background: ${state.element.dataset.hex};width: 20px; height: 20px; display: block; border: 1px solid #000; margin-right: 15px"></span>
                    ${state.element.dataset.title}
                </div>`
            );
        }
        return $state;
    }
}
const promotionEditDom = new PromotionEditDom();

Object.freeze(promotionEditDom);

export default promotionEditDom;
