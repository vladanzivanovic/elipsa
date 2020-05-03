import datePicker from 'bootstrap-datepicker';
import couponsEditMapper from "../Mapper/CouponsEditMapper";
import CouponHandler from "../Handler/CouponHandler";

class CouponsEditController {
    constructor() {
        this.mapper = couponsEditMapper;
        this.handler = new CouponHandler();

        this.setDatePickerElm(this.mapper.validFrom);
        this.setDatePickerElm(this.mapper.validTo);

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
        this.mapper.submitBtn.on('click touchend', e => {
            this.handler.save()
        });
    }
}

export default CouponsEditController;