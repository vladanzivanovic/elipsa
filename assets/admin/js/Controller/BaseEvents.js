import baseFormMapper from "../Mapper/BaseFormMapper";

class BaseEvents {
    constructor() {
        if(!BaseEvents.instance) {
            this.baseFormMapper = baseFormMapper;

            BaseEvents.instance = this;
        }

        return BaseEvents.instance;
    }

    events() {
        $(this.baseFormMapper.form).on("submit", function(event) {
            return false;
        });

        $('body').on('keyup', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (e.keyCode === 13) {
                $(this.baseFormMapper.submitBtn).trigger('click');
            }
        });
    }
}

const baseEvents = new BaseEvents();

Object.freeze(baseEvents);

export default baseEvents;
