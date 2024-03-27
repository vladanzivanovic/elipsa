import FormHelperService from "../../../../js/Helper/FormHelperService";
import couponsEditMapper from "../../Mapper/CouponsEditMapper";

class CouponApiHandler {
    #mapper;

    constructor() {
        if (!CouponApiHandler.instance) {
            this.#mapper = couponsEditMapper;

            CouponApiHandler.instance = this;
        }

        return CouponApiHandler.instance;
    }

    async save()
    {
        let result;
        let route = Routing.generate('admin.add_promotion_api');
        let type = 'POST';

        const data = FormHelperService.formToJson($(this.#mapper.form));

        if (1 === IS_EDIT) {
            route = Routing.generate('admin.edit_promotion_api', {id: ID});
            type = 'PUT';
        }


        try {
            result = await $.ajax({
                type,
                url: route,
                data: JSON.stringify(data),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Content-Language': LOCALE,
                }
            })
        }catch (error) {
            result = error;
        }

        return result;
    }
}
const couponApiHandler = new CouponApiHandler();

Object.freeze(couponApiHandler);

export default couponApiHandler;

